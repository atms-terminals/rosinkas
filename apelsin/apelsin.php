<h1>Апельсин</h1>
<?php
ini_set("display_errors", "1");
ini_set("error_reporting", E_ALL);

$url = "https://xml.apelsintm.ru/xml/";
$port = 463;
$point = 8515; // Номер точки
$agent = 32; // Номер агента
$login = 'xml_test';
$password = 'xml_test';
// Пароль сертификата: 12345 

$service = '2'; // билайн
$phone = '9039831188';

$checkPhone = "<?xml version=\"1.0\" encoding=\"windows-1251\"?>
<request>
    <login>$login</login>
    <point>$point</point>
    <dealer>$agent</dealer>
    <soft>terminal</soft>
    <version>5.0.5.1</version>
    <type>verify_pay</type>
    <pay_params>
        <service>$service</service>
        <acc>$phone</acc>
    </pay_params>
</request>";

$amount = 50;
$date = '28.09.2017';
$time = '11:00:00';
$idPay = 125;
$nBill = '0123456789';
$sign = getSign($idPay, $service, $phone, $nBill, $amount, $date, $time, 'test_xml.p12', $pass = '12345');

$pay = "<?xml version=\"1.0\" encoding=\"windows-1251\"?>
<request>
    <login>$login</login>
    <point>$point</point>
    <dealer>$agent</dealer>
    <soft>terminal</soft>
    <version>5.0.5.0</version>
    <type>pay</type>
    <pay_params>
        <service>$service</service>
        <acc>$phone</acc>
        <check>$nBill</check>
        <id>$idPay</id>
        <amount>$amount</amount>
        <date>$date</date>
        <time>$time</time>
        <sign>$sign</sign>
    </pay_params>
</request>";

$xml = iconv('UTF-8', 'windows-1251', $pay);

$headers = array(
    "Content-type: text/xml",
    "Content-length: ".strlen($xml),
    "Connection: close",
);

var_dump($xml);

// $res = curl -E ./file.crt.pem --key ./file.key.pem https://myservice.com/service?wsdl

$ch = curl_init();
curl_setopt($ch, CURLOPT_URL, $url);
curl_setopt($ch, CURLOPT_PORT, $port);
curl_setopt($ch, CURLOPT_POST, true);
curl_setopt($ch, CURLOPT_POSTFIELDS, $xml);
curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
// curl_setopt($process, CURLOPT_HEADER, 1);
// http auth
curl_setopt($ch, CURLOPT_USERPWD, "$login:$password");
// ssl options
curl_setopt($ch, CURLOPT_SSLCERT, 'test_xml.p12');
curl_setopt($ch, CURLOPT_SSLCERTPASSWD, '12345');
curl_setopt($ch, CURLOPT_SSLCERTTYPE, "P12");
curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
// receive server response ...
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

$server_output = curl_exec($ch);

curl_close($ch);

var_dump($server_output);

function getSign($id, $service, $acc, $check, $amount, $date, $time, $key_file, $pass = '')
{
    $fp = fopen($key_file, "r");
    $priv_key = fread($fp, 8192);
    fclose($fp);

    $certs = array();
    $res = openssl_pkcs12_read($priv_key, $certs, $pass);
    // $key = openssl_get_privatekey($priv_key, $pass);
    $data = $id.$service.$acc.$check.$amount.$date.$time;
    openssl_sign($data, $signature, $certs['pkey'], OPENSSL_ALGO_MD5);
    // openssl_free_key($certs['pkey']);
    $signature = base64_encode($signature);
    return $signature;
}
