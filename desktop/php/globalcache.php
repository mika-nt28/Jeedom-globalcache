<?php
if (!isConnect('admin')) {
    throw new Exception('{{401 - Accès non autorisé}}');
}
sendVarToJS('eqType', 'globalcache');
$eqLogics = eqLogic::byType('globalcache');
?>
<div class="row row-overflow">
    <div class="col-lg-2 col-md-3 col-sm-4">
        <div class="bs-sidebar">
            <ul id="ul_eqLogic" class="nav nav-list bs-sidenav">
				<a class="btn btn-default eqLogicAction" style="width : 100%;margin-top : 5px;margin-bottom: 5px;" data-action="add"><i class="fa fa-plus-circle"></i> {{Ajouter un équipement}}</a>
                <li class="filter" style="margin-bottom: 5px;">
					<input class="filter form-control input-sm" placeholder="{{Rechercher}}" style="width: 100%"/>
				</li>
                <?php
                foreach (eqLogic::byType('globalcache') as $eqLogic) {
					echo '<li class="cursor li_eqLogic" data-eqLogic_id="' . $eqLogic->getId() . '"><a>' . $eqLogic->getHumanName(true) . '</a></li>';
                }
                ?>
            </ul>
        </div>
    </div>
	<div class="col-lg-10 col-md-9 col-sm-8 eqLogicThumbnailDisplay" style="border-left: solid 1px #EEE; padding-left: 25px;">
		<legend>{{Gestion}}</legend>
		<div class="eqLogicThumbnailContainer">
			<?php
				if (config::byKey('include_mode', 'globalcache', 0) == 1) {
					echo '<div class="cursor changeIncludeState include card" data-mode="1" data-state="0" style="background-color : #8000FF; height : 140px;margin-bottom : 10px;padding : 5px;border-radius: 2px;width : 160px;margin-left : 10px;" >';
					echo '<center class="includeicon">';
					echo '<i class="fa fa-spinner fa-pulse" style="font-size : 6em;color:#406E88;"></i>';
					echo '</center>';
					echo '<span style="font-size : 1.1em;position:relative; top : 15px;word-break: break-all;white-space: pre-wrap;word-wrap: break-word;color:#406E88"><center>{{Arrêter Scan}}</center></span>';
					echo '</div>';
				} else {
					echo '<div class="cursor changeIncludeState include card" data-mode="1" data-state="1" style="background-color : #ffffff; height : 140px;margin-bottom : 10px;padding : 5px;border-radius: 2px;width : 160px;margin-left : 10px;" >';
					echo '<center class="includeicon">';
					echo '<i class="fa fa-bullseye" style="font-size : 6em;color:#406E88;"></i>';
					echo '</center>';
					echo '<span style="font-size : 1.1em;position:relative; top : 15px;word-break: break-all;white-space: pre-wrap;word-wrap: break-word;color:#406E88"><center>{{Lancer Scan}}</center></span>';
					echo '</div>';
				}
			?>
			<div class="cursor eqLogicAction" data-action="add" style="background-color : #ffffff; height : 140px;margin-bottom : 10px;padding : 5px;border-radius: 2px;width : 160px;margin-left : 10px;" >
				<center>
					<i class="fa fa-plus-circle" style="font-size : 5em;color:#406E88;"></i>
				</center>
				<span style="font-size : 1.1em;position:relative; top : 23px;word-break: break-all;white-space: pre-wrap;word-wrap: break-word;color:#406E88"><center>{{Ajouter}}</center></span>
			</div>
			<div class="cursor eqLogicAction" data-action="gotoPluginConf" style="height: 120px; margin-bottom: 10px; padding: 5px; border-radius: 2px; width: 160px; margin-left: 10px; position: absolute; left: 170px; top: 0px; background-color: rgb(255, 255, 255);">
				<center>
			      		<i class="fa fa-wrench" style="font-size : 5em;color:#767676;"></i>
			    	</center>
			    	<span style="font-size : 1.1em;position:relative; top : 15px;word-break: break-all;white-space: pre-wrap;word-wrap: break-word;color:#767676"><center>Configuration</center></span>
			</div>
		</div>
        <legend>{{Mes Equipments}}</legend>
		<div class="eqLogicThumbnailContainer">
			<?php
			if (count($eqLogics) == 0) {
				echo "<br/><br/><br/><center><span style='color:#767676;font-size:1.2em;font-weight: bold;'>{{Vous n'avez pas encore de module, cliquez sur Ajouter pour commencer}}</span></center>";
			} else {
				foreach ($eqLogics as $eqLogic) {
					echo '<div class="eqLogicDisplayCard cursor" data-eqLogic_id="' . $eqLogic->getId() . '" style="background-color : #ffffff; height : 200px;margin-bottom : 10px;padding : 5px;border-radius: 2px;width : 160px;margin-left : 10px;" >';
					echo '<center><img src="plugins/globalcache/doc/images/globalcache_icon.png" height="105" width="95" /></center>';
					echo '<span style="font-size : 1.1em;position:relative; top : 15px;word-break: break-all;white-space: pre-wrap;word-wrap: break-word;"><center>' . $eqLogic->getHumanName(true, true) . '</center></span>';
					echo '</div>';
				}
			} 
			?>
		</div>
    </div>
	<div class="col-lg-10 col-md-9 col-sm-8 eqLogic" style="border-left: solid 1px #EEE; padding-left: 25px;display: none;">
	<a class="btn btn-success eqLogicAction pull-right" data-action="save"><i class="fa fa-check-circle"></i> Sauvegarder</a>
	<a class="btn btn-danger eqLogicAction pull-right" data-action="remove"><i class="fa fa-minus-circle"></i> Supprimer</a>
	<a class="btn btn-default eqLogicAction pull-right" data-action="configure"><i class="fa fa-cogs"></i> Configuration avancée</a>
	<a class="btn btn-default eqLogicAction pull-right expertModeVisible " data-action="copy"><i class="fa fa-copy"></i>{{Dupliquer}}</a>
	<ul class="nav nav-tabs" role="tablist">
		<li role="presentation">
			<a href="#" class="eqLogicAction" aria-controls="home" role="tab" data-toggle="tab" data-action="returnToThumbnailDisplay">
				<i class="fa fa-arrow-circle-left"></i>
			</a>
		</li>
		<li role="presentation" class="active">
			<a href="#eqlogictab" aria-controls="home" role="tab" data-toggle="tab" aria-expanded="true">
				<i class="fa fa-tachometer"></i> Equipement</a>
		</li>
		<li role="presentation" class="">
			<a href="#commandtab" aria-controls="profile" role="tab" data-toggle="tab" aria-expanded="false">
				<i class="fa fa-list-alt"></i> Commandes</a>
		</li>
		<li role="presentation" class="">
			<a href="#monitortab" aria-controls="profile" role="tab" data-toggle="tab" aria-expanded="false">
				<i class="fa fa-list-alt"></i> Moniteur</a>
		</li>
	</ul>
		<div class="tab-content" style="height:calc(100% - 50px);overflow:auto;overflow-x: hidden;">
			<div role="tabpanel" class="tab-pane active" id="eqlogictab">
				<br>
				<div class="row">
					<div class="col-sm-6">
						<form class="form-horizontal">
							<fieldset>			
								<div class="form-group">
									<label class="col-md-4 control-label">
										{{Nom de l'équipement}}
										<sup>
											<i class="fa fa-question-circle tooltips" title="Indiquez le nom de votre équipement" style="font-size : 1em;color:grey;"></i>
										</sup>
									</label>
									<div class="col-md-6">
										<input type="text" class="eqLogicAttr form-control" data-l1key="id" style="display : none;" />
										<input type="text" class="eqLogicAttr form-control" data-l1key="name" placeholder="{{Nom de l'équipement KNX}}"/>
									</div>
								</div>
								<div class="form-group expertModeVisible">
									<label class="col-md-4 control-label ">{{Adresse IP de l'equipement}}
										<sup>
											<i class="fa fa-question-circle tooltips" title="Indiquez l'adresse IP de votre équipement. Cette information est obigatoire pour permetre la connexion avec votre equipement" style="font-size : 1em;color:grey;"></i>
										</sup>
									</label>
									<div class="col-md-6">
										<input type="text" class="eqLogicAttr form-control" data-l1key="logicalId"/>
									</div>
								</div>
								<div class="form-group">
									<label class="col-md-4 control-label" >
										{{Objet parent}}
										<sup>
											<i class="fa fa-question-circle tooltips" title="Indiquez l'objet dans lequel le widget de cette equipement apparaiterai sur le dashboard" style="font-size : 1em;color:grey;"></i>
										</sup>
									</label>
									<div class="col-md-6">
										<select id="sel_object" class="eqLogicAttr form-control" data-l1key="object_id">
											<?php
											foreach (object::all() as $object) {
												echo '<option value="' . $object->getId() . '">' . $object->getName() . '</option>';
											}
											?>
										</select>
									</div>
								</div>
								<div class="form-group">
									<label class="col-md-4 control-label">
										{{Catégorie}}
										<sup>
											<i class="fa fa-question-circle tooltips" title="Choisissez une catégorie
										Cette information n'est pas obligatoire mais peut etre utile pour filtrer les widget" style="font-size : 1em;color:grey;"></i>
										</sup>
									</label>
									<div class="col-md-8">
										<?php
										foreach (jeedom::getConfiguration('eqLogic:category') as $key => $value) {
											echo '<label class="checkbox-inline">';
											echo '<input type="checkbox" class="eqLogicAttr" data-l1key="category" data-l2key="' . $key . '" />' . $value['name'];
											echo '</label>';
										}
										?>

									</div>
								</div>
								<div class="form-group">
									<label class="col-sm-4 control-label" >
										{{Etat du widget}}
										<sup>
											<i class="fa fa-question-circle tooltips" title="Choisissez les options de visibilité et d'activation
										Si l'equipement n'est pas activé il ne sera pas utilisable dans jeedom, mais visible sur le dashboard
										Si l'equipement n'est pas visible il ne sera caché sur le dashbord, mais utilisable dans jeedom" style="font-size : 1em;color:grey;"></i>
										</sup>
									</label>
									<div class="col-sm-6">
										<label class="checkbox-inline"><input type="checkbox" class="eqLogicAttr" data-l1key="isEnable" checked/>{{Activer}}</label>
										<label class="checkbox-inline"><input type="checkbox" class="eqLogicAttr" data-l1key="isVisible" checked/>{{Visible}}</label>
									</div>
								</div>		
							</fieldset> 
						</form>
					</div>
					<div class="col-sm-6">	
						<form class="form-horizontal">
							<fieldset>
								<div class="form-group">
									<label class="col-sm-2 control-label" >
										{{Module}}
										<sup>
											<i class="fa fa-question-circle tooltips" title="Séléctionner l'adresse du conneteur de communication" style="font-size : 1em;color:grey;"></i>
										</sup>
									</label>
									<div class="col-sm-9">
										<select class="eqLogicAttr form-control input-sm" data-l1key="configuration" data-l2key="module">
											<option value="1">{{Module 1}}</option>
											<option value="2">{{Module 2}}</option>
											<option value="3">{{Module 3}}</option>
											<option value="4">{{Module 4}}</option>
											<option value="5">{{Module 5}}</option>
											<option value="6">{{Module 6}}</option>
											<option value="7">{{Module 7}}</option>
										</select>
									</div>
								</div>
								<div class="form-group">
									<label class="col-sm-2 control-label" >
										{{Voies}}
										<sup>
											<i class="fa fa-question-circle tooltips" title="Séléctionner l'adresse du conneteur de communication" style="font-size : 1em;color:grey;"></i>
										</sup>
									</label>
									<div class="col-sm-9">
										<select class="eqLogicAttr form-control input-sm" data-l1key="configuration" data-l2key="voie">
											<option value="1">{{Voie 1}}</option>
											<option value="2">{{Voie 2}}</option>
											<option value="3">{{Voie 3}}</option>
											<option value="4">{{Voie 4}}</option>
										</select>
									</div>
								</div>
								<div class="form-group">
									<label class="col-sm-4 control-label" >{{Led d'activitée}}</label>
									<div class="col-sm-6">
										<label class="checkbox-inline"><input type="checkbox" class="eqLogicAttr" data-l1key="configuration" data-l2key="blink" checked/>{{Activer}}</label>
									</div>
								</div>
								<div class="form-group">
									<label class="col-sm-2 control-label" >
										{{Type de connexion}}
										<sup>
											<i class="fa fa-question-circle tooltips" title="Séléctionner le type d'equipement piloté" style="font-size : 1em;color:grey;"></i>
										</sup>
									</label>
									<div class="col-sm-9">
										<select class="eqLogicAttr form-control input-sm" data-l1key="configuration" data-l2key="type">
											<option value="ir">{{Infra-rouge}}</option>
											<option value="serial">{{RS232}}</option>
											<option value="relay">{{Relais}}</option>
										</select>
									</div>
								</div>
								<div class="EquipementParameter">
								</div>
							</fieldset> 
						</form>
					</div>
				</div>		
			</div>
			<div role="tabpanel" class="tab-pane" id="commandtab">
				<br>
				<a class="btn btn-success btn-sm cmdAction pull-right" data-action="add"><i class="fa fa-plus-circle"></i> Ajouter une commande</a>
				<table id="table_cmd" class="table table-bordered table-condensed">
					<thead>
						<tr>
							<th ></th>
							<th>Nom</th>
							<th>Commande</th>
							<th>Paramètre</th>
							<th></th>
						</tr>
					</thead>
					<tbody></tbody>
				</table>
			</div>
			<div role="tabpanel" class="tab-pane" id="monitortab">
				<table id="table_Monitor" class="table table-bordered table-condensed tablesorter">
				    <thead>
					<tr>
					    <th>{{Date}}</th>
					    <th>{{TX/RX}}</th>
					    <th>{{Valeur}}</th>
					</tr>
				    </thead>
				    <tbody></tbody>
				</table>
			</div>
		</div>
	</div>
</div>
<?php 
include_file('desktop', 'globalcache', 'js', 'globalcache');
include_file('core', 'plugin.template', 'js'); 
?>
