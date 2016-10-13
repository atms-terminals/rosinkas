<?php
namespace controllers\IndexController;

// include_once ROOT.'/models/News.php';
// use models\News as model;

/**
* productController
*/
class IndexController
{
    public function actionIndex()
    {
        $newsList = array();

        // $newsList = model\News::getNewsList();

        require_once(ROOT.'/views/index.php');
        return true;
    }
}
