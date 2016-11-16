<?php

namespace components\Proffit;

/**
 * Работа с сервером интеграции Проффит
 */
class Proffit
{
    /**
     * @var string $timeout Таймаут ответа сервера Проффит
     * @var string $url Адрес сервера
     * @var string $user Пользователь
     * @var string $password Пароль
     * @var string $termId Вид платежа - код кассы
     */
    private static $timeout = 3;
    private static $url = 'http://192.168.0.105:8000';
    // private static $url = 'http://192.168.3.71:8000';
    private static $user = 'User';
    private static $password = 'Password';
    private static $termId = 4;

    /**
     * Формирование запроса на баланс
     *
     * @param string $card Номер карты
     *
     * @return string Строка запроса;
     */
    private static function makeReqBalance($card)
    {
        $user = self::$user;
        $password = self::$password;

        return "<?xml version='1.0'?>
            <proffit code='PS' ver='1' lang='RU'>
            <user name='$user' pass='$password'/>
            <command name='GET_STATUS_CARD' CARD_CODE='$card' DAYS='900'/>
            </proffit>";
    }

    /**
     * Формирование запроса на зачисление платежа.
     *
     * @param string $card   Номер карты
     * @param string $amount Сумма
     *
     * @return string Строка запроса;
     */
    private static function makeReqPay($card, $itemId, $amount, $cnt)
    {
        $user = self::$user;
        $password = self::$password;
        $termId = self::$termId;

        return "<?xml version='1.0'?>
            <proffit code='PS' ver='1' lang='RU'>
            <user name='$user' pass='$password'/>
            <command name='ADD_PAYMENT' CARD_CODE='$card' />
            <ITEM ID='$itemId' SYMA='$amount' KASSA_ID='$termId' CNT='$cnt'/>
            </proffit>";
    }

    /**
     * Проверка соединения с Проффит
     *
     * @return bool результат проверки
     */
    public static function checkConnection()
    {
        $streamOptions = array(
            'http' => array(
               'method' => 'GET',
               'timeout' => self::$timeout,
               'header' => "Content-type: application/xml\r\n",
            ),
        );

        $context = stream_context_create($streamOptions);

        return strstr(@file_get_contents(self::$url, null, $context), "Proffit Sport") ? true : false;
    }

    /**
     * Отправка запроса в Проффит
     *
     * @param string $post строка запроса
     *
     * @return array результат запроса
     */
    private static function sendRequest($post)
    {
        $streamOptions = array(
            'http' => array(
               'method' => 'POST',
               'timeout' => self::$timeout,
               'header' => "Content-type: application/xml\r\n",
               'content' => $post,
            ),
        );

        $context = stream_context_create($streamOptions);
        if ($response = @file_get_contents(self::$url, null, $context)) {
            // convert the XML result into array
            $data = json_decode(json_encode(simplexml_load_string($response)), true);

            if (!empty($data['result']['@attributes']['code'])) {
                throw new \Exception($data['result']['@attributes']['text'], -2);
            }

            return $data;
        }
        throw new \Exception('Нет связи с сервером Проффит', -1);
    }

    /**
     * Получение списка подключенных услуг.
     *
     * @param string $card номер карты
     *
     * @return array статус карты;
     */
    public static function getBalance($card)
    {
        $raw = self::sendRequest(self::makeReqBalance($card));

        $result = array();
        $customer = (empty($raw['answer']['CLIENT']['@attributes']['NAME'])) ? '' : $raw['answer']['CLIENT']['@attributes']['NAME'];

        foreach ($raw['answer']['ITEM'] as $abon) {
            if ($abon['@attributes']['ACTIVE'] == 1) {
                $result[] = array (
                    'id' => $abon['@attributes']['ID'],
                    'name' => $abon['@attributes']['NAME'],
                    'dtFinish' => (empty($abon['@attributes']['PURCHASE_FINISH'])) ? 'Бессрочный' : $abon['@attributes']['PURCHASE_FINISH'],
                    'balance' => "{$abon['@attributes']['QTY']} {$abon['@attributes']['UNIT']}",
                    'purchaseAmount' => $abon['@attributes']['PURCHASE_SYMA'],
                    'price' => $abon['@attributes']['PURCHASE_SYMA'] / $abon['@attributes']['PURCHASE_QTY'],
                    'customer' => $customer
                    );
            }
        }
        return $result;
    }

    /**
     * Зачисление платежа.
     *
     * @param string $card номер карты
     */
    public static function pay($card, $idAbonement, $amount, $countUnits)
    {
        return self::sendRequest(self::makeReqPay($card, $idAbonement, $amount, $countUnits));
    }
}
