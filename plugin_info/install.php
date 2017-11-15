<?php
require_once dirname(__FILE__) . '/../../../core/php/core.inc.php';
function globalcache_install(){
}
function globalcache_update(){
	log::add('globalcache','debug','Lancement du script de mise a jours'); 
	foreach(eqLogic::byType('globalcache') as $eqLogic){
    foreach($eqLogic->getCmd() as $cmd){
      $cmd->save();
    }
		$eqLogic->save();
	}
	log::add('globalcache','debug','Fin du script de mise a jours');
}
function globalcache_remove(){
}
?>
