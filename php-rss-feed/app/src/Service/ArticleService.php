<?php


namespace App\Service;


use App\Entity\Article;
use App\Repository\ArticleRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class ArticleService extends AbstractController
{

    /**
     * @var ArticleRepository
     */
    private $repository;

    /**
     * @var EntityManagerInterface
     */
    private $em;

    /**
     * @var TextManipulationService
     */
    private $text;

    /**
     * @var HtmlService
     */
    private $html;

    /**
     * @var array
     */
    private $articles;

    /**
     * @var array
     */
    private $words;

    public function __construct(
        ArticleRepository $articleRepository,
        TextManipulationService $textManipulationService,
        HtmlService $htmlService,
        EntityManagerInterface $entityManager
    )
    {
        $this->repository = $articleRepository;

        $this->em = $entityManager;

        $this->text = $textManipulationService;

        $this->html = $htmlService;

        $this->words = [];

        $this->articles = [];
    }

    /**
     * @param array $list
     * @return array
     * @throws \Exception
     */
    public function refreshArticles(array $list)
    {

        // Get articles from DB
        $entities = $this->repository->findAll();

        // Articles unique codes from DB
        $articles_unique_code = array_map(function ($item) {
            /** @var Article $item */
            return $item->getUniqueCode();
        }, $entities);

        // Words arrays from DB
        $this->words = array_map(function ($item) {
            /** @var Article $item */
            return $item->getWords();
        }, $entities);

        // Check if we have article not in DB
        $new_article = [];
        foreach ($list as $key => $item) {
            if (!in_array($item['unique_code'], $articles_unique_code)) {
                array_push($new_article, $item);
            } else {
                array_push($this->articles,$item);
            }
        }

        // Add new articles
        try {
            if (count($new_article)) {
                $this->addNewArticles($new_article);
            }
        } catch (\Exception $exception){
            throw new \Exception($exception->getMessage(),$exception->getCode());
        }

        // Count summary words usage
        $summary = $this->countSummary();

        return ['articles' => $this->articles, 'summary' => $summary];
    }

    /**
     * @return array
     */
    private function countSummary(): array
    {

        $result = [];
        foreach ($this->words as $item) {
            foreach ($item as $word) {
                if (!in_array((string)$word['word'], array_keys($result))) {
                    $result[(string)$word['word']] = 0;
                }
                try {
                    $result[(string)$word['word']] += $word['counter'];
                } catch (\Exception $exception){
                    trigger_error($exception);
                }
            }
        }

        arsort($result);

        return array_slice($result, 0, 20);
    }


    /**
     * @param array $articles
     * @throws \Exception
     */
    public function addNewArticles(array $articles):void
    {
        $exclude = ["the", "be", "to", "of", "and", "a", "in", "that", "have", "i", "it", "for", "not", "on", "with", "he", "as", "you", "do", "at", "this", "but", "his", "by", "from", "they", "we", "say", "her", "she", "or", "an", "will", "my", "one", "all", "would", "there", "their", "what", "so", "up", "out", "if", "about", "who", "get", "which", "go", "me"];

        foreach ($articles as $item) {

            $article = $this->html->getArticleDataByURL($item['link']);

            $words = $this->text->fetchWordsArrayFromText($article['text']);

            // sort words bt value
            arsort($words);

            // Remove values from result array by removing words equal $exclude
            $result = [];
            foreach ($words as $key => $value) {
                if (!in_array($key, $exclude) && count($result) < 50 && strlen($key)>1) {
                    array_push($result, ['word' => $key, 'counter' => $value]);
                }
            }

            // Store in DB
            try {
                $this->createArticle($item, $result);
            } catch (\Exception $exception){
                throw new \Exception($exception->getMessage(),$exception->getCode());
            }
        }
    }


    /**
     * @param array $data
     * @param array $words
     * @throws \Exception
     */
    public function createArticle(array $data, array $words):void
    {

        $entity = new Article();

        $entity
            ->setUpdated(new \DateTime($data['updated']))
            ->setUniqueCode($data['unique_code'])
            ->setWords($words);

        array_push($this->articles, $this->transformArticles($entity));
        array_push($this->words,$words);

        try {
            $this->em->persist($entity);
            $this->em->flush();
            $this->em->clear();
        } catch (\Exception $exception){
            throw new \Exception($exception->getMessage(),$exception->getCode());
        }

    }

    /**
     * @param Article $article
     * @param bool $add_words
     * @return array
     */
    private function transformArticles(Article $article, bool $add_words = false):array
    {
        return [
            'id' => $article->getId(),
            'updated' => $article->getUpdated(),
            'unique_code' => $article->getUniqueCode(),
            'words' => ($add_words)?$article->getWords():[]
        ];

    }

}