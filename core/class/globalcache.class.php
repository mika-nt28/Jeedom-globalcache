<?php
require_once dirname(__FILE__) . '/../../../../core/php/core.inc.php';
class globalcache extends eqLogic {
	public function preInsert() {
	}
	public function preSave() {   
	}
	public function postSave() {		
	}
	public function Send($data){
		$adresss=$this->getConfiguration('module').':'.$this->getConfiguration('voie');
		$Ip=$this->getLogicalId();
		$socket = $this->createSocket($Ip);
		switch($this->getConfiguration('type')){
			case 'relay':
				$data="setstate,".$adresss.",".$data;
			break;
			case 'ir':
				$cmd="set_IR,".$adresss.",".$this->getConfiguration('mode');
				$this->sendData($socket,$Ip,null,$cmd);
				$data="sendir,".$adresss.",".$data;
			break;
			case 'serial':
				$cmd="set_SERIAL,".$adresss.",".$this->getConfiguration('baudrate').",".$this->getConfiguration('flowcontrol').",".$this->getConfiguration('parity');
				$this->sendData($socket,$Ip,null,$cmd);
			break;
		}
		$this->sendData($socket,$Ip,null,$data);
		$this->closeSocket($socket);
	}
	private function sendData($socket,$Ip,$Port=4998,$data){
		if (!$data)
			throw new Exception(__("Can't send - empty data", __FILE__));
		if (preg_match('/^[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}$/', $this->host)){
			$ip_address = $Ip;
		}else{
			$ip_address = gethostbyname($this->host);

			if ($ip_address == $Ip)
				throw new Exception(__("DNS resolution of ".$Ip." failed", __FILE__));
		}

		if (!@socket_sendto($socket, $data, strlen($data), 0, $ip_address, $Port)){
			$err_no = socket_last_error($this->socket);
			throw new Exception(__("Failed to send data to ".$ip_address.":".$Port.". Source IP ".$Port.", source port: ".$Port.". ".socket_strerror($err_no), __FILE__));
		}

		log::add('globalcache','info','TX : '.$data);
	}
	private function readMessage($socket,$from){
		$from = '';
		$port = 0;
		if (!@socket_recvfrom($socket, $rx_msg, 10000, 0, $from,$port))
			throw new Exception(__("", __FILE__));
		log::add('globalcache','info','RX: '.$rx_msg);
	}
	private function createSocket($Ip,$Port=4998){ 
		if (!$Ip)
			throw new Exception(__("Source IP not defined.", __FILE__));
		if (!$socket = @socket_create(AF_INET, SOCK_DGRAM, SOL_UDP)){
			$err_no = socket_last_error($socket);
			throw new Exception(__(socket_strerror($err_no), __FILE__));
		}

	/*	if (!@socket_bind($socket, $Ip, $Port)){
			$err_no = socket_last_error($socket);
			throw new Exception(__("Failed to bind ".$Ip.":".$Port." ".socket_strerror($err_no), __FILE__));
		}

		$microseconds = $this->fr_timer * 1000;

		$usec = $microseconds % 1000000;

		$sec = floor($microseconds / 1000000);

		if (!@socket_set_option($socket, SOL_SOCKET, SO_RCVTIMEO, array("sec"=>$sec,"usec"=>$usec))){
			$err_no = socket_last_error($socket);
			throw new Exception(__(socket_strerror($err_no), __FILE__));
		}

		if (!@socket_set_option($socket, SOL_SOCKET, SO_SNDTIMEO, array("sec"=>5,"usec"=>0))){
			$err_no = socket_last_error($socket);			
			throw new Exception(__(socket_strerror($err_no), __FILE__));
		}*/
		return $socket;
	}
	private function closeSocket($socket){
		socket_close($socket);
	}
  }
class globalcacheCmd extends cmd {
	public function execute($_options = null){
		switch($this->getSubType()){
			case 'slider':
				$data=$_options['slider'];
			break;
			case 'color':
				$data=$_options['color'];
			break;
			case 'message':
				$data=$_options['message'];
			break;
			case 'other':
				$data=$this->getConfiguration('value');
			break;
		}
		$this->getEqLogic()->Send($data);
	}
}
?>
