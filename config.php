<?php

use Monolog\Logger;
use PinaLog\CSVMonologHandler;
use PinaLog\LogProcessor;

return function(Logger $logger) {
    $name = $logger->getName();
    $name =  preg_replace('/[^a-zA-Z0-9]/ui', '-', $name);

    if ($name != 'request') {
        $name = 'log';
    }

    $handler = new CSVMonologHandler(__DIR__ . '/../var/log/'.$name.'.csv', Logger::INFO, true, 0666);
    $logger->pushHandler($handler);

    $handler = new CSVMonologHandler(__DIR__ . '/../var/log/error.csv', Logger::ERROR, true, 0666);
    $logger->pushHandler($handler);

    $processor = new LogProcessor();
    $logger->pushProcessor($processor);
};
