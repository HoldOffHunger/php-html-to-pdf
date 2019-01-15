<?php
						
	// PHP HTML-to-PDF Converter
	// Author: holdoffhunger
	// Source code released under: BSD-3-Clause License
	// -----------------------------------------------------
	// -----------------------------------------------------

/*
			Japanese Example
			--------------------------------------

	require('../app/fpdf-write-html/fpdf-write-html.php');
	
	$pdf_object = new HTMLtoPDF([
		'Author'=>'HoldOffHunger',
		'Title'=>'Privacy Policy',
	]);
		
	WriteHTML([
		'html'=>'<h1>I can say hello in 日本人!</h2><p>こんにちは!</p>',
		'language'=>'ja',
	]);

			Chinese Example
			--------------------------------------

	require('../app/fpdf-write-html/fpdf-write-html.php');
	
	$pdf_object = new HTMLtoPDF([
		'Author'=>'HoldOffHunger',
		'Title'=>'Privacy Policy',
	]);
		
	WriteHTML([
		'html'=>'<h1>I can say hello in 汉语!</h2><p>你好!</p>',
		'language'=>'zh',
	]);

			Englsh Example
			--------------------------------------

	require('../app/fpdf-write-html/fpdf-write-html.php');
	
	$pdf_object = new HTMLtoPDF([
		'Author'=>'HoldOffHunger',
		'Title'=>'Privacy Policy',
	]);
		
	WriteHTML([
		'html'=>'<h1>I can say hello in English!</h2><p>Hello!</p>',
		'language'=>'en',
	]);
	
*/

	require('../app/tfpdf/tfpdf.php');
	
	class HTMLtoPDF extends tFPDF {
		protected $HREF;
				
					// Constructor
					// -----------------------------------------------------
					// -----------------------------------------------------
					// -----------------------------------------------------
		
		function __construct($args) {
			$this->constructParent($args);
			$this->setMetaData($args);
			$this->setInitialState();
			
			return $this;
		}
		
		function constructParent($args) {
			if($args['orientation']) {
				$orientation = $args['orientation'];
			} else {
				$orientation = $this->getDefaultOrientation();
			}
			
			if($args['unit']) {
				$unit = $args['unit'];
			} else {
				$unit = $this->getDefaultUnit();
			}
			
			if($args['format']) {
				$format = $args['format'];
			} else {
				$format = $this->getDefaultFormat();
			}
			parent::__construct($orientation, $unit, $format);
			
			return TRUE;
		}
		
		function setMetaData($args) {
			if($args['Title']) {
				$this->SetTitle($args['Title'], TRUE);
			}
			
			if($args['Author']) {
				$this->SetAuthor($args['Author'], TRUE);
			}
			
			if($args['Subject']) {
				$this->SetSubject($args['Subject'], TRUE);
			}
			
			if($args['Keywords']) {
				$this->SetKeywords($args['Keywords'], TRUE);
			}
			
			if($args['Creator']) {
				$this->SetCreator($args['Creator'], TRUE);
			}
			
			return TRUE;
		}
		
		function setInitialState() {
			$this->HREF = '';
			$this->issetcolor = FALSE;
			
			return TRUE;
		}
		
		function getSupportedFontsList() {
			if($this->font_list) {
				return $this->font_list;
			}
			
			$supported_fonts = $this->getSupportedFonts();
			
			$supported_fonts_list = [];
			
			foreach($supported_fonts as $supported_font) {
				$supported_fonts_list[$supported_font] = TRUE;
			}
			
			return $this->font_list = $supported_fonts_list;
		}
		
		function getSupportedFonts() {
			return [
				'arial',
				'times',
				'courier',
				'helvetica',
				'symbol',
			];
		}
		
					// Main Function to Call
					// -----------------------------------------------------
					// -----------------------------------------------------
					// -----------------------------------------------------
		
		function WriteHTML($args) {
			$html = $args['html'];
			$language = $args['language'];
			
			$this->setHTMLFonts(['language'=>$language]);
			$this->setStartingFont();
			$this->setStartingMargins();
			
			$this->WriteHTMLContent(['html'=>$html]);
			
			return TRUE;
		}
		
					// Prepare the Data
					// -----------------------------------------------------
					// -----------------------------------------------------
					// -----------------------------------------------------
		
		function WriteHTMLContent($args) {
			$html = $args['html'];
			
			$html = $this->cleanseInput(['input'=>$html]);
			
			$dom = $this->buildDom(['html'=>$html]);
			
			return $this->parseDom([dom=>$dom]);
		}
		
		function cleanseInput($args) {
			$input = $args['input'];
			
			$input = strip_tags($input,$this->getSupportedHTMLTags());
			$input = preg_replace('/[\r\n]+/', ' ', $input);
			
			return $input;
		}
		
		function buildDom($args) {
			$html = $args['html'];
			
			$dom = preg_split('/<(.*)>/U', $html, -1, PREG_SPLIT_DELIM_CAPTURE);
			
			return $dom;
		}
		
		function setHTMLFonts($args) {
			$language = $args['language'];
			
			switch($language) {
				case 'zh':
					$this->AddFont('chinese','','chinese.ttf',true);
					break;
					
				case 'ja':
					$this->AddFont('japanese','','japanese.ttf',true);
					break;
			}
			
			$this->AddFont('DejaVu','','DejaVuSansCondensed.ttf',true);
			$this->AddFont('DejaVu','Bold','DejaVuSans-Bold.ttf',true);
			$this->AddFont('DejaVu','Italic','DejaVuSans-Oblique.ttf',true);
			
			$this->base_font = $this->getBaseFont(['language'=>$language]);
			$this->bold_font = $this->getBoldFont(['language'=>$language]);
			$this->italics_font = $this->getItalicsFont(['language'=>$language]);
			
			return TRUE;
		}
		
		function setStartingFont() {
			$this->SetFont($this->base_font, '', 14);
			$this->line_height = 14;
			
			return TRUE;
		}
		
		function setStartingMargins() {
			$this->AddPage();
			$this->SetTopMargin(10);
			$this->SetLeftMargin(10);
			$this->SetRightMargin(10);
			
			return TRUE;
		}
		
		function getSupportedHTMLTags() {
			$supported_tags = '';
			
			foreach($this->getSupportedHTMLTagsList() as $tag) {
				$supported_tags .= '<' . $tag . '>';
			}
			
			return $supported_tags;
		}
		
					// Parse the Data
					// -----------------------------------------------------
					// -----------------------------------------------------
					// -----------------------------------------------------
			
		function parseDom($args) {
			$dom = $args['dom'];
			
			$is_text_content = TRUE;
			
			foreach($dom as $i => $dom_piece) {
				if($is_text_content) {
					$this->parseDom_handleContent([
						'text'=>$dom_piece,
					]);
				} else {
					$this->parseDom_handleTag([
						'tag'=>$dom_piece,
					]);
				}
				$is_text_content = !$is_text_content;
			}
			return TRUE;
		}
		
		function parseDom_handleContent($args) {
			$text = $args['text'];
			
			if($this->HREF) {
				$this->parseDom_handleContent_link(['text'=>$text]);
			} else {
				$this->parseDom_handleContent_text(['text'=>$text]);
			}
			
			return TRUE;
		}
		
		function parseDom_handleContent_text($args) {
			$text = $args['text'];
			
			if($this->li) {
				$text = '&bull; ' . $text;
				$this->li = FALSE;
			}
			
			$this->Write($this->line_height,stripslashes($this->adjustHTMLEntities($text)));
			
			return TRUE;
		}
		
		function parseDom_handleContent_link($args) {
			$text = $args['text'];
			
			$URL = $this->HREF;
			$this->SetTextColor(0,0,255);
			$this->underline = TRUE;
			$this->Write($this->line_height,$text,$URL);
			$this->underline = FALSE;
			$this->SetTextColor(0);
			$this->HREF = FALSE;
			
			return TRUE;
		}
		
		function parseDom_handleTag($args) {
			$tag = $args['tag'];
	
			if($tag[0] == '/') {
				$this->parseDom_handleTag_closingTag($args);
			} else {
				$this->parseDom_handleTag_openingTag($args);
			}
			
			return TRUE;
		}
		
		function parseDom_handleTag_closingTag($args) {
			$tag = $args['tag'];
			$this->CloseTag(strtoupper(substr($tag,1)));
			
			return TRUE;
		}
		
		function parseDom_handleTag_openingTag($args) {
			$tag = $args['tag'];
			
			$tag_pieces = explode(' ',$tag);
			$tag = strtoupper(array_shift($tag_pieces));
			
			$attributes = $this->getAttributesFromTag(['tag_pieces'=>$tag_pieces]);
			$this->OpenTag($tag, $attributes);
			
			return TRUE;
		}
		
		function getAttributesFromTag($args) {
			$tag_pieces = $args['tag_pieces'];
			
			$attributes = [];
			
			foreach($tag_pieces as $tag_piece) {
				if(preg_match('/([^=]*)=["\']?([^"\']*)/', $tag_piece, $tag_piece_values)) {
					$attributes[strtoupper($tag_piece_values[1])] = $tag_piece_values[2];
				}
			}
			
			return $attributes;
		}
		
					// Apply Tag Stylings to the Data
					// -----------------------------------------------------
					// -----------------------------------------------------
					// -----------------------------------------------------
		
		function OpenTag($tag, $attr) {
			$tag = $this->swapTag(['tag'=>$tag]);
			
			switch($tag) {
				case 'B':
					$this->SetFont($this->bold_font);
					break;
					
				case 'I':
					$this->SetFont($this->italics_font);
					break;
					
				case 'U':
					$this->underline = true;
					break;
					
				case 'A':
					if(!$attr['NAME']) {
						$this->HREF = $attr['HREF'];
					}
					break;
					
				case 'IMG':
					if(isset($attr['SRC']) && (isset($attr['WIDTH']) || isset($attr['HEIGHT']))) {
						if(!isset($attr['WIDTH'])) {
							$attr['WIDTH'] = 0;
						}
						
						if(!isset($attr['HEIGHT'])) {
							$attr['HEIGHT'] = 0;
							$this->Image($attr['SRC'], $this->GetX(), $this->GetY(), $this->convertPixelToMM(['pixels'=>$attr['WIDTH']]), $this->convertPixelToMM(['pixels'=>$attr['HEIGHT']]));
						}
					}
					$this->Ln(20);
					break;
					
				case 'FONT':
					$font_list = $this->getSupportedFontsList();
					if (isset($attr['COLOR']) && $attr['COLOR']!='') {
						$colors = $this->convertHTMLColorToNumeric(['color'=>$attr['COLOR']]);
						$this->SetTextColor($colors['red'], $colors['green'], $colors['blue']);
					}
					if (isset($attr['FACE']) && $font_list[strtolower($attr['FACE'])]) {
						$this->SetFont(strtolower($attr['FACE']));
					}
					break;
		        
			        case 'LI':
			        	$this->li = true;
			        	break;
			        	
			        case 'P':
					$this->SetFont($this->base_font, '', 14);
					$this->line_height = 8;
			        	break;
			        
			        case 'UL':
			        case 'BLOCKQUOTE':
					$this->SetLeftMargin(25);
					$this->SetRightMargin(25);
			        	break;
			        
			        case 'H1':
					$this->SetFont($this->bold_font, '', 40);
					$this->line_height = 20;
			        	break;
			        
			        case 'H2':
					$this->SetFont($this->base_font, '', 30);
					$this->line_height = 15;
			        	break;
			        
			        case 'H3':
					$this->SetFont($this->base_font, '', 25);
					$this->line_height = 12;
			        	break;
			        
			        case 'H4':
					$this->SetFont($this->base_font, '', 20);
					$this->line_height = 10;
			        	break;
			        
			        case 'H5':
					$this->SetFont($this->base_font, '', 18);
					$this->line_height = 9;
			        	break;
			        
			        case 'H6':
					$this->SetFont($this->base_font, '', 16);
					$this->line_height = 8;
			        	break;
			}
		}
		
		function CloseTag($tag) {
			$tag = $this->swapTag(['tag'=>$tag]);
		     	switch($tag) {
		     		case 'B':
		     			$this->SetFont($this->base_font);
		     			break;
		     			
		     		case 'I':
		        		$this->SetFont($this->base_font);
		        		break;
		        	
		        	case 'U':
		        		$this->underline = false;
		        		break;
		        
		        	case 'FONT':
					$this->SetTextColor(0);
					$this->SetFont($this->base_font);
					break;
			        
			        case 'UL':
			        case 'BLOCKQUOTE':
					$this->SetLeftMargin(10);
					$this->SetRightMargin(10);
			        	break;
			}
			
			switch($tag) {
			        case 'BLOCKQUOTE':
			        case 'P':
			        case 'H1':
			        case 'H2':
			        case 'H3':
			        case 'H4':
			        case 'H5':
			        case 'H6':
			        case 'LI':
					$this->Ln($this->line_height);
					break;
			}
			
			return true;
		}
				
					// Defaults
					// -----------------------------------------------------
					// -----------------------------------------------------
					// -----------------------------------------------------
		
		function getSupportedHTMLTagsList() {
			return [
				'a',
				'b',
				'blockquote',
				'br',
				'em',
				'font',
				'h1',
				'h2',
				'h3',
				'h4',
				'h5',
				'h6',
				'i',
				'img',
				'li',
				'p',
				'strong',
				'u',
				'ul',
			];
		}
		
		function getDefaultOrientation() {
			return 'P';
		}
		
		function getDefaultUnit() {
			return 'mm';
		}
		
		function getDefaultFormat() {
			return 'A4';
		}
		
		public function getBaseFont($args) {
			$language = $args['language'];
			
			switch($language) {
				default:
				case 'en':
					$font = 'dejavu';
					break;
				
				case 'ja':
					$font = 'japanese';
					break;
				
				case 'zh':
					$font = 'chinese';
					break;
			}
			
			return $font;
		}
		
		public function getBoldFont($args) {
			$language = $args['language'];
			
			switch($language) {
				default:
				case 'en':
					$font = 'dejavu b';
					break;
				
				case 'ja':
					$font = 'japanese';
					break;
				
				case 'zh':
					$font = 'chinese';
					break;
			}
			
			return $font;
		}
		
		public function getItalicsFont($args) {
			$language = $args['language'];
			
			switch($language) {
				default:
				case 'en':
					$font = 'dejavuITALIC';
					break;
				
				case 'ja':
					$font = 'japanese';
					break;
				
				case 'zh':
					$font = 'chinese';
					break;
			}
			
			return $font;
		}
		
		function swapTag($args) {
			$tag = $args['tag'];
			
			switch($tag){
				case 'STRONG':
					$tag = 'B';
					break;
		     	   	
		     	   	case 'EM':
		     	   		$tag = 'I';
		     	   		break;
		     	}
		     	
		     	return $tag;
		}
				
					// Utilities
					// -----------------------------------------------------
					// -----------------------------------------------------
					// -----------------------------------------------------
		
		function convertPixelToMM($args) {
			$pixels = $args['pixels'];
			
			return $pixels * $this->pixelToMMRatio();
		}
		
		function pixelToMMRatio() {
			if($this->pixeltommratio) {
				return $this->pixeltommratio;
			}
			
			return $this->pixeltommratio = 25.4/72;
		}
		
		function convertHTMLColorToNumeric($args) {
			$color = $args['color'];
			
			$colors = str_split($color, 2);
			
			$table_colors = [
				'red'=>$colors[0],
				'green'=>$colors[1],
				'blue'=>$colors[2],
			];
			
			return $table_colors;
		}
		
		function adjustHTMLEntities($html) {
			$trans = get_html_translation_table(HTML_ENTITIES, ENT_COMPAT, 'UTF-8');
			$trans = array_flip($trans);
			$result = strtr($html, $trans);
			
			return $result;
		}
	}
?>