<?php

$xml = new XMLWriter();

$xml->openURI("php://output");
$xml->startDocument();
$xml->setIndent(true);

$xml->startElement('Response');

$xml->startElement('Say');
$xml->writeAttribute('voice', 'alice');
$xml->writeRaw($_GET['intro']);
$xml->endElement();

$items = explode("_",$_GET['text']);
$fh = fopen('pimp2.log', 'a+');
	if ( $fh )
	{
		fwrite($fh, print_r($items, true));
		fclose($fh);
	}
$xml->startElement('Pause');
$xml->writeAttribute('length', '0.5');
$xml->endElement();

$xml->startElement('Say');
$xml->writeAttribute('voice', 'alice');
$xml->writeRaw($items[0]);
$xml->endElement();

$xml->startElement('Pause');
$xml->writeAttribute('length', '0.5');
$xml->endElement();

$xml->startElement('Say');
$xml->writeAttribute('voice', 'alice');
$xml->writeRaw($items[1]);
$xml->endElement();

$xml->startElement('Pause');
$xml->writeAttribute('length', '0.2');
$xml->endElement();

$xml->startElement('Say');
$xml->writeAttribute('voice', 'alice');
$xml->writeRaw("After the beep, Say Seats to get seating info, or parking to get parking info");
$xml->endElement();


$xml->startElement('Record');
$xml->writeAttribute('action', "processSpeech.php?seat=".urlencode($_GET['seaturl'])."&park=".urlencode($_GET['parkurl']));
$xml->endElement();

$xml->endElement();

?>