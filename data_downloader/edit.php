<?php
	require '../database_information/db.php';
		
	$seat = $_GET["seat"];
	$nameID = $_GET["nameID"];
	$votes = $_GET["votes"];
	
	if($seat=="parliament")
	{
		$query="UPDATE namelist_parliament SET VotesGet='{$votes}' WHERE nameID=$nameID";
		$parliamentID = $_GET["parliamentID"];
	}elseif($seat=="dun")
	{
		$query="UPDATE namelist_dun SET VotesGet='{$votes}' WHERE nameID=$nameID";
		$dunID = $_GET["dunID"];
		$state = $_GET["state"];
		$stateID = $_GET["stateID"];
	}
		
	if(!($connection = @ mysql_connect($hostName,$userName,$userPass)))
		die("failed to connect database");
    if(! mysql_select_db($dbName,$connection))
		die("failed to search in database");
	if(! (mysql_query($query,$connection)))
		die("can't update");
	
	//update Board
	if($seat=="parliament")
	{
		if(!($result = mysql_query("SELECT * FROM namelist_parliament WHERE ParliamentID='{$parliamentID}' ORDER BY VotesGet DESC LIMIT 1",$connection)))
		die("failed to read Parliament namelist");
		$winner=mysql_fetch_array($result);
			
		if($winner['VotesGet']!=0)
		{
			$query = "UPDATE seat_parliament SET winner ='{$winner['Party']}' WHERE ParliamentID='{$parliamentID}'";
			if(! mysql_query($query,$connection))
			die("failed to update Parliament");
		}else
		{
			$query = "UPDATE seat_parliament SET winner =NULL WHERE ParliamentID='{$parliamentID}'";
			if(! mysql_query($query,$connection))
			die("failed to update Parliament");
		}
		header("location:admin-P.php");
		
	}elseif($seat=="dun")
	{
		if(!($result = mysql_query("SELECT * FROM namelist_dun WHERE DunID='{$dunID}' ORDER BY VotesGet DESC LIMIT 1",$connection)))
		die("failed to read Dun namelist");
		$winner=mysql_fetch_array($result);
		
		if($winner['VotesGet']!=0)
		{
			$query = "UPDATE seat_dun SET winner ='{$winner['Party']}' WHERE StateID={$stateID} AND dunID='{$dunID}'";
			if(! mysql_query($query,$connection))
			die("failed to update DUN");
		}else
		{
			$query = "UPDATE seat_dun SET winner =NULL WHERE StateID={$stateID} AND DunID='{$dunID}'";
			if(! mysql_query($query,$connection))
			die("failed to update Parliament");
		}
		header("location:admin-D.php?state=".$state);
	}
?>