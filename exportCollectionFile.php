<?php
namespace export;

// подключение файлов системы
define('ROOT', __DIR__);
include_once ROOT.'/controllers/ExportController.php';

use controllers\Export as exportController;

if ($_SERVER['SERVER_ADDR'] == '127.0.0.1') {
    ini_set('display_errors', '1');
    ini_set('error_reporting', E_ALL | E_STRICT | E_NOTICE);
} else {
    ini_set('display_errors', '0');
    ini_set('error_reporting', E_ERROR);
}

$fn = exportController\ExportController::makeXml('24.01.2018');

$fn = exportController\ExportController::makeXml('23.01.2018');

$fn = exportController\ExportController::makeXml('25.01.2018');

$fn = exportController\ExportController::makeXml('26.01.2018');

$fn = exportController\ExportController::makeXml('28.01.2018');

$fn = exportController\ExportController::makeXml('29.01.2018');

$fn = exportController\ExportController::makeXml('30.01.2018');

