<?php
namespace controllers\SecondController;

// include_once ROOT.'/models/News.php';
// use models\News as model;

/**
* productController
*/
class SecondController
{
    public function actionIndex()
    {
        $newsList = array();

        // $newsList = model\News::getNewsList();

        require_once(ROOT.'/views/second.php');
        return true;
    }
}
