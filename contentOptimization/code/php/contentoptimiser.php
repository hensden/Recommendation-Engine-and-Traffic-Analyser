<!DOCTYPE html>
<html>
<head>
	<title></title>
        <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <link rel="stylesheet" href="http://maxcdn.bootstrapcdn.com/bootstrap/3.3.5/css/bootstrap.min.css">
  <script src="https://ajax.googleapis.com/ajax/libs/jquery/1.11.3/jquery.min.js"></script>
  <script src="http://maxcdn.bootstrapcdn.com/bootstrap/3.3.5/js/bootstrap.min.js"></script>
</head>
<body>

<?php
if (!empty($_POST)) {
	$mysql_host = 'localhost';
	$mysql_user = 'aayn';
	$mysql_pass = 'justdoit123';
	$mysql_db   = 'content_optimization';
	$conn       = mysql_connect($mysql_host, $mysql_user, $mysql_pass) or die("connection failed".mysql_error());
	//echo 'Connected successfully CO';

	mysql_select_db('content_optimization');

	$sql    = "SELECT * from `url_and_baseScore_and_pageViews`;";
	$retval = mysql_query($sql, $conn) or die("connection failed 2".mysql_error());
	$finalscorearray;
	$sumtagscore = 0;
	$visitorId   = $_POST['visitor_id'];
	while ($row = mysql_fetch_array($retval, MYSQL_ASSOC)) {
		$temp    = $row['url'];
		$sql1    = "SELECT distinct `tag` from `personal_score` where `url`='$temp';";
		$retval1 = mysql_query($sql1, $conn) or die("connection failed 3".mysql_error());

		$sumofall = 0;
		while ($row1 = mysql_fetch_array($retval1, MYSQL_ASSOC)) {
			$temp    = $row1['tag'];
			$sql2    = "SELECT `tagScore` from `personal_score` where `tag`='$temp' and `visitorId`='$visitorId';";
			$retval2 = mysql_query($sql2, $conn) or die("connection failed 4".mysql_error());

			while ($row2 = mysql_fetch_array($retval2, MYSQL_ASSOC)) {
				$sumtagscore += $row2['tagScore'];
			}
		}

		$sumallscore = $sumtagscore;
		$temp        = $row['url'];
		$sumallscore += $row['baseScore'];
		$sql4    = "SELECT `viewScore` from `personal_pageview_score` where `visitorId`='$visitorId' and `url`='$temp';";
		$retval4 = mysql_query($sql4, $conn) or die("connection failed 5".mysql_error());
		while ($row4 = mysql_fetch_array($retval4, MYSQL_ASSOC)) {
			$sumallscore += $row4['viewScore'];

		}
		$temp = $row['url'];

		$finalscorearray["$temp"] = $sumallscore;

	}

	arsort($finalscorearray);
	//print_r($finalscorearray);
	$sliced_array = array_slice($finalscorearray, 0, 5);

	$titlearray;
	foreach ($finalscorearray as $key => $value) {
		$sql    = "SELECT * from `url_and_pageTitle` where `url`='$key';";
		$retval = mysql_query($sql, $conn);
		while ($row = mysql_fetch_array($retval, MYSQL_ASSOC)) {
			$temp              = $row['url'];//'url'
			$titlearray[$temp] = $row['pageTitle'];//"title"
		}

	}
	//print_r($titlearray);
	$i            = 1;
	$sliced_title = array_slice($titlearray, 0, 5);
echo "<h2 style=\"text-align:center\">Recommended Pages</h2>";
echo "<div class = \"container\" style = \"font-size:200%\">";

echo "<table class=\"table table-hover\">";

   echo "<tbody>";
	foreach ($sliced_title as $key => $value) {
                echo "<tr class = \"success\">";
                echo "<td>";
                echo "$i";
                echo "</td>";
                echo "<td>";
                echo "<a href=$key>$value</a>";
                echo "</td>";
                echo "</tr>";
		$i += 1;
	}
   echo "</tbody>";
	//$j_titlearray = json_encode($titlearray);
	//print_r($j_titlearray);
}
?>


</body>
</html>