<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="windows-1251">
<?php
ini_set('display_errors', '1');
ini_set("error_reporting", E_ALL);

// обработчик ошибки работы с USB
set_error_handler('anyError');
function anyError($errno, $errstr, $errfile, $errline)
{
    $response = array(
        'code' => $errno,
        'message' => $errstr,
        'str' => "$errfile:$errline"
        );
    echo json_encode($response);
    exit;
}
 
function microtime_float()
{
    list($usec, $sec) = explode(" ", microtime());
    return ((float) $usec + (float) $sec);
}

include 'PhpSerial.php';

// открываем порт
$serial = new PhpSerial;
$serial->deviceSet("/dev/ttyUSB0");
$serial->deviceOpen();

$response = array(
    'code' => '0',
    'message' => '',
    'str' => ''
    );

// делаем что нужно
if (!empty($_POST['action']) && $_POST['action'] == 'read') {
    // устанавливаем таймаут 10 сек
    $start = microtime_float();
    $isTimeout = false;

    $timeout = empty($_POST['timeout']) ? (float)10 : (float)$_POST['timeout'];

    $read = '';
    while (!strstr($read, 'Marin')) {
        if ((microtime_float() >= $start + $timeout)) {
            $isTimeout = true;
            break;
        }
        $read = $serial->readPort();
    }

    if ($isTimeout) {
        $response = array(
            'code' => 'TIMEOUT',
            'message' => "Таймаут $timeout сек. при работе со считкой",
            'str' => ''
            );
    } else {
        $response['str'] = $read;
    }
}

$serial->deviceClose();

echo json_encode($response);
