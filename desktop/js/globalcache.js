$('.cmdAction[data-action=learn]').hide();
$("#table_cmd").sortable({axis: "y", cursor: "move", items: ".cmd", placeholder: "ui-state-highlight", tolerance: "intersect", forcePlaceholderSize: true});
$('.eqLogicAction[data-action=learnStart]').off().on('click', function () {
	var _this = this;
	$.ajax({
		type: "POST", 
		async: false,
		url: "plugins/globalcache/core/ajax/globalcache.ajax.php",
		data: {
			action: "IrLearn",
			id:$('.eqLogicAttr[data-l1key=id]').val()
		},
		dataType: 'json',
		error: function (request, status, error) {
			handleAjaxError(request, status, error);
		},
		success: function (data) { 
			if (data.state != 'ok') {
				$('#div_alert').showAlert({message: data.result, level: 'danger'});
				return;
			}
			/*$(_this).parents()
				.append($('<a class="btn btn-primary eqLogicAction pull-right" data-action="learnStop">')
					.append($('<i class="fa fa-bullseye">'))
					.append('{{Mode apprentissage}}'));*/
			$(_this).removeClass('btn-primary');
			$(_this).attr('data-action','');
			
		}
	});
});
$('.eqLogicAction[data-action=learnStop]').off().on('click', function () {
	var _this = this;
	$.ajax({
		type: "POST",
		async: false,
		url: "plugins/globalcache/core/ajax/globalcache.ajax.php",
		data: {
			action: "IrLearn",
			id:$('.eqLogicAttr[data-l1key=id]').val()
		},
		dataType: 'json',
		error: function (request, status, error) {
			handleAjaxError(request, status, error);
		},
		success: function (data) {
			if (data.state != 'ok') {
				$('#div_alert').showAlert({message: data.result, level: 'danger'});
				return;
			}
			$('.cmdAction[data-action=learn]').hide();
			$(_this).parents()
				.append($('<a class="btn btn-primary eqLogicAction pull-right" data-action="learnStart">')
					.append($('<i class="fa fa-bullseye">'))
					.append('{{Mode apprentissage}}'));
			$(_this).remove();
		}
	});
});learnStop
$('.changeIncludeState').off().on('click', function () {
	$.ajax({
		type: "POST",
		async: false,
		url: "plugins/globalcache/core/ajax/globalcache.ajax.php",
		data: {
			action: "changeIncludeState"
		},
		dataType: 'json',
		error: function (request, status, error) {
			handleAjaxError(request, status, error);
		},
		success: function (data) {
			if (data.state != 'ok') {
				$('#div_alert').showAlert({message: data.result, level: 'danger'});
				return;
			}
		}
	});
});

$('body').off().on('globalcache::IRL', function (_event,_options) {
	alert("Mode apprentissage actif");
	$('.cmdAction[data-action=learn]').show();
});
$('body').off().on('globalcache::includeDevice', function (_event,_options) {
	if (_options == '')
		window.location.reload();
	else
		window.location.href = 'index.php?v=d&p=globalcache&m=globalcache&id=' + _options;
});
$('body').off().on('change','.eqLogicAttr[data-l1key=configuration][data-l2key=type]',function(){
	$('.SerialParameter').hide();
	$('.IrParameter').hide();
	$('.cmdAttr[data-l1key=configuration][data-l2key=codage]').show(); 
	$('.cmdAttr[data-l1key=configuration][data-l2key=CR]').parent().show(); 
	$('.cmdAttr[data-l1key=configuration][data-l2key=LF]').parent().show(); 
	$('.cmdAttr[data-l1key=configuration][data-l2key=reponse]').parent().show(); 
	switch($(this).val()){
	       case 'ir':
			$('.IrParameter').show();
			$('.cmdAttr[data-l1key=configuration][data-l2key=codage]').val('DEC').hide(); 
			$('.cmdAttr[data-l1key=configuration][data-l2key=CR]').parent().hide(); 
			$('.cmdAttr[data-l1key=configuration][data-l2key=LF]').parent().hide(); 
			$('.cmdAttr[data-l1key=configuration][data-l2key=reponse]').parent().hide(); 
		break;
		case 'serial':
			$('.SerialParameter').show();
		break;
	}
});
function addCmdToTable(_cmd) {
	if (!isset(_cmd)) {
		var _cmd = {configuration: {}};
	}
	var tr =$('<tr class="cmd" data-cmd_id="' + init(_cmd.id) + '">');
  	tr.append($('<td>')
		.append($('<i class="fa fa-minus-circle pull-right cmdAction cursor" data-action="remove">'))
		.append($('<i class="fa fa-arrows-v pull-left cursor bt_sortable" style="margin-top: 9px;">')));
	tr.append($('<td>')
			.append($('<input type="hidden" class="cmdAttr form-control input-sm" data-l1key="id">'))
			.append($('<input class="cmdAttr form-control input-sm" data-l1key="name" value="' + init(_cmd.name) + '" placeholder="{{Name}}" title="Name">')));
	var td=$('<td>');
	if($('.eqLogicAttr[data-l1key=configuration][data-l2key=type]').val() != 'ir'){			
		td.append($('<div>')
			.append($('<label>')
				.text('{{Codage}}')
				.append($('<sup>')
					.append($('<i class="fa fa-question-circle tooltips" title="Séléctionner le type de codage" style="font-size :1em;color:grey;">'))))
			.append($('<div>')
				.append($('<select class="cmdAttr form-control input-sm" data-l1key="configuration" data-l2key="codage">')
				.append($('<option>').attr('value','ASCII').text('ASCII'))
				.append($('<option>').attr('value','DEC').text('DEC'))
					.append($('<option>').attr('value','HEXA').text('HEXA')))));
	}
	td.append($('<div>')
		.append($('<label >')
			.text('{{Valeur}}')
			.append($('<sup>')
				.append($('<i class="fa fa-question-circle tooltips" style="font-size : 1em;color:grey;">')
				.attr('title','Saisisser la valeur par defaut de votre commande'))))
		.append($('<div>')
			.append($('<textarea class="cmdAttr" data-l1key="configuration" data-l2key="value" style="margin: 0px; width: 500px; height: 95px;">'))));
	td.append($('<div>')
		.append($('<label class="checkbox-inline">')
			.append($('<input type="checkbox" class="cmdAttr" data-l1key="configuration" data-l2key="CR" checked>'))
			.append('{{Retour à la ligne}}'))
		.append($('<label class="checkbox-inline">')
			.append($('<input type="checkbox" class="cmdAttr" data-l1key="configuration" data-l2key="LF" checked>'))
			.append('{{Fin de ligne}}')));
	tr.append(td);	
	tr.append($('<td>')	
			.append($('<span class="type" type="' + init(_cmd.type) + '">')
				.append(jeedom.cmd.availableType()))
			.append($('<span class="subType" subType="'+init(_cmd.subType)+'">')));
	var parmetre=$('<td>');
	if($('.eqLogicAttr[data-l1key=configuration][data-l2key=type]').val() == 'ir' && init(_cmd.id)!=''){
		parmetre.append($('<a class="btn btn-success btn-xs cmdAction tooltips" data-action="learn">')
			.append($('<i class="fa fa-signal">')
				.text('{{Apprentissage}}')));
		parmetre.append($('</br>'));
	}
	if($('.eqLogicAttr[data-l1key=configuration][data-l2key=type]').val() != 'ir'){
		parmetre.append($('<label class="checkbox-inline">')
		      .append($('<input type="checkbox" class="cmdAttr" data-l1key="configuration" data-l2key="reponse">'))
		      .append('{{Attendre la réponse}}'));
	}
	if (is_numeric(_cmd.id)) {
		parmetre.append($('<a class="btn btn-default btn-xs cmdAction" data-action="test">')
			.append($('<i class="fa fa-rss">')
				.text('{{Tester}}')));
	}
	parmetre.append($('<a class="btn btn-default btn-xs cmdAction tooltips" data-action="configure">')
		.append($('<i class="fa fa-cogs">')));
	if(init(_cmd.id)!=''){
		parmetre.append($('<a class="btn btn-default btn-xs cmdAction tooltips" data-action="copy" title="{{Dupliquer}}">')
			.append($('<i class="fa fa-files-o">')));
	}
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
 	$('.eqLogicAttr[data-l1key=configuration][data-l2key=type]').trigger('change');
 	getMonitor($('.eqLogicAttr[data-l1key=id]').val());
	$('.cmdAction[data-action=learn]').off().on('click',function() {
		var _cmd = $(this).closest('.cmd');
		$.ajax({
			type: "POST", 
			async: false,
			url: "plugins/globalcache/core/ajax/globalcache.ajax.php",
			data: {
				action: "getCode",
				id:$('.eqLogicAttr[data-l1key=id]').val()
			},
			dataType: 'json',
			error: function (request, status, error) {
				handleAjaxError(request, status, error);
			},
			success: function (data) {
				if (data.state != 'ok') {
					$('#div_alert').showAlert({message: data.result, level: 'danger'});
					return;
				}
				_cmd.find('.cmdAttr[data-l1key=configuration][data-l2key=value]').text(data.result);
				//window.location.href = 'index.php?v=d&p=globalcache&m=globalcache&id=' + $('.eqLogicAttr[data-l1key=id]').val();
			}
		});
	});
}
function getMonitor(id) {
	$.ajax({
		type: 'POST',
		async: false,
		url: 'plugins/globalcache/core/ajax/globalcache.ajax.php',
		data: {
			action: 'getCacheMonitor',
			id:id
		},
		dataType: 'json',
		global: false,
		error: function(request, status, error) {
			setTimeout(function() {
				getMonitor(id)
			}, 100);
		},
		success: function(data) {
			if (data.state != 'ok') {
				$('#div_alert').showAlert({message: data.result, level: 'danger'});
				return;
			}
			$('#table_Monitor tbody').html('');
			if(data.result != false){
				var monitors=jQuery.parseJSON(data.result);
				jQuery.each(monitors.reverse(),function(key, value) {
				 	$('#table_Monitor tbody').append($("<tr>")
						.append($("<td>").text(value.datetime))
						.append($("<td>").text(value.sense))
						.append($("<td>").text(value.monitor)));
				});				
				$('#table_Monitor').trigger('update');
            		}
			setTimeout(function() {
				getMonitor(id)
			}, 100);
			
		}
	});
}
