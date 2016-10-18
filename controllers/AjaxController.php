<?php

namespace controllers\AjaxController;

use components\DbHelper as dbHelper;
use components\User as user;
use components\Proffit as proffit;

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
        $uid = user\User::getId();

        if (!$idAbonement || !$amount || !$card || !$price) {
            // уходим на первый экран
            $_POST['nextScreen'] = user\User::getFirstScreen();
            $this->actionMove($e->getMessage());
            exit();
        }

        $countUnits = ceil($amount / $price);

        // записываем запрос на оплату
        $query = '/*'.__FILE__.':'.__LINE__.'*/ '.
            "SELECT payments_prepare($uid, '$idAbonement', '$card', '$amount', $countUnits) idPayment;";
        $pay = dbHelper\DbHelper::selectRow($query);
        $idPayment = $pay['idPayment'];

        // пишем платеж в проффит
        try {
            // получаем список услуг
            $servicesList = proffit\Proffit::pay($card, $idAbonement, $amount, $countUnits);
        } catch (\Exception $e) {
            // уходим на первый экран
            $_POST['nextScreen'] = user\User::getFirstScreen();
            $this->actionMove($e->getMessage());
            exit();
        }

        // подтверждаем оплату
        $query = '/*'.__FILE__.':'.__LINE__.'*/ '.
            "SELECT payments_confirm($uid, $idPayment)";
        $pay = dbHelper\DbHelper::selectRow($query);

        $replArray = $this->makeReplaceArray($nextScreen);
        // добавляем список сервисов
        $replArray['patterns'][] = '{ABONEMENT_ID}';
        $replArray['values'][] = $idAbonement;

        $response = $this->getScreen($nextScreen, $replArray);

        $response['message'] = '';
        $response['code'] = 0;
        
        //отправляем результат
        echo json_encode($response);
    }

    ///////////////////////////////////////////////////////////////////////////////////////////////////
    /**
     * Обработка команды получения эрана приема денег
     */
    public function actionGetMoneyScreen()
    {
        $nextScreen = (empty($_POST['nextScreen'])) ? user\User::getFirstScreen() : dbHelper\DbHelper::mysqlStr($_POST['nextScreen']);
        $idAbonement = (empty($_POST['values']['idAbonement'])) ? 0 : dbHelper\DbHelper::mysqlStr($_POST['values']['idAbonement']);
        $card = (empty($_POST['values']['card'])) ? 0 : dbHelper\DbHelper::mysqlStr($_POST['values']['card']);
        $price = (empty($_POST['values']['price'])) ? 0 : dbHelper\DbHelper::mysqlStr($_POST['values']['price']);

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

        $response = $this->getScreen($nextScreen, $replArray);

        $response['message'] = '';
        $response['code'] = 0;
        
        //отправляем результат
        echo json_encode($response);
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
            $_POST['nextScreen'] = user\User::getFirstScreen();
            $this->actionMove($e->getMessage());
            exit();
        }

        $rows = '';
        foreach ($servicesList as $service) {
            $rows .= "<tr>
                    <td>{$service['name']}</td>
                    <td class='text-center'>{$service['balance']}</td>
                    <td class='text-center'>{$service['dtFinish']}</td>
                    <!-- td class='text-center'>{$service['purchaseAmount']}</td>
                    <td class='text-center'>{$service['price']}</td-->
                    <td class='text-center'>
                        <input class='nextScreen' type='hidden' value='4' />
                        <input class='activity' type='hidden' value='getMoneyScreen' />
                        <input class='value idAbonement' type='hidden' value='{$service['id']}' />
                        <input class='value price' type='hidden' value='{$service['price']}' />
                        <input class='value card' type='hidden' value='$card' />
                        <a class='btn btn-primary action small'>Пополнить</a>
                    </td>
                </tr>";
        }

        $replArray = $this->makeReplaceArray($nextScreen);
        // добавляем список сервисов
        $replArray['patterns'][] = '{SERVICES_LIST}';
        $replArray['values'][] = $rows;

        $response = $this->getScreen($nextScreen, $replArray);

        $response['servicesList'] = $servicesList;
        $response['message'] = '';
        $response['code'] = 0;
        
        //отправляем результат
        echo json_encode($response);
    }

    ///////////////////////////////////////////////////////////////////////////////////////////////////
    /**
     * Обработка команды перехода
     */
    public function actionMove($message = '')
    {
        $nextScreen = (empty($_POST['nextScreen'])) ? user\User::getFirstScreen() : dbHelper\DbHelper::mysqlStr($_POST['nextScreen']);

        $replArray = $this->makeReplaceArray($nextScreen);
        $response = $this->getScreen($nextScreen, $replArray);

        $response['message'] = $message;
        $response['code'] = 0;
        
        //отправляем результат
        echo json_encode($response);
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
        if (!empty($xml->$idScreen->print)) {
            $query = '/*'.__FILE__.':'.__LINE__.'*/ '.
                "SELECT s.html from screens s where s.id = '{$xml->$idScreen->print}'";
            $row = dbHelper\DbHelper::selectRow($query);
            $response['printForm'] = stripslashes($row['html']);
            $response['printForm'] = str_replace($replArray['patterns'], $replArray['values'], $response['printForm']);
            $response['printForm'] = preg_replace('/({.*?})/ui', '', $response['printForm']);
        }

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
