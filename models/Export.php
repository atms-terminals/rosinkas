<?php
namespace models\Export;

use components\DbHelper as dbHelper;

/**
* model Export
*/
class Export
{
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
