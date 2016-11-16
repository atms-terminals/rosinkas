<?php
namespace controllers\AjaxController;

use components\DbHelper as dbHelper;
use components\User as user;
use components\Proffit as proffit;

define('ERROR_SCREEN', 7);
define('LOCK_SCREEN', 12);

/**
 * обработка запросов ajax.
 */
class AjaxController
{
    ///////////////////////////////////////////////////////////////////////////////////////////////////
    /**
     * Обработка команды оплаты
     */
    public function actionPay()
    {
        $nextScreen = (empty($_POST['nextScreen'])) ? user\User::getFirstScreen() : dbHelper\DbHelper::mysqlStr($_POST['nextScreen']);
        $idAbonement = (empty($_POST['values']['idAbonement'])) ? 0 : dbHelper\DbHelper::mysqlStr($_POST['values']['idAbonement']);
        $amount = (empty($_POST['values']['amount'])) ? 0 : dbHelper\DbHelper::mysqlStr($_POST['values']['amount']);
        $price = (empty($_POST['values']['price'])) ? 0 : dbHelper\DbHelper::mysqlStr($_POST['values']['price']);
        $card = (empty($_POST['values']['card'])) ? 0 : dbHelper\DbHelper::mysqlStr($_POST['values']['card']);
        $customer = (empty($_POST['values']['customer'])) ? 0 : dbHelper\DbHelper::mysqlStr($_POST['values']['customer']);
        $serviceName = (empty($_POST['values']['serviceName'])) ? '' : dbHelper\DbHelper::mysqlStr($_POST['values']['serviceName']);

$amount = 800;

        $prepayment = (empty($_POST['values']['prepayment'])) ? 0 : dbHelper\DbHelper::mysqlStr($_POST['values']['prepayment']);
        $purchaseAmount = (empty($_POST['values']['purchaseAmount'])) ? 0 : dbHelper\DbHelper::mysqlStr($_POST['values']['purchaseAmount']);

        $uid = user\User::getId();

        if (!$idAbonement /*|| !$amount*/ || !$card || !$price) {
            // уходим на первый экран
            $_POST['nextScreen'] = user\User::getFirstScreen();
            $this->actionMove('Не все поля переданы');
            exit();
        }

        $replArray = $this->makeReplaceArray($nextScreen);

        // записываем запрос на оплату
        $query = '/*'.__FILE__.':'.__LINE__.'*/ '.
            "call payments_prepare($uid, '$idAbonement', '$card', '$customer', '$amount', $price, '$purchaseAmount', @idPayment, @countUnits, @prepayment);";
        $pay = dbHelper\DbHelper::call($query);
        $query = "/*".__FILE__.':'.__LINE__."*/ "."SELECT @idPayment idPayment, @countUnits countUnits, @prepayment prepayment";
        $payment = dbHelper\DbHelper::selectRow($query);

        $replArray['patterns'][] = '{SERVICE_NAME}';
        // $replArray['values'][] = $serviceName;
        $replArray['values'][] = 'Внесение наличных в счет оплаты';

        $prepayment = $payment['prepayment'];
        $replArray['patterns'][] = '{PREPAYMENT_BEFORE}';
        $replArray['values'][] = $prepayment;

        $idPayment = $payment['idPayment'];
        $replArray['patterns'][] = '{TRN}';
        $replArray['values'][] = $idPayment;

        $countUnits = $payment['countUnits'];
        $replArray['patterns'][] = '{COUNT_UNITS}';
        $replArray['values'][] = $countUnits;

        $replArray['patterns'][] = '{PRICE}';
        $replArray['values'][] = $price;

        $replArray['patterns'][] = '{SUMM}';
        $replArray['values'][] = $countUnits * $price;

        $replArray['patterns'][] = '{AMOUNT}';
        $replArray['values'][] = $amount;

        
        if ($countUnits) {
            // пишем платеж в проффит
            try {
                // получаем список услуг
                $servicesList = proffit\Proffit::pay($card, $idAbonement, $countUnits * $price, $countUnits);
            } catch (\Exception $e) {
                // уходим на первый экран
                $_POST['nextScreen'] = ERROR_SCREEN;
                $_POST['values']['type'] = 'proffit';
                $_POST['values']['message'] = 'Ошибка зачисления платежа: '.$e->getMessage();
                $this->actionWriteLog();
                exit();
            }
        }

        // подтверждаем оплату
        $query = '/*'.__FILE__.':'.__LINE__.'*/ '.
            "call payments_confirm($uid, $idPayment, @prepayment)";
        $pay = dbHelper\DbHelper::call($query);
        $query = "/*".__FILE__.':'.__LINE__."*/ "."SELECT @prepayment prepayment";
        $prepayment = dbHelper\DbHelper::selectRow($query);

        $preAmount = $prepayment['prepayment'];
        $replArray['patterns'][] = '{PREPAYMENT}';
        $replArray['values'][] = $preAmount;

        // добавляем список сервисов
        $replArray['patterns'][] = '{ABONEMENT_ID}';
        $replArray['values'][] = $idAbonement;

        $response = $this->getScreen($nextScreen, $replArray);

        $response['printForm']['amount'] = $amount;

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
        $nextScreen = (empty($_POST['nextScreen'])) ? user\User::getFirstScreen() : dbHelper\DbHelper::mysqlStr($_POST['nextScreen']);
        $idAbonement = (empty($_POST['values']['idAbonement'])) ? 0 : dbHelper\DbHelper::mysqlStr($_POST['values']['idAbonement']);
        $card = (empty($_POST['values']['card'])) ? 0 : dbHelper\DbHelper::mysqlStr($_POST['values']['card']);
        $price = (empty($_POST['values']['price'])) ? 0 : dbHelper\DbHelper::mysqlStr($_POST['values']['price']);
        $customer = (empty($_POST['values']['customer'])) ? '' : dbHelper\DbHelper::mysqlStr($_POST['values']['customer']);
        $serviceName = (empty($_POST['values']['serviceName'])) ? '' : dbHelper\DbHelper::mysqlStr($_POST['values']['serviceName']);
        $purchaseAmount = (empty($_POST['values']['purchaseAmount'])) ? 0 : dbHelper\DbHelper::mysqlStr($_POST['values']['purchaseAmount']);
        $prepayment = (empty($_POST['values']['prepayment'])) ? 0 : dbHelper\DbHelper::mysqlStr($_POST['values']['prepayment']);

        if (!$idAbonement) {
            // уходим на первый экран
            $_POST['nextScreen'] = user\User::getFirstScreen();
            $this->actionMove($e->getMessage());
            exit();
        }

        $replArray = $this->makeReplaceArray($nextScreen);
        // добавляем список сервисов
        $replArray['patterns'][] = '{ABONEMENT_ID}';
        $replArray['values'][] = $idAbonement;
        $replArray['patterns'][] = '{CARD}';
        $replArray['values'][] = $card;
        $replArray['patterns'][] = '{PRICE}';
        $replArray['values'][] = $price;
        $replArray['patterns'][] = '{CUSTOMER}';
        $replArray['values'][] = $customer;
        $replArray['patterns'][] = '{SERVICE_NAME}';
        $replArray['values'][] = $serviceName;
        $replArray['patterns'][] = '{PURCHASE_AMOUNT}';
        $replArray['values'][] = $purchaseAmount;
        $replArray['patterns'][] = '{PREPAYMENT}';
        $replArray['values'][] = number_format($prepayment, 2, '.', ' ');
        $replArray['patterns'][] = '{MIN_SUMM}';
        $replArray['values'][] = number_format($purchaseAmount - $prepayment, 2, '.', ' ');

        $response = $this->getScreen($nextScreen, $replArray);

        $response['message'] = '';
        $response['code'] = 0;
        
        //отправляем результат
        echo json_encode($response);
        return true;
    }

    ///////////////////////////////////////////////////////////////////////////////////////////////////
    /**
     * Обработка команды получения списка услуг
     */
    public function actionGetBalance()
    {
        // $card = '64FA32000D';
        // $card = '92FC820003';

        $nextScreen = (empty($_POST['nextScreen'])) ? user\User::getFirstScreen() : dbHelper\DbHelper::mysqlStr($_POST['nextScreen']);
        $card = (empty($_POST['values']['card'])) ? 0 : dbHelper\DbHelper::mysqlStr($_POST['values']['card']);

        try {
            // получаем список услуг
            $servicesList = proffit\Proffit::getBalance($card);
        } catch (\Exception $e) {
            // уходим на первый экран
            $_POST['nextScreen'] = ERROR_SCREEN;
            $_POST['values']['type'] = 'proffit';
            $_POST['values']['message'] = 'Ошибка запроса баланса: '.$e->getMessage();
            $this->actionWriteLog();
            exit();
        }

        $replArray = $this->makeReplaceArray($nextScreen);

        // получаем текущие авансы
        $query = "/*".__FILE__.':'.__LINE__."*/ ".
            "SELECT p.amount 
            from cards c
                join prepayments p on c.id = p.id_card
            where c.card = '$card'";
        $row = dbHelper\DbHelper::selectRow($query);
        $replArray['patterns'][] = '{PREPAYMENT}';
        $prepayment = empty($row['amount']) ? 0 : $row['amount'];
        $replArray['values'][] = $prepayment;

        $rows = '';
        foreach ($servicesList as $service) {
            $minSumm = $service['purchaseAmount'] - $prepayment;
            $rows .= "<tr>
                    <td>{$service['name']}</td>
                    <td class='text-center'>{$service['balance']}</td>
                    <td class='text-center'>{$service['dtFinish']}</td>
                    <td class='text-center'>$minSumm</td>
                    <td class='text-center'>{$service['price']}</td>
                    <td class='text-center'>
                        <input class='nextScreen' type='hidden' value='4' />
                        <input class='activity' type='hidden' value='getMoneyScreen' />
                        <input class='value idAbonement' type='hidden' value='{$service['id']}' />
                        <input class='value price' type='hidden' value='{$service['price']}' />
                        <input class='value card' type='hidden' value='$card' />
                        <input class='value customer' type='hidden' value='{$service['customer']}' />
                        <input class='value serviceName' type='hidden' value='{$service['name']}' />
                        <input class='value purchaseAmount' type='hidden' value='{$service['purchaseAmount']}' />
                        <input class='value prepayment' type='hidden' value='$prepayment' />
                        <a class='btn btn-primary action small'>Пополнить</a>
                    </td>
                </tr>";
        }

        // добавляем список сервисов
        $replArray['patterns'][] = '{SERVICES_LIST}';
        $replArray['values'][] = $rows;

        $response = $this->getScreen($nextScreen, $replArray);

        $response['servicesList'] = $servicesList;
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

        $replArray = $this->makeReplaceArray($nextScreen);
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
        $uid = user\User::getId();

        $query = "/*".__FILE__.':'.__LINE__."*/ "."CALL hws_status_write($uid, '$type', $isError, '$message')";
        $row = dbHelper\DbHelper::call($query);

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

        // вцыполнение проверок
        $response['check']['hw'] = (empty($xml->$idScreen->check->hw)) ? '0' : '1';
        // проверяем проффит
        if (!empty($xml->$idScreen->check->proffit)) {
            if (!proffit\Proffit::checkConnection()) {
                $query = '/*'.__FILE__.':'.__LINE__.'*/ '.
                    "call hws_status_write(".user\User::getId().", 'proffit', 1, 'Нет связи с сервером')";
                $row = dbHelper\DbHelper::call($query);

                $_POST['nextScreen'] = ERROR_SCREEN;
                $this->actionMove();
                exit;
            } else {
                $query = '/*'.__FILE__.':'.__LINE__.'*/ '.
                    "call hws_status_write(".user\User::getId().", 'proffit', 0, 'ОК')";
                $row = dbHelper\DbHelper::call($query);
            }
        }

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
            $response['tAction'] = (empty($xml->$idScreen->timer->action)) ? 'move' : (string) $xml->$idScreen->timer->action;
            $timer['tScreen'] = $response['tScreen'];
            $timer['tTimeout'] = $response['tTimeout'];
            $timer['tAction'] = $response['tAction'];
        }

        // печатная форма
        if (!empty($xml->$idScreen->print->top)) {
            $query = '/*'.__FILE__.':'.__LINE__.'*/ '.
                "SELECT s.html from screens s where s.id = '{$xml->$idScreen->print->top}'";
            $row = dbHelper\DbHelper::selectRow($query);

            $printForm = stripslashes($row['html']);
            $printForm = str_replace($replArray['patterns'], $replArray['values'], $printForm);
            $printForm = preg_replace('/({.*?})/ui', '', $printForm);
            $response['printForm']['top'] = $printForm;
        }
        if (!empty($xml->$idScreen->print->bottom)) {
            $query = '/*'.__FILE__.':'.__LINE__.'*/ '.
                "SELECT s.html from screens s where s.id = '{$xml->$idScreen->print->bottom}'";
            $row = dbHelper\DbHelper::selectRow($query);

            $printForm = stripslashes($row['html']);
            $printForm = str_replace($replArray['patterns'], $replArray['values'], $printForm);
            $printForm = preg_replace('/({.*?})/ui', '', $printForm);
            $response['printForm']['bottom'] = $printForm;
        }
        if (!empty($xml->$idScreen->print->elements)) {
            $query = '/*'.__FILE__.':'.__LINE__.'*/ '.
                "SELECT s.html from screens s where s.id = '{$xml->$idScreen->print->elements}'";
            $row = dbHelper\DbHelper::selectRow($query);

            $printForm = stripslashes($row['html']);
            $printForm = str_replace($replArray['patterns'], $replArray['values'], $printForm);
            $printForm = preg_replace('/({.*?})/ui', '', $printForm);
            $response['printForm']['elements'] = $printForm;
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
