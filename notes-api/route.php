<?php

require_once __DIR__.'/vendor/autoload.php';

use NotesApi\Config\EnvLoader;
use NotesApi\Router;

EnvLoader::load(__DIR__.'/.env');

$router = new Router;
$router->loadRouteFile(__DIR__.'/routes/routes.php');

$terminalWidth = (int) exec('tput cols');
if (! $terminalWidth) {
    $terminalWidth = 80;
}

$command = $argv[1] ?? null;
$withMiddleware = in_array('--with-middleware', $argv);

switch ($command) {
    case 'list':
        foreach ($router->getRoutes() as $method => $routes) {
            foreach ($routes as $path => $route) {
                $handler = '';
                if (is_array($route['handler'])) {
                    $handler = $route['handler'][0].'::'.$route['handler'][1];
                } elseif (is_object($route['handler'])) {
                    $handler = get_class($route['handler']);
                } else {
                    $handler = $route['handler'];
                }

                $methodAndPath = str_pad($method, 6, ' ', STR_PAD_RIGHT).'  '.$path;

                $dotsLength = max(1, $terminalWidth - strlen($methodAndPath) - strlen($handler) - 1);
                $dots = str_repeat('.', $dotsLength);

                echo $methodAndPath.$dots.$handler."\n";

                if (! $withMiddleware) {
                    continue;
                }

                for ($i = 0; $i < count($route['middleware']); $i++) {
                    $middleware = $route['middleware'][$i];
                    if ($i < count($route['middleware']) - 1) {
                        echo $middleware::class.' -> ';
                    } else {
                        echo $middleware::class."\n";
                    }
                }

                echo "\n";
            }
        }
        break;
    default:
        echo "Usage: php route.php [list]\n";
        break;
}
