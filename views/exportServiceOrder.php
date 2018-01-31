<?php
    $xml = new XMLWriter();
    $xml->openMemory();
    // $xml->openURI('test.xml');
    $xml->setIndent(true); 
    $xml->setIndentString(' '); 


    $xml->startDocument('1.0' , 'utf-8');
    $xml->startElement("orders");
    $xml->startElement("order");

    $idRec = 1;
    $xml->writeElement("id", $idRec);
    $xml->writeElement("org", ORG);
    $xml->writeElement("idTerminal", $id);
    $xml->writeElement("address", $current['address']);
    $xml->writeElement("money", $current['money']);
    $xml->writeElement("problem", $message);

    if (!empty($current['devices'])) {
        $xml->startElement("devices");
        foreach ($current['devices'] as $device) {
            $xml->startElement("device");
            $xml->writeElement("dt", $device['dt']);
            $xml->writeElement("type", $device['type']);
            $xml->writeElement("isError", $device['is_error']);
            $xml->writeElement("message", $device['message']);
            $xml->endElement();
        }
        $xml->endElement();
    }

    $xml->endElement();
    $xml->endElement();
    return $xml->outputMemory(true);
?>
