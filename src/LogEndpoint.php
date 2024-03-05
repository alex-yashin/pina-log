<?php

namespace PinaLog;

use Pina\App;
use Pina\Http\Endpoint;
use Pina\Input;
use Pina\Response;

class LogEndpoint extends Endpoint
{
    public function show($id)
    {
        if (!in_array($id, ['error', 'log', 'request'])) {
            exit;
        }

        $page = intval($this->query()->get('page'));
        $suffix = $page > 0 ? ('.' . $page) : '';

        $gzipped = false;
        $path = App::path() . "/../var/log/" . $id . ".csv" . $suffix;
        if (!file_exists($path)) {
            $gzipped = true;
            $path = App::path() . "/../var/log/" . $id . ".csv" . $suffix . '.gz';
        }

        if (!file_exists($path)) {
            return Response::notFound();
        }

        if ($this->isExport()) {
            header("Content-type: text/csv");
        } else {
            echo '<pre>';
        }
        if ($gzipped) {
            readgzfile($path);
        } else {
            readfile($path);
        }
        if (!$this->isExport()) {
            echo '</pre>';
        }
        exit;
    }

    protected function isExport()
    {
        $resource = Input::getResource();
        return (pathinfo($resource, PATHINFO_EXTENSION) === 'csv');
    }
}