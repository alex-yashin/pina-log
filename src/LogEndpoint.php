<?php

namespace PinaLog;

use Pina\App;
use Pina\Http\Endpoint;
use Pina\Input;

class LogEndpoint extends Endpoint
{
    public function show($id)
    {
        if (!in_array($id, ['error', 'log', 'request'])) {
            exit;
        }
        $path = App::path() . "/../var/log/" . $id . ".csv";
        if ($this->isExport()) {
            header("Content-type: text/csv");
            readfile($path);
        } else {
            echo '<pre>' . file_get_contents($path) . '</pre>';
        }
        exit;
    }

    protected function isExport()
    {
        $resource = Input::getResource();
        return (pathinfo($resource, PATHINFO_EXTENSION) === 'csv');
    }
}