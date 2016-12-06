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
        'cash' => 'Купюроприемник',
        'fr' => 'Фискальный регистратор',
        'proffit' => 'Сервер Проффит',
        'webSocket' => 'Ubuntu',
        );

    /**
     * получение списка инкассаций
     * @return array массива состояний
     */
    public static function getCollections()
    {
        $query = "/*".__FILE__.':'.__LINE__."*/ ".
            "SELECT u.address, s.`action`, date_format(s.dt, '%d.%m.%Y %H:%i') dt
            from system_log s
                join users u on s.id_user = u.id
                join (
                    select max(s.dt) dt, s.id_user 
                    from system_log s 
                    where s.id_action = 15
                    group by s.id_user) t on t.dt = s.dt
                        and t.id_user = s.id_user
            order by 1";
        $collections = dbHelper\DbHelper::selectSet($query);

        // наличка
        $query = "/*".__FILE__.':'.__LINE__."*/ ".
            "SELECT p.id_user, if(p.dt_confirm is null, 'notConfirmed', 'confirmed') confirmed, sum(p.amount) summ, u.address
            from payments p
                join users u on u.id = p.id_user
            where p.collected = 0
            group by p.id_user, if(p.dt_confirm is null, 'notConfirmed', 'confirmed')";
        $tmoney = dbHelper\DbHelper::selectSet($query);
        $money = array();
        foreach ($tmoney as $row) {
            if (empty($money[$row['address']])) {
                $money[$row['address']]['confirmed'] = 0;
                $money[$row['address']]['notConfirmed'] = 0;
            }
            $money[$row['address']][$row['confirmed']] = $row['summ'];
        }

        return array('collections' => $collections,
            'money' => $money);
    }

    /**
     * получение списка услуг
     * @return array массив услуг
     */
    public static function getPriceGroup($status)
    {
        $sql = $status ? 'p.status = 1' : '1';
        $query = "/*".__FILE__.':'.__LINE__."*/ ".
            "SELECT p.id, p.id_parent, p.`desc`, p.`status`
            from v_custom_price_list p
            where $sql
            order by p.id_parent, p.`desc`";
        $tservices = dbHelper\DbHelper::selectSet($query);
        $tlist = array();
        // переупорядочиваем по корневому элементу
        foreach ($tservices as $row) {
            $tlist[$row['id_parent']][] = $row;
        }
        return $tlist;
    }

    /**
     * получение списка состояний оборудования
     * @return array массив состояний
     */
    public static function getHwsState()
    {
        // тех. состояние
        $query = "/*".__FILE__.':'.__LINE__."*/ ".
            "SELECT u.id, u.address, h.`type`, h.is_error, date_format(h.dt, '%d.%m.%Y %H:%i') dt, h.message
            from hws_status h
                join users u on h.id_user = u.id
            order by u.address, h.`type`";
        $list = dbHelper\DbHelper::selectSet($query);

        // преобразуем
        $result = array();
        foreach ($list as $row) {
            if (empty($result[$row['address']])) {
                foreach (self::$devices as $key => $nameRus) {
                    $result[$row['address']][$key] = array (
                        'dt' => '',
                        'isError' => -1,
                        'message' => ''
                    );
                }
            }
            $result[$row['address']][$row['type']]['dt'] = $row['dt'];
            $result[$row['address']][$row['type']]['isError'] = $row['is_error'];
            $result[$row['address']][$row['type']]['message'] = $row['message'];
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
            order by u.login";
        $list = dbHelper\DbHelper::selectSet($query);

        return $list;
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
