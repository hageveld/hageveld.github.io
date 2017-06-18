<?php
if(isset($_GET['stats'])) {
	date_default_timezone_set('Europe/Amsterdam');
	$db = mysqli_connect();
	$query = mysqli_query($db,"SELECT * FROM mrstemmen");
	$totaal = 0;
	$stemmen = array();
	while($row = mysqli_fetch_assoc($query)) {
		srand($seed . $row['ID']);
		if($row['Tijd']+(rand(40,60)*rand(2,10) < time())) {
			$totaal++;
			if(!isset($stemmen[$row['Stem']])) {
				$stemmen[$row['Stem']] = 1; 
			}
			else {
				$stemmen[$row['Stem']]++;
			}
		}
	}
	echo json_encode(array("totaal" => $totaal, "stemmen" => $stemmen));
	mysqli_close($db);
	die();
}
?>
<!DOCTYPE html>
<html>
<head>
<title>Admin</title>
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/semantic-ui/2.2.10/semantic.min.css" integrity="sha256-5+W3JHnvGYIJkVxUBsw+jBi9+pOlu9enPX3vZapXj5M=" crossorigin="anonymous" />
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<style>
h1 {
	margin-top: 5% !important;
}
#piechart {
	margin-top: 1% !important;
	margin-left: -12% !important;
	width: 700px;
	height: 500px;
}
</style>
</head>
<body>
<div class="ui container">
<h1>LMR Verkiezingen</h1>
<b>Aantal stemmen</b>: <span id="stemmen">laden...</span><BR>
<b>Tijd tot sluiting</b>: <span id="tijd">laden...></span><BR>
<b>Stemvolgorde-hash</b>: 65B1EF962A3E95419D869555AFB37DB65F611F66 (<a href="http://passwordsgenerator.net/sha1-hash-generator/">SHA1</a>)
<div id="piechart"></div>
</div>
</body>
<script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.2.1/jquery.min.js" integrity="sha256-hwg4gsxgFZhOsEEamdOYGBf13FyQuiTwlAQgxVSNgt4=" crossorigin="anonymous"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/semantic-ui/2.2.10/semantic.min.js" integrity="sha256-97Q90i72uoJfYtVnO2lQcLjbjBySZjLHx50DYhCBuJo=" crossorigin="anonymous"></script>
<script type="text/javascript" src="https://www.gstatic.com/charts/loader.js"></script>
<script>
var chart;
var countDownDate = new Date("Jul 3, 2017 18:00:00").getTime();
var options = {
	title: ''
};

function drawChart(data) {
	chart = new google.visualization.PieChart(document.getElementById('piechart'));
	chart.draw(data, options);
}

function updateStats() {
	$.get("admin.php?stats",function(data) {
		data = JSON.parse(data);
		$("#stemmen").html(data.totaal);
		var stemmen = [['Persoon', 'Aantal stemmen']];
		$.each(data.stemmen,function(i, data) {
			stemmen.push([i,data]);
		});
		data = google.visualization.arrayToDataTable(stemmen);
		drawChart(data);
	});
}

function updateTime() {
  var now = new Date().getTime();
  var distance = countDownDate - now;
  var days = Math.floor(distance / (1000 * 60 * 60 * 24));
  var hours = Math.floor((distance % (1000 * 60 * 60 * 24)) / (1000 * 60 * 60));
  var minutes = Math.floor((distance % (1000 * 60 * 60)) / (1000 * 60));
  var seconds = Math.floor((distance % (1000 * 60)) / 1000);

  document.getElementById("tijd").innerHTML = days + " dagen, " + hours + " uur, " + minutes + " minuten en " + seconds + " seconden";
  if (distance < 0) {
    clearInterval(x);
    document.getElementById("tijd").innerHTML = "0 dagen, 0 uur, 0 minuten en 0 seconden";
  }
}

google.charts.load('current', {'packages':['corechart']});
google.charts.setOnLoadCallback(updateStats);
updateTime();
setInterval(updateTime,1000);
//setInterval(updateStats,2000);
</script>
</html>