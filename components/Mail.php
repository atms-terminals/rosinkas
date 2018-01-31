<?php
namespace components\Mailer;

/**
* отправка почты
*/
class Mailer
{
    /**
     * отправка письма с аттачем
     * @var $from от кого
     * @var $to кому (несколько перечисляются через запятую)
     * @var $cc копия (несколько перечисляются через запятую)
     * @var $subject тема
     * @var $text сообщение
     * @var $fileName имя файла для письма
     * @var $fileContent содержимое файла
     */
    public static function sendAttachEmail($from, $to, $cc, $subject, $text, $fileName, $fileContent)
    {
        $un = strtoupper(uniqid(time()));
        $subject = '=?UTF-8?B?'.base64_encode($subject).'?=';
        // если не заполнено поле TO, то копируем из CC
        if (!$to) {
            $to = $cc;
            $cc = '';
        }

        $head = "From: $from\n";
        $head .= "To: $to\n";
        $head .= "Subject: $subject\n";
        $head .= "X-Mailer: servicedesc\n";
        $head .= "Cc: $cc\n";
        $head .= "Reply-To: $from\n";
        $head .= "Mime-Version: 1.0\n";
        $head .= "Content-Type:multipart/mixed;";
        $head .= "boundary=\"----------".$un."\"\n\n";
        $zag  = "------------".$un."\nContent-Type:text/html; charset=UTF-8\n";
        $zag .= "Content-Transfer-Encoding: 8bit\n\n$text\n\n";
        $zag .= "------------".$un."\n";
        $zag .= "Content-Type: application/octet-stream;";
        $zag .= "name=\"".basename($fileName)."\"\n";
        $zag .= "Content-Transfer-Encoding:base64\n";
        $zag .= "Content-Disposition:attachment;";
        $zag .= "filename=\"".basename($fileName)."\"\n\n";
        $zag .= chunk_split(base64_encode($fileContent))."\n";
        $zag .= "------------".$un."\n";

        return mail($to, $subject, $zag, $head) ? 'success' : 'fail';
    }

    /**
     * отправка письма без аттача
     * @var $from от кого
     * @var $to кому (несколько перечисляются через запятую)
     * @var $cc копия (несколько перечисляются через запятую)
     * @var $subject тема
     * @var $text сообщение
     */
    public static function sendEmail($from, $to, $cc, $subject, $text)
    {
        $subject = '=?UTF-8?B?'.base64_encode($subject).'?=';
        // если не заполнено поле TO, то копируем из CC
        if (!$to) {
            $to = $cc;
            $cc = '';
        }

        /* Для отправки HTML-почты вы можете установить шапку Content-type. */
        $headers= "MIME-Version: 1.0\r\n";
        $headers .= "Content-type: text/html; charset=UTF-8\r\n";
        /* дополнительные шапки */
        $headers .= "From: $from\r\n";
        $headers .= "Reply-To: $from\r\n";
        $headers .= "Cc: $cc\r\n";
        /* и теперь отправим из */
        return mail($to, $subject, $text, $headers) ? 'success' : 'fail';
    }

}
