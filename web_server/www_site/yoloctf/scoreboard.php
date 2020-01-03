<?php
    ini_set('session.cookie_httponly', 1);
    ini_set('session.cookie_secure', 1);
    header_remove("X-Powered-By");
    header("X-XSS-Protection: 1");
    header('X-Frame-Options: SAMEORIGIN'); 
    session_start ();
?>

<!DOCTYPE html>
<html lang="fr">
<head>
  <title>Y0L0 CTF</title>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <link rel="stylesheet" href="/yoloctf/js/bootstrap.min.css">
  <link rel="stylesheet" href="style.css">
  <script src="/yoloctf/js/jquery.min.js"></script>
  <script src="/yoloctf/js/popper.min.js"></script>
  <script src="/yoloctf/js/bootstrap.min.js"></script>

  <script src="/yoloctf/js/moment.min.js"></script>
	<script src="/yoloctf/js/Chart.min.js"></script>
	<script src="/yoloctf/js/Chart_utils.js"></script>
	<style>
		canvas {
			-moz-user-select: none;
			-webkit-user-select: none;
			-ms-user-select: none;
		}
	</style>


</head>
<body>

<!--- Page Header  -->
<?php
    include "Parsedown.php";
    $Parsedown = new Parsedown();
	include 'header.php'; 
	require_once 'ctf_env.php'; 
?>


<div class="container-fluid">
    <div class="row">
        <!--- Page TOC  -->
        <div class="col-md-auto">
            <?php include 'toc.php' ?>
        </div>

        <!--- Page Content  -->
        <div class="col">
			<div class="container">
				<div>
					<canvas id='canvas_00'></canvas>
				</div>

			</div>
			<button type="submit" class="btn btn-primary" onclick="return refreshChartFlags()">Refresh</button>   
        </div>
    </div>
</div>


  
</body>
</html>

<script>
	

	var color = Chart.helpers.color;

	function addFlagDataset(myBarChart, user) {
		var user_dataset_url = "https://localhost/yoloctf/zen_data.php?UsersFlags=5e0f2b9684325";
		var user_dataset = [{ x: '1/3/2020 11:55', y: 1}, { x: '1/3/2020 11:55', y: 3}, { x: '1/3/2020 11:55', y: 8}, { x: '1/3/2020 11:56', y: 13}, { x: '1/3/2020 11:56', y: 18}, { x: '1/3/2020 11:56', y: 23}, { x: '1/3/2020 11:56', y: 28}, { x: '1/3/2020 11:56', y: 33}, { x: '1/3/2020 11:56', y: 38}, { x: '1/3/2020 11:56', y: 43}, { x: '1/3/2020 11:56', y: 48}, { x: '1/3/2020 11:56', y: 53}, { x: '1/3/2020 11:56', y: 58}, { x: '1/3/2020 11:56', y: 65}, { x: '1/3/2020 11:56', y: 72}, { x: '1/3/2020 11:56', y: 79}, { x: '1/3/2020 11:57', y: 89},]
		var r=Math.floor(Math.random() * 88);
		var g=40+Math.floor(Math.random() * 80);
		var b=40+Math.floor(Math.random() * 80);
		var color_str = 'rgb('+r.toString()+', '+g.toString()+', '+b.toString()+')';
		$.get(
			"https://localhost/yoloctf/zen_data.php",
			{UsersFlags : user},
			function(data) {
				//alert(data);
				//data = '[ { "x": "1/3/2020 11:55", "y": 1} ]';
				var dataset = {
						label: user, 
						backgroundColor: color(color_str).alpha(0.5).rgbString(),
						borderColor: color_str,
						fill: false,
						data: JSON.parse(data),
				}
				//alert(dataset);
				myBarChart.data.datasets.push(dataset);
				myBarChart.update();
			}
		);
		
			
	}
	window.onload = function() {
		initChartFlags();
		loadChartFlags();
	}

	function refreshChartFlags() {
		l_00.data.datasets=[];
		l_00.update();
		loadChartFlags();
	}

	var l_00=null;
	function initChartFlags() {
		var timeFormat = 'MM/DD/YYYY HH:mm';
		var config_00 = {
			type: 'line',
			data: {
				labels: [],
				
				datasets: [	 ]
			},
			options: {
				title: {
					text: 'Scoreboard'
				},
				scales: {
					xAxes: [{
						type: 'time',
						time: {
							parser: timeFormat,
							// round: 'day'
							tooltipFormat: 'll HH:mm'
						},
						scaleLabel: {
							display: true,
							labelString: 'Date'
						}
					}],
					yAxes: [{
						scaleLabel: {
							display: true,
							labelString: 'Flags'
						}
					}]
				},
			}
		};
		var ctx_00 = document.getElementById('canvas_00').getContext('2d');
		l_00 = new Chart(ctx_00, config_00);
	}

	function loadChartFlags() {
		addFlagDataset(l_00, "5e0f2b9684325");
		addFlagDataset(l_00, "5e0f2b96816fe");
		addFlagDataset(l_00, "5e0f2b9681c12");
		addFlagDataset(l_00, "5e0f2b968809d");
	}
	
</script>




