<?php
namespace components\dbHelper;

/**
* Подключаем mySql
*/
class DbHelper
{
    /**
     * @var object коннект к базе
     */
    private static $connection = null;

    /**
     * Создание соединения mySql
     * @return int дескриптор соединения
     */
    private static function getConnection()
    {
        if (self::$connection == null) {
            $paramsPath = ROOT.'/config/dbParams.php';
            $params = include($paramsPath);
            $connection = new \mysqli($params['host'], $params['user'], $params['password'], $params['dbname']);
            
            /* check connection */
            if (mysqli_connect_errno()) {
                printf("Не могу создать соединение: %s\n", mysqli_connect_error());
                exit();
            }

            self::$connection = $connection;

            $query = "set names 'utf8'";
            $connection->multi_query($query);
            $connection->set_charset('utf8');
        }
        return $connection;
    }

    /**
     * Синоним для mysql_escape_str
     * @param string $str строка запроса
     * @return string экранированная строка
     *
     */
    public static function mysqlStr($str)
    {
        if (self::$connection == null) {
            self::getConnection();
        }
        return self::$connection->escape_string($str);
    }

    /**
     * Выполнение выборки (несколько строк)
     * @param string $query строка запроса к БД
     * @return array результаты запроса
     *
     */
    public static function selectSet($query)
    {
        // echo "<pre>"; print_r($query); echo "</pre>";
        if (self::$connection == null) {
            self::getConnection();
        }

        $array = array();
        if ($result = self::$connection->query($query)) {
            while ($row = $result->fetch_assoc()) {
                $array[] = $row;
            }
            $result->free();
            return $array;
        } else {
            throw new \Exception("mySql: Ошибка: ".self::$connection->error."<hr /> <pre>$query</pre>", 100);
        }
        return true;
    }

    /**
     * Выполнение выборки (1 строки)
     * @param string $query строка запроса к БД
     * @return array результаты запроса
     *
     */
    public static function selectRow($query)
    {
        // echo "<pre>"; print_r($query); echo "</pre>";
        if (self::$connection == null) {
            self::getConnection();
        }

        $array = array();
        if ($result = self::$connection->query($query)) {
            while ($row = $result->fetch_assoc()) {
                $result->free();
                return $row;
            }
            $result->free();
            return false;
        } else {
            throw new \Exception("mySql: Ошибка: ".self::$connection->error."<hr /> <pre>$query</pre>", 100);
        }
        return true;
    }

    /**
     * Выполнение процедуры
     * @param string $query строка запроса к БД
     * @return array результаты запроса
     *
     */
    public static function call($query)
    {
        // echo "<pre>"; print_r($query); echo "</pre>";
        if (self::$connection == null) {
            self::getConnection();
        }

        $array = array();
        if ($result = self::$connection->query($query)) {
            return true;
        } else {
            throw new \Exception("mySql: Ошибка: ".self::$connection->error."<hr /> <pre>$query</pre>", 100);
        }
    }
}
