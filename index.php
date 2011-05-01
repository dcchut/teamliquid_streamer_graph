<?php
// serve a list of TL.net streamers in JSON format
// being careful not to grab too often!
// by dcchut dcc.nitrated.net

// our local files
$streamFile = '.streams';
$lockFile   = '.lock';

$lockHandle = fopen($lockFile, "r+");

// lock to prevent weird shit
if (flock($lockHandle, LOCK_EX)) {
	if (!file_exists($streamFile)) {
		touch($streamFile, time() - 60);
	}

	// data is old, so update it
	if (time() - filemtime($streamFile) > 30) {
		$streamHandle = popen("python getStreamersJSON.py", "r");
		file_put_contents($streamFile, stream_get_contents($streamHandle);
		pclose($streamHandle);
	}
	
	// display the data, die
	echo file_get_contents($streamFile);
	
	flock($lockHandle, LOCK_UN);
}