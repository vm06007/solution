<?php
/**
 * Created by PhpStorm.
 * User: Дима
 * Date: 13.04.2019
 * Time: 15:00
 */

namespace App\Service;

use Fluent\Logger\FluentLogger;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class LoggerService extends AbstractController
{

    private $fluent;
    private $host;
    private $app;
    private $remote_host;
    private $remote_port;
    private $uuid4;
    private $from_type;


    public function __construct(string $host, string $app_name, string $remote_host, string $remote_port)
    {
        $this->host = $host;
        $this->app = $app_name;
        $this->remote_host = $remote_host;
        $this->remote_port = $remote_port;
    }

    public function setAppName(string $log_name){
        $this->app = $log_name;
        return $this;
    }

    public function setAppType(int $from_type){
        $this->from_type = $from_type;
        return $this;
    }

    public function setLogName($uuid4 = ''){
        $this->uuid4 = $uuid4;
        return $this;
    }

    public function debug($message,$message_array = [],$show = false){
        if ($this->from_type < 2) {
            $message = is_array($message) ? $message : [$message];
            $this->fluent = new FluentLogger($this->remote_host, $this->remote_port);
            $this->fluent->post($this->app,
                array_merge(
                    $this->custom_format('DEBUG'),
                    $message_array,
                    [
                        'message' => $message,
                        'link' => $this->uuid4
                    ]
                ));
        }
        if ($show) print $this->current_time().(is_array($message)?json_encode($message):$message).PHP_EOL;
    }

    public function info($message, $message_array = [], $show = false){

        if ($this->from_type < 3) {
            $message = is_array($message) ? $message : [$message];
            $this->fluent = new FluentLogger($this->remote_host, $this->remote_port);
            $this->fluent->post(
                $this->app,
                array_merge(
                    $this->custom_format('INFO'),
                    $message_array,
                    [
                        'message' => $message,
                        'link' => $this->uuid4
                    ]));
        }

        if ($show) print $this->current_time().(is_array($message)?json_encode($message):$message).PHP_EOL;
    }

    public function error($message, $message_array = [], $show = false){

        if ($show) print $this->current_time().(is_array($message)?json_encode($message):$message).PHP_EOL;

        $message = is_array($message) ? $message : [$message];
        $this->fluent = new FluentLogger($this->remote_host, $this->remote_port);
        $this->fluent->post(
            $this->app,
            array_merge(
                $this->custom_format('ERROR'),
                $message_array,
                [
                    'message' => $message,
                    'link' => $this->uuid4
                ]));

    }

    public function warning($message, $message_array = [], $show = false) {

        if ($show) print $this->current_time().(is_array($message)?json_encode($message):$message).PHP_EOL;

        if ($this->from_type < 4) {

            $message = is_array($message) ? $message : [$message];
            $this->fluent = new FluentLogger($this->remote_host, $this->remote_port);
            $this->fluent->post(
                $this->app,
                array_merge(
                    $this->custom_format('WARNING'),
                    $message_array,
                    [
                        'message' => $message,
                        'link' => $this->uuid4
                    ]));
        }

    }

    private function current_time(){
        return '[' . date('H:i:s') . '] ';
    }

    private function custom_format($type)
    {
        return [
            'username' => ($this->getUser())?$this->getUser()->getUsername():null,
            'role' => ($this->getUser())?$this->getUser()->getRoles():null,
            'host' => $this->host,
            'where' => '',
            'type' => $type,
            'created' => microtime(true),
            'stack_trace' => '',
            'stack_info' => '',
        ];
    }

}