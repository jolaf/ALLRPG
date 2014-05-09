$(document).ready(function(){
	$('button').attr('type','button');
	$('button').button();
	$('.inputcheckbox, .inputradio, input[type="file"]').styler();

	$('span.jq-checkbox.mustbe').each(function() {
		$(this).after('<div class="checkbox_mustbe"></div>');
	});

	$(document).on('click','.calendar_table td[rel-date]',function() {
		reldate=$(this).attr('rel-date');
		block_double=false;
		$('html, body').animate({
			scrollTop: $(".content").offset().top
		}, function() {
			if($('.menutable .string1:visible').length && !block_double) {
				$('.menutable .string1:visible').hide(400, function() {
					$('.menutable tr[dates~="'+reldate+'"]').show(400);
				});
			}
			else {
				block_double=true;
				$('.menutable tr[dates~="'+reldate+'"]').show(400);
			}
		});
	});

	if($('select[name="team"]').val()==0) {
		$('#div_teamkolvo').next().hide();
		$('#div_teamkolvo').next().next().hide();
		$('#div_teamkolvo').hide();
		$('#help_teamkolvo').hide();
		$('#name_teamkolvo').hide();
	}

	$('select[name="team"]').on('change',function() {
		if($(this).val()==1) {
			$('#div_teamkolvo').next().show();
			$('#div_teamkolvo').next().next().show();
			$('#div_teamkolvo').show();
			$('#name_teamkolvo').show();
		}
		else {
			$('#div_teamkolvo').next().hide();
			$('#div_teamkolvo').next().next().hide();
			$('#div_teamkolvo').hide();
			$('#help_teamkolvo').hide();
			$('#name_teamkolvo').hide();
		}
	});

	$('textarea[name="rolevalues"]').each(function(){
		if($(this).val()=='' && $('select[name="roletype"]').val()!='select' && $('select[name="roletype"]').val()!='multiselect') {
			$(this).attr('disabled',true);
		}
	});

	$('select[name="roletype"]').on('change',function(){
		if($(this).val()=='select' || $(this).val()=='multiselect') {
			$('textarea[name="rolevalues"]').attr('disabled',false);
		}
		else {
			$('textarea[name="rolevalues"]').attr('disabled',true);
			$('textarea[name="rolevalues"]').val('');
		}
	});

	$(document).on('focus','textarea[name="rolevalues"]',function() {
		if($('select[name="roletype"]').val()=='select' || $('select[name="roletype"]').val()=='multiselect') {
			if($(this).val()=='') {
				$(this).val('[1][]\r\n[2][]\r\n[3][]');
			}
		}
	});

	$(document).on('keydown','textarea[name="rolevalues"]',function(e) {
		if($('select[name="roletype"]').val()=='select' || $('select[name="roletype"]').val()=='multiselect') {
			if(e.keyCode==13) {
				val=$(this).val();
				if(val.length==$(this).prop("selectionStart")) {
					e.preventDefault();
					values=val.match(/\[\d\]/g);
					lastone=values[values.length-1].replace("\[","").replace("\]","");
					lastone++;
					$(this).val($(this).val()+'\r\n['+lastone+'][]');
					$(this).scrollTop($(this)[0].scrollHeight - $(this).height());
				}
			}
		}
	});

	$(document).on('keyup','input[type="text"][maxlength],input[type="password"][maxlength]',function() {
		ok_red($(this),$(this).val().length<=$(this).attr('maxlength'),'Излишнее количество символов!');
	});

	$(document).on('keyup','input[type="text"][minlength],input[type="password"][minlength]',function() {
		ok_red($(this),$(this).val().length>=$(this).attr('minlength'),'Недостаточное количество символов!');
	});

	$(document).on('keyup','input[type="password"][name$="2"].mustbe',function() {
		ok_red($(this),$(this).val()==$('input[type="password"][name="'+$(this).attr('name').substring(0,$(this).attr('name').length-1)+'"].mustbe').val(),'Введеные пароли не совпадают!');
	});

	$(document).on('change','input[type="text"][name="em"].mustbe',function() {
		var regex = /^([а-яёА-ЯЁa-zA-Z0-9_\.\-\+])+\@(([а-яёА-ЯЁa-zA-Z0-9\-])+\.)+([а-яёА-ЯЁa-zA-Z0-9]{2,4})+$/;
		ok_red($(this),regex.test($(this).val()),'Неверный формат e-mail!');
	});

	$('.colorSelector').each(function() {
		$(this).outerHeight($(this).next('input').outerHeight());
	});
	$('.cpkr').colorpicker({
		parts: ['map', 'bar', 'hex', 'rgb', 'alpha', 'preview', 'footer'],
		showNoneButton: true,
		alpha: true,
		colorFormat: 'RGBA',
		select: function(event, color) {
			$(this).trigger('change');
		},
	});
	$(document).on('change','.cpkr', function () {
		$(this).prev('.colorSelector').css('backgroundColor', $(this).val());
	});
	$(document).on('click','.colorSelector', function () {
		$(this).next('.cpkr').trigger('click');
	});

	$(document).on('click','button:not(.careful)',function() {
		if(typeof $(this).attr('href')!='undefined') {
			window.location=$(this).attr('href');
		}
	});

	$.noty.defaults.layout="bottomRight";
	$.noty.defaults.closeWith=['click'];

    $("#qwerty").autocomplete({
      source: "http://www.allrpg.info/helpers/search.php",
      minLength: 3,
      appendTo: '#qwerty-container',
      select: function(e, ui){
      	$("#qwerty").val(ui.item.value);
      	$("#qwerty_form").submit();
      },
      response: function(event, ui) {
          if (ui.content) {
              $("#qwerty-empty-message").hide();
          } else {
              $("#qwerty-empty-message").show();
          }
      },
    });
    $("#qwerty").on("change keyup",function() {
		value = $(this).val();
		if(value.length<=2 && value.length>0) {
			$("#qwerty-helper").show();
			$("#qwerty-empty-message").hide();
		}
		else if(value.length==0) {
			$("#qwerty-empty-message").hide();
			$("#qwerty-helper").hide();
		}
		else {
			$("#qwerty-helper").hide();
		}
    });
    $("#qwerty").on('blur',function() {
		$("qwerty-helper").hide();
    });

	$('.tile a').each(function() {
		var html = $(this).html().replace("<br>"," ");
		html = html.replace("<hr>"," ");
		var parts = html.split(' ');
		var word=parts.reduce(function (x, y) { return x.length > y.length ? x : y; });
		var size=100-((word.length-7)*10);
		if(size<100) {
			$(this).css('font-size',size+'%');
		}
	});

	$('.tile_enter .text').click(function() {
		$('.tile_enter #login_choice').height(0);
		$('.tile_enter #login_choice').show();
		$('.tile_enter .text').animate({
			height: '0%'
		});
		$('.tile_enter .text').hide();
		$('.tile_enter #login_choice').animate({
			height: '100%'
		});
	});
	$(document).click(function(e) {
		if(!$(e.target).closest('#tile_enter').length) {
			$('.tile_enter #login_choice').hide();
			$('.tile_enter #login_remind').hide();
			$('.tile_enter .text').show();
			$('.tile_enter .text').height('');
		}
		if(!$(e.target).closest('#login').length) {
			$('.login_options').hide();
		}
		if(!$(e.target).closest('.tile_myorders2').length && !$(e.target).closest('.tile_myorders').length) {
			$('.tile_myorders2').css('display','none');
			$('.tile_myorders').css('display','block');
		}
		if(!$(e.target).closest('.tile_mysitesorders2').length && !$(e.target).closest('.tile_mysitesorders').length) {
			$('.tile_mysitesorders2').css('display','none');
			$('.tile_mysitesorders').css('display','block');
		}
	});

	$("#btn_make_remind").button("disable");
	$("#btn_login").button("disable");

	$("#btn_remind").on('click',function(e){
		$('.tile_enter #login_choice').animate({
				height: '0%'
			},function(){
				$(this).hide();
				$('.tile_enter #login_remind').show();
				$('.tile_enter #login_remind').animate({
					height: '100%'
				});
			});
	});
	$("#login, #pass").on("change keyup",function(e){
		if($("#login").val()!="") {
			$("#btn_login").button("enable");
		}
		else {
			$("#btn_login").button("disable");
		}
	});
	$("#em").on("change keyup",function(){
		if($(this).val()!="") {
			$("#btn_make_remind").button("enable");
		}
		else {
			$("#btn_make_remind").button("disable");
		}
	});
	$("#btn_make_remind").on('click',function(e){
		var regex = /^([а-яёА-ЯЁa-zA-Z0-9_\.\-\+])+\@(([а-яёА-ЯЁa-zA-Z0-9\-])+\.)+([а-яёА-ЯЁa-zA-Z0-9]{2,4})+$/;
	  	if(regex.test($("#em").val())) {
			$(this).closest("form").submit();
		}
		else {
			noty({text: "Неверный формат e-mail!",type:"error",timeout:5000});
		}
		return false;
	});

	$('.login .text').click(function() {
		if($('.login_options.big').not('.remind').length) {
			$('.login_options.big').not('.remind').css('left',$(this).parent().offset().left);
			$('.login_options.big').not('.remind').css('top',$(this).parent().offset().top);
			$('.login_options.big').not('.remind').outerWidth($(this).parent().outerWidth());
			$('.login_options.big').not('.remind').height(0);
			$('.login_options.big').not('.remind').show();
			$('.login_options.big').not('.remind').animate({
				height: $('.header_right').height()-2
			});
		}
		else {
			$('.login_options').height(0);
			$('.login_options').css('left',$(this).parent().offset().left);
			$('.login_options').css('top',$(this).parent().offset().top);
			$('.login_options').outerWidth($(this).parent().outerWidth());
			$('.login_options').show();
			$('.login_options').animate({
				height: $('.login').outerHeight(true)+$('.qwerty_space').outerHeight()
			});
		}
	});

	$("#btn_make_remind_global").button("disable");
	$("#btn_login_global").button("disable");

	$("#btn_remind_global").on('click',function(e){
		$('.login_options.big').not('.remind').animate({
				height: '0%'
			},function(){
				$(this).hide();
				$('.login_options.remind').height(0);
				$('.login_options.remind').css('left',$(this).parent().offset().left);
				$('.login_options.remind').css('top',$(this).parent().offset().top);
				$('.login_options.remind').outerWidth($(this).parent().outerWidth());
				$('.login_options.remind').show();
				$('.login_options.remind').animate({
					height: $('.header_right').height()-2
				});
			});
	});
	$("#login_global, #pass_global").on("change keyup",function(e){
		if($("#login_global").val()!="") {
			$("#btn_login_global").button("enable");
		}
		else {
			$("#btn_login_global").button("disable");
		}
	});
	$("#em_global").on("change keyup",function(){
		if($(this).val()!="") {
			$("#btn_make_remind_global").button("enable");
		}
		else {
			$("#btn_make_remind_global").button("disable");
		}
	});
	$("#btn_make_remind_global").on('click',function(e){
		var regex = /^([а-яёА-ЯЁa-zA-Z0-9_\.\-\+])+\@(([а-яёА-ЯЁa-zA-Z0-9\-])+\.)+([а-яёА-ЯЁa-zA-Z0-9]{2,4})+$/;
	  	if(regex.test($("#em_global").val())) {
			$(this).closest("form").submit();
		}
		else {
			noty({text: "Неверный формат e-mail!",type:"error",timeout:5000});
		}
		return false;
	});

	$(window).keydown(function(e){
    	if(e.which === 9){ //tab
    		if(e.target.id!='login_global') {
	    		e.preventDefault();
	    		var selected = $('.selected');
	    		var focused = $(':focus');
	    		if(selected.length) {
					var tabIndex = +selected.attr('tabIndex') + 1; //plus sign at beginnign converts it to a number
				}
				else if(focused.length) {
					var tabIndex = +focused.attr('tabIndex') + 1; //plus sign at beginnign converts it to a number
				}
				else {
					var tabIndex = 1;
				}
				var next = $('[tabIndex=' + tabIndex + ']');
				if(focused.length) {
					next.focus();
				}
				next.click();
			}
			else {
				$('#pass_global').click();
			}
    	}
    });
    $(document).on('change keyup','.sarissa',function(e) {
    	if(e.type=="change" && $(this).prop('tagName').toLowerCase() == 'input') {
    		return false;
    	}
    	if(!($.isNumeric($(this).val()))) {
        	if($(this).val().length<3) {
	        	var target=$('#'+$(this).attr('target'));
	        	target.attr('disabled',true);
    			target.children().remove();
	    		target.append($('<option>').text('введите не менее 3 символов...'));
	    		return false;
	    	}
    	}
    	var $this=$(this);
    	var url=$this.attr('action');
    	var target=$this.attr('target');
    	var defaultchoicename=$this.attr('defaultchoicename');
    	url=url+'input='+$this.val();
    	var target2=target;
    	while(typeof target2!='undefined') {
    		target2=$('#'+target2);
    		target2.attr('disabled',true);
    		target2.children().remove();
    		target2=target2.attr('target');
    	}
    	target=$('#'+target);
    	target.append($('<option>').text('идет поиск...'));
    	$.get(url, function(jsonData) {
    		//console.log(jsonData);
    		target.children().remove();
    		if(typeof defaultchoicename!='undefined') {
    			target.append($('<option>').text('- '+defaultchoicename+' -'));
    		}
    		else {
    			target.append($('<option>').text('- Выбрать из найденного -'));
    		}
           	$.each(jsonData,function (i,value) {
           		target.append($('<option>').text(value['value']).val(value['id']));
           	});
           	target.removeAttr('disabled');
    	},'json');
    });
	$('input[type="text"], input[type="password"], textarea, select').each( function () {
    	var field=$(this);
    	if(typeof field.attr('placehold')!='undefined') {
	    	var placehold=field.attr('placehold');
	    	if(field.val()=='') {
	    		field.val(placehold);
	    		field.css('color','#999999');
	    	}
	    	field.on('focus',function() {
				if($(this).val()==placehold) {
					$(this).val('');
					$(this).css('color','black');
				}
	    	});
	    	field.on('blur',function() {
				if($(this).val()=='') {
					$(this).val(placehold);
					$(this).css('color','#999999');
				}
	    	});
		}
	});

	$(document).on('click','.fieldvalue', function (e) {
   		var check_name = $('.selected').attr("id");
   		if(typeof check_name!='undefined') {
	   		check_name=check_name.substring(5);
	   	}
	   	else {
	   		check_name='';
	   	}
	   	var $this=$(this);
   		var name=$this.attr("id");
   		name=name.substring(4);
   		if(check_name!=name) {
	   		$('.help').hide();
	   		$('.fieldname').removeClass('selected');
	   		var df=$('#choice_'+name);
	   		var dropfields=$('.dropfield2:visible').not(df);
	   		if(dropfields.length) {
	   			$('.dropfield.hovered').not(df).removeClass('hovered');
	   			dropfields.animate({
					height: '0px'
					},function(){
						$(this).hide();
					}
				);
	   		}
	   		showHelpAndName(name);
	   	}
	   	else {
	   		showHelpAndName(name);
	   	}
	   	e.stopPropagation();
	});
	$(document).on('click','.fieldname', function(e) {
	    var name=$(this).attr("id");
	    name=name.substring(5);
		$("[id=div_"+name+"]").click();
		if($("[id=selected_"+name+"]").length) {
			$("[id=selected_"+name+"]").click();
		}
		if($("[name="+name+"]").length) {
			$("[name="+name+"]").focus();
		}
		e.stopPropagation();
	});
	$(document).on('click','.dropfield', function(e) {
		var df=$(this);
		var df2=df.next('.dropfield2');

		var dropfields=$('.dropfield2:visible').not(df2);
   		if(dropfields.length) {
   			$('.dropfield.hovered').not(df).removeClass('hovered');
   			dropfields.animate({
				height: '0px'
				},function(){
					$(this).hide();
				}
			);
   		}

		if(df2.is(":visible")) {
			df2.animate({
				height: '0px'
				},function(){
                    df.removeClass('hovered');
					df2.hide();
				}
			);
		}
		else {
			df2.outerWidth(df.outerWidth()-4);
			df.addClass('hovered');
			df2.show();
			df2.animate({
				height: '160px'
			});
		}
	});
	$(document).on('change','.dropfield2 .inputcheckbox', function(e) {
		var df2=$(this).parent().parent();
		var df=df2.prev('.dropfield');
		var i=0;
		df.children().remove();
		df2.children('div').each(function() {
			var $this=$(this).children('.inputcheckbox');
			var text=$this.next('label').text();
			if($this.is(':checked')) {
				i++;
				$('<div>'+text+'</div>').appendTo(df);
			}
		});
		if(i==0) {
			$('<div>– Выбрать –</div>').appendTo(df);
		}
	});
	$(document).on('change','.dropfield2 .inputradio', function(e) {
		var df2=$(this).parent().parent();
		var df=df2.prev('.dropfield');
		df.children().remove();
		if($(this).val()!='') {
			$('<div>'+$('label[for="'+$(this).attr('id')+'"]').text()+'</div>').appendTo(df);
		}
		else {
			$('<div>– Выбрать –</div>').appendTo(df);
		}
		df.trigger('click');
	});
	$(document).on('click','.dropfield2, .searchtable .dropfield, .maininfotable .dropfield', function(e) {
		e.stopPropagation();
	});
	$(document).on('click', function(){
		$('.help').hide();
	   	$('.fieldname').removeClass('selected');
	   	var dropfields=$('.dropfield2:visible');
   		if(dropfields.length) {
   			dropfields.animate({
				height: '0px'
				},function(){
					$(this).hide();
					$('.dropfield.hovered').removeClass('hovered');
				}
			);
   		}
	});
	$(window).on('scroll', function () {
    	$(".help").hide();
	});
	$(document).on('keydown','input[type="text"], input[type="password"]', function(e){
		if (e.keyCode == 13) {
			e.preventDefault();
			if($(this).closest("form").length) {
				$(this).closest("form").submit();
			}
			else if ($(this).parent().prevAll("form:first").length) {
				$(this).parent().prevAll("form:first").submit();
			}
			else {
				$(this).parent().parent().prevAll("form:first").submit();
			}
		}
	});
	$(document).on('keydown','select, textarea', function(e){
		if (e.ctrlKey && e.keyCode == 13) {
			e.preventDefault();
			if($(this).closest("form").length) {
				$(this).closest("form").submit();
			}
			else if ($(this).parent().prevAll("form:first").length) {
				$(this).parent().prevAll("form:first").submit();
			}
			else {
				$(this).parent().parent().prevAll("form:first").submit();
			}
		}
	});
	$(".dpkr").datepicker({
		regional: 'ru',
		changeMonth: true,
		changeYear: true,
	});

	$(document).on('click','button.main',function(e){
		if($(this).closest("form").length) {
			$(this).closest("form").submit();
		}
		else if ($(this).parent().prevAll("form:first").length) {
			$(this).parent().prevAll("form:first").submit();
		}
		else {
			$(this).parent().parent().prevAll("form:first").submit();
		}
	});
	filesChanged=false;
	$(document).on('change','input[type=file]',function() {
		filesChanged=true;
	});
	do_submit=false;
	$(document).on('submit','form',function(e){
	    $this=$(this);
	    if($this.find('button.main').length) {
	    	var btn=$this.find('button.main');
	    }
	    else {
	    	var btn=$this.nextAll('tr').children('td').children('button.main:first');
	    }
	    btn.button("disable");
		$('.ui-button-text',btn).css('color','transparent');
		$('.ui-button-text',btn).addClass('button_load');
		if(!filesChanged && $this.attr('id')!='qwerty_form' && !do_submit) {
			var href=$this.attr('action');
			var data=$this.serialize();
			data+='&dynrequest=1';
		    $.post(href, data, function(jsonData) {
		    	//console.log(jsonData);
		    	do_submit=false;
		    	if(typeof jsonData['redirect']!='undefined') {
		    		if(jsonData['redirect']=='stayhere') {
		    			location.reload();
		    		}
		    		else if(jsonData['redirect']=='submit') {
		    			do_submit=true;
		    			$this.submit();
		    		}
		    		else {
		    			window.location=jsonData['redirect'];
		    		}
		    	}
		    	else {
			    	if(typeof jsonData['errors']!='undefined') {
			    		for(var key in jsonData['errors']) {
							var tmt=jsonData['errors'][key][1].length*25;
							if(tmt<5000) {
								tmt=5000;
							}
							noty({text: jsonData['errors'][key][1],type:jsonData['errors'][key][0],timeout:tmt});
						}
			    	}
			    	$('.red').removeClass('red');
			    	if(typeof jsonData['fields']!='undefined') {
			    		for(var key in jsonData['fields']) {
							var val=jsonData['fields'][key];
							if($.isNumeric(val)) {
								$('#line_'+val).addClass('red');
							}
							else {
								$('#name_'+val).addClass('red');
							}
						}
			    	}
			    	if(typeof jsonData['fields']=='undefined') {
			    		var timestamp=Math.round(new Date().getTime() / 1000);
			    		$('.timestamp').val(timestamp);
			    	}
			    	$('.ui-button-text',btn).removeClass('button_load');
			    	$('.ui-button-text',btn).css('color','');
			    	btn.button("enable");
		    	}
		    },'json');
	    	return false;
	  	}
	  	do_submit=false;
  	});
	$(document).on('click','.careful',function(e){
		var btn=$(this);
		noty({
		  text: 'Вы уверены?',
		  modal: true,
		  layout: 'center',
		  buttons: [
		    {addClass: 'btn btn-primary', text: 'Да', onClick: function($noty) {
		        $noty.close();
		        var href=btn.attr('href');
				if(typeof href != 'undefined') {
					$('.ui-button-text',btn).css('color','transparent');
					$('.ui-button-text',btn).addClass('button_load');
					$.get(href, function(jsonData) {
				    	//console.log(jsonData);
				    	if(typeof jsonData['redirect']!='undefined') {
				    		window.location=jsonData['redirect'];
				    	}
				    	else {
					    	if(typeof jsonData['errors']!='undefined') {
					    		for(var key in jsonData['errors']) {
									var tmt=jsonData['errors'][key][1].length*25;
									if(tmt<5000) {
										tmt=5000;
									}
									noty({text: jsonData['errors'][key][1],type:jsonData['errors'][key][0],timeout:tmt});
								}
					    	}
						}
					},'json');
				}
		      }
		    },
		    {addClass: 'btn btn-danger', text: 'Отмена', onClick: function($noty) {
		        $noty.close();
		      }
		    }
		  ]
		});
		return false;
	});

	$(document).on('click','.modal-window',function (e) {
		e.preventDefault();
		$.get($(this).attr("data-url"), function(data){
			$(data).modal({
				overlayId: 'modal-overlay',
				containerId: 'modal-container',
				overlayClose: true,
				onOpen: function (dialog) {
					dialog.overlay.fadeIn(200, function () {
						dialog.container.fadeIn(200, function () {
							dialog.data.fadeIn(200, function () {
								$('#modal-container .modal-content').animate({
									height: '100%'
								}, function () {
									$('.modal-content form').fadeIn('slow');
								});
							});
						});
					});
				},
				onClose: function (dialog) {
					$('#modal-container form').fadeOut('slow',function () {
						$('#modal-container .modal-content').animate({
							height: 40
						}, function () {
							dialog.data.fadeOut(200, function () {
								dialog.container.fadeOut(200, function () {
									dialog.overlay.fadeOut(200, function () {
										$.modal.close();
									});
								});
							});
						});
					});
				},
			});
		});
	});

	$(document).on('change','.inputtextarea',function() {
		var maxchar=$(this).attr('maxchar');
		textCounter($(this),maxchar);
	});

	$(document).on('change','.inputnum',function() {
		checknum($(this));
	});

	$(document).on('click','.block_open',function() {
		$('.tile').hide();
		$('#block_'+$(this).attr('block')).show();
		$('#block_'+$(this).attr('block')+' .tile').show();
	});

	$(document).on('click','.block_close',function() {
		$('.menu_block').hide();
		$('.menu_wrapper .tile').show();
	});

	$('.menu a').hover(function() {
			$(this).addClass('arrow_down');
		},function() {
            $(this).removeClass('arrow_down');
		}
	);
	$('.arrow_up').hover(function() {
			$(this).removeClass('arrow_up');
			$(this).addClass('arrow_down');
		},function() {
            $(this).removeClass('arrow_down');
			$(this).addClass('arrow_up');
		}
	);
	$('.arrow_down').hover(function() {
			$(this).removeClass('arrow_down');
			$(this).addClass('arrow_up');
		},function() {
            $(this).removeClass('arrow_up');
			$(this).addClass('arrow_down');
		}
	);

	if('.login .messages_count'.length) {
		setInterval(function() {
			var htm=$('.login .text').html();
			$('.login .text').html($('.login .messages_count').html());
			$('.login .messages_count').html(htm);
		},5000);
	}

    scaleFont();
    show_errors();

    $('.wysihtml5-editor').each(function(){
	    var $this=$(this);
	    var editor = new wysihtml5.Editor($this.attr('id'), {
	      toolbar:     $this.attr('id')+"-toolbar",
	      parserRules: wysihtml5ParserRules
	    }).on('focus',function(){$this.closest('div').trigger('click');})
	});

    menuDraw($('nav'));

    $(window).resize(function() {
    	if($('.login_options.big').not('.remind').length) {
    		$('.login_options.big').hide();
    	}
    	else {
    		$('.login_options').hide();
    	}
    	scaleFont();
    	menuDraw($('nav'));
    	$('.colorSelector').each(function() {
			$(this).outerHeight($(this).next('input').outerHeight());
		});
    });
});

function menuDraw(nav) {
   	if(typeof nav.attr('data_height')!='undefined' && typeof nav.attr('data_width')!='undefined') {
   		var modifier=nav.outerWidth()/nav.attr('data_width');
   		$('.maincontent').outerHeight(nav.attr('data_height')*modifier);
   		$('.maincontent').css('padding','0px');
   		$('.maincontent').css('background','none');
   		$('.additional_commands').hide();
   		nav.children('div').children('li').children('a').css('border-radius',(nav.outerWidth()/50)+'px');
   		nav.children('div').children('li').children('div').css('border-radius',(nav.outerWidth()/50)+'px');
   		$('.avatar').css('border-top-left-radius',(nav.outerWidth()/50)+'px');
   		$('.avatar').css('border-top-right-radius',(nav.outerWidth()/50)+'px');
   	}
}

function scaleFont() {
  var viewPortWidth = $(window).width();
  var viewPortHeight = $(window).height();

  if (viewPortWidth >= 1900) {
  	$('body').css('font-size','75%');
  	$('div.fullpage').css('paddingRight','5%');
  	$('div.fullpage').css('paddingLeft','5%');
  }
  else if (viewPortWidth >= 1400) {
  	$('body').css('font-size','75%');
  	$('div.fullpage').css('paddingRight','3%');
  	$('div.fullpage').css('paddingLeft','3%');
  }
  else if (viewPortWidth >= 1200) {
  	$('body').css('font-size','75%');
  	$('div.fullpage').css('paddingRight','3%');
  	$('div.fullpage').css('paddingLeft','3%');
  }
  else if (viewPortWidth >= 1000) {
  	$('body').css('font-size','62.5%');
  }
  else if (viewPortWidth >= 700) {
  	$('body').css('font-size','50%');
  }
  else {
  	$('body').css('font-size','40%');
  }

  $('.header').css('height',$('.header').width()/6.286);
}

function showHelpAndName(name) {
	var helpName=$("#help_"+name);
	var divName=$("#div_"+name);
	if(helpName.length && !divName.hasClass('read')) {
		helpName.outerWidth(divName.children('#'+name+'-styler,#selected_'+name+',input[name='+name+'][type!=checkbox],select[name='+name+'],textarea[name='+name+'],#'+name+'-toolbar').outerWidth());
		if(helpName.outerWidth()<40) {
			helpName.outerWidth(divName.outerWidth()-parseInt(divName.css('paddingLeft').substring(0,divName.css('paddingLeft').length-2)));
		}
		var pos=divName.children('#'+name+'-styler,#selected_'+name+',input[name='+name+'][type!=checkbox],select[name='+name+'],textarea[name='+name+'],#'+name+'-toolbar').position();
		var height = helpName.height();
		helpName.css('margin-top',(-1-height));
		helpName.css('left',pos.left);
		helpName.show();
	}
	var nameName=$("#name_"+name);
	if(nameName.length) {
		nameName.addClass('selected');
	}
}

function show_errors() {
	for(var key in errors) {
		var tmt=errors[key][1].length*25;
		if(tmt<5000) {
			tmt=5000;
		}
		noty({text: errors[key][1],type:errors[key][0],timeout:tmt});
	}
    errors=[];
}

function checknum(that){
	if(that.val()=="") {
		that.val("0");
	}
	else {
		t=parseFloat(that.val());
		if (isNaN(t)) {
			that.val("0");
			noty({text: "В данное поле можно вводить только цифры!",type:"error",timeout:5000});
		}
		else {
			if(t<0)
			{
				t=t+(t*2*-1);
			};
			that.val(t);
		}
	}
}

function textCounter(field, maxlimit) {
	var val=field.val();
	if (val.length > maxlimit) {
		field.val(val.substring(0, maxlimit));
		noty({text: "В данном поле не может быть более "+maxlimit+" символов.",type:"error",timeout:5000});
	}
}

function getMultiList(url,ids,val,all) {
	if($.isArray(ids)) {
	}
	else {
		console.log('getMultiList now takes only an array as list of ids.');
		return false;
	}
	if(url!='' && val!='') {
		url=url+'?value='+val;
		for (var i = 0; i < ids.length; i++) {
			$('#selected_'+ids[i]).html('<div>идет поиск...</div>');
		}
    	$.get(url, function(jsonData) {
			for (var i = 0; i < ids.length; i++) {
 				$('#selected_'+ids[i]).html('<div>- Выбрать из найденного -</div>');

	           	content='';
	           	if(all!='') {
					content='<div><input type="checkbox" name="'+ids[i]+'[0]" id="'+ids[i]+'[0]" class="inputcheckbox"><label for="'+ids[i]+'[0]"> '+all+'</label></div>';
				}
	           	$.each(jsonData,function (z,value) {
	           		content+='<div><input type="checkbox" name="'+ids[i]+'['+value['id']+']" id="'+ids[i]+'['+value['id']+']" class="inputcheckbox"><label for="'+ids[i]+'['+value['id']+']"> '+value['value']+'</label></div>';
	           	});
 				$('#choice_'+ids[i]).html(content);
			}
           	$('.inputcheckbox').styler();
    	},'json');
    }
    else {
		for (var i = 0; i < ids.length; i++) {
			content='';
			if(all!='') {
				content='<div><input type="checkbox" name="'+ids[i]+'[0]" id="'+ids[i]+'[0]" class="inputcheckbox"><label for="'+ids[i]+'[0]"> '+all+'</label></div>';
			}
			$('#selected_'+ids[i]).html('<div>– Выбрать –</div>');
			$('#choice_'+ids[i]).html(content);
		}
		$('.inputcheckbox').styler();
    }
}

function newplayer(sid) {
	name=prompt("Введите ИНП игрока, которому хотите передать данную заявку. Внимание! Заявка станет видна данному пользователю целиком.", sid);
	if(name!=null && name!="" && name>0) {
		document.location="action=newplayer&sid="+name;
	}
}

function ok_red($this,allgood,title) {
	if($this.val()=='') {
		$this.removeClass('ok');
		$this.removeClass('attention_red');
		$this.attr('title','');
	}
	else if(!allgood) {
		$this.removeClass('ok');
		$this.addClass('attention_red');
		$this.attr('title',title);
	}
	else {
		$this.addClass('ok');
		$this.removeClass('attention_red');
		$this.attr('title','');
	}
}