<?php
	/*
	INPUT: none
	GLOBAL : $_SESSION

	*/
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

			<div>
				<button type="submit" class="btn btn-primary" onclick="return refreshMyFlags()">Mes Flags</button> 
				<button type="submit" class="btn btn-primary" onclick="return refreshChartFlags()">TOP 20</button>   
			</div>
			<div id='IUT'>	</div>
			<div id='Top20'>
					
			</div>
			
			
    </div>
</div>


  
</body>
</html>

<script>
	

	var color = Chart.helpers.color;

	/* user can be unsafe and could need to be escaped */
	function addFlagDataset(myBarChart, user, uid) {
		var user_dataset_url = "https://localhost/yoloctf/zen_data.php?UsersFlags=5e0f2b9684325";
		var user_dataset = [{ x: '1/3/2020 11:55', y: 1}, { x: '1/3/2020 11:55', y: 3}, { x: '1/3/2020 11:55', y: 8}, { x: '1/3/2020 11:56', y: 13}, { x: '1/3/2020 11:56', y: 18}, { x: '1/3/2020 11:56', y: 23}, { x: '1/3/2020 11:56', y: 28}, { x: '1/3/2020 11:56', y: 33}, { x: '1/3/2020 11:56', y: 38}, { x: '1/3/2020 11:56', y: 43}, { x: '1/3/2020 11:56', y: 48}, { x: '1/3/2020 11:56', y: 53}, { x: '1/3/2020 11:56', y: 58}, { x: '1/3/2020 11:56', y: 65}, { x: '1/3/2020 11:56', y: 72}, { x: '1/3/2020 11:56', y: 79}, { x: '1/3/2020 11:57', y: 89},]
		var r=55+Math.floor(Math.random() * 200);
		var g=55+Math.floor(Math.random() * 200);
		var b=55+Math.floor(Math.random() * 200);
		var color_str = 'rgb('+r.toString()+', '+g.toString()+', '+b.toString()+')';
		$.get(
			"zen_data.php",
			{UsersFlags : uid},
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
		initIUT();

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

	function top20_table_start() {
		return ' \
		<table class="table table-striped">\
		<thead>\
			<tr>\
			<th scope="col">#</th>\
			<th scope="col">Team</th>\
			<th scope="col">Score</th>\
			<th scope="col">IUT</th>\
			<th scope="col">Lycee</th>\
			</tr>\
		</thead>\
		<tbody>\
		';
	}



function escapeHtml(unsafe) {
	if (unsafe === null) return "";
	unsafe = unsafe.toString();
    return unsafe
         .replace(/&/g, "&amp;")
         .replace(/</g, "&lt;")
         .replace(/>/g, "&gt;")
         .replace(/"/g, "&quot;")
		 .replace(/é/g, "&eacute;") // é=&eacute;
         .replace(/'/g, "&#039;");
 }



	function top20_table_entry(count, entry){
		return ' \
		<tr> \
			<th scope="row">'+count.toString()+'</th> \
			<td>'+escapeHtml(entry.login)+'</td> \
			<td>'+escapeHtml(entry.score)+'</td> \
			<td>'+escapeHtml(entry.etablissement)+'</td>  \
			<td>'+escapeHtml(entry.lycee)+'</td>  \
    	</tr>' ;
	
	}
	function top20_table_stop() {
		return ' \
		</tbody> \
		</table> \
		';
	}

	function loadChartFlags() {
		$.get(
			"zen_data.php",
			{Top20 : 20},
			function(data) {
				table = top20_table_start();
				//alert(data);

				classement = JSON.parse(data);
				count=1;
				for (const entry of classement) {
					table+=top20_table_entry(count, entry);
					count=count+1;
					addFlagDataset(l_00, entry.login, entry.UID);

				}
				table += top20_table_stop();
				document.getElementById('Top20').innerHTML = table; 
			}
		);

	}

	function initIUT()
	{
		$.get(
			"zen_data.php", {IUTList : 0},
			function(data) {				
				iutlist="";
				for (const entry of data) {
					iutlist+='<button type="submit" class="btn btn-info" onclick="loadIUTFlags(\''+entry.etablissement+'\')">'+entry.etablissement+'</button>';
					//	+"<div id='iut_"+entry.etablissement+"'></div>";
				}
				document.getElementById('IUT').innerHTML = iutlist; 
			}
		);

	}

	function loadIUTFlags(iut)
	{
		l_00.data.datasets=[];
		l_00.update();
		$.get(
			"zen_data.php",
			{Top20 : 200, iut : iut},
			function(data) {
				table = top20_table_start();
				//alert(data);

				classement = JSON.parse(data);
				count=1;
				for (const entry of classement) {
					table+=top20_table_entry(count, entry);
					count=count+1;
					addFlagDataset(l_00, entry.login, entry.UID);

				}
				table += top20_table_stop();
				document.getElementById('Top20').innerHTML = table; 
			}
		);


	}


	function refreshMyFlags(){
		l_00.data.datasets=[];
		l_00.update();
		addFlagDataset(l_00, '<?php print htmlspecialchars($_SESSION['login']) ?>', '<?php echo  $_SESSION['uid'] ?>');
	}
	
</script>




