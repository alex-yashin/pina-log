<?php

namespace PinaLog;

use Pina\Input;
use Pina\Url;

class LogProcessor
{
    private $protectedFields = [
        'apikey',
        'password', 'password2', 'new_password', 'new_password2',
        'csrf_token',
        'passport_no', 'passport_date', 'passport_expired_at', 'address',
        'email', 'phone', 'vk', 'facebook', 'skype', 'website', 'instagram', 'whatsapp', 'telegram',
        'comment', 'code',
        'street', 'building', 'block', 'apartment'
    ];

    /**
     * @param array $record
     * @return array
     * @throws \Psr\Container\ContainerExceptionInterface
     * @throws \Psr\Container\NotFoundExceptionInterface
     */
    public function __invoke(array $record): array
    {
        $record['extra']['user_id'] = $_SERVER['USER_ID'];
        if (isset($_SERVER["REQUEST_METHOD"])) {
            $record['extra']['method'] = Input::getMethod();
            $record['extra']['resource'] = Input::getResource();
            list($controller, $action, $parsed) = Url::route(Input::getResource(), Input::getMethod());
            $record['extra']['controller'] = $controller;
            $record['extra']['action'] = $action;
        }
        $record['extra']['request_id'] = $_SERVER["REQUEST_ID"] ?? '';

        if (defined('PINA_STARTED')) {
            $record['extra']['time'] = round(array_sum(explode(' ', microtime())) - PINA_STARTED, 4);
        }

        if (!empty($_SERVER)) {
            $record['extra']['http'] = [];
            foreach ($_SERVER as $k => $v) {
                if ($k == 'HTTP_COOKIE') {
                    continue;
                }
                if (strpos($k, 'HTTP_') === 0) {
                    $record['extra']['http'][substr($k, 5)] = $v;
                }
            }
        }

        foreach ($this->protectedFields as $field) {
            if (!isset($record['context'][$field])) {
                continue;
            }
            $record['context'][$field] = '***';
        }

        return [
            'datetime' => $record['datetime'],
            'channel' => $record['channel'],
            'level' => $record['level'],
            'level_name' => $record['level_name'],
            'user_id' => $record['extra']['user_id'] ?? 0,
            'time' => $record['extra']['time'] ?? '',
            'method' => $record['extra']['method'] ?? '',
            'resource' => $record['extra']['resource'] ?? '',
            'controller' => $record['extra']['controller'] ?? '',
            'action' => $record['extra']['action'] ?? '',
            'request_id' => $record['extra']['request_id'],
            'message' => $record['message'],
            'context' => json_encode($record['context'], JSON_UNESCAPED_UNICODE),
        ];
    }


}