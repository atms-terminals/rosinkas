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
            return trim($_SERVER['REQUEST_URI'], '/');
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
