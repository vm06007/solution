<?php


namespace App\Service;


use Twig\Environment;
use Swift_Mailer;
use Swift_Message;
use Swift_Attachment;
use Swift_Plugins_Loggers_ArrayLogger;
use Swift_Plugins_LoggerPlugin;
use Exception;

class EmailService
{

    /**
     * @var Swift_Mailer
     */
    private $mailer;

    /**
     * @var Environment
     */
    private $twig;

    /**
     * @var Swift_Plugins_Loggers_ArrayLogger
     */
    private $swift_logger;

    /**
     * @var LoggerService
     */
    private $logger;

    public function __construct(Swift_Mailer $mailer, LoggerService $loggerService, Environment $twig)
    {
        $this->mailer = $mailer;

        $this->twig = $twig;

        $this->logger = $loggerService;

        $this->swift_logger = new Swift_Plugins_Loggers_ArrayLogger();

        $this->mailer->registerPlugin(new Swift_Plugins_LoggerPlugin($this->swift_logger));
    }

    /**
     * @param $template
     * @param $sender
     * @param $recipients
     * @param $subject
     * @param $data
     * @param string $reply
     * @param array $attach
     * @throws Exception
     */
    public function sendContactMail($template, $sender, $recipients, $subject, $data, $reply = '', $attach = [])
    {

        if (!empty($template)) {
            try {
                $body = $this->twig->render(
                    $template,
                    $data
                );
            } catch (Exception $exception) {
                throw new Exception($exception->getMessage());
            }
        } else {
            $body = $data;
        }

        try {
            $message = (new Swift_Message($subject))
                ->setFrom($sender)
                ->setTo($recipients)
                ->setBody(
                    $body,
                    'text/html'
                );

            if (!empty($reply)) {
                $message->setReplyTo($reply);
            }

            if (count($attach)) {
                foreach ($attach as $item) {
                    $message->attach(Swift_Attachment::fromPath($item['path'])->setFilename($item['title']));
                }
            }

            $this->mailer->send($message);
            $this->logger->info($this->swift_logger->dump());
        } catch (Exception $exception) {
            $this->logger->error($exception->getMessage());
            throw new Exception($exception->getMessage());
        }
    }
}