(function(){ // Import GET Vars
   document.$_GET = [];
   var urlHalves = String(document.location).split('?');
   if(urlHalves[1]){
      var urlVars = urlHalves[1].split('&');
      for(var i=0; i<=(urlVars.length); i++){
         if(urlVars[i]){
            var urlVarPair = urlVars[i].split('=');
            document.$_GET[urlVarPair[0]] = urlVarPair[1];
         }
      }
   }
})();

$(function() {
        var data = [];
        var options =   {
                            grid: {
                                backgroundColor: { colors: ["#fff", "#eee"] },
                                hoverable: true
                            },
                            legend: {
                                position: "nw",
                            },
                        };
        var graph = $("#graph");
        var updateInterval = 35000;
        var plot = $.plot(graph, data, options);

        function showTooltip(x, y, contents) {
            $('<div id="tooltip">' + contents + '</div>').css({
                position: 'absolute',
                display: 'none',
                top: y + 5,
                left: x + 5,
                border: '1px solid #fdd',
                padding: '2px',
                'background-color': '#fee',
                opacity: 0.80
            }).appendTo("body").fadeIn(200);
        }
        
        var previousPoint = null;
        graph.bind("plothover", function(event, pos, item){
            if (item){
                if (previousPoint != item.dataIndex){
                    previousPoint = item.dataIndex;
                    
                    $("#tooltip").remove();
                    var x = item.datapoint[0].toFixed(0),
                        y = item.datapoint[1].toFixed(0);
                
                    showTooltip(item.pageX, item.pageY, 
                                item.series.label + ' (' + getTime(fetchedTimeM + 60*x) + ', ' + y + ')');
                }
            } else {
                $("#tooltip").remove();
                previousPoint = null;
            }
        });
        
        var alreadyFetched = {};
        var a2 = {}
        var counter = 0;
        var fetchedTime = 0;
        var fetchedTimeM = 0;

		function pad2(n){
			return (n < 10 ? '0' : '') + n;
		}

		function getTime(timestamp){
			d = new Date(timestamp * 1000)
			return pad2(d.getHours()) + ":" + pad2(d.getMinutes());
		}
		
		function tf(val, axis){
			// val contains number of minutes past 0 time
			return getTime(fetchedTimeM + val*60);
		}

        function doUpdate(series){
            // get the u value first
            fetchedTime = series[0].u;
            if (fetchedTimeM == 0){
		        fetchedTimeM = series[0].m;
			}

			// construct our ticks
			options.xaxis = {
								tickFormatter: tf
							};

            // get the rest of the data?
            for (i=1;i<series.length;i++){
                if (series[i].length == 0){
                    continue;
                }
                
                if (!a2[series[i].label]){
                    a2[series[i].label] = true;
                    alreadyFetched[series[i].label] = counter;
                    counter++;
                    data.push(series[i]);
                } else {
                    // magic exists
                    data[alreadyFetched[series[i].label]].data = data[alreadyFetched[series[i].label]].data.concat(series[i].data);
                }
            }
			
			if (counter > 10){
				options.legend.noColumns = 2;
			}
          // do the approximation rubbish!
          approxdata = [];
          
          for (i=0;i<data.length;i++){
            approxdata[i] = {
                             label: data[i].label, 
                             data: data[i].slice(0)};
          }
         
            $.plot(graph, data, options);
            setTimeout(update, updateInterval);
      }
        
        function update(){
            $.ajax({
                url: "data.php",
                method: 'GET',
                data: "u="+fetchedTime+"&m="+fetchedTimeM+"&min=" + document.$_GET['min'] + "&max=" + document.$_GET['max'],
                dataType: 'json',
                success: doUpdate});
        }
        
        update();
        
});
