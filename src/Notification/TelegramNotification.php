<?php
namespace Deploy\Notification;

use Deploy\Config\Config;
use Http\Adapter\Guzzle6\Client;
use Http\Factory\Guzzle\RequestFactory;
use Http\Factory\Guzzle\StreamFactory;
use stdClass;
use TgBotApi\BotApiBase\ApiClient;
use TgBotApi\BotApiBase\BotApi;
use TgBotApi\BotApiBase\BotApiNormalizer;
use TgBotApi\BotApiBase\Method\SendMessageMethod;

class TelegramNotification {

    public static function prepare_message(stdClass $message) {
        $notify_header = "🚀" . " <b>***** St-Dev Deploy *****</b>" . "🚀" . PHP_EOL . PHP_EOL;
        $notify_footer = "<b>Deploy end</b>";

        $notify_body = 'The Github user <a href="https://github.com/'
                       . $message->payload->pusher->name .'">@' . $message->payload->pusher->name . '</a>'
                       . ' has pushed to ' . $message->payload->repository->url . PHP_EOL . PHP_EOL;

        $notify_body .= 'Here\'s a brief list of what has been changed:' . PHP_EOL . PHP_EOL;


        foreach ($message->payload->commits as $commit) {
            $notify_body .= "✅" . $commit->message . PHP_EOL;
            $notify_body .= 'Added: <i>' . count($commit->added) . PHP_EOL
                .'</i>modified: <i>' . count($commit->modified) . PHP_EOL
                .'</i>removed: <i>'.count($commit->removed) . PHP_EOL
                .'</i><a href="' . $commit->url
                . '">read more</a>' . PHP_EOL . PHP_EOL;
        }

        $notify_body .= '<b>What follows is the output of the script:</b>' . PHP_EOL;
        $notify_body .= '<pre>' . $message->output . '</pre>' . PHP_EOL . PHP_EOL;

        return $notify_header . $notify_body . $notify_footer;
    }

    public static function send_message(stdClass $message) {
        $config = Config::get_config();
        $prepared_message = self::prepare_message($message);

        $requestFactory = new RequestFactory();
        $streamFactory = new StreamFactory();
        $client = new Client();

        $apiClient = new ApiClient($requestFactory, $streamFactory, $client);
        $bot = new BotApi($config->notification->channels->telegram->bot_token, $apiClient, new BotApiNormalizer());

        $userId = $config->notification->channels->telegram->chat_id;
        $m = SendMessageMethod::create($userId, $prepared_message);
        $m->parseMode = 'HTML';
        $bot->send($m);
    }

}
