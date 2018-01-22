<?php
namespace controllers\AjaxController;

use components\DbHelper as dbHelper;
use components\User as user;

define('FIRST_SCREEN', 1);
define('FIRST_ACTION', 'move');
define('GET_MONEY_SCREEN', 2);
define('GET_MONEY_ACTION', 'getMoneyScreen');
define('GET_CARD_SCREEN', 13);
define('GET_CARD_ACTION', 'move');
define('ERROR_SCREEN', 7);
define('LOCK_SCREEN', 12);
define('NO_CARD_SCREEN', 13);
// define('NO_SERVICES_SCREEN', 14);
define('SERVICE_LIST_SCREEN', 1);

/**
 * обработка запросов ajax.
 */
class AjaxController
{
    ///////////////////////////////////////////////////////////////////////////////////////////////////
    /**
     * кладем в массивы для замены в шаблонах массив POST.
     *
     * @return array массивы для замены
     */
    private function putPostIntoReplaceArray(&$replArray)
    {
        if (!empty($_POST['values'])) {
            foreach ($_POST['values'] as $key => $value) {
                $replArray['patterns'][] = '{'.strtoupper($key).'}';
                $replArray['values'][] = $value;
            }
        }
    }

    ///////////////////////////////////////////////////////////////////////////////////////////////////
    /**
     * Обработка команды оплаты
     */
    public function actionPay()
    {
        $uid = user\User::getId();

        $nextScreen = (empty($_POST['nextScreen'])) ? user\User::getFirstScreen() : dbHelper\DbHelper::mysqlStr($_POST['nextScreen']);
        $card_id = (empty($_POST['values']['card_id'])) ? 0 : dbHelper\DbHelper::mysqlStr($_POST['values']['card_id']);
        $amount = (empty($_POST['values']['amount'])) ? 0 : dbHelper\DbHelper::mysqlStr($_POST['values']['amount']);

        if (!$amount) {
            // уходим на первый экран
            $_POST['nextScreen'] = user\User::getFirstScreen();
            $this->actionMove();
            exit();
        }

        $replArray = $this->makeReplaceArray($nextScreen);

        // подтверждаем оплату
        $query = '/*'.__FILE__.':'.__LINE__.'*/ '.
            "SELECT payments_add($uid, '$card_id', '$amount') id";
        $pay = dbHelper\DbHelper::selectRow($query);

        $replArray['patterns'][] = '{TRN}';
        $replArray['values'][] = $pay['id'];

        $replArray['patterns'][] = '{AMOUNT}';
        $replArray['values'][] = number_format($amount, 2, '.', ' ');

        $query = "/*".__FILE__.':'.__LINE__."*/ ".
            "SELECT u.id TERM, u.address ADDRESS, p.card CARD, p.org CARD_ORG, p.address CARD_ADDRESS, date_format(p.dt_insert, '%d.%m.%Y %H:%i:%s') DATETIME_TRN 
            from payments p
                join users u on p.id_user = u.id
            where p.id = '{$pay['id']}'";
        $payParams = dbHelper\DbHelper::selectRow($query);
        foreach ($payParams as $key => $value) {
            $replArray['patterns'][] = '{'.$key.'}';
            $replArray['values'][] = $value;
        }

        $response = $this->getScreen($nextScreen, $replArray);
        
        $response['message'] = '';
        $response['code'] = 0;
        
        //отправляем результат
        echo json_encode($response);
        return true;
    }

    ///////////////////////////////////////////////////////////////////////////////////////////////////
    /**
     * Обработка команды получения экрана приема денег
     */
    public function actionGetMoneyScreen()
    {
        $uid = user\User::getId();

        $nextScreen = (empty($_POST['nextScreen'])) ? user\User::getFirstScreen() : dbHelper\DbHelper::mysqlStr($_POST['nextScreen']);
        $idService = (empty($_POST['values']['idService'])) ? 0 : dbHelper\DbHelper::mysqlStr($_POST['values']['idService']);
        $qty = (empty($_POST['values']['qty'])) ? 0 : dbHelper\DbHelper::mysqlStr($_POST['values']['qty']);
        $idBasket = (empty($_POST['values']['idBasket'])) ? 0 : dbHelper\DbHelper::mysqlStr($_POST['values']['idBasket']);
        if (isset($_POST['values']['idBasket'])) {
            unset($_POST['values']['idBasket']);
        }

        $replArray = $this->makeReplaceArray($nextScreen);
        $this->putPostIntoReplaceArray($replArray);

        $response = $this->getScreen($nextScreen, $replArray);

        $response['message'] = '';
        $response['code'] = 0;
        
        //отправляем результат
        echo json_encode($response);
        return true;
    }

    ///////////////////////////////////////////////////////////////////////////////////////////////////
    /**
     * Обработка команды подтверждения явочной карты
     */
    public function actionConfirm()
    {
        $uid = user\User::getId();
        $card = (empty($_POST['values']['card'])) ? 0 : dbHelper\DbHelper::mysqlStr($_POST['values']['card']);
        $card = str_replace('_', '', $card);
        $nextScreen = (empty($_POST['nextScreen'])) ? user\User::getFirstScreen() : dbHelper\DbHelper::mysqlStr($_POST['nextScreen']);

        $replArray = $this->makeReplaceArray($nextScreen);
        // $this->putPostIntoReplaceArray($replArray);

        $query = "/*".__FILE__.':'.__LINE__."*/ ".
            "SELECT c.id, c.org, c.address
            from custom_cards c 
            where c.num = '$card'";
        $row = dbHelper\DbHelper::selectRow($query);

        if (empty($row)) {
            $_POST['nextScreen'] = user\User::getFirstScreen();
            $this->actionMove();
            exit;
        }

        $replArray['patterns'][] = '{CARD}';
        $replArray['values'][] = $card;

        $replArray['patterns'][] = '{CARD_ID}';
        $replArray['values'][] = $row['id'];

        $replArray['patterns'][] = '{CARD_ORG}';
        $replArray['values'][] = $row['org'];

        $replArray['patterns'][] = '{CARD_ADDRESS}';
        $replArray['values'][] = $row['address'];

        $response = $this->getScreen($nextScreen, $replArray);

        $response['message'] = '';
        $response['code'] = 0;
        
        //отправляем результат
        echo json_encode($response);
        return true;
    }

    ///////////////////////////////////////////////////////////////////////////////////////////////////
    /**
     * Обработка команды перехода
     */
    public function actionMove($message = '')
    {
        if (user\User::getStatus() == 0) {
            $_POST['nextScreen'] = LOCK_SCREEN;
        }

        $nextScreen = (empty($_POST['nextScreen'])) ? user\User::getFirstScreen() : dbHelper\DbHelper::mysqlStr($_POST['nextScreen']);
        $idService = (empty($_POST['values']['idService'])) ? 0 : dbHelper\DbHelper::mysqlStr($_POST['values']['idService']);

        $replArray = $this->makeReplaceArray($nextScreen);
        $this->putPostIntoReplaceArray($replArray);

        $response = $this->getScreen($nextScreen, $replArray);

        $response['message'] = $message;
        $response['code'] = 0;
        
        //отправляем результат
        echo json_encode($response);
        return true;
    }

    ///////////////////////////////////////////////////////////////////////////////////////////////////
    /**
     * Обработка команды инкассации
     */
    public function actionCollection()
    {
        $nextScreen = (empty($_POST['nextScreen'])) ? user\User::getFirstScreen() : dbHelper\DbHelper::mysqlStr($_POST['nextScreen']);
        $uid = user\User::getId();

        $replArray = $this->makeReplaceArray($nextScreen);

        $query = "/*".__FILE__.':'.__LINE__."*/ "."SELECT collection($uid) collectionAmount";
        $row = dbHelper\DbHelper::selectRow($query);
        $replArray['patterns'][] = '{COLLECTION_AMOUNT}';
        $replArray['values'][] = $row['collectionAmount'];

        $response = $this->getScreen($nextScreen, $replArray);

        $response['message'] = '';
        $response['code'] = 0;
        
        //отправляем результат
        echo json_encode($response);
        return true;
    }

    ///////////////////////////////////////////////////////////////////////////////////////////////////
    /**
     * Обработка команды записи ошибки
     */
    public function actionWriteLog()
    {
        $nextScreen = (empty($_POST['nextScreen'])) ? false : dbHelper\DbHelper::mysqlStr($_POST['nextScreen']);
        $type = (empty($_POST['values']['type'])) ? 'NA' : dbHelper\DbHelper::mysqlStr($_POST['values']['type']);
        $message = (empty($_POST['values']['message'])) ? '' : dbHelper\DbHelper::mysqlStr($_POST['values']['message']);
        $isError = (empty($_POST['values']['isError'])) ? 0 : 1;
        $value = (empty($_POST['values']['value'])) ? 0 : dbHelper\DbHelper::mysqlStr($_POST['values']['value']);
        $uid = user\User::getId();

        $query = "/*".__FILE__.':'.__LINE__."*/ "."CALL hws_status_write($uid, '$type', $isError, '$message')";
        $row = dbHelper\DbHelper::call($query);

        if ($type == 'cash') {
            $query = "/*".__FILE__.':'.__LINE__."*/ "."CALL notes_write($uid, '$value')";
            $row = dbHelper\DbHelper::call($query);
        }

        if ($nextScreen) {
            $replArray = $this->makeReplaceArray($nextScreen);
            $response = $this->getScreen($nextScreen, $replArray);
        }

        $response['message'] = $message;
        $response['code'] = 0;
        
        //отправляем результат
        echo json_encode($response);
        return true;
    }

    ///////////////////////////////////////////////////////////////////////////////////////////////////
    /**
     * получение экрана для отправки на клиента.
     *
     * @param array $replArray массивы для замены в шаблоне
     * @param int   $idScreen  номер экрана, который надо получить
     *
     * @return array параметры экрана
     */
    private function getScreen($idScreen, $replArray)
    {
        $response['idScreen'] = $idScreen;

        if (empty($replArray['patterns'])) {
            $replArray['patterns'] = array();
            $replArray['values'] = array();
        }

        $idScreen = "s$idScreen";

        include_once ROOT.'/views/kbd/kbdRus.php';
        include_once ROOT.'/views/kbd/kbdNum.php';
        include_once ROOT.'/views/kbd/kbdRusNum.php';

        // добавляем клавиатуры
        $replArray['patterns'][] = '{KBD_A}';
        $replArray['values'][] = getKbdA();
        $replArray['patterns'][] = '{KBD_N}';
        $replArray['values'][] = getKbdN();
        $replArray['patterns'][] = '{KBD_AN}';
        $replArray['values'][] = getKbdAN();

        // загружаем параметры
        $query = '/*'.__FILE__.':'.__LINE__.'*/ '.
            'SELECT g.params_xml from general_settings g';
        $row = dbHelper\DbHelper::selectRow($query);
        $xmlstr = stripcslashes($row['params_xml']);
        $xml = simplexml_load_string($xmlstr);

        // добавляем название экрана
        if (!empty($xml->$idScreen->desc)) {
            $replArray['patterns'][] = '{SCREEN}';
            $replArray['values'][] = $xml->$idScreen->desc;
        }

        // работа с купюроприемником
        $response['cash'] = (empty($xml->$idScreen->cash)) ? '0' : '1';

        // работа со считкой
        $response['rfid'] = (empty($xml->$idScreen->rfid)) ? array() : $xml->$idScreen->rfid;

        // вцыполнение проверок
        $response['check']['hw'] = (empty($xml->$idScreen->check->hw)) ? '0' : '1';

        // экранная форма
        if (!empty($xml->$idScreen->screen)) {
            $query = '/*'.__FILE__.':'.__LINE__.'*/ '.
                "SELECT s.html from screens s where s.id = '{$xml->$idScreen->screen}'";
            $row = dbHelper\DbHelper::selectRow($query);
            $response['html'] = stripslashes($row['html']);
            $response['html'] = str_replace($replArray['patterns'], $replArray['values'], $response['html']);
            $response['html'] = preg_replace('/({.*?})/ui', '', $response['html']);
        }

        // таймер
        if (!empty($xml->$idScreen->timer)) {
            $response['tScreen'] = (empty($xml->$idScreen->timer->screen)) ? 0 : (int) $xml->$idScreen->timer->screen;
            $response['tTimeout'] = (empty($xml->$idScreen->timer->timeout)) ? 0 : (int) $xml->$idScreen->timer->timeout;
            $response['tTimeoutNoMoney'] = (empty($xml->$idScreen->timer->timeoutNoMoney)) ? 0 : (int) $xml->$idScreen->timer->timeoutNoMoney;
            $response['tAction'] = (empty($xml->$idScreen->timer->action)) ? 'move' : (string) $xml->$idScreen->timer->action;
            $timer['tScreen'] = $response['tScreen'];
            $timer['tTimeout'] = $response['tTimeout'];
            $timer['tTimeoutNoMoney'] = $response['tTimeoutNoMoney'];
            $timer['tAction'] = $response['tAction'];
        }

        // печатная форма
        if (!empty($xml->$idScreen->print->full)) {
            $query = '/*'.__FILE__.':'.__LINE__.'*/ '.
                "SELECT s.html from screens s where s.id = '{$xml->$idScreen->print->full}'";
            $row = dbHelper\DbHelper::selectRow($query);

            $printForm = stripslashes($row['html']);
            $printForm = str_replace($replArray['patterns'], $replArray['values'], $printForm);
            $printForm = preg_replace('/({.*?})/ui', '', $printForm);
            $response['printForm']['full'] = $printForm;
        }

        $top = '';
        if (!empty($xml->$idScreen->print->top)) {
            $query = '/*'.__FILE__.':'.__LINE__.'*/ '.
                "SELECT s.html from screens s where s.id = '{$xml->$idScreen->print->top}'";
            $row = dbHelper\DbHelper::selectRow($query);

            $printForm = stripslashes($row['html']);
            $printForm = str_replace($replArray['patterns'], $replArray['values'], $printForm);
            $printForm = preg_replace('/({.*?})/ui', '', $printForm);
            $top = $printForm;
        }

        $bottom = '';
        if (!empty($xml->$idScreen->print->bottom)) {
            $query = '/*'.__FILE__.':'.__LINE__.'*/ '.
                "SELECT s.html from screens s where s.id = '{$xml->$idScreen->print->bottom}'";
            $row = dbHelper\DbHelper::selectRow($query);

            $printForm = stripslashes($row['html']);
            $printForm = str_replace($replArray['patterns'], $replArray['values'], $printForm);
            $printForm = preg_replace('/({.*?})/ui', '', $printForm);
            $bottom = $printForm;
        }

        if (!empty($xml->$idScreen->print->rest) && !empty($replArray['nofr'])) {
            $query = '/*'.__FILE__.':'.__LINE__.'*/ '.
                "SELECT s.html from screens s where s.id = '{$xml->$idScreen->print->rest}'";
            $row = dbHelper\DbHelper::selectRow($query);

            $printForm = stripslashes($row['html']);

            foreach ($replArray['nofr'] as $oneticket) {
                $pp = array_merge($replArray['patterns'], $oneticket['patterns']);
                $pv = array_merge($replArray['values'], $oneticket['values']);
                $pf = str_replace($pp, $pv, $printForm);
                $pf = preg_replace('/({.*?})/ui', '', $pf);
                $response['printForm']['nofr'][] = array(
                    'line' => $pf
                );
            }
        }

        if (!empty($xml->$idScreen->print->elements) && !empty($replArray['fr'])) {
            $query = '/*'.__FILE__.':'.__LINE__.'*/ '.
                "SELECT s.html from screens s where s.id = '{$xml->$idScreen->print->elements}'";
            $row = dbHelper\DbHelper::selectRow($query);

            $printForm = stripslashes($row['html']);

            foreach ($replArray['fr'] as $oneticket) {
                $pp = array_merge($replArray['patterns'], $oneticket['patterns']);
                $pv = array_merge($replArray['values'], $oneticket['values']);
                $pf = str_replace($pp, $pv, $printForm);
                $pf = preg_replace('/({.*?})/ui', '', $pf);
                $response['printForm']['fr'][] = array(
                    'amount' => $oneticket['amount'],
                    'top' => $top,
                    'bottom' => $bottom,
                    'elements' => $pf,
                );
            }
        }

        date_default_timezone_set('Asia/Omsk');
        $response['dt'] = array(
            'year' => date('Y'),
            'month' => date('m') - 1,
            'date' => date('d'),
            'hours' => date('H'),
            'minutes' => date('i'),
            'seconds' => date('s'),
            );

        return $response;
    }

    ///////////////////////////////////////////////////////////////////////////////////////////////////
    /**
     * формируем массивы для замены в шаблонах.
     *
     * @return array массивы для замены
     */
    private function makeReplaceArray()
    {
        // получаем общие параметры
        $query = '/*'.__FILE__.':'.__LINE__.'*/ '.
            "SELECT date_format(now(), '%d.%m.%Y') `DATE`,
                date_format(now(), '%H:%i:%s') `TIME`,
                date_format(now(), '%d.%m.%Y %H:%i:%s') `DATETIME`
            from general_settings g";
        $info = dbHelper\DbHelper::selectRow($query);

        $info['UID'] = user\User::getId();

        foreach ($info as $pattern => $value) {
            $patterns[] = '{'.$pattern.'}';
            $values[] = stripslashes($value);
        }

        return array('patterns' => $patterns, 'values' => $values);
    }
}
