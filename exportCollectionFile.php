<?php
namespace export;

// подключение файлов системы
define('ROOT', __DIR__);
include_once ROOT.'/controllers/ExportController.php';
include_once ROOT.'/components/Mail.php';

use components\Mailer as mailer;
use controllers\Export as exportController;

if ($_SERVER['SERVER_ADDR'] == '127.0.0.1') {
    ini_set('display_errors', '1');
    ini_set('error_reporting', E_ALL | E_STRICT | E_NOTICE);
} else {
    ini_set('display_errors', '0');
    ini_set('error_reporting', E_ERROR);
}

$date = date('d.m.Y', strtotime(date('Y-m-d') .' -1 day'));

$fn = exportController\ExportController::makeXml($date);

$fileContent = file_get_contents(ROOT.FILES_PATH.$fn);

mailer\Mailer::sendAttachEmail(MY_EMAIL, EXPORT_EMAIL, CC_EMAIL, "Выгрузка по операциям в терминалах (".ORG.")", '', $fn, $fileContent);
