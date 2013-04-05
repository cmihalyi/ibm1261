<?php


$pdf = new PDF("P", "mm", "Letter");

//load data for download version.  This will change:





//Add fonts:

//Lubalin (western languages only)
$pdf->AddFont('Lubalin','','LTe50027.ttf', true);
$pdf->AddFont('LubalinItalic','','LTe50028.ttf', true);
$pdf->AddFont('LubalinBold','','LTe50029.ttf',true);
$pdf->AddFont('LubalinBoldItalic','','LTe50030.ttf',true);

//Basic Arial (western languages only):
$pdf->AddFont("Arial","","arial.ttf",true);
$pdf->AddFont("ArialBold","","arialbd.ttf",true);
$pdf->AddFont("ArialItalic","","ariali.ttf",true);
$pdf->AddFont("ArialBoldItalic","","arialbi.ttf",true);

//Advanced Arial - Comment this out when using Western languages to reduce load time:

/*
$pdf->AddFont("FullArial","","ARIALUNI.ttf",true);
$pdf->AddFont("FullArialBold","","ARIALUNIBold.ttf",true);
$pdf->AddFont("FullArialItalic","","ARIALUNIItalic.ttf",true);
$pdf->AddFont("FullArialBoldItalic","","ARIALUNIBoldItalic.ttf",true);
*/

$pdf->AddFont("Helvetica","","Helvetic.TTF",true);
$pdf->AddFont("HelveticaBold","","Helvetic.TTF",true);
$pdf->AddFont("HelveticaItalic","","Helvetic.TTF",true);
$pdf->AddFont("HelveticaBoldItalic","","Helvetic.TTF",true);

//load template(s) here:
$pagecount = $pdf->setSourceFile('./templates/ibm_1261_pdf_blank.pdf');
$template1 = $pdf->importPage(1, '/MediaBox');







//PAGE 1:
AddNextPage($pdf, $data, $template1, 0);



$pdf->SetFontForTFPDF("Helvetica", "", 12, 0, 0, 0);
$textY = 45;

foreach($data->section[0]->paragraphs as $para) 
{
	$pdf->PlaceAreaText(12, $textY,195, $para, false, 6);
	$textY = $pdf->GetY() + 12;
}


//PAGE 2:
AddNextPage($pdf, $data, $template1, 1);
$nY = 45;
foreach($data->section[1]->tables->calculator as $table)
{
	$pdf->SetFontForTFPDF("Arial", "B", 14, 0,0,0);
	$pdf->PlaceAreaText(12, $nY, 195, $table->header);
	$nY = $pdf->GetY() + 8;
	
	
	foreach($table->inputs as $row)
	{
	$pdf->SetFontForTFPDF("Arial", "", 12, 0,0,0);	
		$pdf->SetFillColor(242,242,242);
		$pdf->Rect(16.4, $nY, 190, 9.2, "F");
		$pdf->PlaceAreaText(19.8, $nY + 3, 190, $row->label);
		$value = $row->value;
		if(property_exists($row, "unit"))
		{
			$value = $row->unit->prepend . $value . $row->unit->append;
		}
		$pdf->PlaceAreaTextCell(160, $nY+3, 40, $value, false, 4, "R");	
		$nY += 10.5;
	}
	$nY += 10;
}

//PAGE 3:
AddNextPage($pdf, $data, $template1, 2);
$nY = 45;
$pdf->SetFontForTFPDF("Helvetica", "", 12, 0, 0, 0);
$pdf->PlaceAreaText(12, $nY, 195, $data->section[2]->paragraphs[0], false, 6);

$nY = $pdf->GetY() + 15;
$nY = MakeTable($pdf, $data->section[2]->tables->table2, $nY, 1);




//PAGE 4:
AddNextPage($pdf, $data, $template1, 2);
$nY = 45;
$pdf->SetFontForTFPDF("Helvetica", "", 12, 0, 0, 0);
$pdf->PlaceAreaText(12, $nY, 195, $data->section[2]->paragraphs[1], false, 6);

$nY = $pdf->GetY() + 15;
$nY = MakeTable($pdf, $data->section[2]->tables->table1, $nY, 1);


//PAGE 5:
AddNextPage($pdf, $data, $template1, 2);
$nY = 45;
$pdf->SetFontForTFPDF("Helvetica", "", 12, 0, 0, 0);
$pdf->PlaceAreaText(12, $nY, 195, $data->section[2]->paragraphs[2], false, 6);

$nY = $pdf->GetY() + 15;
$nY = MakeTable($pdf, $data->section[2]->tables->table3, $nY, 2);

//PAGE 6-8:
AddNextPage($pdf, $data, $template1, 3);
$nY = 45;
$pdf->SetY(45);
$pdf->overridePageBreakFlag = true;
$pdf->overridePageBreakNextLine = 45;
$pdf->SetTopMargin(45);
$pdf->SetFontForTFPDF("Helvetica", "", 12, 0, 0, 0);

$lineSpacing = 6.5;
$lineBreakY = 240;
$fontSize = 14;

foreach($data->section[3]->section as $sav)
{
	if($nY > $lineBreakY)
	{
		AddNextPage($pdf, $data, $template1, 3);
		$nY = 45;		
	}
	$pdf->SetFontForTFPDF("Arial", "B", $fontSize, 0, 0, 0);	
	$pdf->PlaceAreaText(12, $nY, 195, $sav->head, false, $lineSpacing);
	$nY += 10;
	
	if(property_exists($sav, 'list_items'))
	{
		$pdf->SetFontForTFPDF("Helvetica", "", $fontSize, 0, 0, 0);
		$pdf->PlaceAreaText(12, $nY, 195, $sav->paragraphs[0], false, $lineSpacing);
		$nY = $pdf->GetY() + 10;
		foreach($sav->list_items as $list)
		{
			if($nY > $lineBreakY)
			{
				AddNextPage($pdf, $data, $template1, 3);
				$nY = 45;
			}	
			$pdf->SetFontForTFPDF("Helvetica", "", $fontSize, 0, 0, 0);			
			$pdf->PlaceAreaText(18, $nY, 195, $list, true, $lineSpacing);
			$nY = $pdf->GetY() + 10;
		}
		$pdf->SetFontForTFPDF("Helvetica", "", $fontSize, 0, 0, 0);
		$pdf->PlaceAreaText(12, $nY, 195, $sav->paragraphs[1], false, $lineSpacing);
		$nY = $pdf->GetY() + 10;
	}

	else
	{
		foreach($sav->paragraphs as $para)
		{
			$pdf->SetFontForTFPDF("Helvetica", "", $fontSize, 0, 0, 0);
			$pdf->PlaceAreaText(12, $nY, 195, $para, false, $lineSpacing);
			$nY = $pdf->GetY() + 10;
			
			if($nY > $lineBreakY)
			{
				AddNextPage($pdf, $data, $template1, 3);
				$nY = 45;
			}		
		}
	}
}

//FOR page 9/Chart

AddNextPage($pdf, $data, $template1, 4);
$nY = 45;	
$pdf->SetFontForTFPDF("Helvetica", "", 14, 0, 0, 0);
$pdf->PlaceAreaText(12, $nY, 195, $data->section[4]->paragraphs[0], false, $lineSpacing);

//$pdf->PlaceAreaText(12, $nY, 195, "HELLO!");
$nY = $pdf->GetY() + 7;
MakeGraph($pdf, $data->section[4]->figure1, 21, $nY);
$nY += 80;

$pdf->SetFontForTFPDF("Helvetica", "", 12, 0, 0, 0);
$pdf->PlaceAreaText(12, $nY, 195, $data->section[4]->caption, false, $lineSpacing);
$nY = $pdf->GetY() + 7;

$pdf->SetFontForTFPDF("Arial", "B", 12, 0, 0, 0);
$pdf->PlaceAreaText(12, $nY, 195, $data->section[5]->head, false, $lineSpacing);
//$pdf->PlaceAreaText(18, $nY, 195, $data->section[5]->head, true, $lineSpacing);	
$nY = $pdf->GetY() + 10;

foreach($data->section[5]->list_items as $bullet)
{
	$pdf->SetFontForTFPDF("Helvetica", "", 14, 0, 0, 0);	
	$pdf->PlaceAreaText(18, $nY, 195, $bullet, true, $lineSpacing);
	$nY = $pdf->GetY() + 10;
}

// page 10/Disclosures

AddNextPage($pdf, $data, $template1, 6);
$nY = 45;
$pdf->SetFontForTFPDF("Helvetica", "", 10, 0, 0, 0);
foreach($data->section[6]->paragraphs as $para)
{
	$pdf->PlaceAreaText(12, $nY, 195, $para, false, 4.5);
	$nY = $pdf->GetY() + 9;
}




//sundry useful functions:
function MakeTable($pdf, $table, $nY, $type = 1)
{

	if($type == 1)
	{
		$colWidths = array(67, 32, 32, 32, 32);
		$colXs = array(12, 79, 111, 143, 175); 
	}
	if($type == 2)
	{
		$colWidths = array(75, 40, 40, 40);
		$colXs = array(12, 87, 127, 167); 	
	}
	
	$pdf->PlaceAreaTextCell(12, $nY, 195, $table->options->title, false, 4, "C");
	$nY += 10;

	$pdf->SetFillColor(242,242,242);
	$pdf->Rect(12, $nY, 195, 9.8, "F");



	$pdf->SetFontForTFPDF("Arial", "B", 12, 0, 0, 0);
	for($n = 0; $n < count($table->data->cols); $n++) 
	{
		$pdf->PlaceAreaTextCell($colXs[$n] + (($n == 0) ? 4 : 0), $nY + 3, $colWidths[$n], $table->data->cols[$n]->label, false, 4, ($n == 0) ? "L" : "C");
	}
	$nY += 10;
	$rows = $table->data->rows;
	$pdf->SetFontForTFPDF("Arial", "", 10, 0, 0, 0);
	for($r = 0; $r < count($rows) - (($type == 1) ? 1 : 0); $r++)
	{
		for($col = 0; $col < count($rows[$r]->c); $col++)
		{
			$pdf->PlaceAreaTextCell($colXs[$col] + (($col == 0) ? 4 : 0), $nY + 3, $colWidths[$col], $rows[$r]->c[$col]->v , false, 4, ($col == 0) ? "L" : "C");
		}
		$nY += 10;
		if($type == 1 || $r < count($rows) - 1) $pdf->Line(12,$nY,207,$nY);
	}

	if($type == 1)
	{
		$r = count($rows) - 1;
		$pdf->SetFontForTFPDF("Arial", "B", 12, 0, 0, 0);
		for($col = 0; $col < count($rows[$r]->c); $col++)
		{
			$pdf->PlaceAreaTextCell($colXs[$col] + (($col == 0) ? 4 : 0), $nY + 3, $colWidths[$col], $rows[$r]->c[$col]->v , false, 4, ($col == 0) ? "L" : "C");
		}
		$nY += 10;
	}
	return $nY;
}


function AddNextPage($pdf, $data, $template, $sectionNum, $addPage = true){
	if($addPage) $pdf->addPage();
	$pdf->useTemplate($template, 0, 0, 215.9);

	//white title font:	
	$pdf->SetFontForTFPDF("Lubalin", "", 18, 255,255,255);

	//header and subheader:
	$pdf->PlaceAreaText(44,11,200, $data->head);

	$pdf->SetFontForTFPDF("Lubalin", "", 10, 255,255,255);
	$pdf->PlaceAreaText(44,17.5,200, $data->subhead);
	
	$pdf->SetFontForTFPDF("Arial", "B", 17, 0, 138, 191);
	$pdf->PlaceAreaText(11,33, 200, $data->section[$sectionNum]->head);
}


function MakeGraph($pdf, $figure, $baseX=21, $baseY=66)
{
	$pdf->SetDrawColor(0,0,0);
	//$pdf->Line($baseX + 17, $baseY + 16, $baseX + 17, $baseY + 68);
	$pdf->Line($baseX + 17, $baseY + 68, $baseX + 137, $baseY + 68);

	$pdf->SetDrawColor(50,50,50);	
	for($n = 0; $n < 4; $n++)
	{
		$pdf->Line($baseX + 17, $baseY + 16 + $n*13, $baseX + 137, $baseY + 16 + $n*13);
	}
	
	//55,69
	$pdf->SetFontForTFPDF("Arial", "B", 12, 0, 0, 0);
	$pdf->PlaceAreaText($baseX + 25, $baseY + 5, 200, $figure->options->title);
	$max = max($figure->data[1][1], $figure->data[2][1], $figure->data[3][1]);
	$min = min($figure->data[1][1], $figure->data[2][1], $figure->data[3][1]);
	
	$sep = max(floor(($max - $min)/4), 0.5);
	$low = 0;
	$high = 0;
	
	do
	{
		$sep++;	
		$low = floor($min/$sep) * $sep;
		$high = $low + $sep * 4;
	}
	while ($high < $max);
	
	$pdf->SetFontForTFPDF("Arial", "B", 12, 0, 0, 0);
	$pdf->PlaceAreaText($baseX + 5.5, $baseY + 66, 200, $low);
	for($n = 0; $n < 4; $n++) $pdf->PlaceAreaText($baseX + 5.5, $baseY + 14 + $n*13, 200, $high - $sep*$n);

//	$pdf->PlaceAreaTextCell(160, $nY+3, 40, "\$" . $row->value, false, 4, "R");		
	
	$pdf->PlaceAreaTextCell(18+$baseX, 73+$baseY, 40, $figure->data[1][0], false, 4, "C");
	$pdf->PlaceAreaTextCell(58+$baseX, 73+$baseY, 40, $figure->data[2][0], false, 4, "C");
	$pdf->PlaceAreaTextCell(98+$baseX, 73+$baseY, 40, $figure->data[3][0], false, 4, "C");
	
	$pdf->SetDrawColor(51,102,204);
	$pdf->SetLineWidth(0.5);
	$pdf->Line($baseX + 38, $baseY + 68 -($figure->data[1][1] - $low)/($sep*4) * 52, $baseX + 78, $baseY + 68 -($figure->data[2][1] - $low)/($sep*4) * 52);
	$pdf->Line($baseX + 78, $baseY + 68 -($figure->data[2][1] - $low)/($sep*4) * 52, $baseX + 118, $baseY + 68 -($figure->data[3][1] - $low)/($sep*4) * 52);
	$pdf->SetFillColor(51,102,204);
	$pdf->Rect($baseX + 142, $baseY + 16, 3, 3, "F");
	$pdf->PlaceAreaText($baseX + 147, $baseY + 16, 200, "Savings");
	
}


