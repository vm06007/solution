<?php

namespace App\Tests;

use App\Service\RSSService;
use PHPUnit\Framework\TestCase;

class RssServiceTest extends TestCase
{


    public function testGetRssFeedItems()
    {
        $rss_source = 'https://www.theregister.co.uk/software/headlines.atom';

        $rssService = new RSSService($rss_source);
        $result = $rssService->getRssFeedItems();

        // assert that your calculator added the numbers correctly!
        $this->assertEquals(50, count($result));
    }

}