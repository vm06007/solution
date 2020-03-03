<?php


namespace App\Controller;


use App\Service\ArticleService;
use App\Service\RSSService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpKernel\Exception\HttpException;
use Symfony\Component\Routing\Annotation\Route;

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

}