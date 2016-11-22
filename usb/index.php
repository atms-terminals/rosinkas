<?php
header('Access-Control-Allow-Origin: *');
ini_set('max_execution_time', 300);
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

function swap($str)
{
    $part1 = substr($str, 0, 2);
    $part2 = substr($str, 2);
    return $part2.$part1;
}

include 'PhpSerial.php';

// открываем порт
$serial = new PhpSerial;
$serial->deviceSet("/dev/ttyUSB0");
$serial->deviceOpen();

$response = array(
    'code' => 0,
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
        // Преобразуем считанный номер (добавляем лидирующие нули)
        // $read = ' [3300] 255,00053';
        $hasSegments = preg_match('~([0-9a-fA-F]*?)[^\d]*(\d*),(\d*)~', $read, $segments);

        $first = swap($segments[1]);

        $middle = dechex($segments[2]);
        if ((strlen($middle)) == 1) {
            $middle = "0$middle";
        }

        $last = dechex($segments[3]);
        switch (strlen($last)) {
            case 1:
                $last = "000$last";
                break;
            case 2:
                $last = "00$last";
                break;
            case 3:
                $last = "0$last";
                break;
        }
        $last = swap($last);

        $key = strtoupper($last.$middle.$first);
        $response['str'] = $read;
        $response['key'] = $key;
    }
}

$serial->deviceClose();

echo json_encode($response);
