<?php
require_once dirname(__FILE__) . '/../../../../core/php/core.inc.php';
class globalcache extends eqLogic {
	public function preInsert() {
	}
	public function preSave() {   
	}
	public function postSave() {		
	}
	private function sendData($socket,$Ip,$Port=4998,$data){
		if (!$data){
			log::add('globalcache','error',"Can't send - empty data");
			die();
		}

		if (preg_match('/^[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}$/', $this->host)){
			$ip_address = $Ip;
		}else{
			$ip_address = gethostbyname($this->host);

			if ($ip_address == $Ip){
				log::add('globalcache','error',"DNS resolution of ".$Ip." failed");
				die();
			}
		}

		if (!@socket_sendto($socket, $data, strlen($data), 0, $ip_address, $Port)){
			$err_no = socket_last_error($this->socket);
			log::add('globalcache','error',"Failed to send data to ".$ip_address.":".$Port.". Source IP ".$Port.", source port: ".$Port.". ".socket_strerror($err_no));
			die();
		}

		log::add('globalcache','info','TX : '.$data);
	}
	private function readMessage($socket,$from){
		if (!@socket_recvfrom($socket, $rx_msg, 10000, 0, "", 0)){
			die();
		}
		log::add('globalcache','info','RX: '.$this->rx_msg);
	}
	private function createSocket($Ip,$Port=4998){ 
		if (!$Ip){
			log::add('globalcache','error',"Source IP not defined.");
			die();
		}

		if (!$socket = @socket_create(AF_INET, SOCK_DGRAM, SOL_UDP)){
			$err_no = socket_last_error($socket);
			log::add('globalcache','error',socket_strerror($err_no));
			die();
		}

		if (!@socket_bind($socket, $Ip, $Port)){
			$err_no = socket_last_error($socket);
			log::add('globalcache','error',"Failed to bind ".$Ip.":".$Port." ".socket_strerror($err_no));
			die();
		}

		$microseconds = $this->fr_timer * 1000;

		$usec = $microseconds % 1000000;

		$sec = floor($microseconds / 1000000);

		if (!@socket_set_option($socket, SOL_SOCKET, SO_RCVTIMEO, array("sec"=>$sec,"usec"=>$usec))){
			$err_no = socket_last_error($socket);
			log::add('globalcache','error',socket_strerror($err_no));
			die();
		}

		if (!@socket_set_option($socket, SOL_SOCKET, SO_SNDTIMEO, array("sec"=>5,"usec"=>0))){
			$err_no = socket_last_error($socket);
			log::add('globalcache','error',socket_strerror($err_no));
			die();
		}
		return $socket;
	}
	private function closeSocket($socket){
		socket_close($socket);
	}
  }
class globalcacheCmd extends cmd {
	
	public function execute($_options = null){
		
		$Ip=$this->getEqLogic()->getLogicalId();
		$socket = $this->createSocket($Ip);
		switch($this->getConfiguration('type')){
			case 'ir':
				$data="set_IR,".$this->getLogicalId().",".$this->getConfiguration('mode');
				$this->getEqLogic()->sendData($socket,$Ip,null,$data);
			break;
			case 'serial':
				$data="set_SERIAL,".$this->getLogicalId().",".$this->getConfiguration('baudrate').",".$this->getConfiguration('flowcontrol').",".$this->getConfiguration('parity');
				$this->getEqLogic()->sendData($socket,$Ip,null,$data);
			break;
		}
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
			break;
		}
		$this->getEqLogic()->sendData($socket,$Ip,null,$data);
		$this->getEqLogic()->closeSocket($socket);
	}
}
?>
