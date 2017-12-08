<?php
require_once dirname(__FILE__) . '/../../../../core/php/core.inc.php';
class globalcache extends eqLogic {
	protected $_socket=null;
	protected static $_GlobalCache=array(
		"GC-100"=>array(
			"Nom" => "GC-100",
			"Module" => array(
				1 => array(
					"Type" => "serial",
					"Voie" => 1,
					"Port" => 4999
				),
				2 => array(
					"Type" => "serial",
					"Voie" => 1,
					"Port" => 5000
				),
				3 => array(
					"Type" => "relay",
					"Voie" => 3,
					"Port" => 4998
				),
				4 =>array(
					"Type" => "ir",
					"Voie" => 3,
					"Port" => 4998
				),
				5 => array(
					"Type" => "ir",
					"Voie" => 3,
					"Port" => 4998
				)
			)
			
		),
		"iTachWF2IR"=> array(
			"Nom" => "iTach IR",
			"Module" => array(
				1 => array(
					"Type" => "ir",
					"Voie" => 3,
					"Port" => 4998
				)
			)
			
		),
		"iTachIP2IR"=> array(
			"Nom" => "iTach IR",
			"Module" => array(
				1 => array(
					"Type" => "ir",
					"Voie" => 3,
					"Port" => 4998
				)
			)
			
		),
		"iTachIP2IR-P"=> array(
			"Nom" => "iTach IR",
			"Module" => array(
				1 => array(
					"Type" => "ir",
					"Voie" => 3,
					"Port" => 4998
				)
			)
			
		),
		"iTachWF2SL"=> array(
			"Nom" => "iTach Serial",
			"Module" => array(
				1 => array(
					"Type" => "serial",
					"Voie" => 1,
					"Port" => 4999
				)
			)
			
		),
		"iTachIP2SL"=> array(
			"Nom" => "iTach Serial",
			"Module" => array(
				1 => array(
					"Type" => "serial",
					"Voie" => 1,
					"Port" => 4999
				)
			)
			
		),
		"iTachIP2SL-P"=> array(
			"Nom" => "iTach Serial",
			"Module" => array(
				1 => array(
					"Type" => "serial",
					"Voie" => 1,
					"Port" => 4999
				)
			)
			
		),
		"iTachWF2CC"=> array(
			"Nom" => "iTach Relay",
			"Module" => array(
				1 => array(
					"Type" => "relay",
					"Voie" => 3,
					"Port" => 4998
				)
			)
			
		),
		"iTachIP2CC"=> array(
			"Nom" => "iTach Relay",
			"Module" => array(
				1 => array(
					"Type" => "relay",
					"Voie" => 3,
					"Port" => 4998
				)
			)
			
		),
		"iTachIP2CC-P"=> array(
			"Nom" => "iTach Relay",
			"Module" => array(
				1 => array(
					"Type" => "relay",
					"Voie" => 3,
					"Port" => 4998
				)
			)
			
		),
		"Simple_Blaster_Ethernet"=> array(
			"Nom" => "Simple Blaster Ethernet",
			"Module" => array(
				1 => array(
					"Type" => "ir",
					"Voie" => 3,
					"Port" => 4998
				)
			)
			
		)
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
	/*public function preSave(){
		if(self::url_exists($this->getLogicalId()) === false)
				throw new Exception(__('Impossible de se connecter a la cible, Verifier l\'ardresse', __FILE__));
	}*/
	public function postSave(){
		if($this->getLogicalId()!='' /*&& self::url_exists($this->getLogicalId()) === false*/){
			$this->Connect(4998);     
         		$this->Write("getversion,".$this->getConfiguration('module'));
           		$result=$this->Read();
			$this->setConfiguration('version',$result);
			if ($this->getConfiguration('module') !='' && $this->getConfiguration('voie') !=''){
				$adresss=$this->getConfiguration('module').':'.$this->getConfiguration('voie');
				switch($this->getConfiguration('type')){
					case 'relay':	
					break;
					case 'ir':
						$this->Write("set_IR,".$adresss.",".$this->getConfiguration('mode'));
						$this->Read();
						$this->Write("get_IR,".$adresss);
						$this->Read();
					break;
					case 'serial':  
						$this->Write("set_SERIAL,".$adresss.",".$this->getConfiguration('baudrate').",".$this->getConfiguration('flowcontrol').",".$this->getConfiguration('parity'));
              					$this->Read();
					break;
				}
			}
			$this->Disconnect();
		}
	}
	public static function url_exists($url) {
		$fp = fsockopen($url, 4998, $errno, $errstr, 30);
		if (!$fp) 
			return false;
		return true;
	}
	public static function Discovery() {
		if(!($sock = socket_create(AF_INET, SOCK_DGRAM, 0))){
			log::add('globalcache', 'error', "Couldn't create socket: " . socket_strerror(socket_last_error($sock)));
			return false;
		}
		if( !socket_bind($sock, "0.0.0.0" , 9131) ){
			log::add('globalcache', 'error', "Couldn't bind port: " . socket_strerror(socket_last_error($sock)));
			return false;
		}
		if (!socket_set_option($sock, IPPROTO_IP, MCAST_JOIN_GROUP, array("group"=>"239.255.250.250","interface"=>0))) {
			log::add('globalcache', 'error', "socket_set_option() failed: reason: " . socket_strerror(socket_last_error($sock)));
			return false;
			}
		socket_set_timeout($sock,60);
		$GlobalCache=0;
		while($GlobalCache == 0){
			$r = socket_recvfrom($sock, $buf, 512, 0, $remote_ip, $remote_port);
			log::add('globalcache', 'debug', $remote_ip." : ".$remote_port." -- " . $buf);
			$GlobalCache=self::byLogicalId($remote_ip, 'globalcache',true);
		}
		socket_close($sock);
		foreach(explode('<-',str_replace('>','',$buf)) as $param){
			$Model=explode('=',$param);
		  	if($Model[0]=="Model")
				$Type=str_replace(' ','_',$Model[1]);
		}	
		foreach(globalcache::$_GlobalCache[$Type]['Module'] as $Module => $Param){	
			for($Voie=1;$Voie<=$Param['Voie'];$Voie++){		
				$Equipement=self::AddEquipement(globalcache::$_GlobalCache[$Type]['Nom'],$remote_ip,$Param['Type'],$Module,$Voie);
				$Equipement->Connect(4998);    
              			switch($Equipement->getConfiguration('type')){
					case 'relay':	
						$Equipement->Write("device,".$this->getConfiguration('module').",3 RELAY");
					break;
					case 'ir':
						$Equipement->Write("device,".$this->getConfiguration('module').",3 IR");
					break;
					case 'serial':
						$Equipement->Write("device,".$this->getConfiguration('module').",1 SERIAL");
					break;
				}
             			$Equipement->Write("endlistdevices");
				event::add('globalcache::includeDevice',$Equipement->getId());
			}
		}
		config::save('include_mode', 0, 'globalcache');
	}
	public static function AddEquipement($Name,$_logicalId,$Type,$Module,$Voie){  
		foreach(self::byLogicalId($_logicalId, 'globalcache',true) as $Equipement){       
          		if (is_object($Equipement)
                    && $Equipement->getConfiguration('type') == $Type
                    && $Equipement->getConfiguration('module') == $Module
                    && $Equipement->getConfiguration('voie') == $Voie) {
          		return $Equipement;
			} 
		}
		$Equipement = new globalcache();
		$Equipement->setName($Name."-".$Module."-".$Voie);
		$Equipement->setLogicalId($_logicalId);
		$Equipement->setObject_id(null);
		$Equipement->setEqType_name('globalcache');
		$Equipement->setIsEnable(1);
		$Equipement->setIsVisible(1);
		$Equipement->setConfiguration('type',$Type);
		$Equipement->setConfiguration('module',$Module);
		$Equipement->setConfiguration('voie',$Voie);
		$Equipement->save();
		return $Equipement;
	}
	public function Learn(){
		if($_socket == null)
			$this->Connect(4998);
		$this->Write("get_IRL");
		$return = $this->Read();
		event::add('globalcache::IRL', $return);
		$return = $this->Read();
		//$this->Write("stop_IRL");
		$this->Disconnect();
		return $return;
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
	public function Connect($Port){		
		$Ip=$this->getLogicalId();
		log::add('globalcache', 'debug',$this->getHumanName(). " Connexion a l'adresse tcp://$Ip:$Port");
		$this->_socket = stream_socket_client("tcp://$Ip:$Port", $errno, $errstr, 100);
		if (!$this->_socket) {
			throw new Exception(__("$errstr ($errno)", __FILE__));
		} 
	}
	public function Write($data){		
		log::add('globalcache','info',$this->getHumanName(). ' TX : '.$data);
		fwrite($this->_socket, $data."\r");
		$this->addCacheMonitor("TX",$data);
	}
	public function Read(){	
		$Ligne=stream_get_line($this->_socket, 1000000,"\r");
		log::add('globalcache', 'debug',$this->getHumanName(). ' RX: ' . $Ligne);
		$this->addCacheMonitor("RX",$Ligne);
		return $Ligne;
	}
	public function Disconnect(){		
		fclose($this->_socket);
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
		$adresss=$this->getEqLogic()->getConfiguration('module').':'.$this->getEqLogic()->getConfiguration('voie');
		switch($this->getEqLogic()->getConfiguration('type')){
			case 'relay':
				$this->getEqLogic()->Connect(4998);
				$data=implode(',',$bytes);
				$this->getEqLogic()->Write("setstate,".$adresss.",".$data);
				if($this->getConfiguration('reponse'))
					$this->getEqLogic()->Read();
			break;
			case 'ir':
				$freq=round(1000/($bytes[1]*0.241246),0)*1000;
				unset($bytes[0]);
				unset($bytes[1]);
				unset($bytes[2]);
				array_shift($bytes);
				$data=implode(',',$bytes);
				$cmd="sendir,".$adresss.",".$this->getId().",".$freq.",1,1,".$data;
				$this->getEqLogic()->Connect(4998);
				while(true){
					$this->getEqLogic()->Write($cmd);
					$return=$this->getEqLogic()->Read();
					$return=explode(',',trim($return));
					if($return[0] == 'completeir'
					  && $return[1] == $adresss
					  && $return[2] == $this->getId())
					break;
				}
			break;
			case 'serial':
				$this->getEqLogic()->Connect($this->getPort());
				$data=implode(',',$bytes);
				$this->getEqLogic()->Write($data);
				if($this->getConfiguration('reponse'))
					$this->getEqLogic()->Read();
			break;
		}
		$this->getEqLogic()->Disconnect();
	}
}
?>
