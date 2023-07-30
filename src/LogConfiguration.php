<?php


namespace PinaLog;

use Monolog\Logger;
use Pina\App;

class LogConfiguration
{

    /**
     * @param Logger $logger
     * @throws \Exception
     */
    public static function init()
    {
        return function (Logger $logger) {
            $name = $logger->getName();
            $name = preg_replace('/[^a-zA-Z0-9]/ui', '-', $name);

            if ($name != 'request') {
                $name = 'log';
            }

            $path = App::path() . '/../var/log';

            $handler = new CSVMonologHandler($path .'/' . $name . '.csv', Logger::INFO, true, 0666);
            $logger->pushHandler($handler);

            $handler = new CSVMonologHandler($path . '/error.csv', Logger::ERROR, true, 0666);
            $logger->pushHandler($handler);

            $processor = new LogProcessor();
            $logger->pushProcessor($processor);
        };
    }

}