/*
 * jQuery Form Styler v1.3.2
 * http://dimox.name/jquery-form-styler/
 *
 * Copyright 2012-2013 Dimox (http://dimox.name/)
 * Released under the MIT license.
 *
 * Date: 2013.01.27
 *
 */

(function($) {
	$.fn.styler = function(opt) {

		var opt = $.extend({
			idSuffix: '-styler',
			browseText: 'Р’С‹Р±СЂР°С‚СЊ',
			selectVisibleOptions: 0,
			singleSelectzIndex: '100'
		}, opt);

		return this.each(function() {
			var el = $(this);
			var id = '',
					cl = '',
					dataList = '';
			if (el.attr('id') !== undefined && el.attr('id') != '') id = ' id="' + el.attr('id') + opt.idSuffix + '"';
			if (el.attr('class') !== undefined && el.attr('class') != '') cl = ' ' + el.attr('class');
			var data = el.data();
			for (var i in data) {
				if (data[i] != '') dataList += ' data-' + i + '="' + data[i] + '"';
			}
			id += dataList;

			// checkbox
			if (el.is(':checkbox')) {
				el.css({position: 'absolute', left: -9999}).each(function() {
					if (el.next('span.jq-checkbox').length < 1) {
						var checkbox = $('<span' + id + ' class="jq-checkbox' + cl + '" style="display: inline-block"><span></span></span>');
						el.after(checkbox);
						if (el.is(':checked')) checkbox.addClass('checked');
						if (el.is(':disabled')) checkbox.addClass('disabled');
						// РєР»РёРє РЅР° РїСЃРµРІРґРѕС‡РµРєР±РѕРєСЃ
						checkbox.click(function() {
							if (!checkbox.is('.disabled')) {
								if (el.is(':checked')) {
									el.prop('checked', false);
									checkbox.removeClass('checked');
								} else {
									el.prop('checked', true);
									checkbox.addClass('checked');
								}
								el.change();
								return false;
							}
						});
						// РєР»РёРє РЅР° label
						el.parent('label').add('label[for="' + el.attr('id') + '"]').click(function(e) {
							checkbox.click();
							e.preventDefault();
						});
						// РїРµСЂРµРєР»СЋС‡РµРЅРёРµ РїРѕ Space РёР»Рё Enter
						el.change(function() {
							if (el.is(':checked')) checkbox.addClass('checked');
							else checkbox.removeClass('checked');
						})
						// С‡С‚РѕР±С‹ РїРµСЂРµРєР»СЋС‡Р°Р»СЃСЏ С‡РµРєР±РѕРєСЃ, РєРѕС‚РѕСЂС‹Р№ РЅР°С…РѕРґРёС‚СЃСЏ РІ С‚РµРіРµ label
						.keydown(function(e) {
							if (el.parent('label').length && (e.which == 13 || e.which == 32)) checkbox.click();
						})
						.focus(function() {
							if (!checkbox.is('.disabled')) checkbox.addClass('focused');
						})
						.blur(function() {
							checkbox.removeClass('focused');
						});
						// РѕР±РЅРѕРІР»РµРЅРёРµ РїСЂРё РґРёРЅР°РјРёС‡РµСЃРєРѕРј РёР·РјРµРЅРµРЅРёРё
						el.on('refresh', function() {
							if (el.is(':checked')) checkbox.addClass('checked');
								else checkbox.removeClass('checked');
							if (el.is(':disabled')) checkbox.addClass('disabled');
								else checkbox.removeClass('disabled');
						});
					}
				});

			// radio
			} else if (el.is(':radio')) {
				el.css({position: 'absolute', zIndex: '-5'}).each(function() {
					if (el.next('span.jq-radio').length < 1) {
						var radio = $('<span' + id + ' class="jq-radio' + cl + '" style="display: inline-block"><span></span></span>');
						el.after(radio);
						if (el.is(':checked')) radio.addClass('checked');
						if (el.is(':disabled')) radio.addClass('disabled');
						// РєР»РёРє РЅР° РїСЃРµРІРґРѕСЂР°РґРёРѕРєРЅРѕРїРєРµ
						radio.click(function() {
							if (!radio.is('.disabled')) {
								$('input[name="' + el.attr('name') + '"]').prop('checked', false).next().removeClass('checked');
								el.prop('checked', true).next().addClass('checked');
								el.change();
								return false;
							}
						});
						// РєР»РёРє РЅР° label
						el.parent('label').add('label[for="' + el.attr('id') + '"]').click(function(e) {
							radio.click();
							e.preventDefault();
						});
						// РїРµСЂРµРєР»СЋС‡РµРЅРёРµ СЃС‚СЂРµР»РєР°РјРё
						el.change(function() {
							$('input[name="' + el.attr('name') + '"]').next().removeClass('checked');
							el.next().addClass('checked');
						})
						.focus(function() {
							if (!radio.is('.disabled')) radio.addClass('focused');
						})
						.blur(function() {
							radio.removeClass('focused');
						});
						// РѕР±РЅРѕРІР»РµРЅРёРµ РїСЂРё РґРёРЅР°РјРёС‡РµСЃРєРѕРј РёР·РјРµРЅРµРЅРёРё
						el.on('refresh', function() {
							if (el.is(':checked')) {
								$('input[name="' + el.attr('name') + '"]').next().removeClass('checked');
								radio.addClass('checked');
							}
							if (el.is(':disabled')) radio.addClass('disabled');
								else radio.removeClass('disabled');
						});
					}
				});

			// file
			} else if (el.is(':file')) {
				el.css({position: 'absolute', top: '-10%', right: '0%', fontSize: '200px', opacity: 0}).each(function() {
					if (el.parent('span.jq-file').length < 1) {
						var file = $('<span' + id + ' class="jq-file' + cl + '" style="display: inline-block; position: relative; overflow: hidden"></span>');
						var name = $('<div class="name" style="float: left; white-space: nowrap"></div>').appendTo(file);
						var browse = $('<div class="browse" style="float: left">' + opt.browseText + '</div>').appendTo(file);
						el.after(file);
						file.append(el);
						if (el.is(':disabled')) file.addClass('disabled');
						el.change(function() {
							name.text(el.val().replace(/.+[\\\/]/, ''));
						})
						.focus(function() {
							file.addClass('focused');
						})
						.blur(function() {
							file.removeClass('focused');
						})
						.click(function() {
							file.removeClass('focused');
						})
						// РѕР±РЅРѕРІР»РµРЅРёРµ РїСЂРё РґРёРЅР°РјРёС‡РµСЃРєРѕРј РёР·РјРµРЅРµРЅРёРё
						.on('refresh', function() {
							if (el.is(':disabled')) file.addClass('disabled');
								else file.removeClass('disabled');
						})
					}
				});

			// select
			} else if (el.is('select')) {
				el.each(function() {
					if (el.next('span.jqselect').length < 1) {

						function selectbox() {

							// Р·Р°РїСЂРµС‰Р°РµРј РїСЂРѕРєСЂСѓС‚РєСѓ СЃС‚СЂР°РЅРёС†С‹ РїСЂРё РїСЂРѕРєСЂСѓС‚РєРµ СЃРµР»РµРєС‚Р°
							function preventScrolling(selector) {
								selector.bind('mousewheel DOMMouseScroll', function(e) {
									var scrollTo = null;
									if (e.type == 'mousewheel') { scrollTo = (e.originalEvent.wheelDelta * -1); }
									else if (e.type == 'DOMMouseScroll') { scrollTo = 40 * e.originalEvent.detail; }
									if (scrollTo) { e.preventDefault(); $(this).scrollTop(scrollTo + $(this).scrollTop()); }
								});
							}

							var option = el.find('option');
							var list = '';
							// С„РѕСЂРјРёСЂСѓРµРј СЃРїРёСЃРѕРє СЃРµР»РµРєС‚Р°
							function makeList() {
								for (i = 0; i < option.length; i++) {
									var li = '',
											liClass = '',
											optionClass = '',
											optgroupClass = '';
									var disabled = 'disabled';
									var selDis = 'selected sel disabled';
									if (option.eq(i).prop('selected')) liClass = 'selected sel';
									if (option.eq(i).is(':disabled')) liClass = disabled;
									if (option.eq(i).is(':selected:disabled')) liClass = selDis;
									if (option.eq(i).attr('class') !== undefined) optionClass = ' ' + option.eq(i).attr('class');
									li = '<li class="' + liClass + optionClass + '">'+ option.eq(i).text() +'</li>';
									// РµСЃР»Рё РµСЃС‚СЊ optgroup
									if (option.eq(i).parent().is('optgroup')) {
										if (option.eq(i).parent().attr('class') !== undefined) optgroupClass = ' ' + option.eq(i).parent().attr('class');
										li = '<li class="' + liClass + optionClass + ' option' + optgroupClass + '">'+ option.eq(i).text() +'</li>';
										if (option.eq(i).is(':first-child')) {
											li = '<li class="optgroup' + optgroupClass + '">' + option.eq(i).parent().attr('label') + '</li>' + li;
										}
									}
									list += li;
								}
							} // end makeList()

							// РѕРґРёРЅРѕС‡РЅС‹Р№ СЃРµР»РµРєС‚
							function doSelect() {
								var selectbox =
									$('<span' + id + ' class="jq-selectbox jqselect' + cl + '" style="display: inline-block; position: relative; z-index:' + opt.singleSelectzIndex + '">'+
											'<div class="select" style="float: left"><div class="text"></div>'+
												'<b class="trigger"><i class="arrow"></i></b>'+
											'</div>'+
										'</span>');
								el.after(selectbox).css({position: 'absolute', left: -9999});
								var divSelect = selectbox.find('div.select');
								var divText = selectbox.find('div.text');
								var optionSelected = option.filter(':selected');

								// Р±РµСЂРµРј РѕРїС†РёСЋ РїРѕ СѓРјРѕР»С‡Р°РЅРёСЋ
								if (optionSelected.length) {
									divText.text(optionSelected.text());
								} else {
									divText.text(option.first().text());
								}

								// РµСЃР»Рё СЃРµР»РµРєС‚ РЅРµР°РєС‚РёРІРЅС‹Р№
								if (el.is(':disabled')) {
									selectbox.addClass('disabled');

								// РµСЃР»Рё СЃРµР»РµРєС‚ Р°РєС‚РёРІРЅС‹Р№
								} else {
									makeList();
									var dropdown =
										$('<div class="dropdown" style="position: absolute; overflow: auto; overflow-x: hidden">'+
												'<ul style="list-style: none">' + list + '</ul>'+
											'</div>');
									selectbox.append(dropdown);
									var li = dropdown.find('li');
									if (li.filter('.selected').length < 1) li.first().addClass('selected sel');
									var selectHeight = selectbox.outerHeight();
									if (dropdown.css('left') == 'auto') dropdown.css({left: 0});
									if (dropdown.css('top') == 'auto') dropdown.css({top: selectHeight});
									var liHeight = li.outerHeight();
									var position = dropdown.css('top');
									dropdown.hide();

									// РїСЂРё РєР»РёРєРµ РЅР° РїСЃРµРІРґРѕСЃРµР»РµРєС‚Рµ
									divSelect.click(function() {
										el.focus();

										// СѓРјРЅРѕРµ РїРѕР·РёС†РёРѕРЅРёСЂРѕРІР°РЅРёРµ
										var win = $(window);
										var topOffset = selectbox.offset().top;
										var bottomOffset = win.height() - selectHeight - (topOffset - win.scrollTop());
										var visible = opt.selectVisibleOptions;
										var	minHeight = liHeight * 6;
										var	newHeight = liHeight * visible;
										if (visible > 0 && visible < 6) minHeight =  newHeight;
										// СЂР°СЃРєСЂС‹С‚РёРµ РІРІРµСЂС…
										if (bottomOffset < 0 || bottomOffset < minHeight)	{
											dropdown.height('auto').css({top: 'auto', bottom: position});
											if (dropdown.outerHeight() > topOffset - win.scrollTop() - 20 ) {
												dropdown.height(Math.floor((topOffset - win.scrollTop() - 20) / liHeight) * liHeight);
												if (visible > 0 && visible < 6) {
													if (dropdown.height() > minHeight) dropdown.height(minHeight);
												} else if (visible > 6) {
													if (dropdown.height() > newHeight) dropdown.height(newHeight);
												}
											}
										// СЂР°СЃРєСЂС‹С‚РёРµ РІРЅРёР·
										} else if (bottomOffset > minHeight) {
											dropdown.height('auto').css({bottom: 'auto', top: position});
											if (dropdown.outerHeight() > bottomOffset - 20 ) {
												dropdown.height(Math.floor((bottomOffset - 20) / liHeight) * liHeight);
												if (visible > 0 && visible < 6) {
													if (dropdown.height() > minHeight) dropdown.height(minHeight);
												} else if (visible > 6) {
													if (dropdown.height() > newHeight) dropdown.height(newHeight);
												}
											}
										}

										$('span.jqselect').css({zIndex: (opt.singleSelectzIndex-1)}).removeClass('focused');
										selectbox.css({zIndex: opt.singleSelectzIndex});
										if (dropdown.is(':hidden')) {
											$('div.dropdown:visible').hide();
											dropdown.show();
											selectbox.addClass('opened');
										} else {
											dropdown.hide();
											selectbox.removeClass('opened');
										}

										// РїСЂРѕРєСЂСѓС‡РёРІР°РµРј РґРѕ РІС‹Р±СЂР°РЅРЅРѕРіРѕ РїСѓРЅРєС‚Р° РїСЂРё РѕС‚РєСЂС‹С‚РёРё СЃРїРёСЃРєР°
										if (li.filter('.selected').length) {
											dropdown.scrollTop(dropdown.scrollTop() + li.filter('.selected').position().top - dropdown.innerHeight()/2 + liHeight/2);
										}

										preventScrolling(dropdown);
										return false;
									});

									// РїСЂРё РЅР°РІРµРґРµРЅРёРё РєСѓСЂСЃРѕСЂР° РЅР° РїСѓРЅРєС‚ СЃРїРёСЃРєР°
									li.hover(function() {
										$(this).siblings().removeClass('selected');
									});
									var selectedText = li.filter('.selected').text();

									// РїСЂРё РєР»РёРєРµ РЅР° РїСѓРЅРєС‚ СЃРїРёСЃРєР°
									li.filter(':not(.disabled):not(.optgroup)').click(function() {
										var t = $(this);
										var liText = t.text();
										if (selectedText != liText) {
											var index = t.index();
											if (t.is('.option')) index -= t.prevAll('.optgroup').length;
											t.addClass('selected sel').siblings().removeClass('selected sel');
											option.prop('selected', false).eq(index).prop('selected', true);
											selectedText = liText;
											divText.text(liText);
											el.change();
										}
										dropdown.hide();
									});
									dropdown.mouseout(function() {
										dropdown.find('li.sel').addClass('selected');
									});

									// РёР·РјРµРЅРµРЅРёРµ СЃРµР»РµРєС‚Р°
									el.change(function() {
										divText.text(option.filter(':selected').text());
										li.removeClass('selected sel').not('.optgroup').eq(el[0].selectedIndex).addClass('selected sel');
									})
									.focus(function() {
										selectbox.addClass('focused');
									})
									.blur(function() {
										selectbox.removeClass('focused');
									})
									// РїСЂРѕРєСЂСѓС‚РєРё СЃРїРёСЃРєР° СЃ РєР»Р°РІРёР°С‚СѓСЂС‹
									.bind('keydown keyup', function(e) {
										divText.text(option.filter(':selected').text());
										li.removeClass('selected sel').not('.optgroup').eq(el[0].selectedIndex).addClass('selected sel');
										// РІРІРµСЂС…, РІР»РµРІРѕ, PageUp
										if (e.which == 38 || e.which == 37 || e.which == 33) {
											dropdown.scrollTop(dropdown.scrollTop() + li.filter('.selected').position().top);
										}
										// РІРЅРёР·, РІРїСЂР°РІРѕ, PageDown
										if (e.which == 40 || e.which == 39 || e.which == 34) {
											dropdown.scrollTop(dropdown.scrollTop() + li.filter('.selected').position().top - dropdown.innerHeight() + liHeight);
										}
										if (e.which == 13) {
											dropdown.hide();
										}
									});

									// РїСЂСЏС‡РµРј РІС‹РїР°РґР°СЋС‰РёР№ СЃРїРёСЃРѕРє РїСЂРё РєР»РёРєРµ Р·Р° РїСЂРµРґРµР»Р°РјРё СЃРµР»РµРєС‚Р°
									$(document).on('click', function(e) {
										// e.target.nodeName != 'OPTION' - РґРѕР±Р°РІР»РµРЅРѕ РґР»СЏ РѕР±С…РѕРґР° Р±Р°РіР° РІ РћРїРµСЂРµ
										// (РїСЂРё РёР·РјРµРЅРµРЅРёРё СЃРµР»РµРєС‚Р° СЃ РєР»Р°РІРёР°С‚СѓСЂС‹ СЃСЂР°Р±Р°С‚С‹РІР°РµС‚ СЃРѕР±С‹С‚РёРµ onclick)
										if (!$(e.target).parents().hasClass('selectbox') && e.target.nodeName != 'OPTION') {
											dropdown.hide().find('li.sel').addClass('selected');
											selectbox.removeClass('focused opened');
										}
									});
								}
							} // end doSelect()

							// РјСѓР»СЊС‚РёСЃРµР»РµРєС‚
							function doMultipleSelect() {
								var selectbox = $('<span' + id + ' class="jq-select-multiple jqselect' + cl + '" style="display: inline-block"></span>');
								el.after(selectbox).css({position: 'absolute', left: -9999});
								makeList();
								selectbox.append('<ul style="position: relative">' + list + '</ul>');
								var ul = selectbox.find('ul');
								var li = selectbox.find('li').attr('unselectable', 'on').css({'-webkit-user-select': 'none', '-moz-user-select': 'none', '-ms-user-select': 'none', '-o-user-select': 'none', 'user-select': 'none'});
								var size = el.attr('size');
								var ulHeight = ul.outerHeight();
								var liHeight = li.outerHeight();
								if (size !== undefined && size > 0) {
									ul.css({'height': liHeight * size});
								} else {
									ul.css({'height': liHeight * 4});
								}
								if (ulHeight > selectbox.height()) {
									ul.css('overflowY', 'scroll');
									preventScrolling(ul);
									// РїСЂРѕРєСЂСѓС‡РёРІР°РµРј РґРѕ РІС‹Р±СЂР°РЅРЅРѕРіРѕ РїСѓРЅРєС‚Р°
									if (li.filter('.selected').length) {
										ul.scrollTop(ul.scrollTop() + li.filter('.selected').position().top);
									}
								}
								if (el.is(':disabled')) {
									selectbox.addClass('disabled');
									option.each(function() {
										if ($(this).is(':selected')) li.eq($(this).index()).addClass('selected');
									});
								} else {

									// РїСЂРё РєР»РёРєРµ РЅР° РїСѓРЅРєС‚ СЃРїРёСЃРєР°
									li.filter(':not(.disabled):not(.optgroup)').click(function(e) {
										el.focus();
										selectbox.removeClass('focused');
										var clkd = $(this);
										if(!e.ctrlKey) clkd.addClass('selected');
										if(!e.shiftKey) clkd.addClass('first');
										if(!e.ctrlKey && !e.shiftKey) clkd.siblings().removeClass('selected first');

										// РІС‹РґРµР»РµРЅРёРµ РїСѓРЅРєС‚РѕРІ РїСЂРё Р·Р°Р¶Р°С‚РѕРј Ctrl
										if(e.ctrlKey) {
											if (clkd.is('.selected')) clkd.removeClass('selected first');
												else clkd.addClass('selected first');
											clkd.siblings().removeClass('first');
										}

										// РІС‹РґРµР»РµРЅРёРµ РїСѓРЅРєС‚РѕРІ РїСЂРё Р·Р°Р¶Р°С‚РѕРј Shift
										if(e.shiftKey) {
											var prev = false,
													next = false;
											clkd.siblings().removeClass('selected').siblings('.first').addClass('selected');
											clkd.prevAll().each(function() {
												if ($(this).is('.first')) prev = true;
											});
											clkd.nextAll().each(function() {
												if ($(this).is('.first')) next = true;
											});
											if (prev) {
												clkd.prevAll().each(function() {
													if ($(this).is('.selected')) return false;
														else $(this).not('.disabled, .optgroup').addClass('selected');
												});
											}
											if (next) {
												clkd.nextAll().each(function() {
													if ($(this).is('.selected')) return false;
														else $(this).not('.disabled, .optgroup').addClass('selected');
												});
											}
											if (li.filter('.selected').length == 1) clkd.addClass('first');
										}

										// РѕС‚РјРµС‡Р°РµРј РІС‹Р±СЂР°РЅРЅС‹Рµ РјС‹С€СЊСЋ
										option.prop('selected', false);
										li.filter('.selected').each(function() {
											var t = $(this);
											var index = t.index();
											if (t.is('.option')) index -= t.prevAll('.optgroup').length;
											option.eq(index).prop('selected', true);
										});
										el.change();

									});

									// РѕС‚РјРµС‡Р°РµРј РІС‹Р±СЂР°РЅРЅС‹Рµ СЃ РєР»Р°РІРёР°С‚СѓСЂС‹
									option.each(function(i) {
										$(this).data('optionIndex', i);
									});
									el.change(function() {
										li.removeClass('selected');
										var arrIndexes = [];
										option.filter(':selected').each(function() {
											arrIndexes.push($(this).data('optionIndex'));
										});
										li.not('.optgroup').filter(function(i) {
											return arrIndexes.indexOf(i) > -1;
										}).addClass('selected');
									})
									.focus(function() {
										selectbox.addClass('focused');
									})
									.blur(function() {
										selectbox.removeClass('focused');
									});

									// РїСЂРѕРєСЂСѓС‡РёРІР°РµРј СЃ РєР»Р°РІРёР°С‚СѓСЂС‹
									if (ulHeight > selectbox.height()) {
										el.keydown(function(e) {
											// РІРІРµСЂС…, РІР»РµРІРѕ, PageUp
											if (e.which == 38 || e.which == 37 || e.which == 33) {
												ul.scrollTop(ul.scrollTop() + li.filter('.selected').position().top - liHeight);
											}
											// РІРЅРёР·, РІРїСЂР°РІРѕ, PageDown
											if (e.which == 40 || e.which == 39 || e.which == 34) {
												ul.scrollTop(ul.scrollTop() + li.filter('.selected:last').position().top - ul.innerHeight() + liHeight*2);
											}
										});
									}

								}
							} // end doMultipleSelect()
							if (el.is('[multiple]')) doMultipleSelect(); else doSelect();
						} // end selectbox()

						selectbox();

						// РѕР±РЅРѕРІР»РµРЅРёРµ РїСЂРё РґРёРЅР°РјРёС‡РµСЃРєРѕРј РёР·РјРµРЅРµРЅРёРё
						el.on('refresh', function() {
							el.next().remove();
							selectbox();
						});
					}
				});
			}// end select

		});

	}
})(jQuery);