<?php
	if (!isConnect('admin')) {
		throw new Exception('{{401 - Accès non autorisé}}');
	}
	$plugin = plugin::byId('globalcache');
	sendVarToJS('eqType', $plugin->getId());
	$eqLogics = eqLogic::byType($plugin->getId());
?>

<div class="row row-overflow">    
   	<div class="col-xs-12 eqLogicThumbnailDisplay">
  		<legend><i class="fas fa-cog"></i>  {{Gestion}}</legend>
		<div class="eqLogicThumbnailContainer">
			<div class="cursor eqLogicAction logoPrimary" data-action="add">
				<i class="fas fa-plus-circle"></i>
				<br>
				<span>{{Ajouter}}</span>
			</div>
			<?php
                		$cron =cron::byClassAndFunction('globalcache', 'Discovery');
				if (is_object($cron)) {
			?>
			<div class="cursor changeIncludeState logoPrimary" data-mode="1" data-state="0">
				<i class="fas fa-spinner fa-pulse"></i>
				<br>
				<span>{{Arrêter Scan}}</span>
			</div>
					echo '<div class="cur
			<?php
				} else {
			?>
			<div class="cursor changeIncludeState logoPrimary" data-mode="1" data-state="1">
				<i class="fas fa-bullseye"></i>
				<br>
				<span>{{Lancer Scan}}</span>
			</div>
			<?php
				}
			?>
      			<div class="cursor eqLogicAction logoSecondary" data-action="gotoPluginConf">
      				<i class="fas fa-wrench"></i>
    				<br>
    				<span>{{Configuration}}</span>
  			</div>
  		</div>
  		<legend><i class="fas fa-table"></i> {{Mes Equipments}}</legend>
	   	<input class="form-control" placeholder="{{Rechercher}}" id="in_searchEqlogic" />
		<div class="eqLogicThumbnailContainer">
    		<?php
			foreach ($eqLogics as $eqLogic) {
				$opacity = ($eqLogic->getIsEnable()) ? '' : 'disableCard';
				echo '<div class="eqLogicDisplayCard cursor '.$opacity.'" data-eqLogic_id="' . $eqLogic->getId() . '">';
				echo '<img src="' . $plugin->getPathImgIcon() . '"/>';
				echo '<br>';
				echo '<span class="name">' . $eqLogic->getHumanName(true, true) . '</span>';
				echo '</div>';
			}
		?>
		</div>
	</div>
	<div class="col-xs-12 eqLogic" style="display: none;">
		<div class="input-group pull-right" style="display:inline-flex">
			<span class="input-group-btn">
				<a class="btn btn-default btn-sm eqLogicAction roundedLeft" data-action="configure">
					<i class="fa fa-cogs"></i>
					 {{Configuration avancée}}
				</a>
				<a class="btn btn-default btn-sm eqLogicAction" data-action="copy">
					<i class="fas fa-copy"></i>
					 {{Dupliquer}}
				</a>
				<a class="btn btn-sm btn-success eqLogicAction" data-action="save">
					<i class="fas fa-check-circle"></i>
					 {{Sauvegarder}}
				</a>
				<a class="btn btn-danger btn-sm eqLogicAction roundedRight" data-action="remove">
					<i class="fas fa-minus-circle"></i>
					 {{Supprimer}}
				</a>
			</span>
		</div>
		<ul class="nav nav-tabs" role="tablist">
    			<li role="presentation">
				<a href="#" class="eqLogicAction" aria-controls="home" role="tab" data-toggle="tab" data-action="returnToThumbnailDisplay">
					<i class="fa fa-arrow-circle-left"></i>
				</a>
			</li>
    			<li role="presentation" class="active">
				<a href="#eqlogictab" aria-controls="home" role="tab" data-toggle="tab">
				<i class="fa fa-tachometer"></i> 
					{{Equipement}}
				</a>
			</li>
    			<li role="presentation">
				<a href="#commandtab" aria-controls="profile" role="tab" data-toggle="tab">
					<i class="fa fa-list-alt"></i> 
					{{Commandes}}
				</a>
			</li>
  		</ul>
		<div class="tab-content" style="height:calc(100% - 50px);overflow:auto;overflow-x: hidden;">
			<div role="tabpanel" class="tab-pane active" id="eqlogictab">
      				<br/>
				<div class="col-sm-6">
	    				<form class="form-horizontal">
						<fieldset>
							<div class="form-group ">
								<label class="col-sm-3 control-label">
									{{Nom de l'équipement}}
									<sup>
										<i class="fa fa-question-circle tooltips" title="Indiquez le nom de votre équipement" style="font-size : 1em;color:grey;"></i>
									</sup>
								</label>
								<div class="col-sm-3">
									<input type="text" class="eqLogicAttr form-control" data-l1key="id" style="display : none;" />
	                    						<input type="text" class="eqLogicAttr form-control" data-l1key="name" placeholder="{{Nom de l'équipement template}}"/>
								</div>
							</div>
							<div class="form-group">							
								<label class="col-sm-3 control-label">
									{{Adresse IP de l'equipement}}
									<sup>
										<i class="fa fa-question-circle tooltips" title="Indiquez l'adresse IP de votre équipement. Cette information est obigatoire pour permetre la connexion avec votre equipement" style="font-size : 1em;color:grey;"></i>
									</sup>
								</label>
								<div class="col-md-6">
									<input type="text" class="eqLogicAttr form-control" data-l1key="logicalId"/>
								</div>
							</div>
							<div class="form-group">
	                					<label class="col-sm-2 control-label" >
									{{Objet parent}}
									<sup>
										<i class="fa fa-question-circle tooltips" title="Indiquez l'objet dans lequel le widget de cette equipement apparaiterai sur le dashboard" style="font-size : 1em;color:grey;"></i>
									</sup>
								</label>
								<div class="col-sm-3">
									<select id="sel_object" class="eqLogicAttr form-control" data-l1key="object_id">
										<option value="">{{Aucun}}</option>
										<?php
											foreach (jeeObject::all() as $object) 
												echo '<option value="' . $object->getId() . '">' . $object->getName() . '</option>';
										?>
									</select>
								</div>
							</div>
							<div class="form-group">
	                					<label class="col-sm-3 control-label">
									{{Catégorie}}
									<sup>
										<i class="fa fa-question-circle tooltips" title="Choisissez une catégorie
									Cette information n'est pas obligatoire mais peut etre utile pour filtrer les widget" style="font-size : 1em;color:grey;"></i>
									</sup>
								</label>
								<div class="col-sm-9">
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
								<label class="col-sm-3 control-label">
									{{Etat du widget}}
									<sup>
										<i class="fa fa-question-circle tooltips" title="Choisissez les options de visibilité et d'activation
									Si l'equipement n'est pas activé il ne sera pas utilisable dans jeedom, mais visible sur le dashboard
									Si l'equipement n'est pas visible il ne sera caché sur le dashbord, mais utilisable dans jeedom" style="font-size : 1em;color:grey;"></i>
									</sup>
								</label>
								<div class="col-sm-9">
									<label class="checkbox-inline">
										<input type="checkbox" class="eqLogicAttr" data-l1key="isEnable" checked/>
										{{Activer}}
									</label>
									<label class="checkbox-inline">
										<input type="checkbox" class="eqLogicAttr" data-l1key="isVisible" checked/>
										{{Visible}}
									</label>
								</div>
							</div>
	       						<div class="form-group">
	        						<label class="col-sm-3 control-label">{{template param 1}}</label>
							        <div class="col-sm-3">
							            <input type="text" class="eqLogicAttr form-control" data-l1key="configuration" data-l2key="city" placeholder="param1"/>
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
								<label class="col-sm-2 control-label" >
									{{Type de connexion}}
									<sup>
										<i class="fa fa-question-circle tooltips" title="Séléctionner le type d'equipement piloté" style="font-size : 1em;color:grey;"></i>
									</sup>
								</label>
								<div class="col-sm-9">
									<select class="eqLogicAttr form-control input-sm" data-l1key="configuration" data-l2key="type" disabled>
										<option value="IR">{{Infra-rouge}}</option>
										<option value="SERIAL">{{RS232}}</option>
										<option value="RELAY">{{Relais}}</option>
									</select>
								</div>
							</div>
							<div class="form-group">
								<label class="col-sm-4 control-label" >{{Led d'activitée}}</label>
								<div class="col-sm-6">
									<label class="checkbox-inline"><input type="checkbox" class="eqLogicAttr" data-l1key="configuration" data-l2key="blink" checked/>{{Activer}}</label>
								</div>
							</div>
						</fieldset> 
					</form>
				</div>
				<div class="col-sm-6 IrParameter">	
					<form class="form-horizontal">
						<fieldset>
							<div class="form-group">
								<label class="col-sm-2 control-label" >
									{{Mode de transmission}}
									<sup>
										<i class="fa fa-question-circle tooltips" title="Séléctionner le mode de transmission infro-rouge" style="font-size : 1em;color:grey;"></i>
									</sup>
								</label>
								<div class="col-sm-9">
									<select class="eqLogicAttr form-control input-sm" data-l1key="configuration" data-l2key="mode">
										<option value="IR">{{Infra-rouge}}</option>
										<option value="IR_BLASTER">{{Infra-rouge blaster}}</option>
										<option value="IR_NOCARRIER">{{IR_NOCARRIER}}</option>
										<option value="SENSOR">{{SENSOR}}</option>
										<option value="SENSOR_NOTIFY">{{SENSOR_NOTIFY}}</option>
									</select>
								</div>
							</div>
						</fieldset> 
					</form>
				</div>
				<div class="col-sm-6 SerialParameter">	
					<form class="form-horizontal">
						<fieldset>
							<div class="form-group">
								<label class="col-sm-2 control-label" >
									{{Baudrate}}
									<sup>
										<i class="fa fa-question-circle tooltips" title="Séléctionner le baudrate de la connexion" style="font-size : 1em;color:grey;"></i>
									</sup>
								</label>
								<div class="col-sm-9">
									<select class="eqLogicAttr form-control input-sm" data-l1key="configuration" data-l2key="baudrate">
										<option value="1200">{{1200}}</option>
										<option value="2400">{{2400}}</option>
										<option value="4800">{{4800}}</option>
										<option value="9600">{{9600}}</option>
										<option value="14400">{{14400}}</option>
										<option value="19200">{{19200}}</option>
										<option value="38400">{{38400}}</option>
										<option value="57600">{{57600}}</option> 
										<option value="115200">{{115200}}</option> 
									</select>
								</div>
							</div>
							<div class="form-group">
								<label class="col-sm-2 control-label" >
									{{Type de control de flux}}
									<sup>
										<i class="fa fa-question-circle tooltips" title="Séléctionner le type de control de flux de la connexion" style="font-size : 1em;color:grey;"></i>
									</sup>
								</label>
								<div class="col-sm-9">
									<select class="eqLogicAttr form-control input-sm" data-l1key="configuration" data-l2key="flowcontrol">
										<option value="FLOW_HARDWARE">{{FLOW_HARDWARE}}</option>
										<option value="FLOW_NONE">{{FLOW_NONE}}</option>
									</select>
								</div>
							</div>
							<div class="form-group">
								<label class="col-sm-2 control-label" >
									{{Parité}}
									<sup>
										<i class="fa fa-question-circle tooltips" title="Séléctionner la parité de la connexion" style="font-size : 1em;color:grey;"></i>
									</sup>
								</label>
								<div class="col-sm-9">
									<select class="eqLogicAttr form-control input-sm" data-l1key="configuration" data-l2key="parity">
										<option value="PARITY_NO">{{PARITY_NO}}</option>
										<option value="PARITY_ODD">{{PARITY_ODD}}</option>
										<option value="PARITY_EVEN">{{PARITY_EVEN}}</option>
									</select>
								</div>
							</div>
						</fieldset> 
					</form>
				</div>
			</div>
			<div role="tabpanel" class="tab-pane" id="commandtab">	
				<a class="btn btn-success btn-sm cmdAction pull-right" data-action="add" style="margin-top:5px;">
					<i class="fa fa-plus-circle"></i> 
					{{Commandes}}
				</a>
				<br/><br/>
				<table id="table_cmd" class="table table-bordered table-condensed">
					<thead>
						<tr>
							<th ></th>
							<th>{{Nom}}</th>
							<th>{{Commande}}</th>
							<th>{{Paramètre}}</th>
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
