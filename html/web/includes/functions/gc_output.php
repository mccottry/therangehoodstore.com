<?php
/*
  $Id: gc_output.php,v 1.0 2007/10/25

  Copyright (c) 2007 Ken Owen

  Released under the GNU General Public License
*/

////
//create a gift certificate image
//from a postscript template and output a JPEG
//gc_image($gc_to, $gc_from, $gc_price, $order->info['comments'], $order->info['gc_code'], $customer_id);
function gc_image($to, $from, $price, $message, $code, $custno){
	setlocale(LC_MONETARY, 'en_US');
	umask(0002);
	$gc_file=DIR_WS_IMAGES . 'gift_certificate.ps';
	$lines=file($gc_file);
	$fintermediate=fopen(DIR_FS_CATALOG . DIR_WS_GCIMAGES . gc_customer_directory($custno) . "/" . $code . ".ps", "w");

	//if the message is too long, back it into to lines
	$message2='';
	if (strlen($message)>85){
		$linebreak=strcspn(substr($message,60)," ");
		$message2=substr($message,$linebreak + 60);
		$message=substr($message, 0, $linebreak + 60);
		if (strlen($message2)>85){
			$linebreak1=strcspn(substr($message2,60)," ");
                	$message3=substr($message,$linebreak1 + 60);
                	$message2=substr($message2, 0, $linebreak1 + 60);
		}
	}
	$repl_to="(To: " . $to . ")";
	$repl_from="From: " . $from;
	$repl_price="Amount: \\" . money_format('%n',$price);
	$repl_message="Message: " . $message;
	$repl_code="Certificate Code: " . $code;
	$repl_doi="Date of Issue: " . date("m/d/Y");
	//make the expiration date one year from now
	$exp_date=date("m/d/Y", mktime(0,0,0,date("m"), date("d"), date("Y") + 1));
	$repl_expire="Expiration Date: " . $exp_date;
	$repl_border="/bordercolor {" . GC_BORDERCOLOR . "} def";
	$repl_typecolor="/typecolor {" . GC_TYPECOLOR . "} def";
	$repl_giftcopy="/giftcopy {(" . GC_GIFTCOPY . ")} def";
	foreach($lines as $key=>$line){
		$new=$line;
		$new2='';
		$new3='';
		if(preg_match('/\(TO\)/', $line)){
			$new=preg_replace('/\(TO\)/', $repl_to, $line);
		}
		if(preg_match('/\FROM/', $line)){
			$new=preg_replace('/FROM/', $repl_from, $line);
		}
		if(preg_match('/AMOUNT/', $line)){
			$new=preg_replace('/AMOUNT/', $repl_price, $line);
			//exit;
		}
		if(preg_match('/MESSAGE/', $line)){
			//echo "message=" . $message . " and message2=" . $message2 . "<br>";
				$new=preg_replace('/MESSAGE/', $repl_message, $line);
			if(strlen($message2)>0){
				$new2="($message2) 42 -208 regtype";
			}
			if(strlen($message3)>0){
                                $new3="($message3) 42 -224 regtype";
                        }
		}
		if(preg_match('/DATEOFISSUE/', $line)){
			$new=preg_replace('/DATEOFISSUE/', $repl_doi, $line);
		}
		if(preg_match('/CERTIFICATECODE/', $line)){
			$new=preg_replace('/CERTIFICATECODE/', $repl_code, $line);
		}
		if(preg_match('/EXPIRATIONDATE/', $line)){
			$new=preg_replace('/EXPIRATIONDATE/', $repl_expire, $line);
		}
		if(preg_match('/BORDERCOLOR/', $line)){
                        $new=preg_replace('/BORDERCOLOR/', $repl_border, $line);
                }
                if(preg_match('/TYPECOLOR/', $line)){
                        $new=preg_replace('/TYPECOLOR/', $repl_typecolor, $line);
                }
                if(preg_match('/GIFTCOPY/', $line)){
                        $new=preg_replace('/GIFTCOPY/', $repl_giftcopy, $line);
                }
		fwrite($fintermediate, $new);
		if(strlen($new2)>0){
			fwrite($fintermediate, $new2);
		}
	}	
	fclose($fintermediate);
//postscript version
	$returnfile= gc_image_name($code, time(), $custno) . ".jpg";
	$ps2jpg="gs -sOutputFile=" . DIR_FS_CATALOG . DIR_WS_GCIMAGES . gc_customer_directory($custno) . "/" . $code . ".jpg -sDEVICE=jpeg -r300 -dNOPAUSE -dBATCH " . DIR_FS_CATALOG . DIR_WS_GCIMAGES . gc_customer_directory($custno) . "/" . $code . ".ps";
	error_log($ps2jpg);
	exec($ps2jpg);
	$compstring="composite -blend 80 " . DIR_FS_CATALOG . DIR_WS_IMAGES . GCLOGO . " -geometry +678+865 " . DIR_FS_CATALOG . DIR_WS_GCIMAGES . gc_customer_directory($custno) . "/" . $code . ".jpg " . DIR_FS_CATALOG . DIR_WS_GCIMAGES . gc_customer_directory($custno) . "/" . $returnfile;
	error_log($compstring);
	exec($compstring);
        exec("rm " .  DIR_FS_CATALOG . DIR_WS_GCIMAGES . gc_customer_directory($custno)  . "/" . $code . ".jpg "  .  DIR_FS_CATALOG . DIR_WS_GCIMAGES . gc_customer_directory($custno) . "/" . $code . ".ps");

	return $returnfile;
}

///
//function to create the gift certificate jpg name
function gc_image_name($code, $pdate, $custno){
	//the name is made up from:
	//	the code in reverse and the pdate (mmddyyyy), 'X',  and the first 2 digits of the customer number
	//
	$imagename='';
	if(strlen($custno)<2){
		$custno='0' . $custno;
	}
	$formatdate=date('mdY', $pdate);
	$formatdate.='X';
	$formatdate.=substr($custno,0,2);
	for($i=10;$i>=0;$i--){
		$imagename.=substr($formatdate,$i,1);	
		$imagename.=substr($code,$i,1);
	}
	return $imagename;
}

///
//function to set up the customer gc directory
function gc_customer_directory($custno){
	echo "DIR_WS_GCIMAGES=" . DIR_WS_GCIMAGES .  " custno=" . $custno . " directory should be " . DIR_WS_GCIMAGES . $custno . "<br>";
	$newdir=DIR_FS_CATALOG . DIR_WS_GCIMAGES . $custno;
	if(!chdir($newdir)){
		mkdir(DIR_WS_GCIMAGES . $custno, 0777);
	}
	error_log(exec("pwd"));
	return $custno;
}