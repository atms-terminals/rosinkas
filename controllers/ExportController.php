<?php
namespace controllers\Export;

include_once ROOT.'/models/Export.php';
include_once ROOT.'/config/consts.php';
include_once ROOT.'/components/DbHelper.php';
use models\Export as model;
use components\User as user;
use components\DbHelper as dbHelper;

/**
* productController
* Класс занимается различными экспортами данных
*/
class ExportController
{
    /**
     * Формирование XML
     * @return  int id файла
     */
    public static function makeXml($dt)
    {
        $operations = model\Export::getOperations($dt);

        $xml = include(ROOT.'/views/exportXml.php');

        return self::saveFile($dt, $xml);
    }

    private static function saveFile($dt, $content)
    {
        $path = ROOT."/files/";
        $fn = "roit_55001_".str_replace('.', '', $dt)."_001";
        $filename = "$path$fn.xml";
        // echo "<pre>"; print_r($content); echo "</pre>";
        file_put_contents($filename, $content);

        @system("zip -P ".ZIP_PWD." -j \"$path$fn.zip\" \"$path$fn.xml\"");
        unlink($filename);
        return "$path$fn.zip";
    }
}
