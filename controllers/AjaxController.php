<?php

namespace controllers\AjaxController;

use components\DbHelper as dbHelper;
use components\User as user;

/**
 * обработка запросов ajax.
 */
class AjaxController
{
    public function actionMove()
    {
        $nextScreen = (empty($_POST['nextScreen'])) ? 0 : dbHelper\DbHelper::mysqlStr($_POST['nextScreen']);

        $replArray = $this->makeReplaceArray($nextScreen);
        $response = $this->getScreen($nextScreen, $replArray);

        $response['message'] = '';
        $response['code'] = 0;
        
        /////////////////////////////////////////////////////////////////////////////////////////////
        //отправляем результат
        echo json_encode($response);
    }

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
            $response['printForm'] = stripslashes($row[html]);
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
