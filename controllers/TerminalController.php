<?php
namespace controllers\TerminalController;

use components\User as user;

/**
* productController
*/
class TerminalController
{
    public function actionIndex()
    {
        $firstScreen = user\User::getFirstScreen();
        $firstAction = user\User::getFirstAction();
        $sid = user\User::getSid();

        require_once(ROOT.'/views/terminal.php');
        return true;
    }
}
