<?php

//comment out to see echos (1), then comment below:
//use header below if experiencing AJAX difficulties:
header("Access-Control-Allow-Origin: *");

ini_set('display_errors', 0);

require_once('fpdf/alpha_fpdf.php');
require_once('FPDI-1.4.1/fpdi.php');
require_once('./PDFCreator.php');
//require_once('GChartPhp/gChart.php');

//comment out to see all echos (2):
echoOff();

set_include_path('font');


//SET TO project & language code:
require_once('./IBM_1261_chart.php');



$pdf->Output("Data Center Study Assessment Results.pdf", "D");


