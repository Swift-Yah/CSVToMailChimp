<?php
include_once 'src/CSVReader.php';
include_once 'src/MailChimpSubscriber.php';
include_once 'src/ConsoleLog.php';

$log = new ConsoleLog();
$csvReader = new CSVReader('docs/emails.csv');
$mailChimpSender = new MailChimpSubscriber('<api_key>', '<list_id>', $log);
$emails = $csvReader->read();

$mailChimpSender->send($emails);
