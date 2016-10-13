<?php
namespace components\User;

use Exception;
use components\DbHelper as dbHelper;

/**
* создаем пользователя
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
     * @var array привилегии пользователя (по именам)
     */
    public static $rightsByName = array();
    /**
     * @var array привилегии пользователя (по id)
     */
    private static $rightsById = array();
    /**
     * @var array главное меню со всеми пунктами
     */
    private static $menu = array();
    /**
     * @var string начальная страница пользователя
     */
    private static $firstPage = 'index.php';

    /**
     * Проверка логина и пароля, создание сессии
     */
    private static function authMe()
    {
        $login = (empty($_POST['login'])) ? '' : dbHelper\DbHelper::mysqlStr($_POST['login']);
        $password = (empty($_POST['password'])) ? '' : dbHelper\DbHelper::mysqlStr($_POST['password']);
        $ip = getenv("REMOTE_ADDR");

        $query = "/*".__FILE__.':'.__LINE__."*/ "."SELECT session_add('$login', '$password', '$ip') sid";
        $row = dbHelper\DbHelper::selectRow($query);
        if (empty($row['sid'])) {
            throw new \Exception("Not auth", 404);
        } else {
            self::$sid = $row['sid'];
            $_COOKIE['sid'] = $row['sid'];
            setcookie("sid", $row['sid'], time() + 24 * 3600);
        }
        return true;
    }

    /**
     * Получение id пользователя
     */
    private static function getId()
    {
        if (empty($_COOKIE['sid']) || $_SERVER['REQUEST_URI'] == '/') {
            self::authMe();
            $firstTime = true;
        } else {
            self::$sid = dbHelper\DbHelper::mysqlStr($_COOKIE['sid']);
            $firstTime = false;
        }

        $sid = self::$sid;

        // получаем id пользователя
        $query = "/*".__FILE__.':'.__LINE__."*/ "."SELECT session_check('$sid') id_user";
        $row = dbHelper\DbHelper::selectRow($query);
        if (empty($row['id_user'])) {
            setcookie('sid', 0, time() - 24 * 3600);
            throw new \Exception("Not auth", 404);
        } else {
            self::$uid = $row['id_user'];
        }

        $uid = self::$uid;

        // получаем настройки пользователя
        $query = "/*".__FILE__.':'.__LINE__."*/ "."SELECT u.first_page from users u where u.id = $uid";
        $row = dbHelper\DbHelper::selectRow($query);
        self::$firstPage = $row['first_page'];

        if ($firstTime) {
            $_SERVER['REQUEST_URI'] = self::$firstPage;
            // header('Location: '.self::$firstPage);
        }

        // получаем меню
        $query = "/*".__FILE__.':'.__LINE__."*/ "."SELECT * from main_menu m order by m.parent, m.`order`";
        $menu = dbHelper\DbHelper::selectSet($query);
        self::$menu = $menu;

        return true;
    }

    /**
     * Получение настроек пользователя
     */
    public static function get()
    {
        if (self::$uid == null) {
            self::getId();
        }

        $uid = self::$uid;

        // получаем права
        $query = "/*".__FILE__.':'.__LINE__."*/ ".
            "SELECT u.id_right, u.name, u.`status` from v_rights_users u where u.id_user = $uid";
        $trights = dbHelper\DbHelper::selectSet($query);

        if ($trights) {
            foreach ($trights as $row) {
                self::$rightsByName[$row['name']] = $row['status'];
                self::$rightsById[$row['id_right']] = $row['status'];
            }
        }
        return true;
    }

    /**
     * Проверка прав
     * @param string $name название привилегии
     * @return int значение
     */
    public static function checkRight($name = '')
    {
        if (count(self::$rightsByName)) {
            self::get();
        }

        return empty(self::$rightsByName[$name]) ? 0 : 1;
    }
}
