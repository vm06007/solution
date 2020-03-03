<?php


namespace App\Service;


use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class TextManipulationService extends AbstractController
{

    /**
     * @param string $text
     * @return array
     */
    public function fetchWordsArrayFromText(string $text){

        $words = $this->getArrayOfWordsFromString($text);

        $result = $this->countWordsInArray($words);

        return $result;

    }


    /**
     * @param string $text
     * @return array
     */
    public function getArrayOfWordsFromString(string $text):array {

        preg_match_all("/(\w+)/", strtolower($text), $matches);

        if (count($matches[1])) {
            return $matches[1];
        } else {
            return [];
        }

    }


    /**
     * @param array $data
     * @return array
     */
    public function countWordsInArray(array $data):array {

        $result = [];

        foreach ($data as $item){
            if (!is_numeric($item)) {
                if (!isset($result[$item])) {
                    $result[$item] = 0;
                }
                $result[$item]++;
            }
        }

        ksort($result);

        return $result;

    }

}