<?php
namespace components\Url;

/**
* Класс работы с URL
*/
class Url
{
    /**
     * Выделение SID из request.
     *
     * @return string SID
     */
    public static function extractSid()
    {
        $str = $_SERVER['REQUEST_URI'];

        $arr = explode('/', trim($str, '/'));
        $sid = $arr[0];

        if (!preg_match('~^[0-9a-f-]*$~', $sid)) {
            return false;
        }

        return empty($arr[0]) ? false : $arr[0];
    }

    /**
     * возвращает строку запроса URI.
     *
     * @return string URI
     */
    public static function getUri()
    {
        if (!empty($_SERVER['REQUEST_URI'])) {
            if ($pos = strpos($_SERVER['REQUEST_URI'], '?')) {
                return substr($_SERVER['REQUEST_URI'], 0, $pos);
            } else {
                return trim($_SERVER['REQUEST_URI'], '/');
            }
        }

        return '';
    }

    /**
     * возвращает IP-адрес
     *
     * @return string IP
     */
    public static function getIp()
    {
        return getenv('REMOTE_ADDR');
    }
}
