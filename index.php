<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="en" lang="en">
<head>
<title>Europharma management</title>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<link href="style.css" rel="stylesheet" type="text/css" />
<link rel="stylesheet" href="tableagentlist.css" type="text/css"/>
<!--NEW -->
<link href="http://fonts.googleapis.com/css?family=Source+Sans+Pro:200,300,400,600,700,900|Quicksand:400,700|Questrial" rel="stylesheet" />
<!--IMPORTANT FONT -->

<!--END -->	
</head>
<body>
<div id="header">
  <h1><a href="index.php?section=agenti"><img src="images/euro-ellisse.png" alt="" /></a></h1>
</div>
<div id="container">
  <div id="navcontainer">
    <ul id="nav">
      <li id="nav-1"><a href="index.php?section=agenti">Collaboratori</a></li>
      <li id="nav-2"><a href="index.php?section=prodotti">Prodotti</a></li>
      <li id="nav-3"><a href="index.php?section=caricodati">Gestione Dati</a></li>
      <li id="nav-4"><a href="index.php?section=globalstats&action=form">Statistiche</a></li>
	 <li id="nav-5"><a href="index.php?section=enasarco&action=form">Enasarco</a></li>	
    </ul>
  </div>
  <div id="contentleft" align="center">
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
	case 'addagentproducttarget': include('addagentproducttarget.php');
			break;
	case 'caricodati' : include ('imsupload.php');
			break;
	case 'globalstats' : include('globalstats.php');
			break;
	case 'fattura' : include ('fattura.php');
			break;
	case 'statistiche' : include ('stats.php');
			break;

	case 'enasarco' : include ('enasarco.php'); //prendo anni in storico e faccio spinner, tabella con agenti with enasarco (nome,cognome,cf, 1,2,3,4 trimestre, firr, indennità di clientela). Per ogni trimestre e per ogni agente devo calcolare questa puttanata:
// prendo l'imponibile nel trimestre (come prima), calcolo enasarco con nuovo imponibile, se < minimale -> allora enasarco = minimale altrimenti enasarco è quello calcolato poco prima. In questo caso $credito = minimale - enasarcocalcolato. Altro caso sarebbe se enasarco > massimale allora enasarco = massimale (logica di prima in agent.php).
// Nei trimestri successivi al primo, se enasarco > massimale -> enasarco = enasarco - credito, if (enasarco < minimale) -> credito = minimale - enasarco; enasarco = minimale;
// firr= imponibile di tutto l'anno; if imponibile < 6200 -> firr = imponibile*0,04. Else if(imponibile < 9300) firr = 6200*0,04 + (imponibile-6200)*0,02), Else firr = 6200*0,04 + (9300*0,02) + (imponibile-9300)*0,01.
// indennità di clientela. Prendo Imponibile annuale, si calcola quanti anni solari di lavoro (anno scelto - anno di inizio contratto). 
// if anni di lavoro > qualcosa -> indennità = imponibileannuale*(unapercentuale).
			break;
	default: include ('agentlist.php'); 
}


?>
<!--
    <h1>Welcome to <span style="font-weight:bold; color:#C4DA64;">Shades of Grey</span> Template</h1>
    <p><img class="imgleft" src="images/info.png" alt="" /><strong> This template has been tested in Mozilla Firefox and IE7. The page validates as XHTML 1.0 Transitional using valid CSS. It will work in browser widths of 800x600, 1024x768 &amp; 1280x1064. The images used in this template are courtesy of <a href="http://www.sxc.hu/" title="free images">stock xchng</a>. The top navigation menu is from <a href="http://www.snook.ca/archives/html_and_css/improved_bullet/" title="Article:Bulletproof Slants">Article:Bulletproof Slants</a> &amp; the icons are from <a href="http://www.MouseRunner.com" title="free resources"> Mouse Runner</a>.</strong></p>
    <p><strong><em>For more FREE CSS templates visit <a href="http://www.mitchinson.net">my website</a>.</em></strong></p>
    <p>Lorem ipsum dolor sit amet, consectetuer adipiscing elit. Quisque libero orci, interdum id, tempus a, gravida sed, odio. In tincidunt, turpis eu dignissim malesuada, urna massa tincidunt sem, a pharetra turpis nisi sit amet ante. Donec accumsan, diam nec nonummy fringilla, magna quam sollicitudin tellus, quis dignissim lorem dolor non dolor. Fusce vitae arcu. Duis sodales rutrum quam. Aenean nisi. Nullam vel justo sit amet tortor ullamcorper tempor. Praesent fermentum, massa ac tincidunt semper, massa elit malesuada ipsum, euismod commodo tortor arcu iaculis metus. Etiam hendrerit libero eu ante. Cras elit lectus, adipiscing a, sollicitudin eget, varius non, ipsum. Praesent ac massa. Duis pellentesque. Maecenas sit amet risus in purus scelerisque molestie. Sed posuere ullamcorper velit.</p>
    <p>Nullam pulvinar sagittis nisl. Pellentesque habitant morbi tristique senectus et netus et malesuada fames ac turpis egestas. Morbi luctus eros eu eros. Duis blandit nonummy risus. Pellentesque et nisl in quam ultrices ultricies. Proin egestas, massa ac consectetuer imperdiet, nibh elit ultricies turpis, at consequat eros nibh vitae felis. In blandit. In sit amet orci quis tellus hendrerit tempus. Donec porttitor massa id nulla. Nunc sem metus, dapibus id, hendrerit vitae, auctor nec, risus.</p>
  </div>
  <div id="contentright">
    <h2>Header</h2>
    <ul>
      <li><a href="#"><img src="images/home.png" alt="homepage" /></a></li>
      <li><a href="#"><img src="images/magnifier.png" alt="search" /></a></li>
      <li><a href="#"><img src="images/mail.png" alt="contact" /></a></li>
    </ul>
    <p>Lorem ipsum dolor sit amet, consectetuer adipiscing elit. Quisque libero orci, interdum id, tempus a, gravida sed, odio. Maecenas sit amet risus in purus scelerisque molestie. <a href="#"> Read more</a> &raquo; </p>
  </div> -->
</div>
<!--<div id="footer">
  <div id="bottom">
    <div class="col3center">
      <h1>Web Resources</h1>
      <div class="navlist">
        <ul>
          <li><a href="#">Open Designs</a></li>
          <li><a href="#">Open Web Design</a></li>
          <li><a href="#">CSS Drive</a></li>
        </ul>
      </div>
    </div>
    <div class="col3">
      <h1>Links</h1>
      <div class="navlist">
        <ul>
          <li><a href="#">BBC News Frontpage</a></li>
          <li><a href="#">Hartlepool Today</a></li>
          <li><a href="#">SV Horizons Travelblog</a></li>
        </ul>
      </div>
    </div>
    <div class="col3">
      <h1>More Links</h1>
      <div class="navlist">
        <ul>
          <li><a href="#">Dynamic Drive CSS Library</a></li>
          <li><a href="#">Listamatic</a></li>
          <li><a href="#">CSS Play</a></li>
        </ul>
      </div>
    </div>
    <a href="#">homepage</a> | <a href="mailto:denise@mitchinson.net">contact</a> | &copy; 2007 Anyone | Design by <a href="http://www.mitchinson.net"> www.mitchinson.net</a> | Licensed under a <a rel="license" href="http://creativecommons.org/licenses/by/3.0/">Creative Commons Attribution 3.0 License</a> </div>
</div> -->
</body>
</html>
