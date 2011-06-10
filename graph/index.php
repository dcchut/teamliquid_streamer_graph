<?php
session_start();

$gc = function($key) use ($_GET){
  if (!array_key_exists($key, $_GET)) {
    return FALSE;
  } else {
    return $_GET[$key];
  }
};

// save the threshold & bound variables
if ($t = $gc('t')) {
  $_SESSION['threshold'] = $t;
}

if ($b = $gc('b')) {
  $_SESSION['bound'] = $b;
}

?>
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
    <b>teamliquid featured streamers graph</b> - presented by <a href="http://dcc.nitrated.net">dcchut</a><br /><br />
    <div id="graph" style="width:1500px;height:700px;"></div><br />
    all times expressed in AEST - graph updates every 35 seconds</div>
    <script type="text/javascript">
		setTimeout("location.reload(true);", 3600 * 1000);
	</script>
	</body>
</html>
