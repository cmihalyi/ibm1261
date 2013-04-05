<?php


//email of IBM contact to copy, if user requests:
$copyEmail = $email_data->email_content->cc_email;
$toEmail = $email_data->form->inputs[3]->value;


//echo json_decode($_POST['data']);


$reference_number = $pdf->makePin(6);
$timestamp = time();
$reference_number .= "_" . $timestamp;

mkdir("./output/".$reference_number);
chdir('./output/'.$reference_number);
$pdf->Output("IBM_Platform_Symphony.pdf", "F");



chdir('../../');
//$text = 'Text version of email … '. $user_country;
//$text =  $HTTP_RAW_POST_DATA;
$html = $email_data->email_content->user_body[0] . "<br />";
$html .= $email_data->email_content->user_body[1] . "<br />";
$html .= $email_data->email_content->user_body[2] . "<br />";
$html .= "<a href='http://www.ibmcmostudy.com/1261/create_pdf/output/$reference_number/IBM_Platform_Symphony.pdf'>" . $email_data->email_content->user_body[3] . "</a></body></html>";



$file = './output/' . $reference_number .'/IBM_Platform_Symphony.pdf';
$crlf = "\n";
$hdrs = array(
			  'From'    => $email_data->email_content->header_from,
			  'Subject' => $email_data->email_content->header_subject
			  );

$mime = new Mail_mime(array('eol' => $crlf));

$mime->_build_params['html_charset'] = 'UTF-8';


//if($email_data->form->inputs[3]->value == "on") $mime->addCc($copyEmail);
//$mime->setTXTBody($text);
//$mime->addAttachment($file, 'application/pdf');

$mime->setHTMLBody($html);
$body = $mime->get();
$hdrs = $mime->headers($hdrs);
$mail =& Mail::factory('mail');
$mail->send($toEmail, $hdrs, $body);

if($email_data->form->inputs[4]->value == true){

}


//send separate e-mail to IBM contact if customer wants that:
if($email_data->form->inputs[4]->value == true){
    $html = $email_data->email_content->rep_body[0] . "<br />";
    $html .= $email_data->email_content->rep_body[1] . "<br /><br />";
	$html .= $email_data->email_content->rep_body[2] . "<br />";
	$html .= $email_data->form->inputs[0]->label . ":  " . $email_data->form->inputs[0]->value . "<br />";
	$html .= $email_data->form->inputs[1]->label . ":  " . $email_data->form->inputs[1]->value . "<br />";
	$html .= $email_data->form->inputs[2]->label . ":  " . $email_data->form->inputs[2]->value . "<br />";
	$html .= $email_data->form->inputs[3]->label . ":  " . $email_data->form->inputs[3]->value . "<br /><br />";
	$html .= "<a href='http://www.ibmcmostudy.com/1261/create_pdf/output/$reference_number/IBM_Platform_Symphony.pdf'>" . $email_data->email_content->rep_body[3] . "</a>";

	$mime = new Mail_mime(array('eol' => $crlf));
	$mime->_build_params['html_charset'] = 'UTF-8';
	//$mime->setTXTBody($text);
	$mime->setHTMLBody($html);
	$body = $mime->get();
	$hdrs = $mime->headers($hdrs);
	$mail =& Mail::factory('mail');
	$mail->send($copyEmail, $hdrs, $body);
}