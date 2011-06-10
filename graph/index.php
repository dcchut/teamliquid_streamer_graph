<html>
    <head>
        <title>teamliquid featured streamers graph</title>
        <!--[if lte IE 8]><script language="javascript" type="text/javascript" src="./flot/excanvas.min.js"></script><![endif]-->
        <script language="javascript" type="text/javascript" src="./flot/jquery.js"></script>
        <script language="javascript" type="text/javascript" src="./flot/jquery.flot.js"></script>
	 <script language="javascript" type="text/javascript" src="./graph.js"></script>
	 <link rel="stylesheet" type="text/css" href="./style.css" />
    </head>
    <body>
	<div id="main">
    <b><a href="./">teamliquid featured streamers graph</a></b> - presented by <a href="http://dcc.nitrated.net">dcchut</a><br /><br />
    <div id="graph" style="width:1500px;height:700px;"></div><br />
    all times expressed in AEST - graph updates every 35 seconds<br /><br />
    check out the <a href="./?max=2000">low popularity streams</a> 
    and the <a href="./?min=2000">high popularity streams</a>.</div>
    <script type="text/javascript">
		setTimeout("location.reload(true);", 3600 * 1000);
	</script>
	</body>
</html>
