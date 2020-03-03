<?php


namespace App\Controller;


use App\Entity\User;
use App\Repository\UserRepository;
use App\Service\ArticleService;
use App\Service\RSSService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Exception\HttpException;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\User\UserInterface;

class APIController extends AbstractController
{

    /**
     * @Route("/api/feed/list", name="rss_feed",methods={"GET"})
     * @param RSSService $RSSService
     * @param ArticleService $articleService
     * @return JsonResponse
     */
    public function getRssFeed(RSSService $RSSService, ArticleService $articleService){

        $feed = $RSSService->getRssFeedItems();

        try {
            $statistic = $articleService->refreshArticles($feed);
        } catch (\Exception $exception) {
            throw new HttpException(500,$exception->getMessage());
        }

        return new JsonResponse(['articles'=>$statistic['articles'],'summary'=>$statistic['summary'],'status'=>true]);

    }

    /**
     * @Route("/email", name="check_email",methods={"POST"})
     * @return JsonResponse
     */
    public function getEmail(Request $request,UserRepository $userRepository){

        if (!empty($request->getContent())) {
            $data = json_decode($request->getContent(), true);
            if (json_last_error() !== JSON_ERROR_NONE) {
                throw new HttpException(400, 'Invalid json >' . $request->getContent() . '>');
            }
        } else {
            throw new HttpException(400, 'Empty request');
        }

        $email = $data['email'];

        $user = $userRepository->findOneBy(['email'=>$email]);

        return new JsonResponse(['exist'=>($user instanceof UserInterface),'email'=>$email,'status'=>true]);

    }
}