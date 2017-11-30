<?php
namespace controllers\AjaxController;

use components\DbHelper as dbHelper;
use components\User as user;

define('FIRST_SCREEN', 1);
define('FIRST_ACTION', 'getServiceList');
define('GET_MONEY_SCREEN', 2);
define('GET_MONEY_ACTION', 'getMoneyScreen');
define('ERROR_SCREEN', 7);
define('LOCK_SCREEN', 12);
define('NO_CARD_SCREEN', 13);
define('NO_SERVICES_SCREEN', 14);
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

    private function getServiceNamePart($idService, $i = 2)
    {
        $query = "/*".__FILE__.':'.__LINE__."*/ ".
            "SELECT r.id_parent, r.`desc`
            from v_clients_custom_pricelist r
            where r.id = '$idService'";
        $row = dbHelper\DbHelper::selectRow($query);

        if ($row['id_parent'] > 0 && $i > 0) {
            $k = $i - 1;
            return "{$this->getServiceNamePart($row['id_parent'], $k)}. {$row['desc']}";
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

    ///////////////////////////////////////////////////////////////////////////////////////////////////
    /**
     * Обработка команды оплаты
     */
    public function actionPay()
    {
        $nextScreen = (empty($_POST['nextScreen'])) ? user\User::getFirstScreen() : dbHelper\DbHelper::mysqlStr($_POST['nextScreen']);
        $idService = (empty($_POST['values']['idService'])) ? 0 : dbHelper\DbHelper::mysqlStr($_POST['values']['idService']);
        $amount = (empty($_POST['values']['amount'])) ? 0 : dbHelper\DbHelper::mysqlStr($_POST['values']['amount']);

        $uid = user\User::getId();

        if (!$amount) {
            // уходим на первый экран
            $_POST['nextScreen'] = user\User::getFirstScreen();
            $this->actionGetServiceList();
            exit();
        }

        $replArray = $this->makeReplaceArray($nextScreen);
       
        // подтверждаем оплату
        $query = '/*'.__FILE__.':'.__LINE__.'*/ '.
            "SELECT payments_add($uid, '$idService', '$amount') id";
        $pay = dbHelper\DbHelper::selectRow($query);
        $replArray['patterns'][] = '{TRN}';
        $replArray['values'][] = $pay['id'];

        $servParam = $this->getServiceName($idService);

        $replArray['patterns'][] = '{NDS}';
        $replArray['values'][] = $servParam['nds'];
        // if ($servParam['nds'] == 1 && $servParam['price'] <= $amount) {
        //     $nds = number_format($servParam['price'] / 118 * 18, 2, '.', ' ');
        //     $replArray['values'][] = "В том числе НДС 18% $nds";
        // } elseif ($servParam['nds'] == 0) {
        //     $replArray['values'][] = 'Без НДС';
        // } else {
        //     $replArray['values'][] = '';
        // }

        if ($servParam['price'] > $amount) {
            // если денег меньше чем цена
            $replArray['patterns'][] = '{SERVICE}';
            $replArray['values'][] = 'Нет';

            $replArray['patterns'][] = '{PRICE}';
            $replArray['values'][] = '0';

            $rest = $amount;
            $replArray['patterns'][] = '{REST}';
            $replArray['values'][] = number_format($rest, 2, '.', '');
        } else {
            // если есть сдача
            $replArray['patterns'][] = '{SERVICE}';
            $replArray['values'][] = $servParam['name'];

            $replArray['patterns'][] = '{PRICE}';
            $replArray['values'][] = number_format($servParam['price'], 2, '.', '');

            $rest = $amount - $servParam['price'];
            $rest = $rest < 0 ? 0 : $rest;
            $replArray['patterns'][] = '{REST}';
            $replArray['values'][] = number_format($rest, 2, '.', '');
        }

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
        $idService = (empty($_POST['values']['idService'])) ? 0 : dbHelper\DbHelper::mysqlStr($_POST['values']['idService']);

        if (!$idService) {
            // уходим на первый экран
            $_POST['nextScreen'] = user\User::getFirstScreen();
            $this->actionMove($e->getMessage());
            exit();
        }

        $replArray = $this->makeReplaceArray($nextScreen);
        $this->putPostIntoReplaceArray($replArray);

        $servParam = $this->getServiceName($idService);
        $replArray['patterns'][] = '{SERVICE}';
        $replArray['values'][] = $servParam['name'];
        $replArray['patterns'][] = '{PRICE}';
        $replArray['values'][] = $servParam['price'];

        $response = $this->getScreen($nextScreen, $replArray);

        $response['message'] = '';
        $response['code'] = 0;
        
        //отправляем результат
        echo json_encode($response);
        return true;
    }

    ///////////////////////////////////////////////////////////////////////////////////////////////////
    /**
     * Обработка команды получения новых услуг
     */
    public function actionGetServiceList()
    {
        define('BUTTON_PER_SCREEN', 6);

        if (user\User::getStatus() == 0) {
            $_POST['nextScreen'] = LOCK_SCREEN;
        }

        $nextScreen = (empty($_POST['nextScreen'])) ? user\User::getFirstScreen() : dbHelper\DbHelper::mysqlStr($_POST['nextScreen']);
        $id = (empty($_POST['values']['id'])) ? 0 : dbHelper\DbHelper::mysqlStr($_POST['values']['id']);
        $start = (empty($_POST['values']['start'])) ? 0 : dbHelper\DbHelper::mysqlStr($_POST['values']['start']);

        $replArray = $this->makeReplaceArray($nextScreen);
        $this->putPostIntoReplaceArray($replArray);

        // кнопки возврата назад и на 1 уровень вверх
        $controls = '';
        $controls .= "<div class='controlDiv'>";
        if ($id) {
            if ($start) {
                $ns = $start - BUTTON_PER_SCREEN;
                $controls .= "<input class='activity' type='hidden' value='getServiceList' />
                        <input class='nextScreen' type='hidden' value='".SERVICE_LIST_SCREEN."' />
                        <input class='value id' type='hidden' value='$id' />
                        <input class='value start' type='hidden' value='$ns' />
                        <button class='btn btn-primary action service control'>Предыдущий</button>";
            } else {
                $query = "/*".__FILE__.':'.__LINE__."*/ ".
                    "SELECT p.id_parent
                    from v_clients_custom_pricelist p
                    where p.id = '$id'";
                $row = dbHelper\DbHelper::selectRow($query);

                $controls .= "<input class='activity' type='hidden' value='getServiceList' />
                        <input class='nextScreen' type='hidden' value='".SERVICE_LIST_SCREEN."' />
                        <input class='value id' type='hidden' value='{$row['id_parent']}' />
                        <button class='btn btn-primary action service control'>Предыдущий</button>";
            }
        } else {
            $controls .= "&nbsp;";
        }
        $controls .= "</div>";

        if ($id) {
            $controls .= "<div class='controlDiv'>
                    <input class='nextScreen' type='hidden' value='".FIRST_SCREEN."' />
                    <input class='activity' type='hidden' value='".FIRST_ACTION."' />
                    <button class='btn btn-primary action service control'>Отмена</button>   
                </div>";
        }

        // получаем комментарий для экрана
        $query = "/*".__FILE__.':'.__LINE__."*/ ".
            "SELECT p.comment
            FROM v_clients_custom_pricelist p
            WHERE p.id = '$id'";
        $row = dbHelper\DbHelper::selectRow($query);
        $replArray['patterns'][] = '{SCREEN_COMMENT}';
        $replArray['values'][] = $row['comment'];

        // добавляем список сервисов
        $query = "/*".__FILE__.':'.__LINE__."*/ ".
            "SELECT p.id, p.`desc`, round(p.price) price, p.color
            FROM v_clients_custom_pricelist p
            WHERE p.id_parent = '$id'
            ORDER BY p.id_parent, p.order, p.`desc`";
        $rows = dbHelper\DbHelper::selectSet($query);
        $buttons = '';

        for ($i = $start; $i < $start + BUTTON_PER_SCREEN && $i < count($rows); $i++) {
            $cost = $rows[$i]['price'] && $rows[$i]['price'] != '0.00' ? "<hr>{$rows[$i]['price']} руб." : '';
            if ($cost) {
                $cost = empty($rows[$i]['price']) || $rows[$i]['price'] == -1 ? '' : $cost;
                $buttons .= "<span>
                        <input class='nextScreen' type='hidden' value='".GET_MONEY_SCREEN."' />
                        <input class='activity' type='hidden' value='".GET_MONEY_ACTION."' />
                        <input class='value price' type='hidden' value='{$rows[$i]['price']}' />
                        <input class='value idService' type='hidden' value='{$rows[$i]['id']}' />
                        <input class='value serviceName' type='hidden' value='{$rows[$i]['desc']}' />
                        <button class='btn btn-{$rows[$i]['color']} action service'>{$rows[$i]['desc']}$cost</button>
                    </span>";
            } else {
                $buttons .= "<span>
                        <input class='activity' type='hidden' value='getServiceList' />
                        <input class='nextScreen' type='hidden' value='".SERVICE_LIST_SCREEN."' />
                        <input class='value id' type='hidden' value='{$rows[$i]['id']}' />
                        <button class='btn btn-{$rows[$i]['color']} action service'>{$rows[$i]['desc']}$cost</button>   
                    </span>";
            }
        }

        $controls .= "<div class='controlDiv'>";
        if ($start + BUTTON_PER_SCREEN < count($rows)) {
            $start += BUTTON_PER_SCREEN;
            $controls .= "<input class='activity' type='hidden' value='getServiceList' />
                    <input class='nextScreen' type='hidden' value='".SERVICE_LIST_SCREEN."' />
                    <input class='value id' type='hidden' value='$id' />
                    <input class='value start' type='hidden' value='$start' />
                    <input class='value prepayment' type='hidden' value='$prepayment' />
                    <input class='value card' type='hidden' value='$card' />
                    <input class='value customer' type='hidden' value='$customer' />
                    <button class='btn btn-primary action service control'>Следующий</button>";
        } else {
            $controls .= "&nbsp;";
        }
        $controls .= "</div>";

        $replArray['patterns'][] = '{CONTROLS_LIST}';
        $replArray['values'][] = $controls;

        $replArray['patterns'][] = '{SERVICES_LIST}';
        $replArray['values'][] = $buttons;

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
