<?php

namespace App\Tests;

use App\Service\HtmlService;

use PHPUnit\Framework\TestCase;

class HtmlServiceTest extends TestCase
{


    public function testGetHtmlPageByUrl()
    {
        $url = 'https://www.theregister.co.uk/2020/02/14/windows_terminal_and_azure_data_studio_both_get_a_tickle_from_the_microsoft_update_fairy/';

        $htmlService = new HtmlService();
        $result = $htmlService->getHtmlPageByUrl($url);
        $this->assertGreaterThan(1000, strlen($result));

    }

    public function testExtractArticleDataFromHtml()
    {
        $url = 'https://www.theregister.co.uk/2020/02/14/windows_terminal_and_azure_data_studio_both_get_a_tickle_from_the_microsoft_update_fairy/';

        $htmlService = new HtmlService();
        $result = $htmlService->getHtmlPageByUrl($url);
        $article = $htmlService->extractArticleDataFromHtml($result);

        $this->assertEquals(3,count(array_keys($article)));

    }


}