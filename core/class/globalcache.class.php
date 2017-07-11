<?php
require_once dirname(__FILE__) . '/../../../../core/php/core.inc.php';
class globalcache extends eqLogic {
	public function preInsert() {
	}
	public function preSave() {   
	}
	public function postSave() {		
	}
	public static function BusMonitor() {
		$Ip=$this->getLogicalId();
		$socket = stream_socket_client("tcp://$Ip:4998", $errno, $errstr, 100);
		if (!$socket) 
			throw new Exception(__("$errstr ($errno)", __FILE__));
		log::add('globalcache', 'debug', 'Démarrage du démon');
		while (!feof($socket)) { 
			$Ligne=stream_get_line($socket, 1000000,"\n");
			log::add('globalcache', 'debug', $Ligne);
		}
		fclose($socket); 
	}
	public function Send($data){
		$adresss=$this->getConfiguration('module').':'.$this->getConfiguration('voie');
		$Ip=$this->getLogicalId();
		switch($this->getConfiguration('type')){
			case 'relay':
				$cmd="setstate,".$adresss.",".$data;
				$this->sendData($Ip,4998,$cmd);
			break;
			case 'ir':
				$cmd="set_IR,".$adresss.",".$this->getConfiguration('mode');
				$this->sendData($Ip,4998,$cmd);
				$cmd="sendir,".$adresss.",".$data;
				$this->sendData($Ip,4998,$cmd);
			break;
			case 'serial':
				$port=4998+$this->getConfiguration('voie');
				$cmd="set_SERIAL,".$adresss.",".$this->getConfiguration('baudrate').",".$this->getConfiguration('flowcontrol').",".$this->getConfiguration('parity');
				$this->sendData($Ip,$port,$cmd);
				$cmd=$this->EncodeData($data);
				$this->sendData($Ip,$port,$cmd);
			break;
		}
	}
	private function sendData($Ip,$Port=4998,$data){
		$socket = stream_socket_client("tcp://$Ip:$Port", $errno, $errstr, 100);
		if (!$socket) {
			throw new Exception(__("$errstr ($errno)", __FILE__));
		} else {
			log::add('mochad', 'debug', 'Envoie : '.$message);
			fwrite($socket, $data."\n");
			$reponse='';
		}
		fclose($socket);
		log::add('globalcache','info','TX : '.$data);
	}
	private function EncodeData($data){
		switch($this->getConfiguration('codage')){
			case 'ASCII':
			return iconv("UTF-8", "ASCII", $data);
			case 'HEXA':
				$hex='';
				for ($i=0; $i < strlen($data); $i++){
					$hex .= dechex(ord($data[$i]));
				}
			return $hex;
			case 'JS':
			return json_encode($data);
		}
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
