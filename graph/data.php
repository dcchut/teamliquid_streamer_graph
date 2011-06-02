<?php

$dataDirectory = '/home/dcc/pelogger/rdata/';

$threshold = 1; // 1000;
							  // since our new data uses featured streamers
								// just ignore this for now!
$data = array();
$mints = FALSE;  // minimum timestamp
$maxts = FALSE;  // maximum timestamp

// we use these to not have to look through all of our data
// for every update
$m = @(float)$_GET['m'];
$u = @(float)$_GET['u'];

// we need to go through files in sorted order to not go insane
$fileList = array();

$dir = opendir($dataDirectory);
while (FALSE !== ($file = readdir($dir))){
    if ($file == '.' || $file == '..'){
        continue;
    }
    $fileList[] = $file;
}
closedir($dir);
sort($fileList);

foreach ($fileList as $file){
    $f = json_decode(file_get_contents($dataDirectory . $file));

    // data not in valid format, so ignore it
    if (!is_array($f)){
        continue;
    }
    
    $file = (int)$file;

    // only get files for which we are interested
    if ($file < $u){ continue; }
    if (!$mints) { $mints = $file; }
    $maxts = $file;
    
    // save the viewer data
    foreach ($f as $v)
    {
        if (!array_key_exists($v[0], $data)){
            $data[$v[0]] = array();
        }
        
        $data[$v[0]][$file] = (int)$v[2];
    }
}

// how long is the interval in our timestamps (good for 'scaling')
$maxgap = $maxts - $mints;
if ($m == 0) { $m = $mints; }

// our output data
$output = "[{\"u\" : ". $maxts . ", \"m\" : " . $m . "},\n";

foreach ($data as $user => $v){
    $o = array();
    $l = 0;
    
    // is this user worth plotting?
    if (max($v) < $threshold){
        continue;
    }
    
    foreach ($v as $time => $viewers){
        if ($time <= $u){
            continue;
        }
	 // dont want to draw a continuous curve if they stop streaming and start again
        if ($l > 0 && ( (($time - $l) > 0.05 * $maxgap && $u == 0) || ($time - $l) > 0.05 * ($u - $m) && $u != 0)) {
            $o[] = null;
        }

        $o[] = array(((int)$time - $m)/60,$viewers);
        $l = $time;
    }
    $output .= "{\"label\":\"". stripslashes($user) . "\"\n, \"data\": " . json_encode($o)."}\n,";
}

echo substr($output, 0, -1) . "]\n";
