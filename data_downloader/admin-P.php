<?php
	require '../database_information/db.php';
	require_once '../templates/IT.php';
		
	if(!($connection = @ mysql_connect($hostName,$userName,$userPass)))
	die("failed to connect database");
	if(! mysql_select_db($dbName,$connection))
	die("failed to search in database");
	
	$query="SELECT * FROM seat_parliament,namelist_parliament WHERE seat_parliament.ParliamentID=namelist_parliament.ParliamentID";
	if(!($result = mysql_query($query,$connection)))
	die("failed to read namelist!");
	$at = new HTML_Template_IT("../templates");
    $at->loadTemplatefile("readParliament.tpl",true,true);
	$odd="";
	$th="";
	$color=true;
	
	while($row = mysql_fetch_array($result))
	{
		$at->setCurrentBlock("NAMELIST");
		
		if($row["ParliamentID"] != $odd)
		{
			$odd=$row["ParliamentID"];
			if($color)
			{
				$th = "<tr>";
				$color=false;
			}else
			{
				$th = "<tr style='background-color:#CCCCCC'>";
				$color=true;
			}
		}
		
		$at->setVariable("TH",$th);
		$at->setVariable("ID",$row["ParliamentID"]);
		$at->setVariable("PARTY",$row["Party"]);
		$at->setVariable("NAME",$row["Name"]);
		$at->setVariable("VOTESGET",'<input type="hidden" name="seat" value="parliament"><input type="hidden" name="parliamentID" value='.$row['ParliamentID'].'><input type="hidden" name="nameID" value='.$row['nameID'].'><input type="text" name="votes" value='.$row['VotesGet'].'>');
		$at->setVariable("EDIT",'<input type="submit" value="Edit">');
		$at->parseCurrentBlock();
	}
	$at->show();
?>