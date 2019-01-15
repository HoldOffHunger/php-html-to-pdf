<?php
	require('../php-html-to-pdf/php-html-to-pdf.php');
	
	$pdf_object = new HTMLtoPDF([
		'Author'=>'HoldOffHunger',
		'Title'=>'Privacy Policy',
	]);
		
	$pdf_object->WriteHTML([
		'html'=>'<h1>I can say hello in 日本人!</h2><p>こんにちは!</p>',
		'language'=>'ja',
	]);
	
	$pdf_object->Output('file.pdf', "F", TRUE);
	
/*

		Others...

			Chinese Example
			--------------------------------------

	require('../php-html-to-pdf/php-html-to-pdf.php');
	
	$pdf_object = new HTMLtoPDF([
		'Author'=>'HoldOffHunger',
		'Title'=>'Privacy Policy',
	]);
		
	$pdf_object->WriteHTML([
		'html'=>'<h1>I can say hello in 汉语!</h2><p>你好!</p>',
		'language'=>'zh',
	]);

	$pdf_object->Output('file.pdf', "F", TRUE);

			English Example
			--------------------------------------

	require('../php-html-to-pdf/php-html-to-pdf.php');
	
	$pdf_object = new HTMLtoPDF([
		'Author'=>'HoldOffHunger',
		'Title'=>'Privacy Policy',
	]);
		
	$pdf_object->WriteHTML([
		'html'=>'<h1>I can say hello in English!</h2><p>Hello!</p>',
		'language'=>'en',
	]);
	
	$pdf_object->Output('file.pdf', "F", TRUE);
*/
?>