<html>
<head>
<title>Election 2013</title>
<link href="style.css" rel="stylesheet" type="text/css" />
<link rel="icon" href="images/favicon.ico">
<link rel="shortcut icon" href="images/favicon.ico">
<meta property="og:image" content="http://crossrt.net/election2013/images/icon.png"/>
</head>
<body>
	<div id="wrapper">
        <?php include './templates/menu.php'; ?>
        <div id="content">
			<?php
                include 'database_information/db.php';
                
				echo "<p>Sorry, search is currently offline.</p>";
				/*
                $keyword = $_GET["search"];
            
                if(!($connection = @ mysql_connect($hostName,$userName,$userPass)))
                die("failed to connect database");
                if(! mysql_select_db($dbName,$connection))
                die("failed to search in database");
                
                $queryParliament="SELECT * FROM seat_parliament WHERE seat_parliament.ParliamentID='{$keyword}' OR seat_parliament.Location LIKE '%{$keyword}%'";
                if(!($resultParliament = mysql_query($queryParliament,$connection)))
                die("failed to read");
                while($row = mysql_fetch_array($resultParliament))
                {
					
                    print_r($row);
                }
                
                $queryDun="SELECT * FROM seat_dun WHERE seat_dun.DunID='{$keyword}' OR seat_dun.Location LIKE '%{$keyword}%'";
                if(!($resultDun = mysql_query($queryDun,$connection)))
                die("failed to read");
                while($row = mysql_fetch_array($resultDun))
                {
                    print_r($row);
                }
				*/
            ?>