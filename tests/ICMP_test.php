<?php require dirname(__DIR__) . '/vendor/autoload.php';

$icmp = new denniskupec\ICMP;

/* Pass in a hostname */
$host = empty($argv[1]) ? '8.8.8.8' : $argv[1];

$i = 0;

while (true) {
	$a = microtime(true);

	$p = $icmp->send($host);

	echo "Reply from {$host}: time={$p[0]}ms, bytes={$p[1]}, i={$i}" . PHP_EOL;

	$i++;

	sleep(1);
}
