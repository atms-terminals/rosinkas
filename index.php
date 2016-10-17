<?php
use components\Router as router;
use components\User as user;
use components\Url as url;

// FRONT CONTROLLER

if ($_SERVER['SERVER_ADDR'] == '127.0.0.1') {
    ini_set("display_errors", "1");
    ini_set("error_reporting", E_ALL | E_STRICT | E_NOTICE);
} else {
    ini_set("display_errors", "0");
    ini_set("error_reporting", E_ERROR);
}

// подключение файлов системы
define('ROOT', __DIR__);
require_once(ROOT.'/components/Router.php');
require_once(ROOT.'/components/DbHelper.php');
require_once(ROOT.'/components/User.php');
require_once(ROOT.'/components/Url.php');

try {
    // создаем пользователя
    user\User::get();

    // вызов роутера
    router\Router::run();

} catch (Exception $e) {
    switch ($e->getCode()) {
        case 100:
            echo $e->getMessage();
            break;
        case 404:
            router\Router::defaultPage();
            break;
        default:
            echo "<pre>";
            print_r($e);
            echo "</pre>";
    }
}
