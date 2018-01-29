<?php
namespace models\Admin;

use components\DbHelper as dbHelper;

/**
* model News
*/
class Admin
{
    /**
     * список оборудования на терминале
     * @var array список оборудования
     */
    public static $devices = array(
        'connection' => 'Связь',
        'webSocket' => 'Система',
        'cash' => 'Купюроприемник',
        );

    /**
     * получение списка файлов
     * @var int id терминала
     * 
     * @return array массива состояний
     */
    public static function getFiles()
    {
        $files = array();
        if ($handle = opendir('.'.FILES_PATH)) {
            while (false !== ($entry = readdir($handle))) {
                if ($entry != "." && $entry != "..") {
                    $dt = filemtime('.'.FILES_PATH.$entry);
                    $files[$dt.$entry] = array(
                        'path' => $entry,
                        'dt' => date("d.m.Y H:i:s", $dt)
                    );
                }
            }
            closedir($handle);
        }

        krsort($files);
        return $files;
    }

    /**
     * получение списка инкассаций
     * @var int id терминала
     * 
     * @return array массива состояний
     */
    public static function getCollections($id = false)
    {
        $money['collections'] = array();
        $money['free'] = array();
        $money['nominals'] = array();

        $sql = ($id) ? " and u.id = '$id'" : '';

        $query = "/*".__FILE__.':'.__LINE__."*/ ".
            "SELECT date_format(c.dt, '%d.%m.%Y %H:%i') dt, u.address, sum(p.amount) amount, u.id
            from v_payments p
                join users u on p.id_user = u.id
                    $sql
                join collections c on p.id_collection = c.id
            group by u.address, u.id, c.dt
            order by c.dt desc";
        $collections = dbHelper\DbHelper::selectSet($query);
        foreach ($collections as $row) {
            $money['collections'][$row['id']][] = $row;
        }
        // наличка
        $query = "/*".__FILE__.':'.__LINE__."*/ ".
            "SELECT p.id_user, u.address, sum(p.amount) summ
            from v_payments p
                join users u on u.id = p.id_user
                    $sql
            where p.collected = 0
            group by p.id_user";
        $tmoney = dbHelper\DbHelper::selectSet($query);
        foreach ($tmoney as $row) {
            $money['free'][$row['id_user']] = $row['summ'];
        }

        $query = "/*".__FILE__.':'.__LINE__."*/ ".
            "SELECT n.id_user, n.nominal, count(*) cnt
            from notes n
                join users u on u.id = n.id_user
                    $sql
            where n.id_collection is null
            group by n.id_user, n.nominal
            order by n.nominal";
        $tmoney = dbHelper\DbHelper::selectSet($query);
        foreach ($tmoney as $row) {
            $money['nominals'][$row['id_user']][$row['nominal']] = $row['cnt'];

            if (empty($money['nominals'][$row['id_user']]['total'])) {
                $money['nominals'][$row['id_user']]['total'] = $row['cnt'];
            } else {
                $money['nominals'][$row['id_user']]['total'] += $row['cnt'];
            }
        }

        return $money;
    }

    /**
     * получение списка услуг
     * @return array массив услуг
     */
    public static function getPriceGroup($type, $status)
    {
        $sql = $status ? 'p.status = 1' : '1';
        $query = "/*".__FILE__.':'.__LINE__."*/ ".
            "SELECT p.id, p.id_parent, p.`desc`, p.clients_desc, p.`status`, p.color, p.price, p.nds, o.id_day, 
                date_format(t.`start`, '%H:%i') time_start, date_format(t.`finish`, '%H:%i') time_finish, t.id_day time_day, 
                p.comment
            from v_custom_pricelist p
                left join custom_price_redstar_dayoff o on p.id = o.id_item
                left join custom_price_redstar_time t on p.id = t.id_item
            where $sql
                and p.day_type = '$type'
            order by p.id_parent, p.`desc`";
        $tservices = dbHelper\DbHelper::selectSet($query);
        $tlist = array();
        // переупорядочиваем по корневому элементу
        foreach ($tservices as $row) {
            if (empty($tlist[$row['id_parent']][$row['id']])) {
                $tlist[$row['id_parent']][$row['id']] = $row;
                $tlist[$row['id_parent']][$row['id']]['schedule'] = array(
                    '0' => array('en' => 1, 'start' => '', 'finish' => ''),
                    '1' => array('en' => 1, 'start' => '', 'finish' => ''),
                    '2' => array('en' => 1, 'start' => '', 'finish' => ''),
                    '3' => array('en' => 1, 'start' => '', 'finish' => ''),
                    '4' => array('en' => 1, 'start' => '', 'finish' => ''),
                    '5' => array('en' => 1, 'start' => '', 'finish' => ''),
                    '6' => array('en' => 1, 'start' => '', 'finish' => ''),
                );
            }
            if ($row['id_day'] != '') {
                $tlist[$row['id_parent']][$row['id']]['schedule'][$row['id_day']]['en'] = 0;
            }
            if ($row['time_start'] != '') {
                $tlist[$row['id_parent']][$row['id']]['schedule'][$row['time_day']]['start'] = $row['time_start'];
            }
            if ($row['time_finish'] != '') {
                $tlist[$row['id_parent']][$row['id']]['schedule'][$row['time_day']]['finish'] = $row['time_finish'];
            }
        }
        return $tlist;
    }

    /**
     * получение списка состояний оборудования
     * @var int id терминала
     * 
     * @return array массив состояний
     */
    public static function getHwsState($id = false)
    {
        $sql = ($id) ? " and u.id = '$id'" : '';

        // тех. состояние
        $query = "/*".__FILE__.':'.__LINE__."*/ ".
            "SELECT u.id, u.address, h.`type`, h.is_error, date_format(h.dt, '%d.%m.%Y %H:%i') dt, h.message
            from users u 
            left join hws_status h
               on h.id_user = u.id
            where u.id_role = 2
                and u.status = 1
                $sql
            order by u.address, h.`type`";
        $list = dbHelper\DbHelper::selectSet($query);

        // преобразуем
        $result = array();
        foreach ($list as $row) {
            if (empty($result[$row['id']])) {
                $result[$row['id']]['address'] = $row['address'];
                foreach (self::$devices as $key => $nameRus) {
                    $result[$row['id']]['status'][$key] = array (
                        'dt' => '',
                        'isError' => -1,
                        'message' => ''
                    );
                }
            }

            if ($row['dt'] != '') {
                $result[$row['id']]['status'][$row['type']]['dt'] = $row['dt'];
                $result[$row['id']]['status'][$row['type']]['isError'] = $row['is_error'];
                $result[$row['id']]['status'][$row['type']]['message'] = $row['message'];
            }
        }

        $query = "/*".__FILE__.':'.__LINE__."*/ ".
            "SELECT h.id_user id, UNIX_TIMESTAMP(now()) - UNIX_TIMESTAMP(max(h.dt)) period, date_format(max(h.dt), '%d.%m.%Y %H:%i:%s') dt
            from hws_status h 
                join users u on u.id = h.id_user
                    and u.id_role = 2
                    and u.status = 1
                    $sql
            group by h.id_user";
        $list = dbHelper\DbHelper::selectSet($query);
        foreach ($list as $row) {
            $result[$row['id']]['status']['connection']['isError'] = $row['period'] > 60 * 5 ? 1 : 0;
            $result[$row['id']]['status']['connection']['message'] = $row['period'] > 60 * 5 ? 'нет связи' : 'ОК';
            $result[$row['id']]['status']['connection']['dt'] = $row['dt'];
        }

        return $result;
    }

    /**
     * получение списка событий по терминалу
     * @var int id терминала
     * 
     * @return array массив состояний
     */
    public static function getTerminalHistory($id = false)
    {
        $sql = ($id) ? " and a.id_user = '$id'" : '';

        $query = "/*".__FILE__.':'.__LINE__."*/ ".
            "SELECT date_format(a.dt, '%d.%m.%Y %H:%i:%s') dt, a.`action`, a.id_user
            from v_term_activity a 
            where 1
                $sql
            order by a.dt desc
            limit 100";
        $list = dbHelper\DbHelper::selectSet($query);
        foreach ($list as $row) {
            $result[$row['id_user']][] = $row;
        }

        return $result;
    }

    /**
     * получение массива статусов предоплат
     * @return array массива состояний
     */
    public static function findPrepaid($searchStr)
    {
        $searchStr = dbHelper\DbHelper::mysqlStr($searchStr);
        $query = "/*".__FILE__.':'.__LINE__."*/ ".
            "SELECT c.id, c.card, c.name, ifnull(p.amount, 0) amount
            from cards c
                left join prepayments p on c.id = p.id_card
            where c.card regexp '$searchStr'
                or upper(c.name) regexp upper('$searchStr')
            order by c.card";
        return  dbHelper\DbHelper::selectSet($query);
    }

    /**
     * получение списка пользователей
     * @return array список
     */
    public static function getUsers()
    {
        $query = "/*".__FILE__.':'.__LINE__."*/ ".
            "SELECT u.id, u.login, u.status
            from v_real_users u
            where u.id_role != 2
                and u.id > 10
            order by u.login";
        $list = dbHelper\DbHelper::selectSet($query);

        return $list;
    }

    /**
     * получение списка дат
     * @return array список
     */
    public static function getDates()
    {
        $query = "/*".__FILE__.':'.__LINE__."*/ ".
            "SELECT e.id, DATE_FORMAT(e.dt, '%d.%m.%Y') dt
            from extra_days e
            where e.dt_type = 2";
        $works = dbHelper\DbHelper::selectSet($query);

        $query = "/*".__FILE__.':'.__LINE__."*/ ".
            "SELECT e.id, DATE_FORMAT(e.dt, '%d.%m.%Y') dt
            from extra_days e
            where e.dt_type = 1";
        $holidays = dbHelper\DbHelper::selectSet($query);

        return array(
            'worked' => $works,
            'holidays' => $holidays,
        );
    }

    /**
     * получение списка терминалов
     * @return array список
     */
    public static function getTerminals()
    {
        $query = "/*".__FILE__.':'.__LINE__."*/ ".
            "SELECT u.id, u.ip, u.address, u.`status`
            from v_real_users u
            where u.id_role = 2
            order by u.login";
        $list = dbHelper\DbHelper::selectSet($query);

        return $list;
    }
}
