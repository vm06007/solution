<?php


namespace App\Service;


use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class RSSService extends AbstractController
{

    /**
     * @var string
     */
    private $source;

    public function __construct(string $rssSource)
    {

        $this->source = $rssSource;

    }

    /**
     * @return \SimpleXMLElement
     */
    public function getRssFeedRaw(){

        $rss = simplexml_load_file($this->source);

        return $rss;

    }


    /**
     * @return array
     */
    public function getRssFeedItems(){

        $raw = $this->getRssFeedRaw();

        $raw->registerXPathNamespace('x', 'http://www.w3.org/2005/Atom');

        $list = $raw->xpath('//x:entry');

        $result = [];
        foreach ($list as $item){
            array_push($result,$this->transformFeed($item));
        }

        return $result;

    }

    /**
     * @param \SimpleXMLElement $data
     * @return array
     */
    private function transformFeed(\SimpleXMLElement $data):array {

        return [
            'unique_code'=>(string)$data->id[0],
            'updated'=>(string)$data->updated[0],
            'link'=>(string)$data->link[0]->attributes()->href[0],
            'title'=>(string)$data->title[0]
        ];
    }


}