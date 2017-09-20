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
     * @var int $id пользователя
     * @var int $sid пользователя
     * @var string $startUrl начальная страница пользователя
     * @var string $firstScreen первый экран сценария
     * @var string $firstAction первое действие для экрана
     * @var int $status статус блокировки пользователя
     */
    private static $uid = null;
    private static $sid = null;
    private static $startUrl = 'index.php';
    private static $firstScreen = 3;
    private static $firstAction = 'move';
    private static $status = 0;

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
     * Возвращает первое действие
     *
     * @return string firstAction
     */
    public static function getFirstAction()
    {
        return self::$firstAction;
    }

    /**
     * Возвращает id пользователя
     *
     * @return string uid
     */
    public static function getId()
    {
        return self::$uid;
    }

    /**
     * Возвращает статус пользователя
     *
     * @return string status
     */
    public static function getStatus()
    {
        return self::$status;
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
                "SELECT r.start_url, r.first_screen, r.first_action, u.status, u.id_role
                from users u
                    join roles r on u.id_role = r.id
                where u.id = $uid";
        $row = dbHelper\DbHelper::selectRow($query);
        self::$startUrl = $row['start_url'];
        self::$firstScreen = $row['first_screen'];
        self::$firstAction = $row['first_action'];
        self::$status = $row['status'];
        
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
