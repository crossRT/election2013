<?php 
	require '../includes/intranet_functions.php';
	require '../includes/simple_html_dom';
	if(!$connection = @connectDB()) showError();
	if(!@mysql_select_db($dbName,$connection)) showError();
	

	for($i=0;$i<16;$i++)
	{
		$html = file_get_html($stateLink[$i]);


		foreach($html->find('div[data-category=dun]') as $element)
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
					
					$queryNamelist = "INSERT INTO namelist_dun_new VALUES(NULL,'{$name}',NULL,'{$team}','{$stateID[$i]}','{$parliamentID}',NULL)";
					if(! (mysql_query($queryNamelist,$connection)))
					die("ID:{$parliamentID} NAME:{$name} PARTY:{$party} can't update.");
				}
			}
		}
	}
?>