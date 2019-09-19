/* This includes 11 files: src/bootstrap-evo_modal_window.js, src/evo_images.js, src/evo_user_crop.js, src/evo_user_report.js, src/evo_user_contact_groups.js, src/evo_rest_api.js, src/evo_item_flag.js, src/evo_links.js, src/evo_forms.js, ajax.js, src/ddexitpop.js */

var modal_window_js_initialized=!1;function openModalWindow(e,t,o,a,n,i,r,s,d,l,u){var c=void 0===t||"auto"==t?"":"width:"+t+";",_=void 0===o||0==o||""==o?"":"height:"+o,p=_.match(/%$/i)?' style="height:100%;overflow:hidden;"':"",h=o.match(/px/i)?' style="min-height:'+(o.replace("px","")-157)+'px"':"",f=void 0===i||0!=i;if(void 0!==i&&""!=i)if("object"==typeof i)var m=i[0],y=i[1],v=void 0===i[2]?"form":i[2];else m=i,y="btn-primary",v="form";if(void 0!==r&&r&&jQuery("#modal_window").remove(),0==jQuery("#modal_window").length){var j='<div id="modal_window" class="modal fade" tabindex="-1" role="dialog" aria-hidden="true"><div class="modal-dialog" style="'+c+_+'"><div class="modal-content"'+p+">";void 0!==n&&""!=n&&(j+='<div class="modal-header"><button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button><h4 class="modal-title">'+n+"</h4></div>"),j+='<div class="modal-body"'+p+h+">"+e,d&&(jQuery("#"+d),j+="<script>jQuery( document ).ready( function() {var iframe = jQuery( '#"+d+"' );iframe.on( 'load', function() {iframe.closest( '.modal-body' ).find( 'span.loader_img' ).remove();setModalIFrameUnload( '"+d+"' );});});<\/script>"),j+="</div>",f&&(j+='<div class="modal-footer">',void 0!==i&&""!=i&&(j+='<button class="btn '+y+'" type="submit" style="display:none">'+m+"</button>"),j+='<button class="btn btn-default" data-dismiss="modal" aria-hidden="true">'+evo_js_lang_close+"</button></div>"),j+="</div></div></div>",jQuery("body").append(j)}else jQuery("#modal_window .modal-body").html(e);void 0!==d?jQuery("#"+d).load(function(){prepareModalWindow(jQuery(this).contents(),v,f,s),jQuery("#modal_window .loader_img").remove(),jQuery("#"+d).show()}):prepareModalWindow("#modal_window",v,f,s),"function"==typeof l&&jQuery("#modal_window").on("shown.bs.modal",l);var g={};modal_window_js_initialized&&(g="show"),jQuery("#modal_window").modal(g),c||(jQuery("#modal_window .modal-dialog").css({display:"table",width:"auto"}),jQuery("#modal_window .modal-dialog .modal-content").css({display:"table-cell"})),jQuery("#modal_window").on("hidden.bs.modal",function(){jQuery(this).remove(),"function"==typeof u&&u()}),modal_window_js_initialized=!0}function prepareModalWindow(e,t,o,a){o&&(void 0!==a&&a||(jQuery("legend",e).remove(),jQuery("#close_button",e).remove(),jQuery(".panel, .panel-body",e).removeClass("panel panel-default panel-body")),0==jQuery(t+" input[type=submit]",e).length?jQuery("#modal_window .modal-footer button[type=submit]").hide():(jQuery(t+" input[type=submit]",e).hide(),jQuery("#modal_window .modal-footer button[type=submit]").show()),jQuery(t,e).change(function(){var e=jQuery(this).find("input[type=submit]");0<e.length?(e.hide(),jQuery("#modal_window .modal-footer button[type=submit]").show()):jQuery("#modal_window .modal-footer button[type=submit]").hide()}),jQuery("#modal_window .modal-footer button[type=submit]").off("click"),jQuery("#modal_window .modal-footer button[type=submit]").on("click",function(){1!==jQuery(this).data("click_init")&&(jQuery(t+" input[type=submit]",e).click(),jQuery(this).data("click_init",1))})),jQuery(t+" a.btn",e).each(function(){jQuery("#modal_window .modal-footer").prepend("<a href="+jQuery(this).attr("href")+'><button type="button" class="'+jQuery(this).attr("class")+'">'+jQuery(this).html()+"</button></a>"),jQuery(this).remove()}),0<jQuery(t+" #current_modal_title",e).length&&jQuery("#modal_window .modal-title").html(jQuery(t+" #current_modal_title",e).html())}function closeModalWindow(e,t){return void 0===e&&(e=window.document),"function"==typeof t&&jQuery("#modal_window").on("hidden.bs.modal",t),jQuery("#modal_window",e).modal("hide"),!1}function setModalIFrameUnload(e){var o=jQuery("#"+e);o[0].contentWindow.onunload=function(){var e=o.closest(".modal-body"),t=jQuery('<span class="loader_img absolute_center" title="'+evo_js_lang_loading+'"></span>');jQuery(e).prepend(t)}}function user_crop_avatar(e,t,o){void 0===o&&(o="avatar");var a=750,n=320,i=jQuery(window).width(),r=jQuery(window).height(),s=r/i,d=10,l=10;d=n<(i=a<i?a:i<n?n:i)-2*d?10:0,l=n<(r=a<r?a:r<n?n:r)-2*l?10:0;var u=a<i?a:i,c=a<r?a:r;openModalWindow('<span id="spinner" class="loader_img loader_user_report absolute_center" title="'+evo_js_lang_loading+'"></span>',u+"px",c+"px",!0,evo_js_lang_crop_profile_pic,[evo_js_lang_crop,"btn-primary"],!0);var _=jQuery("div.modal-dialog div.modal-body").length?jQuery("div.modal-dialog div.modal-body"):jQuery("#overlay_page"),p=parseInt(_.css("paddingTop")),h=parseInt(_.css("paddingRight")),f=parseInt(_.css("paddingBottom")),m=parseInt(_.css("paddingLeft")),y=(jQuery("div.modal-dialog div.modal-body").length?parseInt(_.css("min-height")):c-100)-(p+f),v={user_ID:e,file_ID:t,aspect_ratio:s,content_width:u-(m+h),content_height:y,display_mode:"js",crumb_user:evo_js_crumb_user};return evo_js_is_backoffice?(v.ctrl="user",v.user_tab="crop",v.user_tab_from=o):(v.blog=evo_js_blog,v.disp="avatar",v.action="crop"),jQuery.ajax({type:"POST",url:evo_js_user_crop_ajax_url,data:v,success:function(e){openModalWindow(e,u+"px",c+"px",!0,evo_js_lang_crop_profile_pic,[evo_js_lang_crop,"btn-primary"])}}),!1}function user_report(e,t){openModalWindow('<span class="loader_img loader_user_report absolute_center" title="'+evo_js_lang_loading+'"></span>',"auto","",!0,evo_js_lang_report_user,[evo_js_lang_report_this_user_now,"btn-danger"],!0);var o={action:"get_user_report_form",user_ID:e,crumb_user:evo_js_crumb_user};return evo_js_is_backoffice?(o.is_backoffice=1,o.user_tab=t):o.blog=evo_js_blog,jQuery.ajax({type:"POST",url:evo_js_user_report_ajax_url,data:o,success:function(e){openModalWindow(e,"auto","",!0,evo_js_lang_report_user,[evo_js_lang_report_this_user_now,"btn-danger"])}}),!1}function user_contact_groups(e){return openModalWindow('<span class="loader_img loader_user_report absolute_center" title="'+evo_js_lang_loading+'"></span>',"auto","",!0,evo_js_lang_contact_groups,evo_js_lang_save,!0),jQuery.ajax({type:"POST",url:evo_js_user_contact_groups_ajax_url,data:{action:"get_user_contact_form",blog:evo_js_blog,user_ID:e,crumb_user:evo_js_crumb_user},success:function(e){openModalWindow(e,"auto","",!0,evo_js_lang_contact_groups,evo_js_lang_save)}}),!1}function evo_rest_api_request(url,params_func,func_method,method){var params=params_func,func=func_method;"function"==typeof params_func&&(func=params_func,params={},method=func_method),void 0===method&&(method="GET"),jQuery.ajax({contentType:"application/json; charset=utf-8",type:method,url:restapi_url+url,data:params}).then(function(data,textStatus,jqXHR){"object"==typeof jqXHR.responseJSON&&eval(func)(data,textStatus,jqXHR)})}function evo_rest_api_print_error(e,t,o){if("string"!=typeof t&&void 0===t.code&&(t=void 0===t.responseJSON?t.statusText:t.responseJSON),void 0===t.code)var a='<h4 class="text-danger">Unknown error: '+t+"</h4>";else a='<h4 class="text-danger">'+t.message+"</h4>",o&&(a+="<div><b>Code:</b> "+t.code+"</div><div><b>Status:</b> "+t.data.status+"</div>");evo_rest_api_end_loading(e,a)}function evo_rest_api_start_loading(e){jQuery(e).addClass("evo_rest_api_loading").append('<div class="evo_rest_api_loader">loading...</div>')}function evo_rest_api_end_loading(e,t){jQuery(e).removeClass("evo_rest_api_loading").html(t).find(".evo_rest_api_loader").remove()}function evo_link_initialize_fieldset(o){if(0<jQuery("#"+o+"attachments_fieldset_table").length){var e=jQuery("#"+o+"attachments_fieldset_table").height();e=320<e?320:e<97?97:e,jQuery("#"+o+"attachments_fieldset_wrapper").height(e),jQuery("#"+o+"attachments_fieldset_wrapper").resizable({minHeight:80,handles:"s",resize:function(e,t){jQuery("#"+o+"attachments_fieldset_wrapper").resizable("option","maxHeight",jQuery("#"+o+"attachments_fieldset_table").height()),evo_link_update_overlay(o)}}),jQuery(document).on("click","#"+o+"attachments_fieldset_wrapper .ui-resizable-handle",function(){var e=jQuery("#"+o+"attachments_fieldset_table").height(),t=jQuery("#"+o+"attachments_fieldset_wrapper").height()+80;jQuery("#"+o+"attachments_fieldset_wrapper").css("height",e<t?e:t),evo_link_update_overlay(o)})}}function evo_link_update_overlay(e){jQuery("#"+e+"attachments_fieldset_overlay").length&&jQuery("#"+e+"attachments_fieldset_overlay").css("height",jQuery("#"+e+"attachments_fieldset_wrapper").closest(".panel").height())}function evo_link_fix_wrapper_height(e){var t=void 0===e?"":e,o=jQuery("#"+t+"attachments_fieldset_table").height();jQuery("#"+t+"attachments_fieldset_wrapper").height()!=o&&jQuery("#"+t+"attachments_fieldset_wrapper").height(jQuery("#"+t+"attachments_fieldset_table").height())}function evo_link_change_position(o,e,t){var a=o,n=o.value,i=o.id.substr(17);return jQuery.get(e+"anon_async.php?action=set_object_link_position&link_ID="+i+"&link_position="+n+"&crumb_link="+t,{},function(e,t){"OK"==(e=ajax_debug_clear(e))?(evoFadeSuccess(jQuery(a).closest("tr")),jQuery(a).closest("td").removeClass("error"),"cover"==n&&jQuery("select[name=link_position][id!="+o.id+"] option[value=cover]:selected").each(function(){jQuery(this).parent().val("aftermore"),evoFadeSuccess(jQuery(this).closest("tr"))})):(jQuery(a).val(e),evoFadeFailure(jQuery(a).closest("tr")),jQuery(a.form).closest("td").addClass("error"))}),!1}function evo_link_insert_inline(e,t,o,a,n,i){if(null==a&&(a=0),void 0!==i){var r="["+e+":"+t;o.length&&(r+=":"+o),r+="]",void 0!==n&&!1!==n&&(r+=n+"[/"+e+"]");var s=jQuery("#display_position_"+t);0!=s.length&&"inline"!=s.val()?(deferInlineReminder=!0,evo_rest_api_request("links/"+t+"/position/inline",function(e){s.val("inline"),evoFadeSuccess(s.closest("tr")),s.closest("td").removeClass("error"),textarea_wrap_selection(i,r,"",a,window.document)},"POST"),deferInlineReminder=!1):textarea_wrap_selection(i,r,"",a,window.document)}}function evo_link_delete(a,n,i,e){return evo_rest_api_request("links/"+i,{action:e},function(e){if("item"==n||"comment"==n||"emailcampaign"==n||"message"==n){var t=window.b2evoCanvas;if(null!=t){var o=new RegExp("\\[(image|file|inline|video|audio|thumbnail):"+i+":?[^\\]]*\\]","ig");textarea_str_replace(t,o,"",window.document)}}jQuery(a).closest("tr").remove(),evo_link_fix_wrapper_height()},"DELETE"),!1}function evo_link_change_order(d,e,l){return evo_rest_api_request("links/"+e+"/"+l,function(e){var t=jQuery(d).closest("tr"),o=t.find("span[data-order]");if("move_up"==l){var a=o.attr("data-order"),n=jQuery(t.prev()).find("span[data-order]"),i=n.attr("data-order");t.prev().before(t),o.attr("data-order",i),n.attr("data-order",a)}else{a=o.attr("data-order");var r=jQuery(t.next()).find("span[data-order]"),s=r.attr("data-order");t.next().after(t),o.attr("data-order",s),r.attr("data-order",a)}evoFadeSuccess(t)},"POST"),!1}function evo_link_attach(e,t,o,a,n){return evo_rest_api_request("links",{action:"attach",type:e,object_ID:t,root:o,path:a},function(e){void 0===n&&(n="");var t=jQuery("#"+n+"attachments_fieldset_table .results table",window.parent.document),o=jQuery(e.list_content);t.replaceWith(jQuery("table",o)).promise().done(function(e){setTimeout(function(){window.parent.evo_link_fix_wrapper_height()},10)})}),!1}function evo_link_ajax_loading_overlay(){var e=jQuery("#attachments_fieldset_table"),t=!1;return 0==e.find(".results_ajax_loading").length&&(t=jQuery('<div class="results_ajax_loading"><div>&nbsp;</div></div>'),e.css("position","relative"),t.css({width:e.width(),height:e.height()}),e.append(t)),t}function evo_link_refresh_list(e,t,o){var a=evo_link_ajax_loading_overlay();return a&&evo_rest_api_request("links",{action:void 0===o?"refresh":"sort",type:e.toLowerCase(),object_ID:t},function(e){jQuery("#attachments_fieldset_table").html(e.html),a.remove(),evo_link_fix_wrapper_height()}),!1}function evo_link_sort_list(o){var a,n=jQuery("#"+o+"attachments_fieldset_table tbody.filelist_tbody tr");n.sort(function(e,t){var o=parseInt(jQuery("span[data-order]",e).attr("data-order")),a=parseInt(jQuery("span[data-order]",t).attr("data-order"));return(o=o||n.length)<(a=a||n.length)?-1:a<o?1:0}),$.each(n,function(e,t){0===e?jQuery(t).prependTo("#"+o+"attachments_fieldset_table tbody.filelist_tbody"):jQuery(t).insertAfter(a),a=t})}function ajax_debug_clear(e){return e=(e=e.replace(/<!-- Ajax response end -->/,"")).replace(/(<div class="jslog">[\s\S]*)/i,""),jQuery.trim(e)}function ajax_response_is_correct(e){return!!e.match(/<!-- Ajax response end -->/)&&""!=ajax_debug_clear(e)}jQuery(document).ready(function(){jQuery("img.loadimg").each(function(){jQuery(this).prop("complete")?(jQuery(this).removeClass("loadimg"),""==jQuery(this).attr("class")&&jQuery(this).removeAttr("class")):jQuery(this).on("load",function(){jQuery(this).removeClass("loadimg"),""==jQuery(this).attr("class")&&jQuery(this).removeAttr("class")})})}),jQuery(document).on("click","a.evo_post_flag_btn",function(){var t=jQuery(this),e=parseInt(t.data("id"));return 0<e&&(t.data("status","inprogress"),jQuery("span",jQuery(this)).addClass("fa-x--hover"),evo_rest_api_request("collections/"+t.data("coll")+"/items/"+e+"/flag",function(e){e.flag?(t.find("span:first").show(),t.find("span:last").hide()):(t.find("span:last").show(),t.find("span:first").hide()),jQuery("span",t).removeClass("fa-x--hover"),setTimeout(function(){t.removeData("status")},500)},"PUT")),!1}),jQuery(document).on("mouseover","a.evo_post_flag_btn",function(){"inprogress"!=jQuery(this).data("status")&&jQuery("span",jQuery(this)).addClass("fa-x--hover")}),jQuery(document).on("keydown","textarea, input",function(e){!e.metaKey&&!e.ctrlKey||13!=e.keyCode&&10!=e.keyCode||jQuery(this).closest("form").submit()});var ddexitpop=function(a){var n={delayregister:0,delayshow:200,hideaftershow:!0,displayfreq:"always",persistcookie:"ddexitpop_shown",fxclass:"rubberBand",mobileshowafter:3e3,onddexitpop:function(){}},e=["bounce","flash","pulse","rubberBand","shake","swing","tada","wobble","jello","bounceIn","bounceInDown","bounceInLeft","bounceInRight","bounceInUp","fadeIn","fadeInDown","fadeInDownBig","fadeInLeft","fadeInLeftBig","fadeInRight","fadeInRightBig","fadeInUp","fadeInUpBig","flipInX","flipInY","lightSpeedIn","rotateIn","rotateInDownLeft","rotateInDownRight","rotateInUpLeft","rotateInUpRight","slideInUp","slideInDown","slideInLeft","slideInRight","zoomIn","zoomInDown","zoomInLeft","zoomInRight","zoomInUp","rollIn"],t="ontouchstart"in window||0<navigator.msMaxTouchPoints?"touchstart":"click";function i(e){var t=new RegExp(e+"=[^;]+","i");return document.cookie.match(t)?document.cookie.match(t)[0].split("=")[1]:null}function r(e,t,o){var a="",n=new Date;if(void 0!==o){var i=parseInt(o)*(/hr/i.test(o)?60:/day/i.test(o)?1440:1);n.setMinutes(n.getMinutes()+i),a="; expires="+n.toUTCString()}document.cookie=e+"="+t+"; path=/"+a}var s={wrappermarkup:'<div id="ddexitpopwrapper"><div class="veil"></div></div>',$wrapperref:null,$contentref:null,displaypopup:!0,delayshowtimer:null,settings:null,ajaxrequest:function(e){var t=function(e){if(/^http/i.test(e)){var t=document.createElement("a");return t.href=e,t.href.replace(RegExp(t.hostname,"i"),location.hostname)}return e}(e);a.ajax({url:t,dataType:"html",error:function(e){alert("Error fetching content.<br />Server Response: "+e.responseText)},success:function(e){s.$contentref=a(e).appendTo(document.body),s.setup(s.$contentref)}})},detectexit:function(e){e.clientY<60&&(this.delayshowtimer=setTimeout(function(){s.showpopup(),s.settings.onddexitpop(s.$contentref)},this.settings.delayshow))},detectenter:function(e){e.clientY<60&&clearTimeout(this.delayshowtimer)},showpopup:function(){null!=this.$contentref&&1==this.displaypopup&&(!0===this.settings.randomizefxclass&&(this.settings.fxclass=e[Math.floor(Math.random()*e.length)]),this.$wrapperref.addClass("open"),this.$contentref.addClass(this.settings.fxclass),this.displaypopup=!1,this.settings.hideaftershow&&a(document).off("mouseleave.registerexit"))},hidepopup:function(){this.$wrapperref.removeClass("open"),this.$contentref.removeClass(this.settings.fxclass),this.displaypopup=!0},setup:function(e){this.$contentref.addClass("animated"),this.$wrapperref=a(this.wrappermarkup).appendTo(document.body),this.$wrapperref.append(this.$contentref),this.$wrapperref.find(".veil").on(t,function(){s.hidepopup()}),"always"!=this.settings.displayfreq&&("session"==this.settings.displayfreq?r(this.settings.persistcookie,"yes"):/\d+(hr|day)/i.test(this.settings.displayfreq)&&(r(this.settings.persistcookie,"yes",this.settings.displayfreq),r(this.settings.persistcookie+"_duration",this.settings.displayfreq,this.settings.displayfreq)))},init:function(e){var t=a.extend({},n,e),o=i(t.persistcookie+"_duration");!o||"session"!=t.displayfreq&&t.displayfreq==o||(r(t.persistcookie,"yes",-1),r(t.persistcookie+"_duration","",-1)),"always"!=t.displayfreq&&i(t.persistcookie)||("random"==t.fxclass&&(t.randomizefxclass=!0),"ajax"==(this.settings=t).contentsource[0]?this.ajaxrequest(t.contentsource[1]):"id"==t.contentsource[0]?(this.$contentref=a("#"+t.contentsource[1]).appendTo(document.body),this.setup(this.$contentref)):"inline"==t.contentsource[0]&&(this.$contentref=a(t.contentsource[1]).appendTo(document.body),this.setup(this.$contentref)),setTimeout(function(){a(document).on("mouseleave.registerexit",function(e){s.detectexit(e)}),a(document).on("mouseenter.registerenter",function(e){s.detectenter(e)})},t.delayregister),0<t.mobileshowafter&&a(document).one("touchstart",function(){setTimeout(function(){s.showpopup()},t.mobileshowafter)}))}};return s}(jQuery);