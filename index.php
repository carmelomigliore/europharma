<!doctype html>
<html lang=''>
<head>
   <meta charset='utf-8'>
   <meta http-equiv="X-UA-Compatible" content="IE=edge">
   <meta name="viewport" content="width=device-width, initial-scale=1">
   <link rel="stylesheet" href="styles.css">
   <script src="http://code.jquery.com/jquery-latest.min.js" type="text/javascript"></script>
   <script src="script.js"></script>
   <title>Europharma</title>
</head>
<body>

<div id='cssmenu'>
<ul>
   <li class='active'><a href='#'>Home</a></li>
   <li><a href='#'>Products</a></li>
   <li><a href='#'>Company</a></li>
   <li><a href='#'>Contact</a></li>
</ul>
</div>

<div>
<?php
$section = $_GET['section'];
switch($section){
	case 'agenti': include ('agentlist.php'); 
		       break;
	case 'viewagent': include ('viewagent.php'); 
		       break;
	case 'prodotti': include ('productlist.php'); 
		       break;
	case 'insertproduct': include ('addproduct.php'); 
		       break;
	case 'addagentarea': include ('addagentarea.php'); 
		       break;
	case 'addagentproduct': include ('addagentproduct.php'); 
		       break;
	case 'addagent' : include('addagent.php');
		       break;
}


?>
</div>

</body>
<html>
