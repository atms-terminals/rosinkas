<?php
namespace controllers\AdminController;

// include_once ROOT.'/models/News.php';
// use models\News as model;

/**
* productController
*/
class AdminController
{
    public function actionIndex()
    {
        $newsList = array();

        // $newsList = model\News::getNewsList();

        require_once(ROOT.'/views/admin.php');
        return true;
    }
}
