<?php

namespace denniskupec;

class ICMP
{

	private $socket;

	public function __construct($timeout=2)
	{
		$this->socket = socket_create(AF_INET, SOCK_RAW, 1);

		if ($this->socket) {
			socket_set_option($this->socket, SOL_SOCKET, SO_RCVTIMEO, [
				'sec' => $timeout, 'usec' => 0
			]);
		} 
		else {
			throw new \Exception("Failed to open a raw socket. Are you root?");
		}
	}

	public function __destruct()
	{
		socket_close($this->socket);
	}

	/*
	 * returns an array:
	 * [latency, payload size]
	 * or false if the host is down
	 */
	public function send($host, $data="harmless_interrogatory_payload")
	{
		$packet = $this->makePacket($data);
		$size = strlen($packet);

		@socket_connect($this->socket, $host, null);
		$a = microtime(true);

		@socket_send($this->socket, $packet, $size, 0);

		if (socket_read($this->socket, 255) !== false) {
			$b = microtime(true) - $a;

			return [round($b*1000, 2), $size];
		}

		return false;
	}

	//https://tools.ietf.org/html/rfc792
	protected function checksum($data)
	{
		if (strlen($data) % 2) {
			$data .= "\x00";
		}

		$data = unpack("n*", $data);
		$sum = array_sum($data);

		while ($sum >> 16) {
			$sum = ($sum >> 16) + ($sum & 0xFFFF);
		}

		return pack("n*", ~$sum);
	}

	protected function makePacket($data)
	{
		$checksum = $this->checksum("\x08\x00\x00\x00\x00\x00\x00\x00" . $data);

		return "\x08\x00" . $checksum . "\x00\x00\x00\x00" . $data;
	}

}
