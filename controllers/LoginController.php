<?php
namespace controllers\LoginController;

/**
* Login Controller
*/

class LoginController
{
    public function actionLogin()
    {
        require_once(ROOT.'/views/login.php');
        return true;
    }
}
