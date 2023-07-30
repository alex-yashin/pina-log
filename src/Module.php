<?php

namespace PinaLog;

use Pina\Input;
use Pina\Log;
use Pina\ModuleInterface;

class Module implements ModuleInterface
{
    public function __construct()
    {
        $_SERVER['PINA_REQUEST_ID'] = $this->getUUID();
        register_shutdown_function(
            function () {
                global $argv;

                if (!isset($_SERVER["REQUEST_METHOD"])) {
                    $data = $argv;
                } elseif ($_SERVER["REQUEST_METHOD"] == 'GET') {
                    $data = ['query' => $_SERVER['QUERY_STRING']];
                } else {
                    $data = Input::getData();
                }

                Log::info('request', 'Обработан запрос', $data);
            }
        );
    }

    public function getPath()
    {
        return __DIR__;
    }

    public function getNamespace()
    {
        return __NAMESPACE__;
    }

    public function getTitle()
    {
        return 'Log';
    }

    public function http()
    {
        return [];
    }

    //https://www.php.net/manual/en/function.uniqid.php#94959
    private function getUUID()
    {
        return sprintf(
            '%04x%04x-%04x-%04x-%04x-%04x%04x%04x',
            // 32 bits for "time_low"
            mt_rand(0, 0xffff),
            mt_rand(0, 0xffff),
            // 16 bits for "time_mid"
            mt_rand(0, 0xffff),
            // 16 bits for "time_hi_and_version",
            // four most significant bits holds version number 4
            mt_rand(0, 0x0fff) | 0x4000,
            // 16 bits, 8 bits for "clk_seq_hi_res",
            // 8 bits for "clk_seq_low",
            // two most significant bits holds zero and one for variant DCE1.1
            mt_rand(0, 0x3fff) | 0x8000,
            // 48 bits for "node"
            mt_rand(0, 0xffff),
            mt_rand(0, 0xffff),
            mt_rand(0, 0xffff)
        );
    }

}