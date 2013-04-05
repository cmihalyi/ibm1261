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



//email of IBM contact to copy, if user requests:
$copyEmail = $email_data->email_content->cc_email;

/* form->input array:
0: Name
1: Company
2: Phone
3: e-mail
4: contact me?  "on" = yes
*/


$toEmail = $email_data->form->inputs[3]->value;


//echo json_decode($_POST['data']);


$reference_number = $pdf->makePin(6);
$timestamp = time();
$reference_number .= "_" . $timestamp;

mkdir("./output/".$reference_number);
chdir('./output/'.$reference_number);
$pdf->Output("IBM_Platform_Symphony.pdf", "F");



chdir('../../');
$text = 'Text version of email ... '. $user_country;
//$text =  $HTTP_RAW_POST_DATA;



$html = $email_data->email_content->description . "<a href='http://www.ibmcmostudy.com/1261/create_pdf/output/$reference_number/IBM_Platform_Symphony.pdf'>" . $email_data->email_content->download_text . "</a></body></html>";



$file = './output/' . $reference_number .'/IBM_Platform_Symphony.pdf';
$crlf = "\n";
$hdrs = array(
			  'From'    => $email_data->email_content->header_from,
			  'Subject' => $email_data->email_content->header_subject
			  );

$mime = new Mail_mime(array('eol' => $crlf));

$mime->_build_params['html_charset'] = 'UTF-8';


//if($email_data->form->inputs[3]->value == "on") $mime->addCc($copyEmail);

$mime->setTXTBody($text);
$mime->setHTMLBody($html);
//$mime->addAttachment($file, 'application/pdf');

$body = $mime->get();
$hdrs = $mime->headers($hdrs);

$mail =& Mail::factory('mail');

$mail->send($toEmail, $hdrs, $body);

//send separate e-mail to IBM contact:

$html = "Received submission from: <br />" . $email_data->form->inputs[0]->value . "<br />" . $email_data->form->inputs[1]->value . "<br />" . $email_data->form->inputs[2]->value . "<br />Copy sent to prospective customer:<br />" . $html;

$mime = new Mail_mime(array('eol' => $crlf));
$mime->_build_params['html_charset'] = 'UTF-8';
$mime->setTXTBody($text);
$mime->setHTMLBody($html);
$body = $mime->get();
$hdrs = $mime->headers($hdrs);
$mail =& Mail::factory('mail');
$mail->send($copyEmail, $hdrs, $body);
