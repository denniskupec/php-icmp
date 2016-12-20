<?php require 'ICMP.php';

$icmp = new ICMP;

if (empty($argv[1])) {
	exit;
}

while (true) {

	$p = $icmp->send($argv[1]);

	echo "Reply from {$argv[1]}: time={$p[0]}ms, bytes={$p[1]}" . PHP_EOL;

	sleep(1);
}
