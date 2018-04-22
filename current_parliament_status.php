<?php
	require 'database_information/db.php';
	require_once './templates/IT.php';
	
	if(!($connection =  mysql_connect($hostName,$userName,$userPass)))
	die("failed to connect database");
	if(! mysql_select_db($dbName,$connection))
	die("failed to search in database");
	
	$BNquery = "SELECT * FROM seat_parliament WHERE winner='BN'";
	$PRquery = "SELECT * FROM seat_parliament WHERE (winner='DAP' OR winner='PKR' OR winner='PAS')";
	$ETCquery = "SELECT * FROM seat_parliament WHERE NOT(winner='BN' OR winner='DAP' OR winner='PKR' OR winner='PAS')";
	$PENDquery = "SELECT * FROM seat_parliament WHERE winner IS NULL";
	
	$BNresult = mysql_query($BNquery,$connection);
	$PRresult = mysql_query($PRquery,$connection);
	$ETCresult = mysql_query($ETCquery,$connection);
	$PENDresult = mysql_query($PENDquery,$connection);
	
	$BNcount=mysql_num_rows($BNresult);
	$PRcount=mysql_num_rows($PRresult);
	$ETCcount=mysql_num_rows($ETCresult);
	$PENDcount=mysql_num_rows($PENDresult);
	
	if($BNcount<$PRcount)
		$title="Ini kalilah!";
	else if($BNcount>$PRcount)
		$title="Lain kalilah!";
	else
		$title="Tolonglah! ";
	
	$url="http://crossrt.net/election2013/";
	$shareFacebook='<a target="_blank" title="Share on Facebook" href="http://www.facebook.com/sharer.php?s=100&amp;p[title]=Current Parliament:&amp;p[url]='.$url.'&amp;p[summary]='.$title.' BN '.$BNcount.' vs PR '.$PRcount.'&amp;p[images][0]=http://www.crossrt.net/election2013/images/icon.jpg"><img src="images/share_fb.png" class="share_logo" /></a>';
	$shareTwitter='<a target="_blank" title="Share on Twitter" href="http://twitter.com/share?url='.$url.'&text=%20%23GE13 Current Parliament: BN '.$BNcount.' vs PR '.$PRcount.'"><img src="images/share_twitter.png" class="share_logo"/></a>';
	$shareGoogle='<a target="_blank" title="Share on Google+" href="https://plusone.google.com/_/+1/confirm?hl=en&url='.$url.'"><img src="images/share_googleplus.png" class="share_logo"/></a>';
	
	$currentParliament = new HTML_Template_IT("./templates");
    $currentParliament->loadTemplatefile("current_parliament.tpl",true,true);
	
	$currentParliament->setCurrentBlock("CURRENTPARLIAMENT");
	$currentParliament->setVariable("BNresult",$BNcount);
	$currentParliament->setVariable("PRresult",$PRcount);
	$currentParliament->setVariable("ETCresult",$ETCcount);
	$currentParliament->setVariable("PENDING",$PENDcount);
	$currentParliament->setVariable("FACEBOOK",$shareFacebook);
	$currentParliament->setVariable("TWITTER",$shareTwitter);
	$currentParliament->setVariable("GOOGLE",$shareGoogle);
	
	$currentParliament->parseCurrentBlock();
	$currentParliament->show();
	
	
?>