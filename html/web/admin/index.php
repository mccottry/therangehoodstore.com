<?php		

		session_start();

		if(!isset($_SESSION['MM_Username'])){		

			header('Location: login.php');			

		}

?>

<?php





  require('includes/application_top.php');

?>

<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">

<html xmlns="http://www.w3.org/1999/xhtml">

<head>

<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />

<title>TA PACKAGE</title>

<script type="text/javascript">

<!--

function MM_goToURL() { //v3.0

  var i, args=MM_goToURL.arguments; document.MM_returnValue = false;

  for (i=0; i<(args.length-1); i+=2) eval(args[i]+".location='"+args[i+1]+"'");

}

//-->

</script>

</head>



<body onload="MM_goToURL('parent','login.php');return document.MM_returnValue">



</body>

</html>

