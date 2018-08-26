<?php

# This file is generated, changes you make will be lost.
# Make your changes in /usr/local/var/www/aeres/helpers.pre instead.

function startProcess($tag, $command, $log = null)
{
    $log = $log ?? "/dev/null";
    $output = "> {$log} 2> {$log}";

    exec("$command tag={$tag} {$output} &");
}

function identifyProcess($tag)
{
    exec("ps -ax | grep '[t]ag={$tag}'", $lines);
    $parts = explode(" ", trim($lines[0]));

    return (int) $parts[0];
}

function stopProcess($tag)
{
    $pid = identifyProcess($tag);

    if (!$pid) {
        return;
    }

    exec("kill -9 {$pid}");
}

use Aerys\Request;
use Amp\Promise;

function fields(Request $request): Promise
{
    return Amp\call(function () use ($request) {
        $parsed = yield Aerys\parseBody($request);
        $fields = $parsed->getAll()["fields"];

        foreach ($fields as $key =>$value) {
            if (is_array($value) && count($value) === 1) {
                $fields[$key] = $value[0];
            }
        }

        return $fields;
    });
}

use Amp\ParallelFunctions;
use League\Plates\Engine;

function view(string $path, array $data = []): \Amp\Promise
{
    return \Amp\call(function () use (&$path , &$data) {
        $templates = __DIR__ . "/resources/views";

        $parallel = ParallelFunctions\parallel([$engine = $engine ?? null, $templates = $templates ?? null, $path = $path ?? null, $data = $data ?? null, "fn" => function () use (&$engine, &$templates, &$path, &$data) {
            $engine = new Engine($templates);
            return $engine->render($path, $data);
        }]["fn"]);

        $response = yield $parallel();

        return $response;
        //	});
    });
}

use Amp\Mysql;

function connect(): Promise
{
    return Amp\call(function () {
        static $connection;

        if ($connection) {
            return $connection;
        }

        $host = "127.0.0.1";
        $port = "3306";
        $name = "async";
        $user = "root";
        $pass = "";

        $connection = yield Mysql\connect("host={$host}:{$port};user={$user};pass={$pass};db={$name}");

        return $connection;
    });
}

function prepare($query, array $values = []): \Amp\Promise
{
    return \Amp\call(function () use (&$query , &$values) {
        $connection = yield connect();
        $statement = yield $connection->prepare($query);
        $result = yield $statement->execute($values);

        return $result;
    });
}
