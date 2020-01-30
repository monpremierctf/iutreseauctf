<script src="/yoloctf/js/moment.min.js"></script>
	<script src="/yoloctf/js/Chart.min.js"></script>
	<script src="/yoloctf/js/Chart_utils.js"></script>


<div class="col text-center">
<div class="col text-left"><h2>Host Monitor</h2><br><br></div>
<div class="col text-center"> 

<!---- Server: CPU, mem, disk  --->

    <div class="">
      <div class="row chall-titre bg-secondary text-white">
        <div class="col-sm text-left">Server</div>
      </div>
        <div class="form-group text-left row">
		  <label for="usr" class="col-2">CPU</label>
		  <label for="usr" class="col-1" id="idCPU" name="id">...</label>

				<div class="col-6">
					<canvas id='canvas_00'></canvas>
				</div>


        </div>
		<div class="form-group text-left row">
          <label for="usr" class="col-2">Mem</label>
          <label for="usr" class="col-2" id="idMem_total" name="id">...</label>
          <label for="usr" class="col-2" id="idMem_available" name="id">...</label>
          <label for="usr" class="col-2" id="idMem_percent" name="id">...</label>
        </div>
        <div class="form-group text-left  row ">
		  <label for="usr" class="col-2">Disk</label>
          <label for="usr" class="col-6" id="idDisk" name="id">...</label>
        </div>
        <button type="submit" class="btn btn-primary" onclick="refreshButton()">Refresh</button>      
     
    </div>

<div class="form-group text-left  row ">
<hr>
</div>

<!---- Dockers  --->
<div class="">
      <div class="row chall-titre bg-secondary text-white">
        <div class="col-sm text-left">Containers</div>
      </div>
        <div class="form-group text-left row">
		  <label for="usr" class="col-2">Container count</label>
		  <label for="usr" class="col-6" id="idContainer_count" name="id">...</label>
        </div>
		<div class="form-group text-left row">
          <label for="usr" class="col-2">Infra</label>
          <label for="usr" class="col-6" id="idContainer_infra" name="id">...</label>
        </div>
        <div class="form-group text-left  row ">
		  <label for="usr" class="col-2">Shared challenges</label>
          <label for="usr" class="col-6" id="idContainer_shared" name="id">...</label>
        </div>
        <div class="form-group text-left  row ">
		  <label for="usr" class="col-2">Dynamics challenges</label>
          <label for="usr" class="col-6" id="idContainer_challs" name="id">...</label>
        </div>
    </div>

<div class="form-group text-left  row ">
<hr>
</div>

<!---- Logs  --->
<div class="">
    <div class="row chall-titre bg-secondary text-white">
        <div class="col-sm text-left">Logs</div>
    </div>
    <button type="submit" class="btn btn-primary" onclick='getStatFromServer("#idLogs", "logsTraefik","logs", true, true, true);'>Traefik</button>   
    <button type="submit" class="btn btn-primary" onclick='getStatFromServer("#idLogs", "logsWebserverNginx","logs", true, true, true);'>Nginx</button>   
    <button type="submit" class="btn btn-primary" onclick='getStatFromServer("#idLogs", "logsWebserverPhp","logs", true, true, true);'>Php-fpm</button>   
    <button type="submit" class="btn btn-primary" onclick='getStatFromServer("#idLogs", "logsWebserverMySQL","logs", true, true, true);'>MySQL</button>   
    <button type="submit" class="btn btn-primary" onclick='getStatFromServer("#idLogs", "logsChallengeProvider","logs", true, true, true);'>Challenge Provider</button>   
    <div class="form-group text-left row">
            <div id="idLogs" class='panel-body bg-light' style='height: 300px; width: 100%; overflow-y: scroll;'>...</div>
    </div>

</div>

<div class="form-group text-left  row ">
<hr>
</div>

         


<script>
/* 
        "/containerCount":  getContainerCount,
        "/containerSummary": getcontainerSummary,
        "/hostMem": getHostMem,
            {"total": 10352635904, 
            "available": 4982751232, 
            "percent": 51.9, 
            "used": 4847341568, "free": 2265042944, "active": 5633941504, "inactive": 1526718464, "buffers": 128135168, "cached": 3112116224, "shared": 245706752, "slab": 642584576}
        "/hostCPU": getHostCPU
        */


function escapeHtml(unsafe) {
    return unsafe
         .replace(/&/g, "&amp;")
         .replace(/</g, "&lt;")
         .replace(/>/g, "&gt;")
         .replace(/"/g, "&quot;")
         .replace(/'/g, "&#039;");
 }



        function getStatFromServer(elementId, urlParam, jsonParam, replaceCRLF=false, insertHTML=false, escapeHTML=false)
        {
            var url = "https://"+ window.location.hostname+"/stats/"+urlParam;
            $.get(url, function( data, status ) {
                try {
                    var jsondata = data; //$.parseJSON(data);
                    if (jsondata.hasOwnProperty(jsonParam)) { 
                        var txt = jsondata[jsonParam];
                        if (escapeHTML) { txt = escapeHtml(txt); }
                        if (replaceCRLF) { txt = txt.replace(/(?:\r\n|\r|\n)/g, '<br>'); }
                        if (insertHTML) {
                            $(elementId).html(txt);
                        } else {
                            $(elementId).text(txt);
                        }
                    } else {
                        $(elementId).text(data);       
                    }       
                }
                catch(error)   {
                    $(elementId).text(error+" "+data); 
                }
            })
            .fail(function(XMLHttpRequest, textStatus, errorThrown) {
                var ret = JSON.parse(XMLHttpRequest.responseText);
                $("#idCPU").text(ret); 
            });        

        }

        function toMB(val) {
            return ((val)/(1024*1024)).toFixed(0);
        }
        function getHostMem()
        {
            var url = "https://"+ window.location.hostname+"/stats/hostMem";
            $.get(url, function( data, status ) {
                var jsondata = data;  
                $("#idMem_total").text("Total: "+toMB(jsondata["total"])+" MB");     
                $("#idMem_available").text("Available: "+toMB(jsondata["available"])+" MB");    
                $("#idMem_percent").text(jsondata["percent"]+" % Used");    
            })
            .fail(function(XMLHttpRequest, textStatus, errorThrown) {
                var ret = JSON.parse(XMLHttpRequest.responseText);
                $("#idMem_total").text(ret); 
            });        
        }
        function getHostDisk()
        {
            var url = "https://"+ window.location.hostname+"/stats/hostDisk";
            $.get(url, function( data, status ) {
                var jsondata = data;  
                var ret = jsondata['df'];
                ret = ret.replace(/(?:\r\n|\r|\n)/g, '<br>');
                $("#idDisk").html(ret);     
  
            })
            .fail(function(XMLHttpRequest, textStatus, errorThrown) {
                var ret = JSON.parse(XMLHttpRequest.responseText);
                $("#idMem_total").text(ret); 
            });        
        }
        function getContainerSummary()
        {
            var url = "https://"+ window.location.hostname+"/stats/containerSummary";
            $.get(url, function( data, status ) {
                var jsondata =data;
                var infra="";
                $.each(data["infra"],function(index, value) {
                    infra +=index+": "+value+"<br />";
                });
                $("#idContainer_infra").html(infra); 
                var sharedchalls="";
                $.each(data["sharedChalls"],function(index, value) {
                    sharedchalls +=index+": "+value+"<br />";
                });
                $("#idContainer_shared").html(sharedchalls);    
                var challs="";
                $.each(data["challs"],function(index, value) {
                    challs +=index+": "+value+"<br />";
                });   
                $("#idContainer_challs").html(challs);       
                          
            })
            .fail(function(XMLHttpRequest, textStatus, errorThrown) {
                var ret = JSON.parse(XMLHttpRequest.responseText);
                $("#idCPU").text(ret); 
            });        
        }

        

    ///////////////////////////////////////
    // CPU chart



	var color = Chart.helpers.color;

    var l_00=null;

	function initCPUChart() {
		var timeFormat = 'MM/DD/YYYY HH:mm:ss';
		var config_00 = {
			type: 'line',
			data: {
				labels: [],				
				datasets: [	 ]
			},
			options: {
				title: {
					text: 'CPU Load'
				},
				scales: {
					xAxes: [{
						type: 'time',
						time: {
							parser: timeFormat,
							// round: 'day'
							tooltipFormat: 'll HH:mm:ss'
						},
						scaleLabel: {
							display: true,
							labelString: 'Date'
						}
					}],
					yAxes: [{
						scaleLabel: {
							display: true,
							labelString: '%CPU'
						},
                        ticks: {
                            suggestedMin: 0,
                            suggestedMax: 100
                        }
					}]
				},
			}
		};
		var ctx_00 = document.getElementById('canvas_00').getContext('2d');
		l_00 = new Chart(ctx_00, config_00);
	}

    //var cpu_dataset = [{ x: '1/3/2020 11:55', y: 1}, { x: '1/3/2020 11:55', y: 3}, { x: '1/3/2020 11:55', y: 8}, { x: '1/3/2020 11:56', y: 13}, { x: '1/3/2020 11:56', y: 18}, { x: '1/3/2020 11:56', y: 23}, { x: '1/3/2020 11:56', y: 28}, { x: '1/3/2020 11:56', y: 33}, { x: '1/3/2020 11:56', y: 38}, { x: '1/3/2020 11:56', y: 43}, { x: '1/3/2020 11:56', y: 48}, { x: '1/3/2020 11:56', y: 53}, { x: '1/3/2020 11:56', y: 58}, { x: '1/3/2020 11:56', y: 65}, { x: '1/3/2020 11:56', y: 72}, { x: '1/3/2020 11:56', y: 79}, { x: '1/3/2020 11:57', y: 89}];
    var cpu_dataset = [];//[{ x: '1/3/2020 11:55:01', y: 1}, { x: '1/3/2020 11:55:33', y: 3} ];
	var dataset ;
    function addCPUDataset(myBarChart) {
		var r=55+Math.floor(Math.random() * 200);
		var g=55+Math.floor(Math.random() * 200);
		var b=55+Math.floor(Math.random() * 200);
		var color_str = 'rgb('+r.toString()+', '+g.toString()+', '+b.toString()+')';

        //data = '[ { "x": "1/3/2020 11:55", "y": 1} ]';
        dataset = {
                label: "CPU", 
                backgroundColor: color(color_str).alpha(0.5).rgbString(),
                borderColor: color_str,
                fill: false,
                data: cpu_dataset, //JSON.parse(cpu_dataset),
                lineTension: 0.1,
        }   
        //alert(dataset);
        myBarChart.data.datasets.push(dataset);
        myBarChart.update();

			
	}
	window.onload = function() {
        getStatFromServer("#idCPU", "hostCPU","cpu_percent");
        getHostMem();
        getHostDisk();

        getStatFromServer("#idContainer_count", "containerCount","count");
        getContainerSummary();

		initCPUChart();
        addCPUDataset(l_00);
        refreshCPUChart();
	}

    function refreshButtonChallProvider() {
        getStatFromServer("#idLogs", "challengeProviderLogs","logs", true, true);
    }
    function refreshButtonMySQL() {
        getStatFromServer("#idLogs", "challengeProviderLogs","logs");
    }
    function refreshButton(){
        getHostMem();
        getHostDisk();

        getStatFromServer("#idContainer_count", "containerCount","count");
        getContainerSummary();
    }
    var optionsAnimation = {
        //Boolean - If we want to override with a hard coded scale
        scaleOverride : true,
        //** Required if scaleOverride is true **
        //Number - The number of steps in a hard coded scale
        scaleSteps : 10,
        //Number - The value jump in the hard coded scale
        scaleStepWidth : 10,
        //Number - The scale starting value
        scaleStartValue : 0
    }
    function getRandomInt(max) {
       return Math.floor(Math.random() * Math.floor(max));
    }   
	function addCPUChartEntry(val) {
		var currentdate = new Date(); 
        var datetime = 
                (currentdate.getMonth()+1)  + "/" 
                + currentdate.getDate() + "/"
                + currentdate.getFullYear() + " "  
                + currentdate.getHours() + ":"  
                + currentdate.getMinutes() + ":" 
                + currentdate.getSeconds();
        var entry = { x: datetime, y: val};
        cpu_dataset.push(entry); 
        if (cpu_dataset.length>200) { cpu_dataset.shift();}
        l_00.data.datasets.shift();
        l_00.data.datasets.push(dataset);
		l_00.update();

	}
    function refreshCPUChart() {
        var url = "https://"+ window.location.hostname+"/stats/hostCPU";
            $.get(url, function( data, status ) {
                var jsondata = data; 
                addCPUChartEntry(jsondata['cpu_percent']);
                $("#idCPU").text(jsondata['cpu_percent']); 
            })
            .fail(function(XMLHttpRequest, textStatus, errorThrown) {
                //var ret = JSON.parse(XMLHttpRequest.responseText);
                //$("#idCPU").text(ret); 
            });   
		//addCPUChartEntry(getRandomInt(10));
        setTimeout(function(){
            refreshCPUChart();
        }, 5000
    );
	}

    

    </script>