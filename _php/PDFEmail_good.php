<?php

header('Access-Control-Allow-Origin: *');  

ini_set('display_errors', 0);

include 'Mail.php';
include 'Mail/mime.php';


require_once('fpdf/alpha_fpdf.php');
require_once('FPDI-1.4.1/fpdi.php');

set_include_path('font');


require_once('./PDFCreator.php');

$rawdata = urldecode($_POST['data']);
$data = json_decode($rawdata);

$rawemail = urldecode($_POST['email_data']);
$email_data = json_decode($rawemail);

require_once('./IBM_1261_chart.php');

require_once('./PDFEmail_process.php');

