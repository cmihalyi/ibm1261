<?php

ini_set('display_errors', 0);

include 'Mail.php';
include 'Mail/mime.php';


require_once('fpdf/alpha_fpdf.php');
require_once('FPDI-1.4.1/fpdi.php');

set_include_path('font');


require_once('./PDFCreator.php');
require_once('./IBM_1261_chart.php');

$toEmail = "doug.grim@somnio.com, TGiovannini@somnio.com";



$reference_number = $pdf->makePin(6);
$timestamp = time();
$reference_number .= "_" . $timestamp;

mkdir("./output/".$reference_number);
chdir('./output/'.$reference_number);
$pdf->Output("IBM_Platform_Symphony.pdf", "F");



chdir('../../');
$text = 'Text version of email ... '. $user_country;
$html = '<html><body><img src="http://www.ibmcmostudy.com/data_center_study/email_pdf/images/email_image.png" type="image/png" /><p><b>Name:</b> ' . $user_name . '<br /><b>Company: </b> ' . $user_company . '<br /><b>Country: </b> ' . $user_country . '<br /><b>Email: </b>' . $user_email . '<br /><b>Phone: </b>' . $user_phone. '<a href="http://www.ibmcmostudy.com/1261/create_pdf/output/' . $reference_number . '/IBM_Platform_Symphony.pdf">Click here to download</a></body></html>';
$file = './output/' . $reference_number .'/IBM_Platform_Symphony.pdf';
$crlf = "\n";
$hdrs = array(
			  'From'    => 'donotreply@datacenterstudy.com',
			  'Subject' => 'IBM - Data Center Study'
			  );

$mime = new Mail_mime(array('eol' => $crlf));

$mime->_build_params['html_charset'] = 'UTF-8';

$mime->addCc($user_email);

$mime->setTXTBody($text);
$mime->setHTMLBody($html);
//$mime->addAttachment($file, 'application/pdf');

$body = $mime->get();
$hdrs = $mime->headers($hdrs);

$mail =& Mail::factory('mail');
$mail->send($toEmail, $hdrs, $body);