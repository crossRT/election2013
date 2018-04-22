<?php 
	require 'simple_html_dom.php';
	include_once "../database_information/db.php";
	
	if(!($connection = @ mysql_connect($hostName,$userName,$userPass)))
	die("can't connect");
	
	if(! mysql_select_db("election2013",$connection))
	die("Can't select");
	
	$stateLink = array("http://www.myundi.com.my/pru13/perlis.aspx",
	"http://www.myundi.com.my/pru13/kedah.aspx","http://www.myundi.com.my/pru13/kelantan.aspx",
	"http://www.myundi.com.my/pru13/terengganu.aspx","http://www.myundi.com.my/pru13/pulaupinang.aspx",
	"http://www.myundi.com.my/pru13/perak.aspx","http://www.myundi.com.my/pru13/pahang.aspx",
	"http://www.myundi.com.my/pru13/selangor.aspx","http://www.myundi.com.my/pru13/kualalumpur.aspx","http://www.myundi.com.my/pru13/putrajaya.aspx",
	"http://www.myundi.com.my/pru13/negerisembilan.aspx","http://www.myundi.com.my/pru13/melaka.aspx",
	"http://www.myundi.com.my/pru13/johor.aspx","http://www.myundi.com.my/pru13/labuan.aspx","http://www.myundi.com.my/pru13/sabah.aspx",
	"http://www.myundi.com.my/pru13/sarawak.aspx");
	
	for($i=0;$i<16;$i++)
	{
		$html = file_get_html($stateLink[$i]);
		foreach($html->find('div[data-category=parlimen]') as $element)
		{
			$parliamentID = $element->children(0)->children(0)->innertext;
			$parliamentName = $element->children(0)->children(1)->innertext;
			$parliamentName = ucwords(strtolower($parliamentName));
			$totalVoters = $element->children(1)->innertext;
			preg_match_all('/\d+/', $totalVoters, $totalVoters);
			$totalVoters = (int)implode('', $totalVoters[0]);
			
			
			$namelist = $element->find('figure[id]');
			foreach($namelist as $namelist2)
			{
				$filter = $namelist2->getAttribute('class');
				if( $filter=="" || $filter!="penyandang")
				{
					$team = $namelist2->getAttribute('id');
					$team = strtoupper($team);
					$name = $namelist2->children(1)->plaintext;
					$name = str_replace("'", "", $name);
					$name = ucwords(strtolower($name));
					//echo $name . $team;
					$queryNamelist = "INSERT INTO namelist_parliament_new VALUES(NULL,'{$name}',NULL,'{$team}','{$parliamentID}',NULL)";
					if(! (@mysql_query($queryNamelist,$connection)))
					die("can't write namelist");
				}
			}
			
			//echo $name;
			/*
			foreach($name as $hello)
			{
				echo $hello;
			}
			*/
			//echo $name;
			/*
			$queryParliament = "INSERT INTO dun VALUES(NULL,'{$parliamentID}','{$parliamentName}',NULL,'{$stateInt[$i]}','{$totalVoters}',NULL,NULL,NULL,NULL)";
			if(! (@mysql_query($queryParliament,$connection)))
			die("can't write");
			*/
		}
	}
?>