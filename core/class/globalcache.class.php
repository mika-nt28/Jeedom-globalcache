<?php
require_once dirname(__FILE__) . '/../../../../core/php/core.inc.php';
class globalcache extends eqLogic {
	public function preInsert() {
	}
	public function preSave() {   
	}
	public function postSave() {		
	}

	public function Send() {		
		$socket = createSocket($this->getLogicalId(),$Port=4998)
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
	}
}
?>
