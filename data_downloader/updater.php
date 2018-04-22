<?php
	require '../database_information/db.php';
	
	if(!($connection = @ mysql_connect($hostName,$userName,$userPass)))
	die("failed to connect database");
	if(! mysql_select_db($dbName,$connection))
	die("failed to search in database");
	
	//update parliament
	for($i=1;$i<=222;$i++)
	{
		if(!($result = mysql_query("SELECT * FROM namelist_parliament WHERE ParliamentID='P{$i}' ORDER BY VotesGet DESC LIMIT 1",$connection)))
		die("failed to read Parliament namelist");
		
		$winner=mysql_fetch_array($result);
		
		if($winner['VotesGet']!=0)
		{
			$query = "UPDATE seat_parliament SET winner ='{$winner['Party']}' WHERE ParliamentID='P{$i}'";
			if(! mysql_query($query,$connection))
			die("failed to update Parliament");
		}else
		{
			$query = "UPDATE seat_parliament SET winner =NULL WHERE ParliamentID='P{$i}'";
			if(! mysql_query($query,$connection))
			die("failed to update Parliament");
		}
	}
	
	//update dun
	if(!($stateResult = mysql_query("SELECT * FROM state ORDER BY orderID",$connection)))
	die("failed to read State");
	while($state = mysql_fetch_array($stateResult))
	{
		for($i=1;$i<=$state['Dun'];$i++)
		{
			if(!($result = mysql_query("SELECT * FROM namelist_dun WHERE StateID={$state['StateID']} AND DunID='N{$i}' ORDER BY VotesGet DESC LIMIT 1",$connection)))
			die("failed to read DUN namelist");
			
			$winner=mysql_fetch_array($result);
			if($winner['VotesGet']!=0)
			{
				$query = "UPDATE seat_dun SET winner ='{$winner['Party']}' WHERE StateID={$state['StateID']} AND DunID='N{$i}'";
				if(! mysql_query($query,$connection))
				die("failed to update Parliament");
			}
			else
			{
				$query = "UPDATE seat_dun SET winner =NULL WHERE StateID={$state['StateID']} AND DunID='N{$i}'";
				if(! mysql_query($query,$connection))
				die("failed to update Dun");
			}
		}
	}
?>