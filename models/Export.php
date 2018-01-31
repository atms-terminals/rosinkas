<?php
namespace models\Export;

use components\DbHelper as dbHelper;

/**
* model Export
*/
class Export
{
    /**
     * получение технического состояния терминала
     * @var  $id id терминала
     * @return array список
     */
    public static function getHwStatus($id)
    {
        // адрес
        $query = "/*".__FILE__.':'.__LINE__."*/ ".
            "SELECT u.address
            from users u
            where u.id = '$id'";
        $row = dbHelper\DbHelper::selectRow($query);
        $address = $row['address'];

        // текущая наличка
        $query = "/*".__FILE__.':'.__LINE__."*/ ".
            "SELECT sum(p.amount) summ
            from payments p
            where p.id_user = '$id'
                and p.id_collection is null";
        $row = dbHelper\DbHelper::selectRow($query);
        $current = $row['summ'];

        $query = "/*".__FILE__.':'.__LINE__."*/ ".
            "SELECT s.`type`, date_format(s.dt, '%d.%m.%Y %H:%i:%s') dt, s.is_error, s.message
            from hws_status s
            where s.id_user = '$id'";
        $list = dbHelper\DbHelper::selectSet($query);

        return array(
            'money' => $current,
            'address' => $address,
            'devices' => $list
        );
    }


    /**
     * получение списка операций для выгрузки за дату
     * @var  $dt дата
     * @return array список
     */
    public static function getOperations($dt)
    {
        $query = "/*".__FILE__.':'.__LINE__."*/ ".
            "SELECT p.card, p.org, p.address, sum(p.amount) amount
            from payments p
            where 1
                and p.confirmed >= str_to_date('$dt', '%d.%m.%Y')
                and p.confirmed < str_to_date('$dt', '%d.%m.%Y') + interval 1 day
            group by p.card, p.org, p.address";
        $list = dbHelper\DbHelper::selectSet($query);

        return $list;
    }
}
