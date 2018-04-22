<html>
<head>
<title>Election 2013</title>
<link href="style.css" rel="stylesheet" type="text/css" />
<link rel="icon" href="images/favicon.ico">
<link rel="shortcut icon" href="images/favicon.ico">
<meta property="og:image" content="http://crossrt.net/election2013/images/icon.png"/>
<meta charset="UTF-8" />
<meta name="google" content="notranslate">
<meta http-equiv="Content-Language" content="en" />
</head>
<body>
	<div id="wrapper">
        <?php include './templates/menu.php'; ?>
        <div id="content">
		<?php
			include 'database_information/db.php';
			require_once "./templates/ITX.php";

			$state = $_GET["state"];
			$seat = $_GET["seat"];
			if($seat=="parliament")
				$query="SELECT * FROM seat_parliament,state WHERE state.page='{$state}' AND state.StateID=seat_parliament.StateID";
			else if($seat=="dun")
				$query="SELECT * FROM seat_dun,state WHERE state.page='{$state}' AND state.StateID=seat_dun.StateID";
			
			$connection = mysql_connect($hostName,$userName,$userPass);
            mysql_select_db($dbName,$connection);
            $result = mysql_query($query,$connection);
			
			
			while($row = mysql_fetch_array($result))
			{
				$template = new HTML_Template_ITX("./templates");
				$template->loadTemplatefile("table_seat.tpl",false,false);
				$template->setCurrentBlock("SEATTABLE");
				
				if($seat=="parliament")
				{
					$seatID=$row['ParliamentID'];
					$template->setVariable("SID",$seatID);
					$queryCandidates="SELECT * FROM namelist_parliament WHERE ParliamentID='{$seatID}'";
				}
				else if($seat=="dun")
				{
					$seatID=$row['DunID'];
					$template->setVariable("SID", $seatID);
					$queryCandidates="SELECT * FROM namelist_dun WHERE StateID={$row['StateID']} AND DunID='{$seatID}'";
				}
				
				$totalVoters = number_format($row['TotalVoters']);
				
				$template->setVariable("SSTATE",$row['shortname']);
				$template->setVariable("SNAME", $row['Location']);
				$template->setVariable("STOTAL", $totalVoters);
				$template->setVariable("CHINESE", $row['chinese']);
				$template->setVariable("MALAY", $row['malay']);
				$template->setVariable("INDIAN", $row['indian']);
				$template->setVariable("ETC", $row['etc']);
				
				if(!($candidates = @ mysql_query($queryCandidates,$connection)))
				die("Can't read candidates");
				
				$candidatesNumber = mysql_num_rows($candidates);
				if($candidatesNumber==2)
				{
					$template->addBlockFile("CANDIDATES","CANDIDATES","candidate_normal.tpl");
					$candidates1=mysql_fetch_array($candidates);
					$candidates2=mysql_fetch_array($candidates);
					$percentStr="";
					$total=0;
					$total += $candidates1['VotesGet'];
					$total += $candidates2['VotesGet'];
					
					$C1win="<div class='party1'>";
					$C2win="<div class='party2'>";
					if($candidates1['VotesGet']>$candidates2['VotesGet'])
					{
						$C1win="<div class='party1 winner'>";
					}else if($candidates1['VotesGet']<$candidates2['VotesGet'])
					{
						$C2win="<div class='party2 winner'>";
					}
					
					//candidate 1
					$partyName = strtolower($candidates1['Party']);
					$flagStr='<img src="./images/logo_'.$partyName.'.png" alt="'.$partyName.'" class="party_logo">';
					$candidateGets = $candidates1['VotesGet'];
					$candidateGets = number_format($candidateGets);
					
					$template->setVariable("C1WIN",$C1win);
					$template->setVariable("C1LOGO",$flagStr);
					$template->setVariable("C1NAME",$candidates1['Name']);
					$template->setVariable("C1GET",$candidateGets);
					
					//candidate 2
					$partyName = strtolower($candidates2['Party']);
					$flagStr='<img src="./images/logo_'.$partyName.'.png" alt="'.$partyName.'" class="party_logo">';
					$candidateGets = $candidates2['VotesGet'];
					$candidateGets = number_format($candidateGets);
					
					$template->setVariable("C2WIN",$C2win);
					$template->setVariable("C2LOGO",$flagStr);
					$template->setVariable("C2NAME",$candidates2['Name']);
					$template->setVariable("C2GET",$candidateGets);
					
					if($total!=0)
					{
						$C1percent=round((float)($candidates1['VotesGet'] / $total) * 100)-1;
						$C2percent=round((float)($candidates2['VotesGet'] / $total) * 100)-1;
					}else
					{
						$C1percent=49;
						$C2percent=49;
					}
					
					if($candidates2["Party"]=="BN")
					{
						$C2color = "#000088";
					}else if($candidates2["Party"]=="PAS")
					{
						$C2color = "#008800";
					}else if($candidates2["Party"]=="DAP")
					{
						$C2color = "#FF0000";
					}else if($candidates2["Party"]=="PKR")
					{
						$C2color = "#3EB2E4";
					}else if($candidates2["Party"]=="KITA")
					{
						$C2color = "#F79F56";
					}else if($candidates2["Party"]=="BERJASA")
					{
						$C2color ="#00CC00";
					}
					else if($candidates2["Party"]=="BERSAMA")
					{
						$C2color ="#CD1117";
					}else if($candidates2["Party"]=="STAR")
					{
						$C2color ="#FF682C";
					}
					else if($candidates2["Party"]=="SWP")
					{
						$C2color ="#F9DF19";
					}
					else if($candidates2["Party"]=="SAPP")
					{
						$C2color ="#D330E7";
					}else
					{
						$C2color = "#FFFFFF";
					}
					
					$percentStr .= '<div class="party_percentage" style="background-color:#000080; width:'.$C1percent.'%"></div>';
					$percentStr .= '<div class="party_percentage" style="background-color:'.$C2color.'; width:'.$C2percent.'%"></div>';
					
					$template->setVariable("PERCENTAGE",$percentStr);
					
					
				}else if($candidatesNumber>2)
				{
					$divSTR="";
					$percentStr="";
					$total=0;
					$highest=1;
					
					while($getTotal = mysql_fetch_array($candidates))
					{
						$total+=$getTotal['VotesGet'];
						if($getTotal['VotesGet']>$highest)
							$highest = $getTotal['VotesGet'];
					}
					
					//set pointer back to beginning
					mysql_data_seek($candidates, 0);
					
					while($candidatesRow = mysql_fetch_array($candidates))
					{
						$Ccolor="";
						$classWinner="";
						
						$candidateName = $candidatesRow['Name'];
						$candidateGets = $candidatesRow['VotesGet'];
						if($candidateGets==$highest)
						{
							$classWinner="per_winner";
						}
						$candidateGets = number_format($candidateGets);
						
						$partyName = strtolower($candidatesRow['Party']);
						$flagStr='<img src="./images/logo_'.$partyName.'.png" alt="'.$partyName.'" class="per_party_logo">';
						$divSTR .= '<div class="per_candidate '.$classWinner.'">
										<div class="per_candidate_logo">'.$flagStr.'</div>
										<div class="per_candidate_name">
											<h6>'.$candidateName.'</h6>
										</div>
										<div class="per_candidate_gets">
											<h6>Get: '.$candidateGets.'</h6>
										</div>
									</div>';
					
						if($total!=0)
						{
							$Cpercent=round((float)($candidatesRow['VotesGet'] / $total) * 100)-1;
						}else
						{
							$Cpercent=(round((float)(1/$candidatesNumber) * 100))-1;
						}
						
						if($candidatesRow["Party"]=="BN")
						{
							$Ccolor = "#000088";
						}else if($candidatesRow["Party"]=="PAS")
						{
							$Ccolor = "#008800";
						}else if($candidatesRow["Party"]=="DAP")
						{
							$Ccolor = "#FF0000";
						}else if($candidatesRow["Party"]=="PKR")
						{
							$Ccolor = "#3EB2E4";
						}else if($candidatesRow["Party"]=="KITA")
						{
							$Ccolor = "#F79F56";
						}else if($candidatesRow["Party"]=="BERJASA")
						{
							$Ccolor ="#00CC00";
						}
						else if($candidatesRow["Party"]=="BERSAMA")
						{
							$Ccolor ="#CD1117";
						}else if($candidatesRow["Party"]=="STAR")
						{
							$Ccolor ="#FF682C";
						}
						else if($candidatesRow["Party"]=="SWP")
						{
							$Ccolor ="#F9DF19";
						}
						else if($candidatesRow["Party"]=="SAPP")
						{
							$Ccolor ="#D330E7";
						}else
						{
							$Ccolor = "#FFFFFF";
						}
						
						$percentStr .= '<div class="multi_party_percentage" style="background-color:'.$Ccolor.'; width:'.$Cpercent.'%;"></div>';
					
					}
					$template->setVariable("CANDIDATES",$divSTR);
					$template->setVariable("PERCENTAGE",$percentStr);
				}
				$template->show();
			}
			if(mysql_num_rows($result)==0)
			{
				echo "<h1>No ".$seat." was found in ".$state."</h1>";
			}
		?>
		</div>
     </div>
</body>
</html>