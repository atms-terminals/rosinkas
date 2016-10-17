<?php
namespace controllers\TerminalController;

use components\User as user;

// require_once(ROOT.'/components/User.php');

// include_once ROOT.'/models/News.php';
// use models\News as model;

/**
* productController
*/
class TerminalController
{
    public function actionIndex()
    {
        $firstScreen = user\User::getFirstScreen();
        $sid = user\User::getSid();

        require_once(ROOT.'/views/terminal.php');
        return true;
    }
}
