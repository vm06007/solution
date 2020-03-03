<?php

namespace App\Controller;

use App\Repository\UserRepository;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Security;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\HttpFoundation\Response;

class PageController extends AbstractController
{
    /**
     * @Route("/", name="app_index")
     * @param Security $security
     * @return Response
     */
    public function index(Security $security): Response
    {
        return $this->render('page/index.html.twig', [
            'controller_name' => 'PageController',
            'user' => ($security->getUser()) ? json_encode($this->userData($security->getUser())) : json_encode([])
        ]);
    }

    private function userData(UserInterface $user)
    {
        $result = [
            'username' => $user->getUsername(),
            'roles' => $user->getRoles(),
        ];

        return $result;

    }

}
