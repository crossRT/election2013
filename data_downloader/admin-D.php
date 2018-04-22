<?php
	require '../database_information/db.php';
	require_once '../templates/IT.php';
	
	
	if(!($connection = @ mysql_connect($hostName,$userName,$userPass)))
	die("failed to connect database");
	if(! mysql_select_db($dbName,$connection))
	die("failed to search in database");
	
	
	//$query="SELECT * FROM seat_dun,state WHERE state.page='{$state}' AND state.StateID=seat_dun.StateID";
	//$query="SELECT * FROM seat_dun,namelist_dunm,state WHERE state.page";
	echo "<form method='get' action='admin-D.php'>Choose a state: 
	<select name='state'>
		<option value='johor'>Johor</option>
		<option value='kedah'>Kedah</option>
		<option value='kelantan'>Kelantan</option>
		<option value='melaka'>Melaka</option>
		<option value='negeri_sembilan'>Negeri Sembilan</option>
		<option value='pahang'>Pahang</option>
		<option value='perak'>Perak</option>
		<option value='perlis'>Perlis</option>
		<option value='pulau_pinang'>Pulau Pinang</option>
		<option value='sabah'>Sabah</option>
		<option value='sarawak'>Sarawak</option>
		<option value='selangor'>Selangor</option>
		<option value='terengganu'>Terengganu</option>
	</select>
	<input type='submit' value='GO'>
	</form>";
	
	if(!@($state = $_GET["state"]))
	die("Select the state first");
	else{echo "<h1>Current State: ".ucwords($state)."</h1>";}
	
	$query="SELECT * FROM namelist_dun,state WHERE state.page='{$state}' AND state.StateID=namelist_dun.StateID";
	$result = mysql_query($query,$connection);
	
	$at = new HTML_Template_IT("../templates");
    $at->loadTemplatefile("readDun.tpl",true,true);
	$odd="";
	$th="";
	$color=true;
	
	while($row = mysql_fetch_array($result))
	{
		$at->setCurrentBlock("NAMELIST");
		if($row["DunID"] != $odd)
		{
			$odd=$row["DunID"];
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
		$at->setVariable("STATE",$row["StateName"]);
		$at->setVariable("ID",$row["DunID"]);
		$at->setVariable("PARTY",$row["Party"]);
		$at->setVariable("NAME",$row["Name"]);
		$at->setVariable("VOTESGET",'<input type="hidden" name="seat" value="dun"><input type="hidden" name="state" value='.$state.'><input type="hidden" name="stateID" value='.$row["StateID"].'><input type="hidden" name="dunID" value='.$row['DunID'].'><input type="hidden" name="nameID" value='.$row['nameID'].'><input type="text" name="votes" value='.$row['VotesGet'].'>');
		$at->setVariable("EDIT",'<input type="submit" value="Edit">');
		$at->parseCurrentBlock();
	}
	$at->show();
	
?>