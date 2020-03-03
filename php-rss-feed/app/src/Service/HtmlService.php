<?php


namespace App\Service;


use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\DomCrawler\Crawler;

class HtmlService extends AbstractController
{

    /**
     * @param string $url
     * @return array
     */
    public function getArticleDataByURL(string $url):array {

        $html = $this->getHtmlPageByUrl($url);

        $article = $this->extractArticleDataFromHtml($html);

        return $article;

    }

    /**
     * @param string $url
     * @return string
     */
    public function getHtmlPageByUrl(string $url):string {

        $result = file_get_contents($url);

        return $result;
    }

    /**
     * @param string $html
     * @return array
     */
    public function extractArticleDataFromHtml(string $html):array {

        $crawler = new Crawler($html);

        // Remove <script></script> from article body
        $crawler->filter('html script')->each(function (Crawler $crawler) {
            $node = $crawler->getNode(0);
            $node->parentNode->removeChild($node);
        });

        $title = $crawler->filterXPath('//h1')->first()->text('',true);

        $subTitle = $crawler->filterXPath('//h2')->first()->text('',true);

        $text = $crawler->filterXPath('//div [contains(@id, "body")]')->text('',true);

        $result = [
            'title'=>$title,
            'subtitle'=>$subTitle,
            'text'=>$text
        ];

        return $result;
    }








}