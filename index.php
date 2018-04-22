<html>
<head>
<title>Election 2013</title>
<link href="style.css" rel="stylesheet" type="text/css" />
<link rel="icon" href="images/favicon.ico">
<link rel="shortcut icon" href="images/favicon.ico">
<meta property="og:title" content="General Election 13 unofficial website"/>
<meta property="og:description" content="Unofficial website of election" />
<meta property="og:url" content="http://crossrt.net/election2013/"/>
<meta property="og:image" content="http://crossrt.net/election2013/images/icon.jpg"/>
<meta charset="UTF-8" />
<meta name="google" content="notranslate">
<meta http-equiv="Content-Language" content="en" />
<script id="_wauiqr">var _wau = _wau || []; _wau.push(["classic", "dfa4rfz89g74", "iqr"]);
(function() {var s=document.createElement("script"); s.async=true;
s.src="http://widgets.amung.us/classic.js";
document.getElementsByTagName("head")[0].appendChild(s);
})();</script>
</head>
<body>
	<div id="wrapper">
        <?php include './templates/menu.php'; ?>
        <div id="content">
        	<h1 id="greeting">Welcome</h1>
            <hr>
			<?php 
				include 'current_parliament_status.php';
								
                if(!($result = mysql_query("SELECT * FROM state ORDER BY orderID",$connection)))
                die("failed to read");
                
                $template = new HTML_Template_IT("./templates");
                $template->loadTemplatefile("table_state.tpl",true,true);
                
                while($row = mysql_fetch_array($result))
                {
					$stateBN="SELECT * FROM seat_dun WHERE StateID={$row['StateID']} AND winner='BN'";
					$statePR="SELECT * FROM seat_dun WHERE StateID={$row['StateID']} AND (winner='DAP' OR winner='PKR' OR winner='PAS')";
					$stateOther="SELECT * FROM seat_dun WHERE StateID={$row['StateID']} AND NOT(winner='BN' OR winner='DAP' OR winner='PKR' OR winner='PAS')";
					
					$countBN = mysql_query($stateBN,$connection);
					$BNcount=mysql_num_rows($countBN);
					
					$countPR = mysql_query($statePR,$connection);
					$PRcount=mysql_num_rows($countPR);
					
					$countOther = @ mysql_query($stateOther,$connection);
					$OtherCount=mysql_num_rows($countOther);
					
					$number = number_format($row["StateVoters"]);
					$flagStr='<img src="./images/'.$row["flag"].'.jpg" alt="'.$row["StateName"].'" class="state_flag">';
					$totalStr='<li>Voters: '.$number.'</li>';
					$parliamentStr='<li><a href="result.php?state='.$row["page"].'&seat=parliament">Parliament: '.$row["Parliament"].'</a></li>';
					$dunStr='<li><a href="result.php?state='.$row["page"].'&seat=dun">DUN: '.$row["Dun"].'</a></li>';
					$shareFacebook='<a target="_blank" title="Share on Facebook" href="http://www.facebook.com/sharer.php?s=100&amp;p[title]='.$row['StateName'].' current DUN:&amp;p[url]='.$url.'&amp;p[summary]=BN '.$BNcount.' vs PR '.$PRcount.' vs Other '.$OtherCount.'&amp;p[images][0]=http://www.crossrt.net/election2013/images/icon.jpg"><img src="images/share_fb.png" class="share_logo" /></a>';
					$shareTwitter='<a target="_blank" title="Share on Twitter" href="http://twitter.com/share?url='.$url.'&text=%20%23GE13 '.$row['StateName'].' current DUN: BN '.$BNcount.' vs PR '.$PRcount.' vs Other '.$OtherCount.'"><img src="images/share_twitter.png" class="share_logo"/></a>';
					
					$template->setCurrentBlock("STATETABLE");
					$template->setVariable("STATENAME", $row["StateName"]);
					$template->setVariable("STATEFLAG", $flagStr);
					$template->setVariable("STATETOTAL",$totalStr);
					$template->setVariable("STATEPARLIAMENT",$parliamentStr);
					$template->setVariable("STATEDUN",$dunStr);
					$template->setVariable("FACEBOOK",$shareFacebook);
					$template->setVariable("TWITTER",$shareTwitter);
					$template->setVariable("BN",$BNcount);
					$template->setVariable("PR",$PRcount);
					$template->setVariable("etc",$OtherCount);
					
					$template->parseCurrentBlock();
                }
                
                $template->show();
            ?>
        </div>
     </div>
</body>
</html>