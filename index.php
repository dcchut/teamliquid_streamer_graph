<?php
// serve a list of TL.net streamers in JSON format
// being careful not to grab too often!
// by dcchut dcc.nitrated.net

// our local files
$streamFile = '/tmp/peweb.streams';
$lockFile   = '/tmp/peweb.lock';

// lock to prevent weird shit
touch($lockFile);
$lockHandle = fopen($lockFile, "r+");

if (flock($lockHandle, LOCK_EX)) {
	if (!file_exists($streamFile)) {
		touch($streamFile, time() - 60);
	}

	// data is old, so update it
	if (time() - filemtime($streamFile) > 30) {
		$sh = popen('./getStreamersJSON.py', 'r');
		file_put_contents($streamFile,
				    stream_get_contents($sh));
		pclose($sh);
	}
	
	// display the data
	echo file_get_contents($streamFile);
	
	flock($lockHandle, LOCK_UN);
}