<?php

namespace components\User;

use components\DbHelper as dbHelper;
use components\Url as url;

/**
 * создаем пользователя.
 */
class User
{
    /**
     * @var int id пользователя
     */
    private static $uid = null;
    /**
     * @var int sid пользователя
     */
    private static $sid = null;
    /**
     * @var string $startUrl начальная страница пользователя
     * @var string $firstScreen первый экран сценария
     */
    private static $startUrl = 'index.php';
    private static $firstScreen = 3;

    /**
     * Проверка логина и пароля, создание сессии.
     */
    private static function authMe()
    {
        $login = (empty($_POST['login'])) ? '' : dbHelper\DbHelper::mysqlStr($_POST['login']);
        $password = (empty($_POST['password'])) ? '' : dbHelper\DbHelper::mysqlStr($_POST['password']);
        $ip = url\Url::getIp();

        $query = '/*'.__FILE__.':'.__LINE__.'*/ '."SELECT sessions_add('$login', '$password', '$ip') sid";
        $row = dbHelper\DbHelper::selectRow($query);
        if (empty($row['sid'])) {
            throw new \Exception('Not auth', 404);
        } else {
            self::$sid = $row['sid'];
        }

        return true;
    }

    /**
     * Возвращает SID.
     *
     * @return string SID
     */
    public static function getSid()
    {
        return self::$sid;
    }

    /**
     * Возвращает начальную страницу.
     *
     * @return string startUrl
     */
    public static function getStartUrl()
    {
        return self::$startUrl;
    }

    /**
     * Возвращает первый экран
     *
     * @return string firstScreen
     */
    public static function getFirstScreen()
    {
        return self::$firstScreen;
    }

    /**
     * Возвращает id пользователя
     *
     * @return string firstScreen
     */
    public static function getId()
    {
        return self::$uid;
    }

    /**
     * Получение id пользователя.
     */
    private static function checktId()
    {
        self::$sid = url\Url::extractSid();
        $firstTime = false;

        if (!self::$sid) {
            self::authMe();
            $firstTime = true;
        }

        $sid = self::$sid;

        // получаем id пользователя
        $query = '/*'.__FILE__.':'.__LINE__.'*/ '."SELECT sessions_check('$sid') id_user";
        $row = dbHelper\DbHelper::selectRow($query);
        if (empty($row['id_user'])) {
            throw new \Exception('Not auth', 404);
        } else {
            self::$uid = $row['id_user'];
        }

        $uid = self::$uid;

        // получаем настройки пользователя
        $query = '/*'.__FILE__.':'.__LINE__.'*/ '.
                "SELECT r.start_url, r.first_screen 
                from users u
                    join roles r on u.id_role = r.id
                where u.id = $uid";
        $row = dbHelper\DbHelper::selectRow($query);
        self::$startUrl = $row['start_url'];
        self::$firstScreen = $row['first_screen'];

        if ($firstTime) {
            $_SERVER['REQUEST_URI'] = "\/$sid\/".self::$startUrl;
        }

        return true;
    }

    /**
     * Получение настроек пользователя.
     */
    public static function get()
    {
        if (self::$uid == null) {
            self::checktId();
        }

        $uid = self::$uid;

        return true;
    }
}
