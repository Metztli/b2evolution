/* This includes 11 files: functions.js, ajax.js, form_extensions.js, backoffice.js, extracats.js, dynamic_select.js, src/evo_modal_window.js, src/evo_user_crop.js, src/evo_user_report.js, src/evo_user_deldata.js, src/evo_user_org.js */
function pop_up_window(a,b,c,d,e){"undefined"==typeof c&&(c=750),"undefined"==typeof d&&(d=550);var f=(screen.width-c)/2,g=(screen.height-d)/2;return"undefined"==typeof e&&(e="scrollbars=yes, status=yes, resizable=yes, menubar=yes"),e="width="+c+", height="+d+", left="+f+", top="+g+", "+e,opened=window.open(a,b,e),opened.focus(),"undefined"==typeof openedWindows?openedWindows=new Array(opened):openedWindows.push(opened),!1}function textarea_replace_selection(a,b,c){textarea_wrap_selection(a,b,"",1,c)}function textarea_wrap_selection(a,b,c,d,e){e=e||document;var f={element:a,before:b,after:c,replace:d,target_document:e};if(!b2evo_Callbacks.trigger_callback("wrap_selection_for_"+a.id,f)){if(window.opener&&"undefined"!=typeof window.opener)try{if(window.opener.b2evo_Callbacks&&"undefined"!=typeof window.opener.b2evo_Callbacks&&window.opener.b2evo_Callbacks.trigger_callback("wrap_selection_for_"+a.id,f))return}catch(g){}if(!(window.parent&&"undefined"!=typeof window.parent&&window.parent.b2evo_Callbacks&&"undefined"!=typeof window.parent.b2evo_Callbacks&&window.parent.b2evo_Callbacks.trigger_callback("wrap_selection_for_"+a.id,f)))if(e.selection)a.focus(),sel=e.selection.createRange(),d?sel.text=b+c:sel.text=b+sel.text+c,a.focus();else if(a.selectionStart||"0"==a.selectionStart){var h,i,j,k=a.selectionStart,l=a.selectionEnd;"textarea"==a.type&&"undefined"!=typeof a.scrollTop&&(i=a.scrollTop,j=a.scrollLeft),d?(a.value=a.value.substring(0,k)+b+c+a.value.substring(l,a.value.length),h=k+b.length+c.length):(a.value=a.value.substring(0,k)+b+a.value.substring(k,l)+c+a.value.substring(l,a.value.length),h=l+b.length+c.length),"undefined"!=typeof i&&(a.scrollTop=i,a.scrollLeft=j),a.focus(),a.selectionStart=h,a.selectionEnd=h}else a.value+=b+c,a.focus()}}function textarea_str_replace(a,b,c,d){d=d||document;var e={element:a,search:b,replace:c,target_document:d};if(!b2evo_Callbacks.trigger_callback("str_replace_for_"+a.id,e)){if(window.opener&&"undefined"!=typeof window.opener)try{if(window.opener.b2evo_Callbacks&&"undefined"!=typeof window.opener.b2evo_Callbacks&&window.opener.b2evo_Callbacks.trigger_callback("str_replace_for_"+a.id,e))return}catch(f){}window.parent&&"undefined"!=typeof window.parent&&window.parent.b2evo_Callbacks&&"undefined"!=typeof window.parent.b2evo_Callbacks&&window.parent.b2evo_Callbacks.trigger_callback("str_replace_for_"+a.id,e)||(a.value=a.value.replace(b,c),a.focus())}}function toggle_filter_area(a){var b=jQuery("#clickdiv_"+a),c=jQuery("#clickimg_"+a);if(0==b.length||0==c.length)return alert("ID "+a+" not found!"),!1;if(c.hasClass("fa")||c.hasClass("glyphicon")){if(""!=c.data("toggle")&&void 0!=c.data("toggle")){var d=c.hasClass("fa")?"fa":"glyphicon";void 0==c.data("toggle-orig-class")&&c.data("toggle-orig-class",c.attr("class").replace(new RegExp("^"+d+" (.+)$","g"),"$1")),c.hasClass(c.data("toggle-orig-class"))?c.removeClass(c.data("toggle-orig-class")).addClass(d+"-"+c.data("toggle")):c.removeClass(d+"-"+c.data("toggle")).addClass(c.data("toggle-orig-class"))}}else{var e=c.css("background-position").match(/-*\d+/g);c.css("background-position",parseInt(e[0])+(b.is(":hidden")?-16:16)+"px "+parseInt(e[1])+"px")}return b.is(":hidden")?(b.slideDown(500),jQuery.post(htsrv_url+"anon_async.php?action=expand_filter&target="+a)):(b.slideUp(500),jQuery.post(htsrv_url+"anon_async.php?action=collapse_filter&target="+a)),!1}function b2evo_Callbacks(){this.eventHandlers=new Array}function evoAlert(a){var b=jQuery(".b2evo_alert");b.length>0&&b.remove(),jQuery("body").append('<div class="b2evo_alert">'+a+"</div>"),setTimeout(function(){jQuery(".b2evo_alert").fadeOut({complete:function(){jQuery(this).remove()}})},3e3),evo_alert_events_initialized||(evo_alert_events_initialized=!0,jQuery(document).on("click",".b2evo_alert",function(){jQuery(this).remove()}))}function ajax_debug_clear(a){var b=/<!-- Ajax response end -->/;return a=a.replace(b,""),a=a.replace(/(<div class="jslog">[\s\S]*)/i,""),jQuery.trim(a)}function ajax_response_is_correct(a){var b=/<!-- Ajax response end -->/,c=a.match(b);return c?(a=ajax_debug_clear(a),""!=a):!1}function get_form(a){for(;"FORM"!=a.tagName;){if("undefined"==typeof a)return!1;a=a.parentNode}return a}function check(a,b){if(form_obj=get_form(a),!form_obj)return alert("Could not find form"),!1;for(i=0;i<form_obj.length;)"checkbox"==form_obj.elements[i].type&&(form_obj.elements[i].checked=b),i++;return!1}function check_all(a){return target=findTarget(a),check(target,!0),cancelClick(a),!1}function uncheck_all(a){return target=findTarget(a),check(target,!1),cancelClick(a),!1}function cancelClick(a){return window.event&&window.event.returnValue&&(window.event.returnValue=!1),a&&a.preventDefault&&a.preventDefault(),!1}function surround_check(a,b,c){var d=findTarget(a);for(el_form=get_form(d),el_inputs=el_form.getElementsByTagName("INPUT"),i=0;i<el_inputs.length;i++)el_input=el_inputs[i],"checkbox"==el_input.type&&(null==c||el_input.checked==c)&&(el_input.parentNode.className=b)}function surround_unchecked(a){surround_check(a,"checkbox_surround",!1)}function surround_checked(a){surround_check(a,"checkbox_surround",!0)}function unsurround_all(a){surround_check(a,"checkbox_surround_init",null)}function init_check_all(){for(var a=document.getElementsByName("check_all_nocheckchanges"),b=0;b<a.length;b++){var c=a[b];jQuery(c).bind({click:check_all,mouseover:surround_unchecked,mouseout:unsurround_all})}for(var d=document.getElementsByName("uncheck_all_nocheckchanges"),b=0;b<d.length;b++){var c=d[b];jQuery(c).bind({click:uncheck_all,mouseover:surround_checked,mouseout:unsurround_all})}}function clear_form(a){for(a=check(a,!1),i=0;i<a.length;)"text"==a.elements[i].type&&(a.elements[i].value=""),i++;return a}function focus_on_first_input(){if(all_inputs=document.getElementsByTagName("input"),all_inputs.length)for(i=0;i<all_inputs.length;i++)if("text"==all_inputs[i].type&&1!=all_inputs[i].disabled){try{all_inputs[i].focus()}catch(a){}break}}function check_combo(a,b,c){"new"==b?(input_text=document.getElementById(a+"_combo"),input_text.style.display="inline",input_text.focus()):(input_text=document.getElementById(a+"_combo"),input_text.style.display="none")}function input_decorated_help(a,b){var c=document.getElementById(a),d=function(){(""==c.value||c.value==b)&&(c.style.color="#666",c.value=b)};jQuery(c).bind("blur",d),jQuery(c).bind("focus",function(){c.style.color="",c.value==b&&(c.value="")}),jQuery(c.form).bind("submit",function(){c.value==b&&(c.value="")}),d()}function findTarget(a){var b;if(window.event&&window.event.srcElement?b=window.event.srcElement:a&&a.target&&(b=a.target),!b)return null;for(;b!=document.body&&"a"!=b.nodeName.toLowerCase();)b=b.parentNode;return"a"!=b.nodeName.toLowerCase()?null:b}function check_extracat(a){for(var b=a.value,c=document.getElementsByName("post_extracats[]"),d=0;d<c.length;d++){var e=c[d];e.value==b&&(e.checked=!0)}}function init_dynamicSelect(){for(var a=0;a<nb_dynamicSelects;a++)dynamicSelect(tab_dynamicSelects[a].parent,tab_dynamicSelects[a].child)}function dynamicSelect(a,b){if(document.getElementById&&document.getElementsByTagName){var c=document.getElementById(a),d=document.getElementById(b),e=d.cloneNode(!0),f=e.getElementsByTagName("option");refreshDynamicSelectOptions(c,d,f),c.onchange=function(){refreshDynamicSelectOptions(c,d,f)}}}function refreshDynamicSelectOptions(a,b,c){for(;b.options.length;)b.remove(0);for(var d=new RegExp("^"+a.options[a.selectedIndex].value+"-.*$"),e=0;e<c.length;e++)(c[e].value.match(d)||""==c[e].value)&&b.appendChild(c[e].cloneNode(!0))}function toggle_clickopen(a,b,c){if(!(clickdiv=document.getElementById("clickdiv_"+a))||!(clickimg=document.getElementById("clickimg_"+a)))return alert("ID "+a+" not found!"),!1;if("undefined"==typeof b&&(b="none"!=clickdiv.style.display),"undefined"==typeof c&&(c=""),clickimg=jQuery(clickimg),clickimg.hasClass("fa")||clickimg.hasClass("glyphicon")){if(""!=clickimg.data("toggle")){var d=clickimg.hasClass("fa")?"fa":"glyphicon";void 0==clickimg.data("toggle-orig-class")&&clickimg.data("toggle-orig-class",clickimg.attr("class").replace(new RegExp("^"+d+" (.+)$","g"),"$1")),clickimg.hasClass(clickimg.data("toggle-orig-class"))?clickimg.removeClass(clickimg.data("toggle-orig-class")).addClass(d+"-"+clickimg.data("toggle")):clickimg.removeClass(d+"-"+clickimg.data("toggle")).addClass(clickimg.data("toggle-orig-class"))}}else{var e=clickimg.css("background-position").match(/-*\d+/g);clickimg.css("background-position",parseInt(e[0])+(b?16:-16)+"px "+parseInt(e[1])+"px")}return clickdiv.style.display=b?"none":c,!1}function evoFadeSuccess(a){evoFadeBg(a,new Array("#ddff00","#bbff00"))}function evoFadeFailure(a){evoFadeBg(a,new Array("#9300ff","#ff000a","#ff0000"))}function evoFadeHighlight(a){evoFadeBg(a,new Array("#ffbf00","#ffe79f"))}function evoFadeBg(selector,bgs,options){var origBg=jQuery(selector).css("backgroundColor"),speed=options&&options.speed||'"slow"',toEval="jQuery(selector).animate({ backgroundColor: ";for(e in bgs)"string"==typeof bgs[e]&&(toEval+='"'+bgs[e]+'"}, '+speed+" ).animate({ backgroundColor: ");toEval+="origBg }, "+speed+', "", function(){jQuery( this ).css( "backgroundColor", "" );});',eval(toEval)}function set_new_form_action(a,b){a.attributes.getNamedItem("action").value;a.attributes.getNamedItem("action").value=b;var c=location.href.replace(/(\/)[^\/]*$/,"$1");return a.attributes.getNamedItem("action").value!=b&&a.attributes.getNamedItem("action").value!=c+b&&(a.action=b,a.attributes.getNamedItem("action").value!=b)?(alert("set_new_form_action: Cannot set new form action (Safari workaround)."),!1):!0}function b2edit_open_preview(a,b){if("b2evo_preview"==a.target)return!1;var c=a.attributes.getNamedItem("action").value;return set_new_form_action(a,b)?(a.target="b2evo_preview",preview_window=window.open("","b2evo_preview"),preview_window.focus(),a.submit(),a.attributes.getNamedItem("action").value=c,a.target="_self",!1):(alert("Preview not supported. Sorry. (Could not set form.action for preview)"),!1)}function b2edit_reload(a,b,c,d,e){if(!set_new_form_action(a,b))return!1;var f=!1;if(a.elements.namedItem("actionArray[update]")?(jQuery(a).append('<input type="hidden" name="action" value="edit_switchtab" />'),f=!0):a.elements.namedItem("actionArray[create]")?(jQuery(a).append('<input type="hidden" name="action" value="new_switchtab" />'),f=!0):(jQuery(a).append('<input type="hidden" name="action" value="switchtab" />'),f=!0),f&&"undefined"!=typeof d)for(param in d)jQuery(a).append('<input type="hidden" name="'+param+'" value="'+d[param]+'" />');return"undefined"!=typeof c&&"undefined"!=c&&(null==c&&(c=""),a.elements.blog.value=c),window.onbeforeunload=null,"undefined"!=typeof e&&1==e&&a.reset(),a.submit(),!1}function b2edit_type(a,b,c){var d=!1;return bozo.nb_changes>0&&(d=!confirm(a)),b2edit_reload(document.getElementById("item_checkchanges"),b,null,{action:c},d)}function b2edit_confirm(a,b,c){return bozo.nb_changes>0&&!confirm(a)?!1:b2edit_reload(document.getElementById("item_checkchanges"),b,null,{action:c},!1)}function openModalWindow(a,b,c,d,e,f){var g="overlay_page_active";"undefined"!=typeof d&&1==d&&(g="overlay_page_active_transparent"),"undefined"==typeof b&&(b="560px");var h="";return"undefined"!=typeof c&&(c>0||""!=c)&&(h=' style="height:'+c+'"'),jQuery("#overlay_page").length>0?void jQuery("#overlay_page").html(a):(jQuery("body").append('<div id="screen_mask"></div><div id="overlay_wrap" style="width:'+b+'"><div id="overlay_layout"><div id="overlay_page"'+h+"></div></div></div>"),jQuery("#screen_mask").fadeTo(1,.5).fadeIn(200),jQuery("#overlay_page").html(a).addClass(g),void jQuery(document).on("click","#close_button, #screen_mask, #overlay_page",function(a){if("overlay_page"==jQuery(this).attr("id")){var b=jQuery("#overlay_page form");if(b.length){var c=b.position().top+jQuery("#overlay_wrap").position().top,d=c+b.height();a.clientY>c&&a.clientY<d||closeModalWindow()}return!0}return closeModalWindow(),!1}))}function closeModalWindow(a){return"undefined"==typeof a&&(a=window.document),jQuery("#overlay_page",a).hide(),jQuery(".action_messages",a).remove(),jQuery("#server_messages",a).insertBefore(".first_payload_block"),jQuery("#overlay_wrap",a).remove(),jQuery("#screen_mask",a).remove(),!1}function user_crop_avatar(a,b,c){"undefined"==typeof c&&(c="avatar");var d=750,e=320,f=jQuery(window).width(),g=jQuery(window).height(),h=f,i=g;i=i>d?d:e>i?e:i,h=h>d?d:e>h?e:h;var j=170,k=g>d?170:205;900>=f&&(j=35,k=325);var l=h-j,m=i-k,n=130;l=n>l?n:l,m=n>m?n:m,openModalWindow('<span class="loader_img loader_user_report absolute_center" title="'+evo_js_lang_loading+'"></span>',h+"px",i+"px",!0,evo_js_lang_crop_profile_pic,[evo_js_lang_crop,"btn-primary hide"],!0);var o={user_ID:a,file_ID:b,image_width:l,image_height:m,display_mode:"js",crumb_user:evo_js_crumb_user};return evo_js_is_backoffice?(o.ctrl="user",o.user_tab="crop",o.user_tab_from=c):(o.blog=evo_js_blog,o.disp="avatar",o.action="crop"),jQuery.ajax({type:"POST",url:evo_js_user_crop_ajax_url,data:o,success:function(a){openModalWindow(a,h+"px",i+"px",!0,evo_js_lang_crop_profile_pic,[evo_js_lang_crop,"btn-primary hide"])}}),!1}function user_report(a,b){openModalWindow('<span class="loader_img loader_user_report absolute_center" title="'+evo_js_lang_loading+'"></span>',"auto","",!0,evo_js_lang_report_user,[evo_js_lang_report_this_user_now,"btn-danger"],!0);var c={action:"get_user_report_form",user_ID:a,crumb_user:evo_js_crumb_user};return evo_js_is_backoffice?(c.is_backoffice=1,c.user_tab=b):c.blog=evo_js_blog,jQuery.ajax({type:"POST",url:evo_js_user_report_ajax_url,data:c,success:function(a){openModalWindow(a,"auto","",!0,evo_js_lang_report_user,[evo_js_lang_report_this_user_now,"btn-danger"])}}),!1}function user_deldata(a,b){return openModalWindow('<span class="loader_img loader_user_deldata absolute_center" title="'+evo_js_lang_loading+'"></span>',"auto","",!0,evo_js_lang_delete_user_data,[evo_js_lang_delete_selected_data,"btn-danger"],!0),jQuery.ajax({type:"POST",url:evo_js_user_deldata_ajax_url,data:{ctrl:"user",user_tab:"deldata",user_tab_from:b,user_ID:a,display_mode:"js",crumb_user:evo_js_crumb_user},success:function(a){openModalWindow(a,"auto","",!0,evo_js_lang_delete_user_data,[evo_js_lang_delete_selected_data,"btn-danger"])}}),!1}function user_add_org(a){return openModalWindow('<span class="loader_img loader_user_deldata absolute_center" title="'+evo_js_lang_loading+'"></span>',"450px","",!0,evo_js_lang_add_user_to_organization,evo_js_lang_add,!0),jQuery.ajax({type:"POST",url:evo_js_user_org_ajax_url,data:{ctrl:"organizations",action:"add_user",org_ID:a,display_mode:"js",crumb_user:evo_js_crumb_organization},success:function(a){openModalWindow(a,"450px","",!0,evo_js_lang_add_user_to_organization,evo_js_lang_add),jQuery("input.autocomplete_login").trigger("added")}}),!1}function user_edit(a,b){return openModalWindow('<span class="loader_img loader_user_deldata absolute_center" title="'+evo_js_lang_loading+'"></span>',"450px","",!0,evo_js_lang_edit_membership,evo_js_lang_edit,!0),jQuery.ajax({type:"POST",url:evo_js_user_org_ajax_url,data:{ctrl:"organizations",action:"edit_user",org_ID:a,user_ID:b,display_mode:"js",crumb_user:evo_js_crumb_organization},success:function(a){openModalWindow(a,"450px","",!0,evo_js_lang_edit_membership,evo_js_lang_edit)}}),!1}b2evo_Callbacks.prototype={register_callback:function(a,b,c){"undefined"==typeof this.eventHandlers[a]&&(this.eventHandlers[a]=new Array),"undefined"!=typeof c&&c?this.eventHandlers[a][0]=b:this.eventHandlers[a][this.eventHandlers[a].length]=b},trigger_callback:function(event,args){if("undefined"==typeof this.eventHandlers[event])return null;for(var r=!1,cb_args="",cb_arguments=arguments,i=1;i<arguments.length;i++)cb_args+="cb_arguments["+i+"], ";cb_args.length&&(cb_args=cb_args.substring(0,cb_args.length-2));for(var i=0;i<this.eventHandlers[event].length;i++){var f=this.eventHandlers[event][i];r=eval("f("+cb_args+");")||r}return r}};var b2evo_Callbacks=new b2evo_Callbacks;evo_alert_events_initialized=!1,jQuery(document).ready(function(){function a(a){var b=jQuery("."+a.val()+"_toolbar");return 0==b.length?!0:void(a.is(":checked")?(b.removeClass("disabled"),b.find("input[type=button]").removeAttr("disabled")):(b.addClass("disabled"),b.find("input[type=button]").attr("disabled","disabled")))}jQuery(document).on("click","[data-func]",function(){var a=jQuery(this).data("func").match(/([^\\|]|\\\|)+/g),b=a[0];a.splice(0,1);for(var c=0;c<a.length;c++)"b2evoCanvas"==a[c]?a[c]=b2evoCanvas:" "==a[c]?a[c]="":a[c]=a[c].replace(/\\\|/g,"|");return jQuery(this).closest(".disabled[class*=_toolbar]").length>0?!1:(window[b].apply(null,a),!1)}),jQuery('input[type=checkbox][name="renderers[]"]').each(function(){a(jQuery(this))}),jQuery('input[type=checkbox][name="renderers[]"]').click(function(){a(jQuery(this))})}),jQuery(document).ready(function(){jQuery("[id^=fadeout-]").each(function(){evoFadeBg(this,new Array("#FFFF33"),{speed:3e3})})}),jQuery(document).on("change",".btn-file :file",function(){var a=jQuery(this).val().replace(/\\/g,"/").replace(/.*\//,"");jQuery(this).parent().next().html(a)}),jQuery(document).keyup(function(a){27==a.keyCode&&closeModalWindow()});