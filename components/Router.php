<?php

namespace components\Router;

use components\User as user;
use components\Url as url;

/**
 * Маршрутизация запросов.
 */
class Router
{
    /**
     * Основной метод роутера.
     */
    public static function run()
    {
        $routesPath = ROOT.'/config/routes.php';
        $routes = include $routesPath;

        // получить строку запроса
        $uri = url\Url::getUri();

        // проверить наличие такого запроса в routes.php
        $done = false;

        foreach ($routes as $uriPattern => $path) {
            if (preg_match("~$uriPattern~", $uri)) {
                $internalRoute = preg_replace("~$uriPattern~", $path, $uri);
                $segments = explode('/', $internalRoute);

                // имя выполняемого скрипта
                $rightName = "{$segments[0]}.php";

                // если есть совпадение, определить какой контроллер и action обрабатывает запрос
                $controllerName = ucfirst(array_shift($segments)).'Controller';
                $fullControllerName = '\\controllers\\'.$controllerName.'\\'.$controllerName;
                $actionName = 'action'.ucfirst(array_shift($segments));

                $parameters = $segments;

                // подключить файл класса контроллера
                $controllerFile = ROOT."/controllers/$controllerName.php";
                if (file_exists($controllerFile)) {
                    include_once $controllerFile;
                } else {
                    throw new \Exception('Not found', 404);
                }

                // создать объект контроллера
                $controllerObject = new $fullControllerName();

                if (method_exists($controllerObject, $actionName)) {
                    // вызываем действие контроллера

                    $result = call_user_func_array(array($controllerObject, $actionName), $parameters);
                    $done = true;
                    break;
                } else {
                    throw new \Exception('Not found', 404);
                }
            }
        }

        if (!$done) {
            throw new \Exception('Not found', 404);
        }
    }

    /**
     * Редирект на страницу с логином
     */
    public static function defaultPage()
    {
        // дефолтный
        $controllerName = 'LoginController';
        $fullControllerName = '\\controllers\\'.$controllerName.'\\'.$controllerName;
        $actionName = 'actionLogin';
        $controllerFile = ROOT."/controllers/$controllerName.php";

        if (file_exists($controllerFile)) {
            include_once $controllerFile;

            // создать объект, вызвать action
            $controllerObject = new $fullControllerName();
            $result = call_user_func_array(array($controllerObject, $actionName), array());
        } else {
            echo '<h1>OOPS!!!</h1>';
        }
    }
}
