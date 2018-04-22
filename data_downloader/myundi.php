<html>
  <head>
    <meta http-equiv="refresh" content="60;url=myundi.php" />
    <title>Myundi Updater</title>
  </head>
  <body>
<?php
	require '../database_information/db.php';
	require 'simple_html_dom.php';
	if(!($connection = @ mysql_connect($hostName,$userName,$userPass)))
	die("failed to connect database");
	if(! mysql_select_db($dbName,$connection))
	die("failed to search in database");
	
	$stateLink = array("http://www.myundi.com.my/pru13/perlis.aspx",
	"http://www.myundi.com.my/pru13/kedah.aspx","http://www.myundi.com.my/pru13/kelantan.aspx",
	"http://www.myundi.com.my/pru13/terengganu.aspx","http://www.myundi.com.my/pru13/pulaupinang.aspx",
	"http://www.myundi.com.my/pru13/perak.aspx","http://www.myundi.com.my/pru13/pahang.aspx",
	"http://www.myundi.com.my/pru13/selangor.aspx","http://www.myundi.com.my/pru13/kualalumpur.aspx","http://www.myundi.com.my/pru13/putrajaya.aspx",
	"http://www.myundi.com.my/pru13/negerisembilan.aspx","http://www.myundi.com.my/pru13/melaka.aspx",
	"http://www.myundi.com.my/pru13/johor.aspx","http://www.myundi.com.my/pru13/labuan.aspx","http://www.myundi.com.my/pru13/sabah.aspx",
	"http://www.myundi.com.my/pru13/sarawak.aspx");
	
	$stateID = array(0=>8,1=>2,2=>3,3=>13,4=>9,5=>7,6=>6,7=>12,8=>14,9=>16,10=>5,11=>4,12=>1,13=>15,14=>10,15=>11);
	$parliamentSuccess=0;
	$dunSuccess=0;
	
	echo "<pre>";
	for($i=0;$i<16;$i++)
	{
		//echo $stateLink[$i]."\n";
		$html = file_get_html($stateLink[$i]);
		
		//obtained parliament
		foreach($html->find('div[data-category=parlimen]') as $element)
		{
			$parliamentID = $element->children(0)->children(0)->plaintext;
			$parliamentName = $element->children(0)->children(1)->plaintext;
			$parliamentName = ucwords(strtolower($parliamentName));
			
			$candidates=$element->find('figure[id]');
			foreach($candidates as $percandidate)
			{
				$filter = $percandidate->getAttribute('class');
				if( $filter=="" || $filter!="penyandang")
				{
					$party = $percandidate->getAttribute('id');
					$party = strtoupper($party);
					
					$name = $percandidate->children(1)->plaintext;
					$name = str_replace("'", "", $name);
					$name = ucwords(strtolower($name));
					
					$vote = $percandidate->children(2)->plaintext;
					preg_match_all('/\d+/', $vote, $vote);
					$vote = (int)implode('', $vote[0]);
					
					$updateQuery = "UPDATE namelist_parliament SET VotesGet={$vote} WHERE ParliamentID='{$parliamentID}' AND Name='{$name}'";
					if(! (mysql_query($updateQuery,$connection)))
					die("ID:{$parliamentID} NAME:{$name} PARTY:{$party} can't update. \n");
					else{$parliamentSuccess+=1;}
				}
			}
		}
		
		//obtained dun
		foreach($html->find('div[data-category=dun]') as $element)
		{
			$dunID = $element->children(0)->children(0)->plaintext;
			$dunName = $element->children(0)->children(1)->plaintext;
			$dunName = ucwords(strtolower($dunName));
			
			$candidates=$element->find('figure[id]');
			foreach($candidates as $percandidate)
			{
				$filter = $percandidate->getAttribute('class');
				if( $filter=="" || $filter!="penyandang")
				{
					$party = $percandidate->getAttribute('id');
					$party = strtoupper($party);
					
					$name = $percandidate->children(1)->plaintext;
					$name = str_replace("'", "", $name);
					$name = ucwords(strtolower($name));
					
					$vote = $percandidate->children(2)->children(0)->plaintext;
					preg_match_all('/\d+/', $vote, $vote);
					$vote = (int)implode('', $vote[0]);
					
					$updateQuery = "UPDATE namelist_dun SET VotesGet={$vote} WHERE StateID={$stateID[$i]} AND DunID='{$dunID}' AND Name='{$name}'";
					if(! (mysql_query($updateQuery,$connection)))
					die("StateID:{$stateID[$i]} ID:{$dunID} NAME:{$name} PARTY:{$party} can't update.\n");
					else{$dunSuccess+=1;}
				}
			}
		}
	}
	echo "Parliament update: ".$parliamentSuccess."\n";
	echo "DUN update: ".$dunSuccess."\n";
	
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
			//clean to NULL
			/*
			else
			{
				$query = "UPDATE seat_dun SET winner =NULL WHERE StateID={$state['StateID']} AND DunID='N{$i}'";
				if(! mysql_query($query,$connection))
				die("failed to update Dun");
			}
			*/
		}
	}
	date_default_timezone_set('Asia/Kuala_Lumpur'); 
	print "\nLast update: ".date('H:i:s');
	echo "</pre>";
	print "refresh countdown: <span id='seconds'>60</span> seconds.";
?>
    <script>
      var seconds = 60;
      setInterval
	  (
        function()
		{
			if(seconds>0)
			{
				document.getElementById('seconds').innerHTML = --seconds;
			}
        }, 1000
      );
    </script>
</body>
</html>