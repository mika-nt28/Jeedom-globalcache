<?php
require_once dirname(__FILE__) . '/../../../../core/php/core.inc.php';
class globalcache extends eqLogic {
	public static $_GlobalCache=array(
		"GC-100"=> array(
			1 => "Serial",
			2 => "Serial",
			3 => "Relay",
			4 => "IR",
			5 => "IR",
			6 => "",
			7 =>""),
		"iTach IR"=> array(
			1 => "IR"),
		"iTach Serial "=> array(
			1 => "Serial"),
		"iTach Relay "=> array(
			1 => "Relay")
	);
	public static function deamon_info() {
		$return = array();
		$return['log'] = 'globalcache';
		$return['launchable'] = 'ok';
		/*$return['state'] = 'nok';
		foreach(eqLogic::byType('globalcache') as $globalcache){
			if($globalcache->getIsEnable()){
				$cron = cron::byClassAndFunction('globalcache', 'Monitor', array('id' => $globalcache->getId()));
				if (!is_object($cron)) 	
					return $return;
			}
		}*/
		$return['state'] = 'ok';
		return $return;
	}
	public static function deamon_start($_debug = false) {
		log::remove('globalcache');
		self::deamon_stop();
		$deamon_info = self::deamon_info();
		if ($deamon_info['launchable'] != 'ok') 
			return;
		if ($deamon_info['state'] == 'ok') 
			return;
		/*foreach(eqLogic::byType('globalcache') as $globalcache){
			if($globalcache->getIsEnable()){
				$globalcache->CreateDemon();   
			}
		}*/
	}
	public static function deamon_stop() {	
		foreach(eqLogic::byType('globalcache') as $globalcache){
			$cache = cache::byKey('globalcache::Monitor::'.$globalcache->getId());	
			$cache->remove();
			$cron = cron::byClassAndFunction('globalcache', 'Monitor', array('id' => $globalcache->getId()));
			if (is_object($cron)) 	
				$cron->remove();
		}
	}	
	public function preSave(){
		if(self::url_exists($this->getLogicalId()) === false)
				throw new Exception(__('Impossible de se connecter a la cible, Verifier l\'ardresse', __FILE__));
	}
	public function postSave(){
		if ($this->getConfiguration('module') !='' && $this->getConfiguration('voie') !=''){
			$adresss=$this->getConfiguration('module').':'.$this->getConfiguration('voie');
			switch($this->getConfiguration('type')){
				case 'relay':	
					$this->sendData(4998,"device,".$this->getConfiguration('module').",3 RELAY");
				break;
				case 'ir':
					$this->sendData(4998,"device,".$this->getConfiguration('module').",3 IR");
					$this->sendData(4998,"set_IR,".$adresss.",".$this->getConfiguration('mode'));

				break;
				case 'serial':
					$this->sendData(4998,"device,".$this->getConfiguration('module').",1 SERIAL");
					$this->sendData(4998,"set_SERIAL,".$adresss.",".$this->getConfiguration('baudrate').",".$this->getConfiguration('flowcontrol').",".$this->getConfiguration('parity'));
				break;
			}
		}
		$this->sendData(4998,"endlistdevices");
	}
	public static function url_exists($url) {
		$fp = fsockopen($url, 4998, $errno, $errstr, 30);
		if (!$fp) 
			return false;
		return true;
	}
	public static function Discovery() {
		//Reduce errors
		error_reporting(~E_WARNING);

		//Create a UDP socket
		if(!($sock = socket_create(AF_INET, SOCK_DGRAM, 0)))
		{
		   	$errorcode = socket_last_error();
		    	$errormsg = socket_strerror($errorcode);
			log::add('globalcache', 'debug', "Couldn't create socket: [$errorcode] $errormsg");
		}
		// Bind the source address
		if( !socket_bind($sock, "239.255.250.250" , 9131) )
		{
		   	$errorcode = socket_last_error();
		    	$errormsg = socket_strerror($errorcode);
			log::add('globalcache', 'debug', "Couldn't create socket: [$errorcode] $errormsg");
		}
		socket_set_timeout($sock,60);
		//Receive some data
		$r = socket_recvfrom($sock, $buf, 512, 0, $remote_ip, $remote_port);
		log::add('globalcache', 'debug', $remote_ip." : ".$remote_port." -- " . $buf);
		socket_close($sock);
		/*
		$result=array();
		foreach(explode('<-',str_replace('>','',$buf)) as $param)
			array_push($result,explode('=',$param));
		$Equipement = self::AddEquipement($result['Config-Name'],$result['Config-URL']);
		$Equipement->setConfiguration('type','ir');
		$Equipement->setConfiguration('module','1');
		$Equipement->setConfiguration('voie','1');
		$return=$Equipement->sendData(4998,"getdevices",true);
		$Equipement->setConfiguration('version',$this->sendData(4998,getversion,".$Equipement->getConfiguration('module'),true));
		$Equipement->save();
		log::add('globalcache', 'debug', $return);*/
		config::save('include_mode', 0, 'globalcache');
		return $buf;
	}
	public static function AddEquipement($Name,$_logicalId) 	{
		$Equipement = self::byLogicalId($_logicalId, 'globalcache');
		if (is_object($Equipement)) {
			$Equipement->setIsEnable(1);
			$Equipement->save();
		} else {
			$Equipement = new globalcache();
			$Equipement->setName($Name);
			$Equipement->setLogicalId($_logicalId);
			$Equipement->setObject_id(null);
			$Equipement->setEqType_name('globalcache');
			$Equipement->setIsEnable(1);
			$Equipement->setIsVisible(1);
			$Equipement->save();
		}
		return $Equipement;
	}
	public static function Monitor($_option) {
		log::add('globalcache', 'debug', 'Objet mis à jour => ' . json_encode($_option));
		$globalcache = globalcache::byId($_option['id']);
		if (is_object($globalcache) && $globalcache->getIsEnable()) {
			$Ip=$globalcache->getLogicalId();
			$Port=$globalcache->getPort();
			log::add('globalcache', 'debug',$globalcache->getHumanName(). " Connexion a l'adresse tcp://$Ip:$Port");
			$socket = stream_socket_client("tcp://$Ip:$Port", $errno, $errstr, 100);
			if (!$socket) 
				throw new Exception(__("$errstr ($errno)", __FILE__));
			log::add('globalcache', 'debug',$globalcache->getHumanName(). ' Démarrage du démon');
			while (!feof($socket)) {
				//$Ligne=stream_get_line($socket, 1000000,"\n");
            			$Ligne = fgets($socket);
				log::add('globalcache', 'debug',$globalcache->getHumanName(). ' RX: ' . $Ligne);
				if($Ligne!==false)
             				$globalcache->addCacheMonitor("TX",$Ligne);
			}
			fclose($socket); 
		}
	}
	private function addCacheMonitor($sense="TX",$_monitor) {
		$cache = cache::byKey('globalcache::Monitor::'.$this->getId());
		$value = json_decode($cache->getValue('[]'), true);
		$value[] = array('datetime' => date('d-m-Y H:i:s'),'sense' => $sense, 'monitor' => $_monitor);
		cache::set('globalcache::Monitor::'.$this->getId(), json_encode(array_slice($value, -250, 250)), 0);
	}
	public function Send($byte){
		$adresss=$this->getConfiguration('module').':'.$this->getConfiguration('voie');
		switch($this->getConfiguration('type')){
			case 'relay':
				$data=implode(',',$byte);
				$cmd="setstate,".$adresss.",".$data;
				$this->sendData(4998,$cmd,$this->getConfiguration('reponse'));
			break;
			case 'ir':
				$id=rand(0,65535);
				$freq=round(1000/($byte[1]*0.241246),0)*1000;
				unset($byte[0]);
				unset($byte[1]);
				unset($byte[2]);
				array_shift($byte);
				$data=implode(',',$byte);
				$cmd="sendir,".$adresss.",".$id.",".$freq.",1,1,".$data;
				$this->sendData(4998,$cmd);
				$cmd="completeir,".$adresss.",".$id;
				$this->sendData(4998,$cmd);
			break;
			case 'serial':
				$data=implode(',',$byte);
				$this->sendData($this->getPort(),$data,$this->getConfiguration('reponse'));
			break;
		}
	}
	public function sendData($Port,$data,$reponse=false){		
		$Ip=$this->getLogicalId();
		log::add('globalcache', 'debug',$this->getHumanName(). " Connexion a l'adresse tcp://$Ip:$Port");
      		$socket = fsockopen($this->getLogicalId(), $Port, $errno, $errstr, 30);
		//$socket = stream_socket_client("tcp://$Ip:$Port", $errno, $errstr, 100);
		if (!$socket) {
			throw new Exception(__("$errstr ($errno)", __FILE__));
		} else {
			log::add('globalcache','info',$this->getHumanName(). ' TX : '.$data);
			fwrite($socket, $data."\r");
             		$this->addCacheMonitor("TX",$data);
			if($reponse){
            			$Ligne = fgets($socket);
				log::add('globalcache', 'debug',$this->getHumanName(). ' RX: ' . $Ligne);
				if($Ligne!==false)
             				$this->addCacheMonitor("RX",$Ligne);
			}
		}
		fclose($socket);
	}
	private function CreateDemon() {
		$cron =cron::byClassAndFunction('globalcache', 'Monitor', array('id' => $this->getId()));
		if (!is_object($cron)) {
			$cron = new cron();
			$cron->setClass('globalcache');
			$cron->setFunction('Monitor');
			$cron->setOption(array('id' => $this->getId()));
			$cron->setEnable(1);
			$cron->setDeamon(1);
			$cron->setSchedule('* * * * *');
			$cron->setTimeout('999999');
			$cron->save();
		}
		$cron->save();
		$cron->start();
		$cron->run();
		return $cron;
	}
	private function getPort(){
		$Port=4998;
		switch($this->getConfiguration('type')){	
			case 'serial':
				/*$NbPrevModule=1;
				foreach(eqLogic::byTypeAndSearhConfiguration('globalcache',array('type'=>'serial')) as $eqLogic){
					if($eqLogic->getConfiguration('module') < $this->getConfiguration('module'))
						$NbPrevModule++;
				}
				$Port+=$NbPrevModule;*/
			    if($this->getConfiguration('module')== 1)       
			      $Port=4999;
			    if($this->getConfiguration('module')== 2)
			      $Port=5000;
            
			break;
		}			
		return $Port;
	}
  }
class globalcacheCmd extends cmd {
	
	public function preSave() {
		if($this->getEqLogic()->getConfiguration('type') == 'ir'){
			$this->setConfiguration('codage','DEC');
		}
	}
	public function execute($_options = null){
		switch($this->getSubType()){
			case 'slider':
				$data=$this->getConfiguration('value') . ' '.$_options['slider'];
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
      		$bytes=array();
    	  	switch($this->getConfiguration('codage')){
			case 'ASCII':
               			$bytes[]=trim($data);
				$CR="\r";
				$LF="\n";
			break;
			case 'DEC':
				foreach(explode(' ',trim($data)) as $byte){
					$bytes[]=hexdec($byte);
				}
				$CR=hexdec(0x0D);
				$LF=hexdec(0x0A);
			break;
			case 'HEXA':
				foreach(explode(' ',trim($data)) as $byte){
					$bytes[]=dechex(hexdec($byte));
				}
				$CR=dechex(hexdec(0x0D));
				$LF=dechex(hexdec(0x0A));
			break;
		}
		if($this->getEqLogic()->getConfiguration('type') != 'ir'){
			if($this->getConfiguration('CR'))
				$bytes[]=$CR;
			if($this->getConfiguration('LF'))
				$bytes[]=$LF;
		}
		$this->getEqLogic()->Send($bytes);
	}
}
?>
