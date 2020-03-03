<?php

namespace App\Controller\Security;

use App\Events\RestorePasswordEvent;
use App\Repository\UserRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Validator\Constraints as Assert;
use Ramsey\Uuid\Uuid;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\HttpException;
use Symfony\Component\PropertyAccess\PropertyAccess;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;
use Symfony\Component\Validator\ConstraintViolationListInterface;
use Symfony\Component\Validator\Validator\ValidatorInterface;
use Exception;

class SecurityController extends AbstractController
{
    private $validator;

    public function __construct(
        ValidatorInterface $validator
    )
    {
        $this->validator = $validator;
    }

    /**
     * @Route("/login", name="app_login")
     * @param AuthenticationUtils $authenticationUtils
     * @return Response
     */
    public function login(AuthenticationUtils $authenticationUtils): Response
    {
        // if ($this->getUser()) {
        //     return $this->redirectToRoute('target_path');
        // }

        // get the login error if there is one
        $error = $authenticationUtils->getLastAuthenticationError();
        // last username entered by the user
        $lastUsername = $authenticationUtils->getLastUsername();

        return $this->render('security/login.html.twig', ['last_username' => $lastUsername, 'error' => $error, 'user' => '']);
    }

    /**
     * @Route("/logout", name="app_logout")
     */
    public function logout()
    {
        throw new Exception('This method can be blank - it will be intercepted by the logout key on your firewall');
    }

    /**
     * @Route("/forget", name="forget",methods={"GET","POST"})
     * @param Request $request
     * @param UserRepository $userRepository
     * @param EntityManagerInterface $entityManager
     * @param EventDispatcherInterface $eventDispatcher
     * @return Response
     * @throws Exception
     */
    public function forget(
        Request $request,
        UserRepository $userRepository,
        EntityManagerInterface $entityManager,
        EventDispatcherInterface $eventDispatcher): Response
    {

        if ($request->getMethod() == 'POST') {

            $email = $request->request->get('email');

            $user = $userRepository->findOneBy(['email' => $email]);

            if (!$user) {
                return $this->render('security/sent.html.twig', ['last_username' => $email, 'error' => true]);
            }

            try {
                $code = str_replace('-', '', Uuid::uuid4()->toString());
            } catch (Exception $exception) {
                $code = random_bytes(32);
            }

            $user->setRecover($code);

            $entityManager->persist($user);
            $entityManager->flush();
            $entityManager->clear();

            $eventDispatcher->dispatch(new RestorePasswordEvent($user), RestorePasswordEvent::SEND_RESTORE_PASSWORD_EMAIL);

            return $this->render('security/sent.html.twig', ['last_username' => $user->getEmail(), 'error' => null]);

        } else {
            return $this->render('security/forget.html.twig', ['error' => []]);
        }
    }

    /**
     * @Route("/recover/{code}", name="recover",methods={"GET","POST"})
     * @param Request $request
     * @param UserRepository $userRepository
     * @param EntityManagerInterface $em
     * @param UserPasswordEncoderInterface $encoder
     * @param $code
     * @return Response
     */
    public function test_recover(
        Request $request,
        UserRepository $userRepository,
        EntityManagerInterface $em,
        UserPasswordEncoderInterface $encoder,
        $code
    ): Response
    {

        if (empty($code)) {
            $this->redirect('/');
        }

        if ($request->getMethod() == 'POST') {

            $user = $userRepository->findOneBy(['recover' => $code]);

            if (!$user) {
                $this->redirect('/');
                throw new HttpException(404, 'Page not found');
            }

            $password1 = $request->request->get('password1');
            $password2 = $request->request->get('password2');

            $violations = $this->checkPassword($password1, $password2);

            if (count($violations) > 0) {

                $accessor = PropertyAccess::createPropertyAccessor();

                $errorMessages = [];

                foreach ($violations as $violation) {

                    $accessor->setValue($errorMessages,
                        $violation->getPropertyPath(),
                        $violation->getMessage());
                }

                return $this->render('security/passwordchanged.html.twig', ['error' => join(',', $errorMessages)]);
            } else {

                $user->setPassword($encoder->encodePassword($user, $password1));
                $user->setRecover(null);

                $em->persist($user);
                $em->flush();
                $em->clear();

                return $this->render('security/passwordchanged.html.twig', ['error' => '']);
            }

        } else {

            $user = $userRepository->findOneBy(['recover' => $code]);

            if (!$user) {
                throw new HttpException(404, 'User not found');
            }

            return $this->render('security/newpasswords.html.twig', ['error' => []]);
        }


    }

    /**
     * @param $password1
     * @param $password2
     * @return ConstraintViolationListInterface
     */
    private function checkPassword(
        $password1, $password2
    )
    {

        $input = ['password1' => $password1, 'password2' => $password2];

        $constraints = new Assert\Collection([
            'password1' => [
                new Assert\Length(['min' => 6, 'max' => 32]),
                new Assert\NotBlank,
//                new Assert\Regex(['pattern' => "/[a-z]/", 'match' => true, 'message' => 'Password must contain at least one lowercase char']),
//                new Assert\Regex(['pattern' => "/[A-Z]/", 'match' => true, 'message' => 'Password must contain at least one uppercase char']),
//                new Assert\Regex(['pattern' => "/\d/", 'match' => true, 'message' => 'Password must contain at least one number']),
//                new Assert\Regex(['pattern' => "/[$&+,:;=?@#|'<>.^*()%!-]/", 'match' => true, 'message' => 'Password must contain at least one special char'])
            ],
            'password2' => [
                new Assert\EqualTo(['value' => $password1, 'message' => 'Password must be equal to each other'])
            ],
        ]);

        $violations = $this->validator->validate($input, $constraints);

        return $violations;

    }
}
