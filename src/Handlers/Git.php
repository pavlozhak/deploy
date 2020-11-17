<?php

namespace Deploy\Handlers;

use Deploy\Exception\DeployException;
use Deploy\Config\Config;
use Deploy\Notification\TelegramNotification;
use stdClass;

class Git {

    const GIT_IP = ['207.97.227.253', '50.57.128.197', '108.171.174.178', '50.57.231.61'];
    const GIT_URL = 'https://github.com/';
    const GIT_REFS = 'refs/heads/';

    private $payload;
    private $config;

    public function __construct() {
        $this->config = Config::get_config();
        $this->check_github_ip();
        $this->get_payload();
    }

    public function check_github_ip() {
        if(!in_array($_SERVER['REMOTE_ADDR'], self::GIT_IP)) {
            DeployException::set_exception(Git::class, 'This does not appear to be a valid requests from Github');
        }

        return TRUE;
    }

    public function get_payload() {
        if(empty($_POST['payload'])) {
            DeployException::set_exception(Git::class, 'Empty payload');
        }

        $this->payload = json_decode($_POST['payload'], FALSE);
    }

    public function update() {
        foreach ($this->config->git->endpoints as $endpoint) {
            if($this->payload->repository->url == self::GIT_URL . $endpoint->repo && $this->payload->ref == self::GIT_REFS . $endpoint->branch) {
                ob_start();
                passthru($endpoint->run);
                $output = ob_get_contents();
                ob_end_clean();

                if($this->config->notification->send) {
                    $message = new stdClass();
                    $message->payload = $this->payload;
                    $message->output = $output;

                    TelegramNotification::send_message($message);
                }
            }
            else {
                DeployException::set_exception(Git::class, 'This does not appear to be a valid requests from Github');
            }
        }
    }

}
