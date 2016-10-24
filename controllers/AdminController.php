<?php
namespace controllers\AdminController;

include_once ROOT.'/models/Admin.php';
use models\Admin as admin;
use components\User as user;
use components\DbHelper as dbHelper;

/**
* productController
*/
class AdminController
{
    public function actionIndex()
    {
        $statuses = admin\Admin::getHwsState();
        $devices = admin\Admin::$devices;
        $sid = user\User::getSid();

        require_once(ROOT.'/views/admin.php');
        return true;
    }

    public function actionGetHwsState()
    {
        $statuses = admin\Admin::getHwsState();
        $devices = admin\Admin::$devices;

        require_once(ROOT.'/views/hwsState.php');
        return true;
    }

    public function actionGetTerminals()
    {
        $list = admin\Admin::getTerminals();

        require_once(ROOT.'/views/terminalsList.php');
        return true;
    }

    public function actionChangeStatus()
    {
        $uid = user\User::getId();
        $id = empty($_POST['id']) ? 0 : dbHelper\DbHelper::mysqlStr($_POST['id']);
        $status = empty($_POST['status']) ? 0 : 1;

        $query = "/*".__FILE__.':'.__LINE__."*/ ".
            "SELECT users_change_status($uid, '$id', $status)";
        $result = dbHelper\DbHelper::selectRow($query);
        $response['code'] = 0;

        echo json_encode($response);
        return true;
    }

    public function actionChangePassword()
    {
        $uid = user\User::getId();
        $id = empty($_POST['id']) ? 0 : dbHelper\DbHelper::mysqlStr($_POST['id']);
        $password = empty($_POST['new']) ? '123' : dbHelper\DbHelper::mysqlStr($_POST['new']);

        $query = "/*".__FILE__.':'.__LINE__."*/ ".
            "SELECT users_change_password($uid, '$id', '$password')";
        $result = dbHelper\DbHelper::selectRow($query);
        $response['code'] = 0;

        echo json_encode($response);
        return true;
    }

    public function actionAddUser()
    {
        $uid = user\User::getId();
        // если есть ip то роль - терминал, иначе - пользователь
        $ip = empty($_POST['ip']) ? '' : dbHelper\DbHelper::mysqlStr($_POST['ip']);
        $idRole = $ip ? 2 : 1;
        $address = empty($_POST['address']) ? '' : dbHelper\DbHelper::mysqlStr($_POST['address']);
        $login = empty($_POST['login']) ? '' : dbHelper\DbHelper::mysqlStr($_POST['login']);

        $query = "/*".__FILE__.':'.__LINE__."*/ ".
            "SELECT users_add($uid, $idRole, '$ip', '$login', '$address')";
        $result = dbHelper\DbHelper::selectRow($query);
        $response['code'] = 0;

        echo json_encode($response);
        return true;
    }

    public function actionEditUser()
    {
        $uid = user\User::getId();
        $id = empty($_POST['id']) ? '' : dbHelper\DbHelper::mysqlStr($_POST['id']);
        $ip = empty($_POST['ip']) ? '' : dbHelper\DbHelper::mysqlStr($_POST['ip']);
        $address = empty($_POST['address']) ? '' : dbHelper\DbHelper::mysqlStr($_POST['address']);
        $login = empty($_POST['login']) ? '' : dbHelper\DbHelper::mysqlStr($_POST['login']);

        $query = "/*".__FILE__.':'.__LINE__."*/ ".
            "SELECT users_edit($uid, '$id', '$ip', '$login', '$address')";
        $result = dbHelper\DbHelper::selectRow($query);
        $response['code'] = 0;

        echo json_encode($response);
        return true;
    }

    public function actionDeleteUser()
    {
        $uid = user\User::getId();
        $id = empty($_POST['id']) ? 0 : dbHelper\DbHelper::mysqlStr($_POST['id']);

        $query = "/*".__FILE__.':'.__LINE__."*/ ".
            "SELECT users_delete($uid, '$id')";
        $result = dbHelper\DbHelper::selectRow($query);
        $response['code'] = 0;

        echo json_encode($response);
        return true;
    }

    public function actionGetUsers()
    {
        $list = admin\Admin::getUsers();

        require_once(ROOT.'/views/usersList.php');
        return true;
    }

    public function actionGetPrepaidStatus()
    {
        $searchStr = empty($_GET['searchStr']) ? false : $_GET['searchStr'];
        if ($searchStr) {
            $statuses = admin\Admin::findPrepaid($searchStr);
            require_once(ROOT.'/views/showPrepaids.php');
        }
        return true;
    }

    public function actionChangePrepaid()
    {
        $uid = user\User::getId();
        $card = empty($_POST['card']) ? 0 : dbHelper\DbHelper::mysqlStr($_POST['card']);
        $amount = empty($_POST['card']) ? 0 : dbHelper\DbHelper::mysqlStr($_POST['amount']);

        $query = "/*".__FILE__.':'.__LINE__."*/ ".
            "SELECT prepayments_change($uid, c.id, '$amount')
            from cards c
            where c.card = '$card'";
        $result = dbHelper\DbHelper::selectRow($query);
        $response['code'] = 0;

        echo json_encode($response);
        return true;
    }
}
