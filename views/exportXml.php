<?php
    $docId = "55".date('dmy').'t';

    $xml = new XMLWriter();
    $xml->openMemory();
    // $xml->openURI('test.xml');
    $xml->setIndent(true); 
    $xml->setIndentString(' '); 


    $xml->startDocument('1.0' , 'Windows-1251');
    $xml->startElement("Document");
        $xml->writeAttribute('DocType', 'ROI_DepositList');
        $xml->writeAttribute('DocVer', '1.0');
        $xml->writeAttribute('DocID', $docId);
        $xml->writeAttribute('CreateDate', date('d.m.Y'));
        $xml->writeAttribute('CreateTime', date('H:i:s'));
        $xml->writeAttribute('ProcessDate', $dt);
        $xml->writeAttribute('Sender', '55001_terminals');
        $xml->writeAttribute('SenderName', 'Омское областное управление инкассации - филиал Российского объединения инкассации (РОСИНКАС)');
        $xml->writeAttribute('Recipient', "РНКО &quot;Р-ИНКАС&quot;(ООО)");
        $xml->writeAttribute('RecipientName', '"РНКО &quot;Р-ИНКАС&quot;(ООО)"');

    $idDeposit = 1;
    if (!empty($operations)) {
        foreach ($operations as $oper) {
            $am = number_format($oper['amount'], 0, '.' , '');
            $xml->startElement("Deposit");
            $xml->writeAttribute('DepositID', sprintf('%06d', $idDeposit++));
            $xml->writeAttribute('BagN', '-');
            $xml->writeAttribute('CardN', $oper['card']);
            $xml->writeAttribute('Name', $oper['org']);
            $xml->writeAttribute('INN', '');
            $xml->writeAttribute('KPP', '');
            $xml->writeAttribute('OKATO', '');
            $xml->writeAttribute('BankName', '');
            $xml->writeAttribute('BIK', '');
            $xml->writeAttribute('Account', '');
            $xml->writeAttribute('Currency', '810');
            $xml->writeAttribute('Value', $am);
            $xml->writeAttribute('MEMO', "Перечисление обработанной денежной наличности проинкассированной $dt года, НДС не облагается.");
            $xml->writeAttribute('Target', '');

                $xml->startElement("KS");
                $xml->writeAttribute('KSSimbol', '02');
                $xml->writeAttribute('KSValue', $am);
                $xml->endElement();

            $xml->endElement();
        }
    }

    $xml->endElement();

    // $xml->flush();
    // echo htmlentities($xml->outputMemory(true));

    return $xml->outputMemory(true);
?>
