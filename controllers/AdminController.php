<?php
namespace controllers\AdminController;

include_once ROOT.'/models/Admin.php';
include_once ROOT.'/controllers/ExportController.php';
include_once ROOT.'/components/Mail.php';

use models\Admin as admin;
use components\User as user;
use components\DbHelper as dbHelper;
use controllers\Export as exportController;
use components\Mailer as mailer;

define('MAX_CASS_CAPASITY', 1500);

/**
* productController
*/
class AdminController
{
    public function actionIndex()
    {
        $money = admin\Admin::getCollections();
        $statuses = admin\Admin::getHwsState();
        $devices = admin\Admin::$devices;
        $dates = admin\Admin::getDates();
        $sid = user\User::getSid();

        require_once(ROOT.'/views/admin.php');
        return true;
    }

    public function actionGetHwsState()
    {
        $money = admin\Admin::getCollections();
        $statuses = admin\Admin::getHwsState();
        $devices = admin\Admin::$devices;

        require_once(ROOT.'/views/hwsState.php');
        return true;
    }

    public function actionGetFiles()
    {
        $files = admin\Admin::getFiles();

        require_once(ROOT.'/views/files.php');
        return true;
    }

    public function actionMakeXml()
    {
        exportController\ExportController::makeXml(date('d.m.Y'));
        return true;
    }

    public function actionServiceOrder()
    {
        $id = empty($_POST['id']) ? 0 : dbHelper\DbHelper::mysqlStr($_POST['id']);
        $message = empty($_POST['message']) ? '' : dbHelper\DbHelper::mysqlStr($_POST['message']);

        $xml = exportController\ExportController::makeServiceOrder($id, $message);

        echo mailer\Mailer::sendAttachEmail(MY_EMAIL, SUPPORT_EMAIL, CC_EMAIL, 'Заявка на обслуживание '.ORG, '', 'order.xml', $xml);
        return true;
    }

    private function getServiceNamePart($idService)
    {
        $query = "/*".__FILE__.':'.__LINE__."*/ ".
            "SELECT r.id_parent, r.`desc`
            from custom_price_redstar r
            where r.id = '$idService'";
        $row = dbHelper\DbHelper::selectRow($query);

        if ($row['id_parent'] > 0) {
            return "{$this->getServiceNamePart($row['id_parent'])}. {$row['desc']}";
        } else {
            return $row['desc'];
        }
    }

    private function getServiceName($idService)
    {
        $query = "/*".__FILE__.':'.__LINE__."*/ ".
            "SELECT r.price, r.nds
            from custom_price_redstar r
            where r.id = '$idService'";
        $row = dbHelper\DbHelper::selectRow($query);

        return array(
            'price' => $row['price'],
            'nds' => $row['nds'],
            'name' => $this->getServiceNamePart($idService)
        );
    }

    public function actionGetCollectionDetail()
    {
        $id = empty($_GET['id']) ? 0 : dbHelper\DbHelper::mysqlStr($_GET['id']);

        $statuses = admin\Admin::getHwsState($id);
        $money = admin\Admin::getCollections($id);

        $html = '';
        $html .= "<div class='h4'>Терминал $id</div>";
        $html .= empty($statuses[$id]['address']) ? '' : "<div class='h5'><i>{$statuses[$id]['address']}</i></div>";

        $html .= "<table class='table table-striped table-bordered'>";
        $html .= "<thead><tr><th>Дата</th><th>Сумма</th></tr></thead>";
        $html .= "<tbody>";
        if (!empty($money['collections'][$id])) {
            foreach ($money['collections'][$id] as $row) {
                $html .= "<tr><td>{$row['dt']}</td><td>{$row['amount']}</td></tr>";
            }
        }
        $html .= "</tbody></table>";

        echo $html;
        return true;

    }

    public function actionGetTerminalHistory()
    {
        $id = empty($_GET['id']) ? 0 : dbHelper\DbHelper::mysqlStr($_GET['id']);

        $history = admin\Admin::getTerminalHistory($id);

        $html = '';
        $html .= "<div class='h4'>Терминал $id</div>";
        $html .= empty($statuses[$id]['address']) ? '' : "<div class='h5'><i>{$statuses[$id]['address']}</i></div>";
        $html .= "<table class='table table-striped table-bordered'>";
        $html .= "<thead><tr><th>Дата</th><th>Событие</th></tr></thead>";
        $html .= "<tbody>";
        if (!empty($history[$id])) {
            foreach ($history[$id] as $row) {
                $html .= "<tr><td>{$row['dt']}</td><td>{$row['action']}</td></tr>";
            }
        }
        $html .= "</tbody></table>";

        echo $html;
        return true;

    }

    public function actionGetCassetState()
    {
        $id = empty($_GET['id']) ? 0 : dbHelper\DbHelper::mysqlStr($_GET['id']);

        $money = admin\Admin::getCollections($id);
        $statuses = admin\Admin::getHwsState($id);

        // echo "<pre>"; print_r($money); echo "</pre>";
        $qty = empty($money['nominals'][$id]['total']) ? 0 : $money['nominals'][$id]['total'];
        $summ = empty($money['free'][$id]) ? 0 : $money['free'][$id];

        $html = '';
        $html .= "<div class='h4'>Терминал $id</div>";
        $html .= empty($statuses[$id]['address']) ? '' : "<div class='h5'><i>{$statuses[$id]['address']}</i></div>";
        $html .= "<p>В терминале находится $qty листов на сумму $summ руб.</p>";
        $html .= "<table class='table table-striped table-bordered'>";
        $html .= "<thead><tr><th>Номинал</th><th>Кол-во</th><th>Сумма</th></tr></thead>";
        $html .= "<tbody>";
        if (!empty($money['nominals'][$id])) {
            foreach ($money['nominals'][$id] as $n => $q) {
                if ($n == 'total') {
                    continue;
                }

                $s = $n * $q;
                $html .= "<tr><td>$n</td><td>$q</td><td>$s</td></tr>";
            }
        }
        $html .= "</tbody></table>";

        echo $html;
        return true;

    }

    public function actionGetCollectionDetails()
    {
        $dt = empty($_POST['dt']) ? 'now()' : "str_to_date('".dbHelper\DbHelper::mysqlStr($_POST['dt'])."', '%d.%m.%Y')";
        $id = empty($_POST['id']) ? 0 : dbHelper\DbHelper::mysqlStr($_POST['id']);

        $query = "/*".__FILE__.':'.__LINE__."*/ ".
            "SELECT u.address, '{$_POST['dt']}' dt
            from users u
            where u.id = '$id'";
        $collectionParams = dbHelper\DbHelper::selectSet($query);

        $query = "/*".__FILE__.':'.__LINE__."*/ ".
            "SELECT date_format(p.dt_insert, '%d.%m.%Y %H:%i') dt_oper, p.`desc` service, p.amount, c.price, c.nds, p.id_service
            from v_payments p
                left join custom_price_redstar c on p.id_service = c.id
            where p.id_user = '$id'
               and date(p.dt_insert) = date($dt)
            order by p.dt_insert";
        $opers = dbHelper\DbHelper::selectSet($query);
        for ($i = 0; $i < count($opers); $i++) {
            if ($opers[$i]['price'] == 0) {
                $opers[$i]['price'] = $opers[$i]['amount'];
            }
            $opers[$i]['fullService'] = $this->getServiceName($opers[$i]['id_service']);
        }

        require_once(ROOT.'/views/collectionDetailsXls.php');
        return true;
    }

    public function actionGetCollectionSummary()
    {
        $dt = empty($_POST['dt']) ? 'now()' : "str_to_date('".dbHelper\DbHelper::mysqlStr($_POST['dt'])."', '%d.%m.%Y')";
        $id = empty($_POST['id']) ? 0 : dbHelper\DbHelper::mysqlStr($_POST['id']);

        $query = "/*".__FILE__.':'.__LINE__."*/ ".
            "SELECT u.address, '{$_POST['dt']}' dt
            from users u
            where u.id = '$id'";
        $collectionParams = dbHelper\DbHelper::selectSet($query);

        $query = "/*".__FILE__.':'.__LINE__."*/ ".
            "SELECT date_format(p.dt_insert, '%d.%m.%Y') dt_oper, p.`desc` service, p.amount, c.price, c.nds, p.id_service, 1 qty
            from v_payments p
                left join custom_price_redstar c on p.id_service = c.id
            where p.id_user = '$id'
               and date(p.dt_insert) = date($dt)
            order by c.`desc`";
        $opers = dbHelper\DbHelper::selectSet($query);
        for ($i = 0; $i < count($opers); $i++) {
            $opers[$i]['fullService'] = $this->getServiceName($opers[$i]['id_service']);
            if ($opers[$i]['price'] == 0) {
                $opers[$i]['paid'] = $opers[$i]['amount'];
                $opers[$i]['rest'] = 0;
            } elseif ($opers[$i]['amount'] >= $opers[$i]['price']) {
                $opers[$i]['rest'] = $opers[$i]['amount'] - $opers[$i]['price'];
                $opers[$i]['paid'] = $opers[$i]['price'];
            } else {
                $opers[$i]['paid'] = 0;
                $opers[$i]['rest'] = $opers[$i]['amount'];
            }
        }

        $res = array();
        $rest = 0;
        for ($i = 0; $i < count($opers); $i++) {
            $ind = $opers[$i]['service'];

            $rest += $opers[$i]['rest'];

            if ($opers[$i]['paid']) {
                if (empty($res[$ind])) {
                    $res[$ind] = $opers[$i];
                    $res[$ind]['summ'] = $opers[$i]['paid'];
                } else {
                    $res[$ind]['qty']++;
                    $res[$ind]['paid'] += $opers[$i]['paid'];
                    $res[$ind]['summ'] += $opers[$i]['paid'];
                }
            }
        }

        if ($rest) {
            $res['Сдача']['dt_oper'] = $_POST['dt'];
            $res['Сдача']['fullService']['name'] = 'Сдача';
            $res['Сдача']['qty'] = '';
            $res['Сдача']['price'] = '';
            $res['Сдача']['summ'] = $rest;
            $res['Сдача']['nds'] = '0000';
        }

        require_once(ROOT.'/views/collectionSummaryXls.php');
        return true;
    }

    public function actionGetCollections()
    {
        $collections = admin\Admin::getCollections();

        require_once(ROOT.'/views/collections.php');
        return true;
    }

    public function actionGetTerminals()
    {
        $list = admin\Admin::getTerminals();

        require_once(ROOT.'/views/terminalsList.php');
        return true;
    }

    public function actionGetPriceGroup()
    {
        $active = empty($_GET['active']) ? 0 : dbHelper\DbHelper::mysqlStr($_GET['active']);
        $type = empty($_GET['type']) ? 2 : dbHelper\DbHelper::mysqlStr($_GET['type']);

        $list = admin\Admin::getPriceGroup($type, $active);

        require_once(ROOT.'/views/priceGroup.php');
        return true;
    }

    public function actionSetPriceGroupStatus()
    {
        $uid = user\User::getId();
        $id = empty($_POST['id']) ? 0 : dbHelper\DbHelper::mysqlStr($_POST['id']);
        $status = empty($_POST['status']) ? 0 : 1;

        $query = "/*".__FILE__.':'.__LINE__."*/ ".
            "SELECT custom_price_set_status($uid, 'redstar', '$id', $status)";
        $result = dbHelper\DbHelper::selectRow($query);
        $response['code'] = 0;

        echo json_encode($response);
        return true;
    }

    public function actionDelDate()
    {
        $uid = user\User::getId();
        $id = empty($_POST['id']) ? '0' : dbHelper\DbHelper::mysqlStr($_POST['id']);
        $year = empty($_POST['year']) ? date('Y') : dbHelper\DbHelper::mysqlStr($_POST['year']);

        $query = "/*".__FILE__.':'.__LINE__."*/ ".
            "SELECT extra_days_del($uid, '$id')";
        $result = dbHelper\DbHelper::selectRow($query);
        $response['code'] = 0;
        $response['html'] = $this->getDates($year);

        echo json_encode($response);
        return true;
    }

    public function actionGetSchedule()
    {
        $uid = user\User::getId();
        $year = empty($_POST['year']) ? date('Y') : dbHelper\DbHelper::mysqlStr($_POST['year']);

        $response['code'] = 0;
        $response['html'] = $this->getDates($year);

        echo json_encode($response);
        return true;
    }

    public function actionAddDate()
    {
        $uid = user\User::getId();
        $id = empty($_POST['dt']) ? '' : dbHelper\DbHelper::mysqlStr($_POST['dt']);
        $isWork = empty($_POST['isWork']) ? 2 : dbHelper\DbHelper::mysqlStr($_POST['isWork']);
        $year = empty($_POST['year']) ? date('Y') : dbHelper\DbHelper::mysqlStr($_POST['year']);

        $query = "/*".__FILE__.':'.__LINE__."*/ ".
            "SELECT extra_days_add($uid, str_to_date('$id', '%d.%m.%Y'), $isWork)";
        $result = dbHelper\DbHelper::selectRow($query);
        $response['code'] = 0;
        $response['html'] = $this->getDates($year);

        echo json_encode($response);
        return true;
    }

    private function getDates($year)
    {
        $query = "/*".__FILE__.':'.__LINE__."*/ ".
            "SELECT e.id, DATE_FORMAT(e.dt, '%d.%m.%Y') dt
            from extra_days e
            where year(e.dt) = '$year'
                and e.dt_type = 2";
        $works = dbHelper\DbHelper::selectSet($query);

        $query = "/*".__FILE__.':'.__LINE__."*/ ".
            "SELECT e.id, DATE_FORMAT(e.dt, '%d.%m.%Y') dt
            from extra_days e
            where year(e.dt) = '$year'
                and e.dt_type = 1";
        $holidays = dbHelper\DbHelper::selectSet($query);

        $html = "<div class='panel panel-default'>
            <div class='panel-heading'>
                <h3 class='panel-title'>Дополнительные рабочие дни</h3>
            </div>
            <div class='panel-body extra-work'>
                <table class='table table-striped'>";

        foreach ($works as $day) {
            $html .= "<tr>";
            $html .= "<td>{$day['dt']}</td>";
            $html .= "<td><button type='button' class='btn btn-primary del-date' value='{$day['id']}'>Удалить</button></td>";
            $html .= "</tr>";
        }

        $html .= "</table>
                </div>
            </div>
            <div class='panel panel-default'>
                <div class='panel-heading'>
                    <h3 class='panel-title'>Дополнительные выходные дни</h3>
                </div>
                <div class='panel-body extra-holiday'>
                    <table class='table table-striped'>";

        foreach ($holidays as $day) {
            $html .= "<tr>";
            $html .= "<td>{$day['dt']}</td>";
            $html .= "<td><button type='button' class='btn btn-primary del-date' value='{$day['id']}'>Удалить</button></td>";
            $html .= "</tr>";
        }

        $html .= "</table>
                </div>
            </div>";
        return $html;
    }

    public function actionDeletePriceItem()
    {
        $uid = user\User::getId();
        $id = empty($_POST['id']) ? 0 : dbHelper\DbHelper::mysqlStr($_POST['id']);

        $query = "/*".__FILE__.':'.__LINE__."*/ ".
            "SELECT custom_price_delete($uid, 'redstar', '$id')";
        $result = dbHelper\DbHelper::selectRow($query);
        $response['code'] = 0;

        echo json_encode($response);
        return true;
    }

    public function actionSetClientsDesc()
    {
        $uid = user\User::getId();
        $id = empty($_POST['id']) ? 0 : dbHelper\DbHelper::mysqlStr($_POST['id']);
        $text = empty($_POST['text']) ? '' : dbHelper\DbHelper::mysqlStr($_POST['text']);

        $query = "/*".__FILE__.':'.__LINE__."*/ ".
            "SELECT custom_price_set_clients_desc($uid, 'redstar', '$id', '$text')";
        $result = dbHelper\DbHelper::selectRow($query);
        $response['code'] = 0;

        echo json_encode($response);
        return true;
    }

    public function actionSetCommentItem()
    {
        $uid = user\User::getId();
        $id = empty($_POST['id']) ? 0 : dbHelper\DbHelper::mysqlStr($_POST['id']);
        $text = empty($_POST['text']) ? '' : dbHelper\DbHelper::mysqlStr($_POST['text']);

        $query = "/*".__FILE__.':'.__LINE__."*/ ".
            "SELECT custom_price_set_comment($uid, 'redstar', '$id', '$text')";
        $result = dbHelper\DbHelper::selectRow($query);
        $response['code'] = 0;

        echo json_encode($response);
        return true;
    }

    public function actionSetPrice()
    {
        $uid = user\User::getId();
        $id = empty($_POST['id']) ? 0 : dbHelper\DbHelper::mysqlStr($_POST['id']);
        $price = empty($_POST['price']) ? '' : dbHelper\DbHelper::mysqlStr($_POST['price']);

        $query = "/*".__FILE__.':'.__LINE__."*/ ".
            "SELECT custom_price_set_price($uid, 'redstar', '$id', '$price')";
        $result = dbHelper\DbHelper::selectRow($query);
        $response['code'] = 0;

        echo json_encode($response);
        return true;
    }

    public function actionSetDayStatus()
    {
        $uid = user\User::getId();
        $id = empty($_POST['id']) ? 0 : dbHelper\DbHelper::mysqlStr($_POST['id']);
        $idDay = empty($_POST['idDay']) ? 0 : dbHelper\DbHelper::mysqlStr($_POST['idDay']);
        $status = empty($_POST['status']) ? 0 : dbHelper\DbHelper::mysqlStr($_POST['status']);

        $query = "/*".__FILE__.':'.__LINE__."*/ ".
            "SELECT custom_price_set_dayoff($uid, 'redstar', '$id', '$idDay', '$status')";
        $result = dbHelper\DbHelper::selectRow($query);
        $response['code'] = 0;

        echo json_encode($response);
        return true;
    }

    public function actionSetNds()
    {
        $uid = user\User::getId();
        $id = empty($_POST['id']) ? 0 : dbHelper\DbHelper::mysqlStr($_POST['id']);
        $nds = empty($_POST['nds']) ? '0000' : dbHelper\DbHelper::mysqlStr($_POST['nds']);

        $query = "/*".__FILE__.':'.__LINE__."*/ ".
            "SELECT custom_price_set_nds($uid, 'redstar', '$id', '$nds')";
        $result = dbHelper\DbHelper::selectRow($query);
        $response['code'] = 0;

        echo json_encode($response);
        return true;
    }

    public function actionSetWorkTime()
    {
        $uid = user\User::getId();
        $id = empty($_POST['id']) ? 0 : dbHelper\DbHelper::mysqlStr($_POST['id']);
        $idDay = !isset($_POST['idDay']) ? 8 : dbHelper\DbHelper::mysqlStr($_POST['idDay']);
        $timeStart = empty($_POST['timeStart']) ? 'null' : "str_to_date('01.01.2001 ".dbHelper\DbHelper::mysqlStr($_POST['timeStart'])."', '%d.%m.%Y %H:%i')";
        $timeFinish = empty($_POST['timeFinish']) ? 'null' : "str_to_date('01.01.2001 ".dbHelper\DbHelper::mysqlStr($_POST['timeFinish'])."', '%d.%m.%Y %H:%i')";

        $query = "/*".__FILE__.':'.__LINE__."*/ ".
            "SELECT custom_price_set_time($uid, 'redstar', '$id', '$idDay',  $timeStart, $timeFinish)";
        $result = dbHelper\DbHelper::selectRow($query);
        $response['code'] = 0;

        echo json_encode($response);
        return true;
    }

    public function actionSetColor()
    {
        $uid = user\User::getId();
        $id = empty($_POST['id']) ? 0 : dbHelper\DbHelper::mysqlStr($_POST['id']);
        $color = empty($_POST['color']) ? 'primary' : dbHelper\DbHelper::mysqlStr($_POST['color']);

        $query = "/*".__FILE__.':'.__LINE__."*/ ".
            "SELECT custom_price_set_color($uid, 'redstar', '$id', '$color')";
        $result = dbHelper\DbHelper::selectRow($query);
        $response['code'] = 0;

        echo json_encode($response);
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
        $idRole = $ip ? 2 : 3;
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

    public function actionGetCards()
    {
        $list = admin\Admin::getCards();

        require_once(ROOT.'/views/cardList.php');
        return true;
    }

    public function actionAddCard()
    {
        $uid = user\User::getId();
        $num = $_POST['num'];
        $org = empty($_POST['org']) ? '' : $_POST['org'];
        $address = empty($_POST['address']) ? '' : $_POST['address'];

        $query = "/*".__FILE__.':'.__LINE__."*/ ".
            "SELECT custom_cards_add($uid, '$num', '$org', '$address')";
        $result = dbHelper\DbHelper::selectRow($query);
        $response['code'] = 0;

        echo json_encode($response);
        return true;
    }

    public function actionEditCard()
    {
        $uid = user\User::getId();
        $id = $_POST['id'];
        $num = empty($_POST['num']) ? null : $_POST['num'];
        $org = empty($_POST['org']) ? '' : $_POST['org'];
        $address = empty($_POST['address']) ? '' : $_POST['address'];

        $query = "/*".__FILE__.':'.__LINE__."*/ ".
            "SELECT custom_cards_edit($uid, $id, '$num', '$org', '$address')";
        $result = dbHelper\DbHelper::selectRow($query);
        $response['code'] = 0;

        echo json_encode($response);
        return true;
    }

    public function actionDeleteCard() {
        $uid = user\User::getId();
        $id = empty($_POST['id']) ? 0 : dbHelper\DbHelper::mysqlStr($_POST['id']);
        $query = "/*".__FILE__.':'.__LINE__."*/ ".
            "SELECT custom_cards_delete($uid, '$id')";
        $result = dbHelper\DbHelper::selectRow($query);
        $response['code'] = 0;

        echo json_encode($response);
        return true;
    }

    public function actionUploadCards() {
        $uid = user\User::getId();
        $uploadfile = ROOT . '/tmp/' . basename($_FILES['file']['name']);

        if(move_uploaded_file($_FILES['file']['tmp_name'], $uploadfile)) {
            $data = file_get_contents($uploadfile);
            $data = explode('***', $data);
            $data = array_values(array_filter($data, function($el) {
                    return !empty($el);
                }));

            $query = "/*" . __FILE__ . ':' . __LINE__ . "*/ " .
                "TRUNCATE TABLE custom_cards";
            $result = dbHelper\DbHelper::call($query);

            foreach($data as $item) {
                $item = preg_replace('/[\']/', '\\\'', $item);
                $arr = array_values(array_filter(explode("\n", $item), function($el) {
                        return !empty($el);
                    }));
                if(count($arr) == 11) {
                    $num = $arr[0];
                    $org = $arr[1] ? $arr[1] : '';
                    $address = $arr[2] ? $arr[2] : '';
                    $query = "/*" . __FILE__ . ':' . __LINE__ . "*/ " .
                        "SELECT custom_cards_add($uid, '$num', '$org', '$address')";
                    $result = dbHelper\DbHelper::selectRow($query);
                }
            }
        }
        $response['code'] = 0;
        echo json_encode($response);
        return true;
    }

}
