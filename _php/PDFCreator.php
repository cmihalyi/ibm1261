<?php

$_echoFlag = true;

function eko($st)
{
	if($_echoFlag) echo $st;
}	

function echoOff()
{
	$_echoFlag = false;
}


class PDF extends FPDI
{

	public $layouts = array(array(),array(),array());
	public $currentTemplate;
	public $currentLayout;
	public $currentLangObj;
	
	public $defaultFont;

	
	private $pdfElements = array();
	private $translations = array();
	
	public $overridePageBreakFlag = false;
	public $overridePageBreakNextLine = 45;
	
	private $specialTranslationObject;
	private $currentFont;
	//private $_echoFlag = true;
	
	var $svg_gradient;	//	array - contient les infos sur les gradient fill du svg classé par id du svg
	var $svg_shadinglist;	//	array - contient les ids des objet shading
	var $gradients;		//	array - contient les infos sur les gradient fill du svg classé par ordre d'aparission
	var $color_chart;		//	array - chartre des couleur nommé pris en compte par le  svg
	var $svg_offset;		//	array - position et scaling general du svg
	var $svg_info;		//	array contenant les infos du svg voulue par l'utilisateur
	var $svg_attribs;		//	array - holds all attributes of root <svg> tag
	var $svg_style;		//	array contenant les style de groupes du svg
	var $svg_string;		//	String contenant le tracage du svg en lui même.
	var $txt_data;		//    array - holds string info to write txt to image
	var $txt_style;		// 	array - current text style	
	
	function PDF($orientation='P',$unit='mm',$format='A4')
	{
		parent::FPDI($orientation,$unit,$format);
$this->gradients = array();
		$this->svg_gradient = array();
		$this->svg_shadinglist = array();
		$this->txt_data = array();
		$this->svg_string = '';
		$this->svg_info = array();
		$this->svg_attribs = array();

		$this->svg_style = array(
			array(
			'fill'				=> 'none',			//	pas de remplissage par defaut
			'fill-opacity'		=> 1,				//	remplissage opaque par defaut
			'fill-rule'			=> 'nonzero',		//	mode de remplissage par defaut
			'stroke'			=> 'none',			//	pas de trait par defaut
			'stroke-linecap'	=> 'butt',			//	style de langle par defaut
			'stroke-linejoin'	=> 'miter',			//
			'stroke-miterlimit'	=> 4,				//	limite de langle par defaut
			'stroke-opacity'	=> 1,				//	trait opaque par defaut
			'stroke-width'		=> 0,				//	epaisseur du trait par defaut
			'transform_x'	=> 1,				// horizontal scale factor
			'transform_y'	=> 1				// vertical scale factor
			)
		);

		$this->txt_style = array(
			array(
			'fill'		=> 'black',		//	pas de remplissage par defaut
			'font-family' 	=> 'helvetica',		// 	PDF built-in families Ariel/Helvetica, Courier, Times, symbol, zapfdingbats
			'font-size'		=> '10',		// 	absolute values only, no percent or relative
			'font-weight'	=> 'normal',	//	normal | bold
			'font-style'	=> 'normal'		//	italic | normal
			)
		);


		$this->svg_offset = array(
			'xo' => 0, 		// offset x position en pt
			'yo' => 0, 		// offset y position en pt
			'xs' => 1, 		// scale sur l'axe des x
			'ys' => 1 		// scale sur l'axe des y
		);

		$this->color_chart = array(
			'aliceblue' => "#F0F8FF",
			'antiquewhite' => "#FAEBD7",
			'aqua' => "#00FFFF",
			'aquamarine' => "#7FFFD4",
			'azure' => "#F0FFFF",
			'beige' => "#F5F5DC",
			'bisque' => "#FFE4C4",
			'black' => "#000000",
			'blanchedalmond' => "#FFEBCD",
			'blue' => "#0000FF",
			'blueviolet' => "#8A2BE2",
			'brown' => "#A52A2A",
			'burlywood' => "#DEB887",
			'cadetblue' => "#5F9EA0",
			'chartreuse' => "#7FFF00",
			'chocolate' => "#D2691E",
			'coral' => "#FF7F50",
			'cornflowerblue' => "#6495ED",
			'cornsilk' => "#FFF8DC",
			'crimson' => "#DC143C",
			'cyan' => "#00FFFF",
			'darkblue' => "#00008B",
			'darkcyan' => "#008B8B",
			'darkgoldenrod' => "#B8860B",
			'darkgray' => "#A9A9A9",
			'darkgreen' => "#006400",
			'darkgrey' => "#A9A9A9",
			'darkkhaki' => "#BDB76B",
			'darkmagenta' => "#8B008B",
			'darkolivegreen' => "#556B2F",
			'darkorange' => "#FF8C00",
			'darkorchid' => "#9932CC",
			'darkred' => "#8B0000",
			'darksalmon' => "#E9967A",
			'darkseagreen' => "#8FBC8F",
			'darkslateblue' => "#483D8B",
			'darkslategray' => "#2F4F4F",
			'darkslategrey' => "#2F4F4F",
			'darkturquoise' => "#00CED1",
			'darkviolet' => "#9400D3",
			'deeppink' => "#FF1493",
			'deepskyblue' => "#00BFFF",
			'dimgray' => "#696969",
			'dimgrey' => "#696969",
			'dodgerblue' => "#1E90FF",
			'firebrick' => "#B22222",
			'floralwhite' => "#FFFAF0",
			'forestgreen' => "#228B22",
			'fuchsia' => "#FF00FF",
			'gainsboro' => "#DCDCDC",
			'ghostwhite' => "#F8F8FF",
			'gold' => "#FFD700",
			'goldenrod' => "#DAA520",
			'gray' => "#808080",
			'grey' => "#808080",
			'green' => "#008000",
			'greenyellow' => "#ADFF2F",
			'honeydew' => "#F0FFF0",
			'hotpink' => "#FF69B4",
			'indianred' => "#CD5C5C",
			'indigo' => "#4B0082",
			'ivory' => "#FFFFF0",
			'khaki' => "#F0E68C",
			'lavender' => "#E6E6FA",
			'lavenderblush' => "#FFF0F5",
			'lawngreen' => "#7CFC00",
			'lemonchiffon' => "#FFFACD",
			'lightblue' => "#ADD8E6",
			'lightcoral' => "#F08080",
			'lightcyan' => "#E0FFFF",
			'lightgoldenrodyellow' => "#FAFAD2",
			'lightgray' => "#FAFAD2",
			'lightgreen' => "#90EE90",
			'lightgrey' => "#FAFAD2",
			'lightpink' => "#FFB6C1",
			'lightsalmon' => "#FFA07A",
			'lightseagreen' => "#20B2AA",
			'lightskyblue' => "#87CEFA",
			'lightslategray' => "#778899",
			'lightslategrey' => "#778899",
			'lightsteelblue' => "#B0C4DE",
			'lightyellow' => "#FFFFE0",
			'lime' => "#00FF00",
			'limegreen' => "#32CD32",
			'linen' => "#FAF0E6",
			'magenta' => "#FF00FF",
			'maroon' => "#800000",
			'mediumaquamarine' => "#66CDAA",
			'mediumblue' => "#0000CD",
			'mediumorchid' => "#BA55D3",
			'mediumpurple' => "#9370DB",
			'mediumseagreen' => "#3CB371",
			'mediumslateblue' => "#7B68EE",
			'mediumspringgreen' => "#00FA9A",
			'mediumturquoise' => "#48D1CC",
			'mediumvioletred' => "#C71585",
			'midnightblue' => "#191970",
			'mintcream' => "#F5FFFA",
			'mistyrose' => "#FFE4E1",
			'moccasin' => "#FFE4B5",
			'navajowhite' => "#FFDEAD",
			'navy' => "#000080",
			'oldlace' => "#FDF5E6",
			'olive' => "#808000",
			'olivedrab' => "#6B8E23",
			'orange' => "#FFA500",
			'orangered' => "#FF4500",
			'orchid' => "#DA70D6",
			'palegoldenrod' => "#EEE8AA",
			'palegreen' => "#98FB98",
			'paleturquoise' => "#AFEEEE",
			'palevioletred' => "#DB7093",
			'papayawhip' => "#FFEFD5",
			'peachpuff' => "#FFDAB9",
			'peru' => "#CD853F",
			'pink' => "#FFC0CB",
			'plum' => "#DDA0DD",
			'powderblue' => "#B0E0E6",
			'purple' => "#800080",
			'red' => "#FF0000",
			'rosybrown' => "#BC8F8F",
			'royalblue' => "#4169E1",
			'saddlebrown' => "#8B4513",
			'salmon' => "#FA8072",
			'sandybrown' => "#F4A460",
			'seagreen' => "#2E8B57",
			'seashell' => "#FFF5EE",
			'sienna' => "#A0522D",
			'silver' => "#C0C0C0",
			'skyblue' => "#87CEEB",
			'slateblue' => "#6A5ACD",
			'slategray' => "#708090",
			'slategrey' => "#708090",
			'snow' => "#FFFAFA",
			'springgreen' => "#00FF7F",
			'steelblue' => "#4682B4",
			'tan' => "#D2B48C",
			'teal' => "#008080",
			'thistle' => "#D8BFD8",
			'tomato' => "#FF6347",
			'turquoise' => "#40E0D0",
			'violet' => "#EE82EE",
			'wheat' => "#F5DEB3",
			'white' => "#FFFFFF",
			'whitesmoke' => "#F5F5F5",
			'yellow' => "#FFFF00",
			'yellowgreen' => "#9ACD32"
		);		
	}
	
	
	function svgGradient($gradient_info){

		$n = count($this->gradients)+1;

		$return = "";

		if ($gradient_info['type'] == 'linear'){
			$this->gradients[$n]['type'] = 2;
			$x1 = ($gradient_info['info']['x1']*$this->svg_offset['xs']);
			$y1 = ($gradient_info['info']['y1']*$this->svg_offset['ys']);
			$x2 = ($gradient_info['info']['x2']*$this->svg_offset['xs']);
			$y2 = ($gradient_info['info']['y2']*$this->svg_offset['ys']);

			$matrix = preg_replace('/matrix\(([0-9\.\- ]*)\)/i','$1',$gradient_info['transform']);
			if ($matrix != ''){
				$tmp = split(' ',$matrix);

				$s = 1;
				if ($x1>$x2){

					$s = -1;
				}

				$a = $tmp[0];
				$b = $s*$tmp[1];
				$c = $tmp[2];
				$d = $tmp[3];
				$e = ($tmp[4]*$this->svg_offset['xs']);
				$f = ($tmp[5]*$this->svg_offset['ys']);



				$_x1 = ($a*$x1) + ($c*$y1) + $e;
				$_y1 = ($b*$x1) + ($d*$y1) + $f;

				$_x2 = ($a*$x2) + ($c*$y2) + $e;
				$_y2 = ($b*$x2) + ($d*$y2) + $f;




				$x1 = $_x1;
				$y1 = $_y1;

				$x2 = $_x2;
				$y2 = $_y2;

			}

			$y2 = $y1*2-$y2;

			$w = $x2-$x1;
			$h = $y2-$y1;

			$a = 1;
			$b = 0;
			$c = 0;
			$d = 1;
			$e = $this->svg_offset['xo']+$x1;
			$f = $this->svg_offset['yo']-$y1;
			$return .= sprintf('%.3f %.3f %.3f %.3f %.3f %.3f cm ', $a, $b, $c, $d, $e, $f);


			$this->gradients[$n]['coords']=array(
				'w'=> $w,
				'h'=> $h
			);

		}
		else if ($gradient_info['type'] == 'radial'){

			$this->gradients[$n]['type'] = 3;

			$this->gradients[$n]['coords']=array(

				'r'=> $gradient_info['info']['r']

			);



			$a = $this->svg_offset['xs'];
			$b = 0;
			$c = 0;
			$d = $this->svg_offset['ys'];
			$e = $this->svg_offset['xo']+($gradient_info['info']['x0']*$this->svg_offset['xs']);
			$f = $this->svg_offset['yo']-($gradient_info['info']['y0']);

			$return .= sprintf('%.3f %.3f %.3f %.3f %.3f %.3f cm ', $a, $b, $c, $d, $e, $f);

		}

		$this->gradients[$n]['color'] = array();

		$n_color = count($gradient_info['color']);
		for ($i = 0;$i<$n_color;$i++){

			$color = array (
				'color' => $gradient_info['color'][$i]['color'],
				'offset' => $gradient_info['color'][$i]['offset'],
				'opacity' => $gradient_info['color'][$i]['opacity']
			);

			array_push($this->gradients[$n]['color'],$color);

		}

		$return .= '/Sh'.count($this->gradients).' sh Q ';
		return $return;

	}

	function svgOffset ($attribs){
	
//echo "svgOffset called<BR>";	

		// save all <svg> tag attributes
		$this->svg_attribs = $attribs;
		
//var_dump($attribs);		
		
		$convert_mm = 2.835;

		$svg_w = preg_replace("/([0-9\.]*)(.*)/i","$1",$attribs['width']);
		$svg_h = preg_replace("/([0-9\.]*)(.*)/i","$1",$attribs['height']);
		$svg_u = preg_replace("/([0-9\.]*)(.*)/i","$2",$attribs['width']);

//echo $svg_w . "<BR>";		
//echo $svg_h . "<BR>";		
//echo $svg_u . "<BR>";		
		
		switch($svg_u){
			case 'mm':
			$convert_svg = $convert_mm;
			break;
			default:
			$convert_svg = 1;
			break;
		}

		//
		switch ($this->svg_info['scale_u']){
			case 'mm':
			switch($this->svg_info['scale_r']){
				case 'width':
				$xscale = ($this->svg_info['scale_x']*$convert_mm)/($svg_w*$convert_svg);
				$yscale = ($this->svg_info['scale_x']*$convert_mm)/($svg_w*$convert_svg);
				break;
				case 'height':
				$xscale = ($this->svg_info['scale_y']*$convert_mm)/($svg_h*$convert_svg);
				$yscale = ($this->svg_info['scale_y']*$convert_mm)/($svg_h*$convert_svg);
				break;
				default:
				$xscale = ($this->svg_info['scale_x']*$convert_mm)/($svg_w*$convert_svg);
				$yscale = ($this->svg_info['scale_y']*$convert_mm)/($svg_h*$convert_svg);
				break;
			}
			break;

			default:
			switch ($this->svg_info['scale_r']){
				case 'width':
				$xscale = $this->svg_info['scale_x']/$svg_w;
				$yscale = $this->svg_info['scale_x']/$svg_w;
				break;
				case 'height':
				$xscale = $this->svg_info['scale_y']/$svg_h;
				$yscale = $this->svg_info['scale_y']/$svg_h;
				break;
				default:
				$xscale = $this->svg_info['scale_x']/$svg_w;
				$yscale = $this->svg_info['scale_y']/$svg_h;
				break;
			}
			break;
		}

		//
		// calcul de la positon du svg
		switch ($this->svg_info['pos_u']){
			case 'mm':
			$xoffset = $this->svg_info['pos_x']*$convert_mm;
			$yoffset = $this->fhPt-($this->svg_info['pos_y']*$convert_mm );
			break;
			default:
			$xoffset = $this->svg_info['pos_x'];
			$yoffset = $this->fhPt-$this->svg_info['pos_y'];
			break;
		}

		$this->svg_offset = array(
			'xo' => $xoffset,		// offset x position en pt
			'yo' => $yoffset,		// offset y position en pt
			'xs' => $xscale,		// scale sur l'axe des x
			'ys' => $yscale		// scale sur l'axe des y
		);

	}


	//
	// check if points are within svg, if not, set to max
	function svg_overflow($x,$y)
	{
		$x2 = $x;
		$y2 = $y;
		if(isset($this->svg_attribs['overflow']))
		{
			if($this->svg_attribs['overflow'] == 'hidden')
			{
				// Not sure if this is supposed to strip off units, but since I dont use any I will omlt this step
				$svg_w = preg_replace("/([0-9\.]*)(.*)/i","$1",$this->svg_attribs['width']);
				$svg_h = preg_replace("/([0-9\.]*)(.*)/i","$1",$this->svg_attribs['height']);
				
				// $xmax = floor($this->svg_attribs['width'] * $this->svg_offset['xs'] + $this->svg_offset['xo']);
				$xmax = floor($svg_w * $this->svg_offset['xs'] + $this->svg_offset['xo']);
				$xmin = $this->svg_offset['xo'];
				// $ymax = floor(($this->svg_attribs['height'] * -1) * ($this->svg_offset['ys']) + ($this->svg_offset['yo']));
				$ymax = floor(($svg_h * -1) * ($this->svg_offset['ys']) + ($this->svg_offset['yo']));
				$ymin = $this->svg_offset['yo'];

				if($x > $xmax) $x2 = $xmax; // right edge
				if($x < $xmin) $x2 = $xmin; // left edge
				if($y < $ymax) $y2 = $ymax; // bottom 
				if($y > $ymin) $y2 = $ymin; // top 

			}
		}


		return array( 'x' => $x2, 'y' => $y2);
	}

	// return the transform scale factor from current style
	function get_transform_factor($xy)
	{
		$tmp = count($this->svg_style)-1;
		$current_style = $this->svg_style[$tmp];
		if($xy == 'x')
		{
			return $current_style['transform_x'];
		}
		elseif($xy == 'y')
		{
			return $current_style['transform_y'];
		}
		else
		{
			return 1;
		}
	}



	//
	//	Cette fonction attribue le style par defaut du groupe contenant la forme ou le groupe de forme
	//	puis analise les attributions de la forme ou du group de forme pour le redistribuer
	//	dans un array rapidement analisable par la fonction svgStyle.
	function svgDefineStyle($critere_style){

		
		//	"copying the default style to know that the previous group"
		//	copiage du style par defaut a savoir, celui du group precedent:
		// 	svg_style is multi-dim. This allows for new styles to be applied with g tag, then removed.
		// 	last style is coppied current_style, and then modified as needed, then returned
		$tmp = count($this->svg_style)-1;
		$current_style = $this->svg_style[$tmp];

		//
		//	si l'attribs style existe, on analise la chaine:

		if (isset($critere_style['style'])){

			$tmp = preg_replace("/(.*)fill:([a-z0-9#]*|none)(.*)/i","$2",$critere_style['style']);
			if ($tmp != $critere_style['style']){ $current_style['fill'] = $tmp;}
			
			$tmp = preg_replace("/(.*)fill-opacity:([a-z0-9#]*|none)(.*)/i","$2",$critere_style['style']);
			if ($tmp != $critere_style['style']){ $current_style['fill-opacity'] = $tmp;}
			
			$tmp = preg_replace("/(.*)fill-rule:([a-z0-9#]*|none)(.*)/i","$2",$critere_style['style']);
			if ($tmp != $critere_style['style']){ $current_style['fill-rule'] = $tmp;}
			
			$tmp = preg_replace("/(.*)stroke:([a-z0-9#]*|none)(.*)/i","$2",$critere_style['style']);
			if ($tmp != $critere_style['style']){ $current_style['stroke'] = $tmp;}
			
			$tmp = preg_replace("/(.*)stroke-linecap:([a-z0-9#]*|none)(.*)/i","$2",$critere_style['style']);
			if ($tmp != $critere_style['style']){ $current_style['stroke-linecap'] = $tmp;}
			
			$tmp = preg_replace("/(.*)stroke-linejoin:([a-z0-9#]*|none)(.*)/i","$2",$critere_style['style']);
			if ($tmp != $critere_style['style']){ $current_style['stroke-linejoin'] = $tmp;}
			
			$tmp = preg_replace("/(.*)stroke-miterlimit:([a-z0-9#]*|none)(.*)/i","$2",$critere_style['style']);
			if ($tmp != $critere_style['style']){ $current_style['stroke-miterlimit'] = $tmp;}
			
			$tmp = preg_replace("/(.*)stroke-opacity:([a-z0-9#]*|none)(.*)/i","$2",$critere_style['style']);
			if ($tmp != $critere_style['style']){ $current_style['stroke-opacity'] = $tmp;}
			
			$tmp = preg_replace("/(.*)stroke-width:([a-z0-9#]*|none)(.*)/i","$2",$critere_style['style']);
			if ($tmp != $critere_style['style']){ $current_style['stroke-width'] = $tmp;}

		}

		if(isset($critere_style['fill'])){
			$current_style['fill'] = $critere_style['fill'];
		}

		if(isset($critere_style['fill-opacity'])){
			$current_style['fill-opacity'] = $critere_style['fill-opacity'];
		}

		if(isset($critere_style['fill-rule'])){
			$current_style['fill-rule'] = $critere_style['fill-rule'];
		}

		if(isset($critere_style['stroke'])){
			$current_style['stroke'] = $critere_style['stroke'];
		}

		if(isset($critere_style['stroke-linecap'])){
			$current_style['stroke-linecap'] = $critere_style['stroke-linecap'];
		}

		if(isset($critere_style['stroke-linejoin'])){
			$current_style['stroke-linejoin'] = $critere_style['stroke-linejoin'];
		}

		if(isset($critere_style['stroke-miterlimit'])){
			$current_style['stroke-miterlimit'] = $critere_style['stroke-miterlimit'];
		}

		if(isset($critere_style['stroke-opacity'])){
			$current_style['stroke-opacity'] = $critere_style['stroke-opacity'];
		}

		if(isset($critere_style['stroke-width'])){
			$current_style['stroke-width'] = $critere_style['stroke-width'];
		}

		// TRANSFORM SCALE
		if (isset($critere_style['transform'])){
			
			$tmp = preg_replace("/(.*)scale\((\d*\.?\d*),?(\d*\.?\d*)?\)(.*)/i","$2",$critere_style['transform']);
			if($tmp != $critere_style['transform']){ $current_style['transform_x'] = $tmp;}
			
			$tmp = preg_replace("/(.*)scale\((\d*\.?\d*),?(\d*\.?\d*)?\)(.*)/i","$3",$critere_style['transform']);
			if($tmp != $critere_style['transform']){ $current_style['transform_y'] = $tmp;}
		}

		return $current_style;

	}

	//
	//	Cette fonction ecrit le style dans le stream svg.
	function svgStyle ($critere_style){

		$path_style = '';

		if (substr_count($critere_style['fill'],'url')>0){
			//
			// couleur degradé
			$id_gradient = preg_replace("/url\(#([\w_]*)\)/i","$1",$critere_style['fill']);

			$fill_gradient = $this->svgGradient($this->svg_gradient[$id_gradient]);

			$path_style = "q ";
			$w = "W";
			$style .= 'N';

		}
		else if ($critere_style['fill'] != 'none'){

			//
			//	fill couleur pleine
			if (isset($this->color_chart[$critere_style['fill']])){

				$fill = $this->color_chart[$critere_style['fill']];

			} else {

				$fill = $critere_style['fill'];

			}

			$fill_r = base_convert(substr($fill,1,2),16,10);
			$fill_g = base_convert(substr($fill,3,2),16,10);
			$fill_b = base_convert(substr($fill,5,2),16,10);
			$path_style .= sprintf('%.3f %.3f %.3f rg ',$fill_r/255,$fill_g/255,$fill_b/255);
			$style .= 'F';
		}

		if ($critere_style['stroke'] != 'none'){

			if (isset($this->color_chart[$critere_style['stroke']])){

				$stroke = $this->color_chart[$critere_style['stroke']];

			} else {

				$stroke = $critere_style['stroke'];

			}


			$stroke_r = base_convert(substr($stroke,1,2),16,10);
			$stroke_g = base_convert(substr($stroke,3,2),16,10);
			$stroke_b = base_convert(substr($stroke,5,2),16,10);
			$path_style .= sprintf('%.3f %.3f %.3f RG ',$stroke_r/255,$stroke_g/255,$stroke_b/255);
			$style .= 'D';

			$path_style .= sprintf('%.2f w ',$critere_style['stroke-width']);

		}

		switch ($style){
			case 'F':
				$op = 'f';
			break;
			case 'FD':
				$op = 'B';
			break;
			case 'ND':
				$op = 'S';
			break;
			case 'D':
				$op = 'S';
			break;
			default:
				$op = 'n';
		}

		$final_style = "$path_style $w $op $fill_gradient";
		// echo 'svgStyle: '. $final_style .'<br><br>';

		return $final_style;

	}

	//
	//	fonction retracant les <path />
	function svgPath($command, $arguments){

		global $xbase, $ybase;

		$path_cmd = '';


		preg_match_all('/[\-^]?[\d.]+/', $arguments, $a, PREG_SET_ORDER);


		//	if the command is a capital letter, the coords go absolute, otherwise relative
		if(strtolower($command) == $command) $relative = true;
		else $relative = false;


		$ile_argumentow = count($a);

		//	each command may have different needs for arguments [1 to 8]

		switch(strtolower($command)){
			case 'm': // move
				for($i = 0; $i<$ile_argumentow; $i+=2){
					$x = $a[$i][0] * $this->get_transform_factor('x'); // SCALE!!!
					$y = $a[$i+1][0] * $this->get_transform_factor('y'); // SCALE!!!
					if($relative){
						$pdfx = ($xbase + $x) * $this->svg_offset['xs'] + $this->svg_offset['xo'];
						$pdfy = ($ybase - $y) * $this->svg_offset['ys'] + $this->svg_offset['yo'];
						$xbase += $x;
						$ybase += -$y;
					}
					else{
						$pdfx = $x * $this->svg_offset['xs'] + $this->svg_offset['xo'];
						$pdfy =  -$y  * $this->svg_offset['ys'] + $this->svg_offset['yo'];
						$xbase = $x;
						$ybase = -$y;
					}
					$pdf_pt = $this->svg_overflow($pdfx,$pdfy);
					if($i == 0) $path_cmd .= sprintf('%.2f %.2f m ', $pdf_pt['x'], $pdf_pt['y']);
					else $path_cmd .= sprintf('%.2f %.2f l ',  $pdf_pt['x'], $pdf_pt['y']);
				}
			break;
			case 'l': // a simple line
				for($i = 0; $i<$ile_argumentow; $i+=2){
					$x = ($a[$i][0]) * ($this->get_transform_factor('x')); // SCALE!!!
					$y = ($a[$i+1][0]) * ($this->get_transform_factor('y')); // SCALE !!!
					if($relative){
						$pdfx = ($xbase + $x) * $this->svg_offset['xs'] + $this->svg_offset['xo'];
						$pdfy = ($ybase - $y) * $this->svg_offset['ys'] + $this->svg_offset['yo'];
						$xbase += $x;
						$ybase += -$y;
					}
					else{
						$pdfx = $x * ($this->svg_offset['xs']) + ($this->svg_offset['xo']);
						$pdfy =  -$y * ($this->svg_offset['ys']) + ($this->svg_offset['yo']);
						$xbase = $x;
						$ybase = -$y;
					}
					$pdf_pt = $this->svg_overflow($pdfx,$pdfy);
					$path_cmd .= sprintf('%.2f %.2f l ',  $pdf_pt['x'], $pdf_pt['y']);
				}
			break;
			case 'h': // a very simple horizontal line
				for($i = 0; $i<$ile_argumentow; $i++){
					$x = ($a[$i][0]) * ($this->get_transform_factor('x')); // SCALE!!!
					if($relative){
						$y = 0;
						$pdfx = ($xbase + $x)  * $this->svg_offset['xs'] + $this->svg_offset['xo'];
						$pdfy = ($ybase - $y)  * $this->svg_offset['ys'] + $this->svg_offset['yo'];
						$xbase += $x;
						$ybase += -$y;
					}
					else{
						$y = -$ybase;
						$pdfx = $x * $this->svg_offset['xs'] + $this->svg_offset['xo'];
						$pdfy =  -$y * $this->svg_offset['ys'] + $this->svg_offset['yo'];
						$xbase = $x;
						$ybase = -$y;
					}
					$pdf_pt = $this->svg_overflow($pdfx,$pdfy);
					$path_cmd .= sprintf('%.2f %.2f l ', $pdf_pt['x'], $pdf_pt['y']);
				}
			break;
			case 'v': // the simplest line, vertical
				for($i = 0; $i<$ile_argumentow; $i++){
					$y = ($a[$i+1][0]) * ($this->get_transform_factor('y')); // SCALE !!!
					if($relative){
						$x = 0;
						$pdfx = ($xbase + $x) * $this->svg_offset['xs'] + $this->svg_offset['xo'];
						$pdfy = ($ybase - $y) * $this->svg_offset['ys'] + $this->svg_offset['yo'];
						$xbase += $x;
						$ybase += -$y;
					}
					else{
						$x = $xbase;
						$pdfx = $x * $this->svg_offset['xs'] + $this->svg_offset['xo'];
						$pdfy =  -$y * $this->svg_offset['ys'] + $this->svg_offset['yo'];
						$xbase = $x;
						$ybase = -$y;
					}
					$pdf_pt = $this->svg_overflow($pdfx,$pdfy);
					$path_cmd .= sprintf('%.2f %.2f l ', $pdf_pt['x'], $pdf_pt['y']);
				}
			break;
			case 's': // bezier with first vertex equal first control
				for($i = 0; $i<$ile_argumentow; $i += 4){
					$x1 = ($a[$i][0]) * ($this->get_transform_factor('x')); // SCALE!!!
					$y1 = ($a[$i+1][0]) * ($this->get_transform_factor('y')); // SCALE!!!
					$x = ($a[$i+2][0]) * ($this->get_transform_factor('x')); // SCALE!!!
					$y = ($a[$i+3][0]) * ($this->get_transform_factor('y')); // SCALE!!!
					if($relative){
						$pdfx1 = ($xbase + $x1) * $this->svg_offset['xs'] + $this->svg_offset['xo'];
						$pdfy1 = ($ybase - $y1) * $this->svg_offset['ys'] + $this->svg_offset['yo'];
						$pdfx = ($xbase + $x) * $this->svg_offset['xs'] + $this->svg_offset['xo'];
						$pdfy = ($ybase - $y) * $this->svg_offset['ys'] + $this->svg_offset['yo'];
						$xbase += $x;
						$ybase += -$y;
					}
					else{
						$pdfx1 = $x1 * $this->svg_offset['xs'] + $this->svg_offset['xo'];
						$pdfy1 = -$y1 * $this->svg_offset['ys'] + $this->svg_offset['yo'];
						$pdfx = $x * $this->svg_offset['xs'] + $this->svg_offset['xo'];
						$pdfy =  -$y * $this->svg_offset['ys'] + $this->svg_offset['yo'];
						$xbase = $x;
						$ybase = -$y;
					}
					// $pdf_pt1 = $this->svg_overflow($pdfx1,$pdfy1);
					$pdf_pt = $this->svg_overflow($pdfx,$pdfy);
					if( ($pdf_pt['x'] != $pdfx) || ($pdf_pt['y'] != $pdfy) )
					{
						$path_cmd .= sprintf('%.2f %.2f l ',  $pdf_pt['x'], $pdf_pt['y']);
					}
					else
					{
						$path_cmd .= sprintf('%.2f %.2f %.2f %.2f v ', $pdf_pt1['x'], $pdf_pt1['y'], $pdf_pt['x'], $pdf_pt['y']);
					}
				}
			break;
			case 'c': // bezier with second vertex equal second control
			for($i = 0; $i<$ile_argumentow; $i += 6){
					$x1 = ($a[$i][0]) * ($this->get_transform_factor('x')); // SCALE!!!
					$y1 = ($a[$i+1][0]) * ($this->get_transform_factor('y')); // SCALE!!!
					$x2 = ($a[$i+2][0]) * ($this->get_transform_factor('x')); // SCALE!!!
					$y2 = ($a[$i+3][0]) * ($this->get_transform_factor('y')); // SCALE!!!
					$x = ($a[$i+4][0]) * ($this->get_transform_factor('x')); // SCALE!!!
					$y = ($a[$i+5][0]) * ($this->get_transform_factor('y')); // SCALE!!!
					if($relative){
						$pdfx1 = ($xbase + $x1) * $this->svg_offset['xs'] + $this->svg_offset['xo'];
						$pdfy1 = ($ybase - $y1) * $this->svg_offset['ys'] + $this->svg_offset['yo'];
						$pdfx2 = ($xbase + $x2) * $this->svg_offset['xs'] + $this->svg_offset['xo'];
						$pdfy2 = ($ybase - $y2) * $this->svg_offset['ys'] + $this->svg_offset['yo'];
						$pdfx = ($xbase + $x) * $this->svg_offset['xs'] + $this->svg_offset['xo'];
						$pdfy = ($ybase - $y) * $this->svg_offset['ys'] + $this->svg_offset['yo'];
						$xbase += $x;
						$ybase += -$y;
					}
					else{
						$pdfx1 = $x1 * $this->svg_offset['xs'] + $this->svg_offset['xo'];
						$pdfy1 = -$y1 * $this->svg_offset['ys'] + $this->svg_offset['yo'];
						$pdfx2 = $x2 * $this->svg_offset['xs'] + $this->svg_offset['xo'];
						$pdfy2 = -$y2 * $this->svg_offset['ys'] + $this->svg_offset['yo'];
						$pdfx = $x * $this->svg_offset['xs'] + $this->svg_offset['xo'];
						$pdfy =  -$y * $this->svg_offset['ys'] + $this->svg_offset['yo'];
						$xbase = $x;
						$ybase = -$y;
					}
					// $pdf_pt2 = $this->svg_overflow($pdfx2,$pdfy2);
					// $pdf_pt1 = $this->svg_overflow($pdfx1,$pdfy1);
					$pdf_pt = $this->svg_overflow($pdfx,$pdfy);
					if( ($pdf_pt['x'] != $pdfx) || ($pdf_pt['y'] != $pdfy) )
					{
						$path_cmd .= sprintf('%.2f %.2f l ',  $pdf_pt['x'], $pdf_pt['y']);
					}
					else
					{
						$path_cmd .= sprintf('%.2f %.2f %.2f %.2f %.2f %.2f c ', $pdfx1, $pdfy1, $pdfx2, $pdfy2, $pdfx, $pdfy);
					}

				}
			break;
			case 'q': // bezier quadratic avec point de control
			for($i = 0; $i<$ile_argumentow; $i += 4){
					$x1 = ($a[$i][0]) * ($this->get_transform_factor('x')); // SCALE!!!
					$y1 = ($a[$i+1][0]) * ($this->get_transform_factor('y')); // SCALE!!!
					$x = ($a[$i+2][0]) * ($this->get_transform_factor('x')); // SCALE!!!
					$y = ($a[$i+3][0]) * ($this->get_transform_factor('y')); // SCALE!!!
					if($relative){
						$pdfx1 = ($xbase + ($x1*2/3)) * $this->svg_offset['xs'] + $this->svg_offset['xo'];
						$pdfy1 = ($ybase - ($y1*2/3)) * $this->svg_offset['ys'] + $this->svg_offset['yo'];
						$pdfx2 = ($xbase + (($x-$x1)*1/3)) * $this->svg_offset['xs'] + $this->svg_offset['xo'];
						$pdfy2 = ($ybase - (($y-$y1)*1/3)) * $this->svg_offset['ys'] + $this->svg_offset['yo'];
						$pdfx = ($xbase + $x) * $this->svg_offset['xs'] + $this->svg_offset['xo'];
						$pdfy = ($ybase - $y) * $this->svg_offset['ys'] + $this->svg_offset['yo'];
						$xbase += $x;
						$ybase += -$y;
					}
					else{
						$pdfx1 = ($xbase+(($x1-$xbase)*2/3)) * $this->svg_offset['xs'] + $this->svg_offset['xo'];
						$pdfy1 = ($ybase-(($y1+$ybase)*2/3)) * $this->svg_offset['ys'] + $this->svg_offset['yo'];
						$pdfx2 = ($x1+($x*1/3)) * $this->svg_offset['xs'] + $this->svg_offset['xo'];
						$pdfy2 = (-$y1-($y*1/3)) * $this->svg_offset['ys'] + $this->svg_offset['yo'];
						$pdfx = $x * $this->svg_offset['xs'] + $this->svg_offset['xo'];
						$pdfy =  -$y * $this->svg_offset['ys'] + $this->svg_offset['yo'];
						$xbase = $x;
						$ybase = -$y;
					}
					// $pdf_pt2 = $this->svg_overflow($pdfx2,$pdfy2);
					// $pdf_pt1 = $this->svg_overflow($pdfx1,$pdfy1);
					$pdf_pt = $this->svg_overflow($pdfx,$pdfy);
					if( ($pdf_pt['x'] != $pdfx) || ($pdf_pt['y'] != $pdfy) )
					{
						$path_cmd .= sprintf('%.2f %.2f l ',  $pdf_pt['x'], $pdf_pt['y']);
					}
					else
					{
						$path_cmd .= sprintf('%.2f %.2f %.2f %.2f %.2f %.2f c ', $pdfx1, $pdfy1, $pdfx2, $pdfy2, $pdfx, $pdfy);
					}
				}
			break;
			case 't': // bezier quadratic avec point de control simetrique a lancien point de control
			break;
			case'z':
				$path_cmd .= 'h ';
			break;
			default:
			break;
			}

		return $path_cmd;

	}

	//
	//	fonction retracant les <rect />
	function svgRect($arguments){

		$x = $arguments['x'] * ($this->get_transform_factor('x')); // SCALE!!!
		$y = $arguments['y'] * ($this->get_transform_factor('y')); // SCALE!!!
		$h = $arguments['h'] * ($this->get_transform_factor('y')); // SCALE!!!
		$w = $arguments['w'] * ($this->get_transform_factor('x')); // SCALE!!!
		$rx = ($arguments['rx']/2) * ($this->get_transform_factor('x')); // SCALE!!!
		$ry = ($arguments['ry']/2) * ($this->get_transform_factor('y')); // SCALE!!!

		if ($rx>0 and $ry == 0){$ry = $rx;}
		if ($ry>0 and $rx == 0){$rx = $ry;}

		if ($rx == 0 and $ry == 0){
			//	trace un rectangle sans angle arrondit
			$path_cmd = sprintf('%.2f %.2f m ', $this->svg_offset['xo']+($x*$this->svg_offset['xs']), $this->svg_offset['yo']-($y*$this->svg_offset['ys']));
			$path_cmd .= sprintf('%.2f %.2f l ', $this->svg_offset['xo']+(($x+$w)*$this->svg_offset['xs']), $this->svg_offset['yo']-($y*$this->svg_offset['ys']));
			$path_cmd .= sprintf('%.2f %.2f l ', $this->svg_offset['xo']+(($x+$w)*$this->svg_offset['xs']), $this->svg_offset['yo']-(($y+$h)*$this->svg_offset['ys']));
			$path_cmd .= sprintf('%.2f %.2f l ', $this->svg_offset['xo']+($x*$this->svg_offset['xs']), $this->svg_offset['yo']-(($y+$h)*$this->svg_offset['ys']));
			$path_cmd .= sprintf('%.2f %.2f l h ', $this->svg_offset['xo']+($x*$this->svg_offset['xs']), $this->svg_offset['yo']-($y*$this->svg_offset['ys']));

			
		}
		else {
			//	trace un rectangle avec les arrondit
			//	les points de controle du bezier sont deduis grace a la constante kappa
			$kappa = 4*(sqrt(2)-1)/3;

			$kx = $kappa*$rx;
			$ky = $kappa*$ry;

			$path_cmd = sprintf('%.2f %.2f m ', $this->svg_offset['xo']+$x+($rx*$this->svg_offset['xs']), $this->svg_offset['yo']-$y);

			$path_cmd .= sprintf('%.2f %.2f l ', $this->svg_offset['xo']+$x+(($w-$rx)*$this->svg_offset['xs']), $this->svg_offset['yo']-$y);

			$path_cmd .= sprintf('%.2f %.2f %.2f %.2f %.2f %.2f c ', 	$this->svg_offset['xo']+$x+(($w-$rx+$kx)*$this->svg_offset['xs']), $this->svg_offset['yo']-$y,
																		$this->svg_offset['xo']+$x+($w*$this->svg_offset['xs']), $this->svg_offset['yo']-$y+((-$ry+$ky)*$this->svg_offset['ys']),
																		$this->svg_offset['xo']+$x+($w*$this->svg_offset['xs']), $this->svg_offset['yo']-$y+(-$ry*$this->svg_offset['ys'])
																		);

			$path_cmd .= sprintf('%.2f %.2f l ', $this->svg_offset['xo']+$x+($w*$this->svg_offset['xs']), $this->svg_offset['yo']-$y+((-$h+$ry)*$this->svg_offset['ys']));

		 	$path_cmd .= sprintf('%.2f %.2f %.2f %.2f %.2f %.2f c ', 	$this->svg_offset['xo']+$x+($w*$this->svg_offset['xs']), $this->svg_offset['yo']-$y+((-$h-$ky+$ry)*$this->svg_offset['ys']),
																		$this->svg_offset['xo']+$x+(($w-$rx+$kx)*$this->svg_offset['xs']), $this->svg_offset['yo']-$y+(-$h*$this->svg_offset['ys']),
																		$this->svg_offset['xo']+$x+(($w-$rx)*$this->svg_offset['xs']), $this->svg_offset['yo']-$y+(-$h*$this->svg_offset['ys'])
																		);

			$path_cmd .= sprintf('%.2f %.2f l ', $this->svg_offset['xo']+$x+($rx*$this->svg_offset['xs']), $this->svg_offset['yo']-$y+(-$h*$this->svg_offset['ys']));

			$path_cmd .= sprintf('%.2f %.2f %.2f %.2f %.2f %.2f c ', 	$this->svg_offset['xo']+$x+(($rx-$kx)*$this->svg_offset['xs']), $this->svg_offset['yo']-$y+(-$h*$this->svg_offset['ys']),
																		$this->svg_offset['xo']+$x, $this->svg_offset['yo']-$y+((-$h-$ky+$ry)*$this->svg_offset['ys']),
																		$this->svg_offset['xo']+$x, $this->svg_offset['yo']-$y+((-$h+$ry)*$this->svg_offset['ys'])
																		);

			$path_cmd .= sprintf('%.2f %.2f l ', $this->svg_offset['xo']+$x, $this->svg_offset['yo']-$y+(-$ry*$this->svg_offset['ys']));

			$path_cmd .= sprintf('%.2f %.2f %.2f %.2f %.2f %.2f c h ', 	$this->svg_offset['xo']+$x, $this->svg_offset['yo']-$y+((-$ry+$ky)*$this->svg_offset['ys']),
																		$this->svg_offset['xo']+$x+(($rx-$kx)*$this->svg_offset['xs']), $this->svg_offset['yo']-$y,
																		$this->svg_offset['xo']+$x+($rx*$this->svg_offset['xs']), $this->svg_offset['yo']-$y
																		);


		}
		return $path_cmd;
	}

	//
	//	fonction retracant les <ellipse /> et <circle />
	//	 le cercle est tracé grave a 4 bezier cubic, les poitn de controles
	//	sont deduis grace a la constante kappa * rayon
	function svgEllipse($arguments){

//echo "<br>making ellipse<br>";


		$kappa = 4*(sqrt(2)-1)/3;
		
		//testing:
		//$this->svg_offset['xs'] = 1;
		//$this->svg_offset['ys'] = 1;
		

//echo $arguments['cx'] . $arguments['cy'] . $arguments['rx'] . $arguments['ry'] ."<BR>";				
//echo $this->get_transform_factor('x') . "<BR>";		
//echo $this->get_transform_factor('y') . "<BR>";	

//echo $this->svg_offset['xs'] . "<BR>";
//echo $this->svg_offset['ys'] . "<BR>";
		
		
		$cx = $arguments['cx'] * ($this->get_transform_factor('x')) * ($this->svg_offset['xs']);
		$cy = $arguments['cy'] * ($this->get_transform_factor('y')) * ($this->svg_offset['ys']);
		$rx = $arguments['rx'] * ($this->get_transform_factor('x')) * ($this->svg_offset['xs']);
		$ry = $arguments['ry'] * ($this->get_transform_factor('y')) * ($this->svg_offset['ys']);
		
//echo "$cx $cy $rx $ry<BR>";		

		$x1 = $this->svg_offset['xo']+$cx;
		$y1 = $this->svg_offset['yo']-$cy+$ry;

		$x2 = $this->svg_offset['xo']+$cx+$rx;
		$y2 = $this->svg_offset['yo']-$cy;

		$x3 = $this->svg_offset['xo']+$cx;
		$y3 = $this->svg_offset['yo']-$cy-$ry;

		$x4 = $this->svg_offset['xo']+$cx-$rx;
		$y4 = $this->svg_offset['yo']-$cy;

		$path_cmd = sprintf('%.2f %.2f m ', $x1, $y1);

		$path_cmd .= sprintf('%.2f %.2f %.2f %.2f %.2f %.2f c ', $x1+($rx*$kappa), $y1, $x2, $y2+($ry*$kappa), $x2, $y2);

		$path_cmd .= sprintf('%.2f %.2f %.2f %.2f %.2f %.2f c ', $x2, $y2-($ry*$kappa), $x3+($rx*$kappa), $y3, $x3, $y3);

		$path_cmd .= sprintf('%.2f %.2f %.2f %.2f %.2f %.2f c ', $x3-($rx*$kappa), $y3, $x4, $y4-($ry*$kappa), $x4, $y4);

		$path_cmd .= sprintf('%.2f %.2f %.2f %.2f %.2f %.2f c ', $x4, $y4+($ry*$kappa), $x1-($rx*$kappa), $y1, $x1, $y1);

		$path_cmd .= 'h ';

//echo "PATH IS: $path_cmd<BR>";		
		
		return $path_cmd;

	}

	//
	//	fonction retracant les <polyline /> et les <line />
	function svgPolyline($arguments){

		$xbase = $this->svg_offset['xo'] + $arguments[0] * ($this->get_transform_factor('x')) * ($this->svg_offset['xs']);
		$ybase = $this->svg_offset['yo'] - $arguments[1] * ($this->get_transform_factor('y')) * ($this->svg_offset['ys']);

		$path_cmd = sprintf('%.2f %.2f m ', $xbase, $ybase);
		for ($i = 2; $i<count($arguments);$i += 2) {

			$tmp_x = $this->svg_offset['xo'] + $arguments[$i] * ($this->get_transform_factor('x')) * ($this->svg_offset['xs']);
			$tmp_y = $this->svg_offset['yo'] - $arguments[($i+1)] * ($this->get_transform_factor('y')) * ($this->svg_offset['ys']);

			$path_cmd .= sprintf('%.2f %.2f l ', $tmp_x, $tmp_y);

		}

		$path_cmd .= 'h ';
		return $path_cmd;

	}

	//
	//	fonction retracant les <polygone />
	function svgPolygon($arguments){

		$xbase = $this->svg_offset['xo'] + $arguments[0] * ($this->get_transform_factor('x')) * ($this->svg_offset['xs']);
		$ybase = $this->svg_offset['yo'] - $arguments[1] * ($this->get_transform_factor('y')) * ($this->svg_offset['ys']);

		$path_cmd = sprintf('%.2f %.2f m ', $xbase, $ybase);
		for ($i = 2; $i<count($arguments);$i += 2) {

			$tmp_x = $this->svg_offset['xo'] + $arguments[$i] * ($this->get_transform_factor('x')) * ($this->svg_offset['xs']);
			$tmp_y = $this->svg_offset['yo'] - $arguments[($i+1)] * ($this->get_transform_factor('y')) * ($this->svg_offset['ys']);

			$path_cmd .= sprintf('%.2f %.2f l ', $tmp_x, $tmp_y);

		}

		$path_cmd .= sprintf('%.2f %.2f l ', $xbase, $ybase);
		$path_cmd .= 'h ';
		return $path_cmd;

	}

	//
	//	write string to image
	function svgText(){
		// $tmp = count($this->txt_style)-1;
		$current_style = array_pop($this->txt_style);
		$style = '';
		/*
		echo $current_style['font-weight'].'<br>';
		echo $current_style['font-style'].'<br>';
		echo $current_style['font-family'].'<br>';
		echo $current_style['font-size'].'<br>';
		echo $current_style['fill'].'<br>';
		echo'<br>';
		*/
		if(isset($this->txt_data[2]))
		{
			// select font
			$style .= ($current_style['font-weight'] == 'bold')?'B':'';
			$style .= ($current_style['font-style'] == 'italic')?'I':'';
			$size = $current_style['font-size'];
			$this->SetSvgFont($current_style['font-family'],$style,$current_style['font-size']);

			if (isset($this->color_chart[$current_style['fill']])){
				$fill = $this->color_chart[$current_style['fill']];
			} else {
				$fill = $current_style['fill'];
			}

			$fill_r = base_convert(substr($fill,1,2),16,10);
			$fill_g = base_convert(substr($fill,3,2),16,10);
			$fill_b = base_convert(substr($fill,5,2),16,10);
			
			// $path_style .= sprintf('%.3f %.3f %.3f rg ',$fill_r/255,$fill_g/255,$fill_b/255);
			// $this->_out(sprintf('BT /F%d %.2f Tf ET',$this->CurrentFont['i'],$this->FontSizePt));


			$x = $this->txt_data[0];
			$y = $this->txt_data[1];
			$txt = $this->txt_data[2];
			$txt = trim($txt);
			$pdfx = $x * $this->svg_offset['xs'] + $this->svg_offset['xo'];
			$pdfy =  -$y  * $this->svg_offset['ys'] + $this->svg_offset['yo'];
			$xbase = $x;
			$ybase = -$y;
			// $path_cmd =  sprintf('q BT /F1 7 Tf %.2f %.2f Td  0 Tr 0 0 0 rg  (%s) Tj ET Q ',$pdfx,$pdfy,$txt);

//echo "Current font is " . $this->CurrentFont['i'];			

			$path_cmd =  sprintf('q BT /F%d %.2f Tf %.2f %.2f Td  0 Tr %.3f %.3f %.3f rg  (%s) Tj ET Q ',$this->CurrentFont['i'],$this->FontSizePt,$pdfx,$pdfy,$fill_r/255,$fill_g/255,$fill_b/255,$txt);
			unset($this->txt_data[0], $this->txt_data[1],$this->txt_data[2]);
		}
		else
		{
			die("No string to write!");
		}
		$path_cmd .= 'h ';
		return $path_cmd;
	}


function svgDefineTxtStyle($critere_style)
{
		// get copy of current/default txt style, and modify it with supplied attributes
		$tmp = count($this->txt_style)-1;
		$current_style = $this->txt_style[$tmp];

		if (isset($critere_style['font'])){

			// [ [ <'font-style'> || <'font-variant'> || <'font-weight'> ]?<'font-size'> [ / <'line-height'> ]? <'font-family'> ]

			$tmp = preg_replace("/(.*)(italic|oblique)(.*)/i","$2",$critere_style['font']);
			if ($tmp != $critere_style['font']){ 
				if($tmp == 'oblique'){
					$tmp = 'italic';
				}
				$current_style['font-style'] = $tmp;
			}
			$tmp = preg_replace("/(.*)(bold|bolder)(.*)/i","$2",$critere_style['font']);
			if ($tmp != $critere_style['font']){ 
				if($tmp == 'bolder'){
					$tmp = 'bold';
				}
				$current_style['font-weight'] = $tmp;
			}
			
			// select digits not followed by percent sign nor preceeded by forward slash
			$tmp = preg_replace("/(.*)\b(\d+)[\b|\/](.*)/i","$2",$critere_style['font']);
			if ($tmp != $critere_style['font']){ $current_style['font-size'] = $tmp; }
			
		}

		if(isset($critere_style['fill'])){
			$current_style['fill'] = $critere_style['fill'];
		}
		
		if(isset($critere_style['font-style'])){
			if(strtolower($critere_style['font-style']) == 'oblique') 
			{
				$critere_style['font-style'] = 'italic';
			}
			$current_style['font-style'] = $critere_style['font-style'];
		}
		
		if(isset($critere_style['font-weight'])){
			if(strtolower($critere_style['font-weight']) == 'bolder')
			{
				$critere_style['font-weight'] = 'bold';
			}
			$current_style['font-weight'] = $critere_style['font-weight'];
		}
		
		if(isset($critere_style['font-size'])){
			$current_style['font-size'] = $critere_style['font-size'];
		}
		
		if(isset($critere_style['font-family'])){
			$current_style['font-family'] = $critere_style['font-family'];
		}
	
	// add current style to text style array (will remove it later after writing text to svg_string)
	array_push($this->txt_style,$current_style);
}

function SetSvgFont($family,$style='',$size=0)
{

//echo "WE SET THE FONT!  Font is $family";

	//Select a font; size given in points
	global $fpdf_charwidths;

	$family=strtolower($family);
	if($family=='')
		$family=$this->FontFamily;
	if($family=='arial')
		$family='helvetica';
	elseif($family=='symbol' || $family=='zapfdingbats')
		$style='';
	$style=strtoupper($style);
	if(strpos($style,'U')!==false)
	{
		$this->underline=true;
		$style=str_replace('U','',$style);
	}
	else
		$this->underline=false;
	if($style=='IB')
		$style='BI';
	if($size==0)
		$size=$this->FontSizePt;
	//Test if font is already selected
	if($this->FontFamily==$family && $this->FontStyle==$style && $this->FontSizePt==$size)
		return;
	//Test if used for the first time
	$fontkey=$family.$style;
	if(!isset($this->fonts[$fontkey]))
	{
		//Check if one of the standard fonts
		if(isset($this->CoreFonts[$fontkey]))
		{
			if(!isset($fpdf_charwidths[$fontkey]))
			{
				//Load metric file
				$file=$family;
				if($family=='times' || $family=='helvetica')
					$file.=strtolower($style);
				include($this->_getfontpath().$file.'.php');
				if(!isset($fpdf_charwidths[$fontkey]))
					$this->Error('Could not include font metric file');
			}
			$i=count($this->fonts)+1;
			$this->fonts[$fontkey]=array('i'=>$i,'type'=>'core','name'=>$this->CoreFonts[$fontkey],'up'=>-100,'ut'=>50,'cw'=>$fpdf_charwidths[$fontkey]);
		}
		else
			$this->Error('Undefined font: '.$family.' '.$style);
	}
	//Select it
	//($fontName, $attributes, $fontSize, $r = -1, $g = -1, $b = -1)

	
	
	$this->FontFamily=$family;
	$this->FontStyle=$style;
	$this->FontSizePt=$size;
	$this->FontSize=$size/$this->k;
	$this->CurrentFont=&$this->fonts[$fontkey];
	
	
	// if($this->page>0)
	//	$this->_out(sprintf('BT /F%d %.2f Tf ET',$this->CurrentFont['i'],$this->FontSizePt));
}


	//
	//	fonction analisant le style du group
	function svgGroup($attribs){

		$array_style = $this->svgDefineStyle($attribs);
		array_push($this->svg_style,$array_style);

	}

	//
	//	fonction fermant le group
	function svgUngroup(){

		array_pop($this->svg_style);

	}

	//
	//	fonction ajoutant un gradient
	function svgAddGradient($id,$array_gradient){

		$this->svg_gradient[$id] = $array_gradient;

	}
	//
	//	Ajoute une couleur dans le gradient correspondant
	function svgStop ($id,$array_color){

		array_push($this->svg_gradient[$id]['color'],$array_color);

	}

	//
	//	function ecrivant dans le svgstring
	function svgWriteString($content){

		$this->svg_string .= $content;

	}


	// CLEAR SVG IMAGES AT THE START OF EACH NEW PAGE
	function svgClearString(){

		$this->svg_string = '';

	}


	function _putshaders(){
		global $fpdf_class;

    	foreach($fpdf_class->gradients as $id=>$grad){

			$this->_newobj();
				array_push($this->svg_shadinglist,$this->n);
				$fpdf_class->gradients[$id]['id'] = ($this->n);
			$this->_out('<<');
			$this->_out('/ShadingType '.$grad['type']);
			$this->_out('/ColorSpace /DeviceRGB');

			if($grad['type']=='2'){

				$w = $grad['coords']['w'];
				$h = $grad['coords']['h'];
				$this->_out(sprintf('/Coords [%.3f %.3f %.3f %.3f]',0,0,$w,$h));

			}
			else if($grad['type']==3){


				$r = $grad['coords']['r'];

				$this->_out(sprintf('/Coords [%.3f %.3f %.3f %.3f %.3f %.3f]',0,0,0,0,0,$r));
			}
			$this->_out('/Extend [true true]');
			$this->_out('/Function');
			$this->_out('<<');
			$this->_out('/FunctionType 3');
			$this->_out('/Domain [0 1]');

			$color_function = "";
			$color_bounds = "";
			$color_encode = "";
			$n_color = count($grad['color'])-1;

			for ($i = 0; $i<$n_color; $i++){

				$color_function .= ($this->n+1+$i)." 0 R ";

				if ($i<$n_color-1){
					$color_bounds .= sprintf('%3f ',$grad['color'][$i+1]['offset']);
				}

				$color_encode .= "0 1 ";
			}

			$this->_out('/Functions ['.trim($color_function).']');
			$this->_out('/Bounds ['.trim($color_bounds).']');
			$this->_out('/Encode ['.trim($color_encode).']');
			$this->_out('>>');
			$this->_out('>>');
			$this->_out('endobj');

			for ($i = 0; $i<$n_color; $i++){

				$this->_newobj();
				$this->_out('<<');
				$this->_out('/FunctionType 2');
				$this->_out('/Domain [0 1]');
				$this->_out('/C0 ['.$grad['color'][$i]['color'].']');
				$this->_out('/C1 ['.$grad['color'][$i+1]['color'].']');
				$this->_out('/N 1');
				$this->_out('>>');
	            $this->_out('endobj');
	    	}

		}
	}

	function _putresourcedict(){
		parent::_putresourcedict();
		$this->_out('/Shading <<');
		for ($i=0; $i<count($this->svg_shadinglist); $i++){

			$this->_out('/Sh'.($i+1).' '.$this->svg_shadinglist[$i].' 0 R');

		}
		$this->_out('>>');
	}

	function _putresources(){
		$this->_putshaders();
		parent::_putresources();
	}

	//
	//	analise le svg et renvoie aux fonctions precedente our le traitement
	function ImageSVG($critere){

//var_dump($critere);
	
//echo("PDFCREATOR >> ImageSVG<br>");
		
		$this->svg_info = $critere;
		$this->svgClearString(); // clear svg_string before starting new image
		
		//
		//	chargement unique des fonctions
		if(!function_exists(xml_svg2pdf_start)){

//echo("function exists<br>");
		
			function xml_svg2pdf_start($parser, $name, $attribs){
				//
				//	definition

//echo("inside xml_svg2pdf_start<br>");

				global	$fpdf_class,$last_gradid;

				switch (strtolower($name)){


					//
					//	analise de la balise <svg /> ou est contenu essentielement les information sur la taille d'origine
					//	le traitement prend en compte la taille d'origine du document, et la taille voulu par l'utilisateur au final
					//	afin de produire $xoffset/$yoffset, coordoné du point d'origine du svg dans le doncument
					//	et $xscale/$yscale multiplicateur qui met le svg a la bonne taille.
					//	selon le format de la page  le multiplicateur change en consequence, et sont origine egalement si
					//	le format est en mode paysage, $yoffset prend automatiquement -height.

					case 'svg':
						$fpdf_class->svgOffset($attribs);
					break;

					//
					//	grstion des forme path, rect circle, ellipse, polygon, polyline
					//	à chaque fois on lui attribu un style par defaut correspond au style du group courant,
					//	le style par defaut est ensuite remplacer par le style de la forme si il y a lieu d'etre.
					//
					//	trace la forme <path /> en suivant les commandes m,z,l,h,v,c,s,q,t
					//	seul la commande a (elleptique arc) n'est pas encore gerée.
					
					case 'path':
					$path = $attribs['d'];
					// echo 'path: ' . $path .'<br><br>';

					// redistribution du contenu du path dans des array:
					preg_match_all('/([a-z]|[A-Z])([ ,\-.\d]+)*/', $path, $commands, PREG_SET_ORDER);
					// preg_match_all('/([A-Z])([\d, ]+)*/', $path, $commands, PREG_SET_ORDER);
					$path_cmd = '';
					//	traitement par action du path

					foreach($commands as $c){
						if(count($c)==3){
							list($tmp, $command, $arguments) = $c;
							// echo 'tmp >> '. $tmp .'<br>';
							// echo 'cmd >> '. $command .'<br>';
							// echo 'arg >> '. $arguments .'<br><br>';
						}
						else{
							list($tmp, $command) = $c;
							$arguments = '';
							// echo 'tmp >> '. $tmp .'<br>';
							// echo 'cmd >> '. $command .'<br>';
						}

						$path_cmd .= $fpdf_class->svgPath($command, $arguments);
					}
					//
					//	definition du style
					$critere_style = $attribs;
					unset($critere_style['d']);
					$path_style = $fpdf_class->svgDefineStyle($critere_style);
					break;

					case 'rect':
					if (!isset($attribs['x'])) {$attribs['x'] = 0;}
					if (!isset($attribs['y'])) {$attribs['y'] = 0;}
					if (!isset($attribs['rx'])) {$attribs['rx'] = 0;}
					if (!isset($attribs['ry'])) {$attribs['ry'] = 0;}
					$arguments = array(
						'x' => $attribs['x'],
						'y' => $attribs['y'],
						'w' => $attribs['width'],
						'h' => $attribs['height'],
						'rx' => $attribs['rx'],
						'ry' => $attribs['ry']
					);
					$path_cmd =  $fpdf_class->svgRect($arguments);
					$critere_style = $attribs;
					unset($critere_style['x'],$critere_style['y'],$critere_style['rx'],$critere_style['ry'],$critere_style['height'],$critere_style['width']);
					$path_style = $fpdf_class->svgDefineStyle($critere_style);
					break;

					case 'circle':
					
//echo("circle!!!!<br>");
					
					if (!isset($attribs['cx'])) {$attribs['cx'] = 0;}
					if (!isset($attribs['cy'])) {$attribs['cy'] = 0;}
					$arguments = array(
						'cx' => $attribs['cx'],
						'cy' => $attribs['cy'],
						'rx' => $attribs['r'],
						'ry' => $attribs['r']
					);
					$path_cmd =  $fpdf_class->svgEllipse($arguments);
					$critere_style = $attribs;
//echo "style is:";
//var_dump($critere_style);					
					unset($critere_style['cx'],$critere_style['cy'],$critere_style['r']);
					$path_style = $fpdf_class->svgDefineStyle($critere_style);
					break;

					case 'ellipse':
					if (!isset($attribs['cx'])) {$attribs['cx'] = 0;}
					if (!isset($attribs['cy'])) {$attribs['cy'] = 0;}
					$arguments = array(
						'cx' => $attribs['cx'],
						'cy' => $attribs['cy'],
						'rx' => $attribs['rx'],
						'ry' => $attribs['ry']
					);
					$path_cmd =  $fpdf_class->svgEllipse($arguments);
					$critere_style = $attribs;
					unset($critere_style['cx'],$critere_style['cy'],$critere_style['rx'],$critere_style['ry']);
					$path_style = $fpdf_class->svgDefineStyle($critere_style);
					break;

					case 'line':
					$arguments = array($attribs['x1'],$attribs['y1'],$attribs['x2'],$attribs['y2']);
					$path_cmd =  $fpdf_class->svgPolyline($arguments);
					$critere_style = $attribs;
					unset($critere_style['x1'],$critere_style['y1'],$critere_style['x2'],$critere_style['y2']);
					$path_style = $fpdf_class->svgDefineStyle($critere_style);
					break;

					case 'polyline':
					$path = $attribs['points'];
					//	redirstribution du contenu de la forme dans un array.
					preg_match_all('/[0-9\-\.]*/',$path, $tmp, PREG_SET_ORDER);
					$arguments = array();
					for ($i;$i<count($tmp);$i++){
						if ($tmp[$i][0] !=''){
							array_push($arguments, $tmp[$i][0]);
						}
					}
					$path_cmd =  $fpdf_class->svgPolyline($arguments);
					$critere_style = $attribs;
					unset($critere_style['points']);
					$path_style = $fpdf_class->svgDefineStyle($critere_style);
					break;

					case 'polygon':

					$path = $attribs['points'];

					//	redirstribution du contenu de la forme dans un array.
					preg_match_all('/[0-9\-\.]*/',$path, $tmp, PREG_SET_ORDER);
					$arguments = array();
					for ($i;$i<count($tmp);$i++){
						if ($tmp[$i][0] !=''){
							array_push($arguments, $tmp[$i][0]);
						}
					}
					$path_cmd =  $fpdf_class->svgPolygon($arguments);
					//	definition du style de la forme:
					$critere_style = $attribs;
					unset($critere_style['points']);
					$path_style = $fpdf_class->svgDefineStyle($critere_style);
					break;

					//
					//	Le linearGradient comme les radials sont declaré avant d'etre utilisé dans leur
					//	les balises <lineargradient /> et <radialgradient /> definisse le placement du gradient
					//	leur couleur sont definit par les balise stop.
					case 'lineargradient':

						$tmp_gradient = array(
							'type' => 'linear',
							'info' => array(
								'x1' => $attribs['x1'],
								'y1' => $attribs['y1'],
								'x2' => $attribs['x2'],
								'y2' => $attribs['y2']
							),
							'transform' => $attribs['gradientTransform'],
							'color' => array()
						);

						$last_gradid = $attribs['id'];

						$fpdf_class->svgAddGradient($attribs['id'],$tmp_gradient);

					break;

					case 'radialgradient':

						$tmp_gradient = array(
							'type' => 'radial',
							'info' => array(
								'x0' => $attribs['cx'],
								'y0' => $attribs['cy'],
								'x1' => $attribs['fx'],
								'y1' => $attribs['fy'],
								'r' => $attribs['r']
							),
							'transform' => $attribs['gradientTransform'],
							'color' => array()
						);

						$last_gradid = $attribs['id'];

						$fpdf_class->svgAddGradient($attribs['id'],$tmp_gradient);

					break;

					//
					//	couleur du dégradé
					case 'stop':

						if (isset($attribs['style']) AND !isset($attribs['stop-color'])){

							$color = preg_replace('/stop-color:([0-9#]*)/i','$1',$attribs['style']);

						} else {

							$color = $attribs['stop-color'];

						}
						$color_r = base_convert(substr($color,1,2),16,10);
						$color_g = base_convert(substr($color,3,2),16,10);
						$color_b = base_convert(substr($color,5,2),16,10);

						$color_final = $path_style .= sprintf('%.3f %.3f %.3f',$color_r/255,$color_g/255,$color_b/255);


						$tmp_color = array(
							'color' => $color_final,
							'offset' => $attribs['offset'],
							'opacity' => $attribs['stop-opacity']
						);

						$fpdf_class->svgStop($last_gradid,$tmp_color);

					break;
					//
					//	les groupes peuvent egalement definir des styles pour plusieurs formes
					//	ont retient donc les styles du groupes à part pour etre reutiliser plus tard par les forme concerné
					//	en ajoutant un nouveau style dans l'array global $svg2pdf_groupstyle.
					case 'g':
						$fpdf_class->svgGroup($attribs);
					break;

					case 'text':
						$fpdf_class->txt_data[0] = $attribs['x'];
						$fpdf_class->txt_data[1] = $attribs['y'];
						$critere_style = $attribs;
						unset($critere_style['x'], $critere_style['y']);
						$fpdf_class->svgDefineTxtStyle($critere_style);

					break;
				}

				//
				//insertion des path et du style dans le flux de donné general.
				if (isset($path_cmd)){


					//productionde  la chaine de character du style grace a l'array $path_style definit auparavant


					//sauvetage de la forme (q) si le style contient un fill gradient
					//	insertion de la forme et de son style dans la chaine svg general

					$get_style = $fpdf_class->svgStyle($path_style);

					// echo 'path >> '.$path_cmd."<br>";
					// echo 'style >> '.$get_style."<br><br>";
//echo "<BR><BR>Writing string ... $path_cmd";
					$fpdf_class->svgWriteString("$path_cmd $get_style");

				}
			}

			function characterData($parser, $data)
			{
				global $fpdf_class;

				if(isset($fpdf_class->txt_data[2]))
				{
					$fpdf_class->txt_data[2] .= $data;
				}
				else
				{
					$fpdf_class->txt_data[2] = $data;
				}
			}


			function xml_svg2pdf_end($parser, $name){

				global $fpdf_class;

				switch($name){
					//
					//	quand un groupe se fini on supprime le dernier
					case "g":
						$fpdf_class->svgUngroup();
					break;
					case "text":
						$path_cmd = $fpdf_class->svgText();
						// echo 'path >> '.$path_cmd."<br><br>";
						// echo "style >> ".$get_style[1]."<br><br>";
						$fpdf_class->svgWriteString($path_cmd);
					break;
				}

			}

		}

		global $fpdf_class;

		$svg2pdf_xml='';
		//
		//
		$fpdf_class = $this;
		//
		//	parsage du xml:

//var_dump($this->svg_string);	
	 		
			$svg2pdf_xml_parser = xml_parser_create("utf-8");
			xml_parser_set_option($svg2pdf_xml_parser, XML_OPTION_CASE_FOLDING, false);
			xml_set_element_handler($svg2pdf_xml_parser, "xml_svg2pdf_start", "xml_svg2pdf_end");
			xml_set_character_data_handler($svg2pdf_xml_parser, "characterData");
			xml_parse($svg2pdf_xml_parser, $this->svg_info['filename']);

//var_dump($this->svg_info);	
			
//var_dump($fpdf_class->svg_string);	
			
		$this->_out($fpdf_class->svg_string);

	}

	
	
	
	
	
	function AcceptPageBreak()
	{
//automatically called when content reaches end of page - add logic for page breaks (resetting y pos, etc, here):	
		//if(!$this->overridePageBreakFlag) return false;
		//$this->SetY($this->overridePageBreakNextLine);
		//return true;
		
		return false;
	}
	

	function PlaceAreaTextCell($x, $y, $width, $text, $bulletPoint=false, $writeDepth=4, $align="L")
	{
		if($y != -1 ) $this->setY($y);
		$this->setX($x);
		
		if($bulletPoint)
		{
			$bX = $this->getX() - 2.5;
			$bY = $this->getY() + 0.8;
			
			$this->SetDrawColor(0,0,0);
			$this->SetFillColor(0,0,0);
			$this->Rect($bX, $bY, 2, 1.5, "DF");
		}
		
		
		$this->MultiCell($width, $writeDepth, $text, 0, $align);
		
	}

	function PlaceAreaText($x, $y, $width, $text, $bulletPoint=false, $writeDepth=4, $align="L", $rotation=0, $lineSpacing=0)
	{
		if($y != -1 ) $this->SetY($y);
		if($x != -1 ) 
		{
			$this->SetLeftMargin($x);
			$this->SetX($x);
			if($y == -1)
			{
				$this->SetY($this->GetY() + $lineSpacing + $writeDepth);
			}
		}
		if($bulletPoint)
		{
			$bX = $this->GetX() - 2.5;
			$bY = $this->GetY() + 1.5;
			
			$this->SetDrawColor(0,0,0);
			$this->SetFillColor(0,0,0);
			$this->Rect($bX, $bY, 2, 2, "DF");
		}		
		
		if($width != -1 ) $this->SetRightMargin(216 - $x - $width);
		//$this->Write(4, $text);
		
		$this->Rotate($rotation,$x,$y);		
		$this->WriteHTML($text, $writeDepth);
		$this->Rotate(0);

	}
	
	function AddLineSpacing($mm)
	{
		$newY = $this->GetY() + $mm;
		$this->SetY($newY);
	}
	
	function AddLayout($fileName, $page)
	{
		$jsonData = file_get_contents("json/layout/$fileName");
		$json = json_decode($jsonData);
		
		foreach($json as $obj) $this->layouts[$page][$obj->id] = $obj;
	}
	
	function SetFontForTFPDF($fontName, $attributes, $fontSize, $r = -1, $g = -1, $b = -1)
	{
		$this->_currentFont = $fontName;
		$fullFontName = $this->_currentFont.$this->GetFullFontName($attributes);
		$underlineFlag = ( strrpos($pdfElement->attributes,"U") === false ) ? "" : "U";
		
//echo "SETTING FONT FOR TFPDF: $fullFontName<BR>";		
		
		$this->setFont($fullFontName, $underlineFlag, $fontSize);
		if($r != -1 && $g != -1 && b != -1)
		{
			$this->SetTextColor($r, $g, $b);
		}
	}
	
	function SetFontByElement($pdfElement)
	{

//echo "SetFontByElement -> $pdfElement->fontName";			
		$this->SetFontForTFPDF($pdfElement->fontName, $pdfElement->attributes, $pdfElement->size);
		$this->setTextColor($pdfElement->r, $pdfElement->g, $pdfElement->b);
	}
	
	function WriteHTML($html, $writeDepth)
	{
		//HTML parser
		$html=str_replace("\n",' ',$html);
		$a=preg_split('/<(.*)>/U',$html,-1,PREG_SPLIT_DELIM_CAPTURE);
		foreach($a as $i=>$e)
		{
			if($i%2==0)
			{
				//Text
				if($this->HREF)
					$this->PutLink($this->HREF,$e, $writeDepth);
				else
					$this->Write($writeDepth,$e);
			}
			else
			{
				//Tag
				if($e{0}=='/')
					$this->CloseTag(strtoupper(substr($e,1)));
				else
				{
					//Extract attributes
					$a2=explode(' ',$e);
					$tag=strtoupper(array_shift($a2));
					$attr=array();
					foreach($a2 as $v)
						if(ereg('^([^=]*)=["\']?([^"\']*)["\']?$',$v,$a3))
							$attr[strtoupper($a3[1])]=$a3[2];
					$this->OpenTag($tag,$attr);
				}
			}
		}
	}
	
	
	function GetFullFontName($attr)
	{

		$attr=str_replace("U","",$attr);
		
		//return "";
		
		switch($attr)
		{
			case "I":
				return "Italic";
				break;
			case "BI":
				//return "Bold";
				return "BoldItalic";
				break;
			case "IB":
				return "BoldItalic";
				break;				
			case "B":
				return "Bold";
				//return "BoldItalic";
				return "";
				break;					
		}
		return "";
	}

	function OpenTag($tag,$attr)
	{
		//Opening tag
		//if($tag=='B' or $tag=='I' or $tag=='U')
		
//echo "opentag -> SETTING FONT TO: ".$this->_currentFont.$this->GetFullFontName($tag)."<BR>";
		
		if($tag=='B' || $tag=='I') $this->setFont($this->_currentFont.$this->GetFullFontName($tag));
		if($tag=='U')
			$this->SetStyle($tag,true);
		if($tag=='A')
			$this->HREF=$attr['HREF'];
		if($tag=='BR')
			$this->Ln(5);
	}

	function CloseTag($tag)
	{
		//Closing tag
		//if($tag=='B' or $tag=='I' or $tag=='U')
		
//echo "closetag -> SETTING FONT TO: ".$this->_currentFont."<BR>";		
		
		if($tag=='B' || $tag=='I') $this->setFont($this->_currentFont);
		if($tag=='U')
			$this->SetStyle($tag,false);
		if($tag=='A')
			$this->HREF='';
	}

	function SetStyle($tag,$enable)
	{
		//Modify style and select corresponding font
		$this->$tag+=($enable ? 1 : -1);
		$style='';
		foreach(array('B','I','U') as $s)
			if($this->$s>0)
				$style.=$s;
		$this->SetFont('',$style);
	}

	function PutLink($URL,$txt,$depth = 5)
	{
		//Put a hyperlink
		$this->SetTextColor(0,0,255);
		$this->SetStyle('U',true);
		$this->Write($depth,$txt,$URL);
		$this->SetStyle('U',false);
		$this->SetTextColor(0);
	}
	
	function LoadJSON($file, $echoit = false)
	{
//		echo "<BR><BR>----------<BR><BR>";
		$jsonData = file_get_contents($file);
		
		//echo $jsonData.'<BR><BR>';
		
		//$jsonData = str_replace("\\u00", "\xDF", $jsonData);
		
//		var_dump(json_decode(substr(utf8_decode($jsonData),1)));
		
		//var_dump(json_decode($jsonData));
		
		return json_decode($jsonData);
		
//		return json_decode(substr($jsonData,3));
	}
	
	function LoadTranslation($file)
	{
		$this->specialTranslationObject = new StdClass;
		
		$datastr=file_get_contents($file);
		$datastr=str_replace("\n","",$datastr);
		$datastr=str_replace("\r","",$datastr);		
		$datastr=str_replace(",,,;;;","",$datastr);	
		
		$data=explode(";;;",$datastr);
		for($n = 1; $n < count($data) - 1; $n++)
		{
			$item=explode(",,,",$data[$n]);
			//echo "$item[0] ,,, $item[1] ;;;";
			if(count($item)==2) $this->specialTranslationObject->$item[0] = $item[1];
		}
		//foreach($this->specialTranslationObject as $obj) echo $obj;
		
	}

	function setElementStyles($textstyles)
	{
		$this->pdfElements = array();
		foreach ($textstyles as $textStyle)
		{
			$this->pdfElements[$textStyle->style] = new PDFElement($textStyle->r,$textStyle->g,$textStyle->b,$textStyle->size,$textStyle->font, $textStyle->attributes);
		}
	}
	
	function SetTranslations($translations)
	{
		foreach($translations as $transElement) $this->translations[$transElement->id]=$transElement;
	}
	
	
	function LayoutTitles($layouts)
	{

		foreach($layouts as $area)
		{


			$transText = "";
			
			foreach($this->translations as $transElement)
			{
				if($transElement->id == $area->id) 
				{
					$id_ = $area->id;
				
					$transText = $transElement->text;
					
					//echo "ID>>>". property_exists ($this->specialTranslationObject, $id_);
					if(property_exists ($this->specialTranslationObject, $id_)) $transText = $this->specialTranslationObject->$id_;
					
					//echo "ID>>>$this->specialTranslationObject->$id_";
					
					
//echo "LayoutTitles...";					
					$this->setFontByElement($this->pdfElements[$transElement->textstyle]);

		
					switch($transElement->type)
					{
						case "hotspot":
							$this->PlaceAreaText($area->x, $area->y, $area->width, $transText, true, 4);
							break;
						case "regular_text":
							$this->PlaceAreaTextCell($area->x, $area->y, $area->width, $transText, false, 4);
							break;
						case "center_align":
							$this->PlaceAreaTextCell($area->x, $area->y, $area->width, $transText, false, 4, "C");
							break;
						case "vertical":
							$this->MakeVerticalText($transText, $area->x, $area->y);
							break;
						default:
							$this->PlaceAreaTextCell($area->x, $area->y, $area->width, $transText, false, $area->height);
							break;
					}
					
				}
			}		
		}
	}

	function SetUserText($page, $id, $text)
	{
		$area = $this->layouts[$page][$id];
		$transElement = $this->translations[$id];
		$transText = $text;
		
		$this->setFontByElement($this->pdfElements[$transElement->textstyle]);
		

		
		switch($transElement->type)
		{
			case "hotspot":
				$this->PlaceAreaText($area->x, $area->y, $area->width, $transText, true, 4);
				break;
			case "regular_text":
				$this->PlaceAreaTextCell($area->x, $area->y, $area->width, $transText, false, 4);
				break;
			case "center_align":
				$this->PlaceAreaTextCell($area->x, $area->y, $area->width, $transText, false, 4, "C");
				break;	
			case "link":
				$this->PlaceAreaText($area->x, $area->y, $area->width, $transText, false, 4);
				break;
			case "vertical":
				$this->PlaceAreaText($area->x, $area->y, $area->width, $transText, false, 4);
				break;
			default:
				$this->PlaceAreaTextCell($area->x, $area->y, $area->width, $transText, false, $area->height);
				break;
		}		
	}
	
	
	function MakeChart($x, $y, $bar1col, $bar2col, $barWidth, $smallSpacing, $largeSpacing, $height, $maxValue, $data, $regionVal)
	{
		$this->SetDrawColor(0);
		
		
		if($regionVal != "total")
		
		{
			for($n = 0; $n < count($data); $n++)
			{
				$arr = $data[$n];

				$this->SetFillColor($bar1col[0], $bar1col[1], $bar1col[2]);
				$barHeight = $height * ($arr[0]/$maxValue);
				$this->Rect($x, $y + $height - $barHeight, $barWidth, $barHeight, "FD");
				
				$x += $barWidth + $smallSpacing;

				$this->SetFillColor($bar2col[0], $bar2col[1], $bar2col[2]);
				$barHeight = $height * ($arr[2]/$maxValue);
				$this->Rect($x, $y + $height - $barHeight, $barWidth, $barHeight, "FD");
				
				$x += $barWidth + $largeSpacing;		
			}
		}
		
		else
		{
			for($n = 0; $n < count($data); $n++)
			{
				$arr = $data[$n];

				$this->SetFillColor($bar2col[0], $bar2col[1], $bar2col[2]);
				$barHeight = $height * ($arr[0]/$maxValue);
				$this->Rect($x, $y + $height - $barHeight, $barWidth * 2 + $smallSpacing, $barHeight, "FD");

				$x += $barWidth * 2 + $smallSpacing + $largeSpacing;
			}
			
			$this->SetDrawColor(255);
			$this->SetFillColor(255,255,255);
			$this->Rect(158.875, 116, 46, 7.125, "FD");
			
		}
	}
	

	function SetQuestionAndAnswer($subQ, $answer, $noResponseText = "No response.")
	{

//	function PlaceAreaTextCell($x, $y, $width, $text, $bulletPoint=false, $writeDepth=4, $align="L")
			//$this->PlaceAreaTextCell(5, -1, 180, $subQ->question_textLabel);
			
			if($this->GetY() > 245)
			{
				$this->addPage();
				$this->useTemplate($this->currentTemplate, 0,0,215.9);
				$this->layoutTitles($this->layouts[2], $this->currentLangObj);
			}			
			
			$this->SetFontForTFPDF ($this->defaultFont, '', 12);
			
			$this->SetX(5);
			$y = $this->GetY();
			

			$questionString = $this->GetTranslation($subQ->question_numberLabel);
			$this->MultiCell(20, 5, $questionString . ".", 0, "R");
			
			$this->SetLeftMargin(25);
			$this->SetY($y);
			
			$textLabel = $this->GetTranslation($subQ->question_textLabel);
			$this->MultiCell(170, 5, $textLabel, 0, "L");
			
			$this->SetFontForTFPDF ($this->defaultFont, 'B', 12);
			for($ans = 0; $ans < count($answer); $ans++)
			{
				if ($answer[$ans] == "SUBQUESTIONS FOLLOW") break;
				$ansText = (count($answer) > 1) ? " ".(string)($ans + 1).": " : ": ";
				$this->MultiCell(170, 5, $this->GetTranslation("_ANSWER_") . $ansText . $this->GetTranslation($answer[$ans]), 0, "L");
			}
			if (count($answer) == 0 ) $this->MultiCell(170, 5, $this->GetTranslation("_NO RESPONSE_"), 0, "L");
			
			$this->MultiCell(170, 5, "", 0, "L");

	}
	
	
	function GetTranslation($id)
	{
		$questID=$id;
		$questionString = $questID;
		if(property_exists ($this->specialTranslationObject, $questID)) $questionString = $this->specialTranslationObject->$questID;
		return $questionString;
	}			
	
	
	
	function SetSubquestionAndAnswer($subquestionText, $answerText)
	{
		if($this->GetY() > 245)
		{
			$this->addPage();
			$this->useTemplate($this->currentTemplate, 0,0,215.9);
			$this->layoutTitles($this->layouts[2], $this->currentLangObj);
			

		}	
		$this->SetFontForTFPDF ($this->defaultFont, '', 12);
		$this->SetLeftMargin(35);
		$this->SetX(35);
		$this->MultiCell(155, 5, $this->GetTranslation($subquestionText), 0, "L");
		
		$this->SetFontForTFPDF ($this->defaultFont, 'B', 12);
		$this->MultiCell(155, 5, $this->GetTranslation("_ANSWER_").": $answerText", 0, "L");
		$this->MultiCell(155, 5, "", 0, "L");
	}
	
		
	function getCorrects($ansTextR, $userArray, $startIndex)
	{
		//var_dump($ansTextR);
		$output = array();
		for($i = $startIndex; $i < count($ansTextR); $i++)
		{
			if($userArray[$i] == 1) array_push($output, $ansTextR[$i]);
		}
		//echo "CORRECT COUNT: ".count($output)."<br>";
		//var_dump($output);
		
		return $output;
	}	
	
	

	function Rotate($angle,$x=-1,$y=-1)
	{
		if($x==-1)
			$x=$this->x;
		if($y==-1)
			$y=$this->y;
		if($this->angle!=0)
			$this->_out('Q');
		$this->angle=$angle;
		if($angle!=0)
		{
			$angle*=M_PI/180;
			$c=cos($angle);
			$s=sin($angle);
			$cx=$x*$this->k;
			$cy=($this->h-$y)*$this->k;
			$this->_out(sprintf('q %.5F %.5F %.5F %.5F %.2F %.2F cm 1 0 0 1 %.2F %.2F cm',$c,$s,-$s,$c,$cx,$cy,-$cx,-$cy));
		}
	}


	function MakeVerticalText($text, $x, $y)
	{
		$this->Rotate(90, $x, $y);
		$this->setXY($x,$y);
		$this->Write(5, $text);
		$this->Rotate(0);
	}
	
	function makePin($length) 
	{ 
		$aZ09 = array_merge(range('A', 'Z'), range('a', 'z'),range(0, 9)); 
		$out =''; 
		for($c=0;$c < $length;$c++) 
		{ 
		   $out .= $aZ09[mt_rand(0,count($aZ09)-1)]; 
		} 
		return $out; 
	}


	
	
	
}


class PDFElement
{
		public $r;
		public $g;
		public $b;
		public $type;
		public $size;
		public $fontName;
		public $attributes;
		
		function __construct($r, $g, $b, $size, $fontName = 'Arial', $attributes = "")
		{
			$this->r = $r;
			$this->g = $g;
			$this->b = $b;
			$this->fontName = $fontName;
			$this->type = $type;
			$this->size = $size;		
			$this->attributes = $attributes;
		}
}



////////////////    END OF CLASSES    ////////////////////////////////////////////////////////////





//DOWNLOADS OR E-MAILS AT THIS POINT