$("#table_cmd").sortable({axis: "y", cursor: "move", items: ".cmd", placeholder: "ui-state-highlight", tolerance: "intersect", forcePlaceholderSize: true});
$('body').on('click','.cmdAttr[data-l1key=configuration][data-l2key=type]',function(){
	//Ajout des parametre de configuration spécific a chaque type
	$(this).closest('tr').find('.cmdAttr[data-l1key=configuration][data-l2key=methode]').html('');
	var paramerter=$(this).closest('tr').find('.cmdAttr[data-l1key=configuration][data-l2key=methode]').parent().parent();
	switch($(this).val()){
	       case 'ir':
			paramerter.append($('<select class="cmdAttr form-control input-sm" data-l1key="configuration" data-l2key="mode">')
				.append($('<option>').attr('value','IR').text('IR'))
				.append($('<option>').attr('value','SENSOR').text('SENSOR'))
				.append($('<option>').attr('value','SENSOR_NOTIFY').text('SENSOR_NOTIFY'))
				.append($('<option>').attr('value','IR_NOCARRIER').text('IR_NOCARRIER')));
			/*paramerter.append($('<select class="cmdAttr form-control input-sm" data-l1key="configuration" data-l2key="methode">')
				.append($('<option>').attr('value','sendir').text('sendir'))
				.append($('<option>').attr('value','completeir').text('completeir'))
				.append($('<option>').attr('value','stopir').text('stopir')));	*/
		break;
		case 'serial':
			paramerter.append($('<select class="cmdAttr form-control input-sm" data-l1key="configuration" data-l2key="baudrate">')
				.append($('<option>').attr('value','1200').text('1200'))
				.append($('<option>').attr('value','2400').text('2400'))
				.append($('<option>').attr('value','4800').text('4800'))
				.append($('<option>').attr('value','9600').text('9600'))
				.append($('<option>').attr('value','19200').text('19200'))
				.append($('<option>').attr('value','38400').text('38400'))
				.append($('<option>').attr('value','57600').text('57600')));	
			paramerter.append($('<select class="cmdAttr form-control input-sm" data-l1key="configuration" data-l2key="flowcontrol">')
				.append($('<option>').attr('value','FLOW_HARDWARE').text('FLOW_HARDWARE'))
				.append($('<option>').attr('value','FLOW_NONE').text('FLOW_NONE')));
			paramerter.append($('<select class="cmdAttr form-control input-sm" data-l1key="configuration" data-l2key="parity">')
				.append($('<option>').attr('value','PARITY_NO').text('PARITY_NO'))
				.append($('<option>').attr('value','PARITY_ODD').text('PARITY_ODD'))
				.append($('<option>').attr('value','PARITY_EVEN').text('PARITY_EVEN')));
			paramerter.append($('<select class="cmdAttr form-control input-sm" data-l1key="configuration" data-l2key="codage">')
				.append($('<option>').attr('value','ASCII ').text('ASCII'))
				.append($('<option>').attr('value','JS').text('JS'))
				.append($('<option>').attr('value','HEXA').text('HEXA')));
		break;
	}
});
function addCmdToTable(_cmd) {
	var tr =$('<tr class="cmd" data-cmd_id="' + init(_cmd.id) + '">');
  	tr.append($('<td>')
		.append($('<i class="fa fa-minus-circle pull-right cmdAction cursor" data-action="remove">'))
		.append($('<i class="fa fa-arrows-v pull-left cursor bt_sortable" style="margin-top: 9px;">')));
	tr.append($('<td>')
			.append($('<input type="hidden" class="cmdAttr form-control input-sm" data-l1key="id">'))
			.append($('<input class="cmdAttr form-control input-sm" data-l1key="name" value="' + init(_cmd.name) + '" placeholder="{{Name}}" title="Name">')));
	tr.append($('<td>')
			.append($('<input class="cmdAttr form-control input-sm" data-l1key="logicalId" placeholder="{{Adresse}}" title="Adresse">')));
	tr.append($('<td>')
		.append($('<div >')
			.append($('<label>')
				.text('{{Type de connexion}}')
				.append($('<sup>')
					.append($('<i class="fa fa-question-circle tooltips" style="font-size : 1em;color:grey;">')
					.attr('title','Choisissez le type de commande'))))
			.append($('<select class="cmdAttr form-control input-sm" data-l1key="configuration" data-l2key="type">')
			       .append($('<option>')
				      .attr('value','ir')
				      .text('Infra-rouge'))
				.append($('<option>')
				      .attr('value','serial')
				      .text('RS232'))))
		  .append($('<div>')
			 .append($('<select class="cmdAttr form-control input-sm" data-l1key="configuration" data-l2key="methode">')))
		  .append($('<div>')
			.append($('<label>')
				.text('{{Retour d\'état}}')
				.append($('<sup>')
					.append($('<i class="fa fa-question-circle tooltips" style="font-size : 1em;color:grey;">')
					.attr('title','Choisissez un objet jeedom contenant la valeur de votre commande'))))
			.append($('<div class="input-group">')
				.append($('<input class="cmdAttr form-control input-sm" data-l1key="value">'))
				.append($('<span class="input-group-btn">')
					.append($('<a class="btn btn-success btn-sm bt_selectCmdExpression" id="value">')
						.append($('<i class="fa fa-list-alt">')))))));
	tr.append($('<td>')	
		.append($('<div class="parametre">')
			.append($('<span class="type" type="' + init(_cmd.type) + '">')
				.append(jeedom.cmd.availableType()))
			.append($('<span class="subType" subType="'+init(_cmd.subType)+'">'))));
		var parmetre=$('<td>');
	if (is_numeric(_cmd.id)) {
		parmetre.append($('<a class="btn btn-default btn-xs cmdAction" data-action="test">')
			.append($('<i class="fa fa-rss">')
				.text('{{Tester}}')));
	}
	parmetre.append($('<a class="btn btn-default btn-xs cmdAction tooltips" data-action="configure">')
		.append($('<i class="fa fa-cogs">')));
	parmetre.append($('<a class="btn btn-default btn-xs cmdAction tooltips" data-action="copy" title="{{Dupliquer}}">')
		.append($('<i class="fa fa-files-o">')));
	parmetre.append($('<div>')
		.append($('<span>')
			.append($('<label class="checkbox-inline">')
				.append($('<input type="checkbox" class="cmdAttr checkbox-inline" data-size="mini" data-label-text="{{Historiser}}" data-l1key="isHistorized" checked/>'))
				.append('{{Historiser}}')
				.append($('<sup>')
					.append($('<i class="fa fa-question-circle tooltips" style="font-size : 1em;color:grey;">')
					.attr('title','Souhaitez vous Historiser les changements de valeur'))))));
	parmetre.append($('<span>')
			.append($('<label class="checkbox-inline">')
				.append($('<input type="checkbox" class="cmdAttr checkbox-inline" data-size="mini" data-label-text="{{Afficher}}" data-l1key="isVisible" checked/>'))
				.append('{{Afficher}}')
				.append($('<sup>')
					.append($('<i class="fa fa-question-circle tooltips" style="font-size : 1em;color:grey;">')
					.attr('title','Souhaitez vous afficher cette commande sur le dashboard')))));
	tr.append(parmetre);
	$('#table_cmd tbody').append(tr);
	$('#table_cmd tbody tr:last').setValues(_cmd, '.cmdAttr');
	jeedom.cmd.changeType($('#table_cmd tbody tr:last'), init(_cmd.subType));
}
