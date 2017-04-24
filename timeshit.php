<?php

date_default_timezone_set('Europe/Prague');

function getArgVal($names, $def){
	global $argv;

	if(!is_array($names))
		$names = [$names];

	foreach ($names as $name) {
		if(($idx = array_search($name, $argv)) !== false){
			if(isset($argv[$idx + 1]))
				return $argv[$idx + 1];
		}
	}

	return $def;
}

$shitlen = abs(intval(getArgVal(['h', 'hours'], 8)));
if($shitlen == 0){
	echo "SPATNE ZADANA DELKA SMENY!!!\n";
	return;
}
else $endhour = $shitlen + 8;

$barlen = abs(intval(getArgVal(['b', 'bar'], 60)));
$sleep = abs(intval(getArgVal(['s', 'sleep'], 1)));

$end = new DateTime("now");
$end->setTime($endhour, 0);

while(1){
	$diff = (new DateTime("now"))->diff($end);
	if($diff->invert){
		echo "HELL JE U KONCE!!!!!\n";
		return;
	}

	echo $diff->format("Cas do konce hellu: %H:%I:%S");

	$piss = $diff->format("%h") * 10 + round($diff->format("%i") / 6.0);
	//echo " Pisstoli: $piss";
	printf(" Pisstoli: %3d", $piss);

	$secs_to_end = $diff->format("%h") * 3600 + $diff->format("%i") * 60 + $diff->format("%s");
	$max_secs = $shitlen*3600;

	$percent = 1 - ((float)$secs_to_end / (float)$max_secs);
	$percent_str = " " . round($percent * 100) . "% ";

	if($barlen > 0){
		$parts = round($percent  * $barlen);

		if($parts >= 0)
			$barstr = str_repeat("#", $parts) . str_repeat(" ", $barlen - $parts);
		else{
			$parts = abs($parts);
			$barstr = str_repeat(" ", $barlen - $parts) . str_repeat("#", $parts);
		}

		$barstr = substr_replace($barstr, $percent_str, round((strlen($barstr) - strlen($percent_str)) / 2.0), strlen($percent_str));

		echo " [$barstr]";
	}
	else echo $percent_str;

	echo "\n";

	sleep($sleep);
}