<?php
namespace models\News;

use components\DbHelper as dbHelper;

/**
* model News
*/
class News
{
    /**
     * получение одиночной новости
     * @param integer $id
     */
    public static function getNewsById($id)
    {
        $query = "SELECT p.id, p.author, p.text from phrases p where p.id = $id";
        $newsList = dbHelper\DbHelper::select($query);

        return $newsList;
    }

    /**
     * получение массива новостей
     * @param integer $id
     */
    public static function getNewsList()
    {
        $query = "SELECT p.id, p.author, p.text from phrases p";
        $newsList = dbHelper\DbHelper::select($query);

        return $newsList;
    }
}
