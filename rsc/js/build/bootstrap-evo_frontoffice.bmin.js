/* This includes 11 files: build/evo_generic.bmin.js, src/bootstrap-evo_modal_window.js, src/evo_images.js, src/evo_user_crop.js, src/evo_user_report.js, src/evo_user_contact_groups.js, src/evo_rest_api.js, src/evo_item_flag.js, src/evo_links.js, src/evo_forms.js, ajax.js */

function evo_prevent_key_enter(e){jQuery(e).keypress(function(e){if(13==e.keyCode)return!1})}jQuery(document).ready(function(){"undefined"!=typeof evo_comment_rating_config&&jQuery("#comment_rating").html("").raty(evo_comment_rating_config)}),jQuery(document).ready(function(){"undefined"!=typeof evo_widget_coll_search_form&&(jQuery(evo_widget_coll_search_form.selector).tokenInput(evo_widget_coll_search_form.url,evo_widget_coll_search_form.config),void 0!==evo_widget_coll_search_form.placeholder&&jQuery("#token-input-search_author").attr("placeholder",evo_widget_coll_search_form.placeholder).css("width","100%"))}),jQuery(document).ready(function(){"undefined"!=typeof evo_autocomplete_login_config&&(jQuery("input.autocomplete_login").on("added",function(){jQuery("input.autocomplete_login").each(function(){if(!jQuery(this).hasClass("tt-input")&&!jQuery(this).hasClass("tt-hint")){var t="";t=jQuery(this).hasClass("only_assignees")?restapi_url+evo_autocomplete_login_config.url:restapi_url+"users/logins",jQuery(this).data("status")&&(t+="&status="+jQuery(this).data("status")),jQuery(this).typeahead(null,{displayKey:"login",source:function(e,a){jQuery.ajax({type:"GET",dataType:"JSON",url:t,data:{q:e},success:function(e){var t=new Array;for(var o in e.list)t.push({login:e.list[o]});a(t)}})}})}})}),jQuery("input.autocomplete_login").trigger("added"),evo_prevent_key_enter(evo_autocomplete_login_config.selector))}),jQuery(document).ready(function(){"undefined"!=typeof evo_widget_poll_initialize&&(jQuery('.evo_poll__selector input[type="checkbox"]').on("click",function(){var e=jQuery(this).closest(".evo_poll__table"),t=jQuery(".evo_poll__selector input:checked",e).length>=e.data("max-answers");jQuery(".evo_poll__selector input[type=checkbox]:not(:checked)",e).prop("disabled",t)}),jQuery(".evo_poll__table").each(function(){var e=jQuery(this);e.width()>e.parent().width()&&(jQuery(".evo_poll__title",e).css("white-space","normal"),jQuery(".evo_poll__title label",e).css({width:Math.floor(e.parent().width()/2)+"px","word-wrap":"break-word"}))}))}),jQuery(document).ready(function(){if("undefined"!=typeof evo_plugin_auto_anchors_settings){jQuery("h1, h2, h3, h4, h5, h6").each(function(){if(jQuery(this).attr("id")&&jQuery(this).hasClass("evo_auto_anchor_header")){var e=location.href.replace(/#.+$/,"")+"#"+jQuery(this).attr("id");jQuery(this).append(' <a href="'+e+'" class="evo_auto_anchor_link"><span class="fa fa-link"></span></a>')}});var t=jQuery("#evo_toolbar").length?jQuery("#evo_toolbar").height():0;jQuery(".evo_auto_anchor_link").on("click",function(){var e=jQuery(this).attr("href");return jQuery("html,body").animate({scrollTop:jQuery(this).offset().top-t-evo_plugin_auto_anchors_settings.offset_scroll},function(){window.history.pushState("","",e)}),!1})}}),jQuery(document).ready(function(){if("undefined"!=typeof evo_plugin_table_contents_settings){var o=jQuery("#evo_toolbar").length?jQuery("#evo_toolbar").height():0;jQuery(".evo_plugin__table_of_contents a").on("click",function(){var e=jQuery("#"+jQuery(this).data("anchor"));if(0==e.length||!e.prop("tagName").match(/^h[1-6]$/i))return!0;var t=jQuery(this).attr("href");return jQuery("html,body").animate({scrollTop:e.offset().top-o-evo_plugin_table_contents_settings.offset_scroll},function(){window.history.pushState("","",t)}),!1})}});var modal_window_js_initialized=!1;function openModalWindow(e,t,o,a,r,n,i,s,l,d,_){var u=void 0===t||"auto"==t?"":"width:"+t+";",c=void 0===o||0==o||""==o?"":"height:"+o,p=c.match(/%$/i)?' style="height:100%;overflow:hidden;"':"",h=o.match(/px/i)?' style="min-height:'+(o.replace("px","")-157)+'px"':"",y=void 0===n||0!=n;if(void 0!==n&&""!=n)if("object"==typeof n)var v=n[0],f=n[1],m=void 0===n[2]?"form":n[2];else v=n,f="btn-primary",m="form";if(void 0!==i&&i&&jQuery("#modal_window").remove(),0==jQuery("#modal_window").length){var j='<div id="modal_window" class="modal fade" tabindex="-1" role="dialog" aria-hidden="true"><div class="modal-dialog" style="'+u+c+'"><div class="modal-content"'+p+">";if(void 0!==r&&""!=r&&(j+='<div class="modal-header"><button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button><h4 class="modal-title">'+r+"</h4></div>"),j+='<div class="modal-body"'+p+h+">"+e,l){jQuery("#"+l);j+="<script>jQuery( document ).ready( function() {var iframe = jQuery( '#"+l+"' );iframe.on( 'load', function() {iframe.closest( '.modal-body' ).find( 'span.loader_img' ).remove();setModalIFrameUnload( '"+l+"' );});});<\/script>"}j+="</div>",y&&(j+='<div class="modal-footer">',void 0!==n&&""!=n&&(j+='<button class="btn '+f+'" type="submit" style="display:none">'+v+"</button>"),j+='<button class="btn btn-default" data-dismiss="modal" aria-hidden="true">'+evo_js_lang_close+"</button></div>"),j+="</div></div></div>",jQuery("body").append(j)}else jQuery("#modal_window .modal-body").html(e);void 0!==l?jQuery("#"+l).on("load",function(){prepareModalWindow(jQuery(this).contents(),m,y,s),jQuery("#modal_window .loader_img").remove(),jQuery("#"+l).show()}):prepareModalWindow("#modal_window",m,y,s),"function"==typeof d&&jQuery("#modal_window").on("shown.bs.modal",d);var g={};modal_window_js_initialized&&(g="show"),jQuery("#modal_window").modal(g),u||(jQuery("#modal_window .modal-dialog").css({display:"table",width:"auto"}),jQuery("#modal_window .modal-dialog .modal-content").css({display:"table-cell"})),jQuery("#modal_window").on("hidden.bs.modal",function(){jQuery(this).remove(),"function"==typeof _&&_()}),modal_window_js_initialized=!0}function prepareModalWindow(e,t,o,a){o&&(void 0!==a&&a||(jQuery("legend",e).remove(),jQuery("#close_button",e).remove(),jQuery(".panel, .panel-body",e).removeClass("panel panel-default panel-body")),0==jQuery(t+" input[type=submit]",e).length?jQuery("#modal_window .modal-footer button[type=submit]").hide():(jQuery(t+" input[type=submit]",e).hide(),jQuery("#modal_window .modal-footer button[type=submit]").show()),jQuery(t,e).change(function(){var e=jQuery(this).find("input[type=submit]");0<e.length?(e.hide(),jQuery("#modal_window .modal-footer button[type=submit]").show()):jQuery("#modal_window .modal-footer button[type=submit]").hide()}),jQuery("#modal_window .modal-footer button[type=submit]").off("click"),jQuery("#modal_window .modal-footer button[type=submit]").on("click",function(){1!==jQuery(this).data("click_init")&&(jQuery(t+" input[type=submit]",e).click(),jQuery(this).data("click_init",1))})),jQuery(t+" a.btn",e).each(function(){jQuery("#modal_window .modal-footer").prepend("<a href="+jQuery(this).attr("href")+'><button type="button" class="'+jQuery(this).attr("class")+'">'+jQuery(this).html()+"</button></a>"),jQuery(this).remove()}),0<jQuery(t+" #current_modal_title",e).length&&jQuery("#modal_window .modal-title").html(jQuery(t+" #current_modal_title",e).html())}function closeModalWindow(e,t){return void 0===e&&(e=window.document),"function"==typeof t&&jQuery("#modal_window").on("hidden.bs.modal",t),jQuery("#modal_window",e).modal("hide"),!1}function setModalIFrameUnload(e){var o=jQuery("#"+e);o[0].contentWindow.onunload=function(){var e=o.closest(".modal-body"),t=jQuery('<span class="loader_img absolute_center" title="'+evo_js_lang_loading+'"></span>');jQuery(e).prepend(t)}}function user_crop_avatar(e,t,o){void 0===o&&(o="avatar");var a=750,r=jQuery(window).width(),n=jQuery(window).height(),i=n/r,s=10,l=10;s=320<(r=a<r?a:r<320?320:r)-2*s?10:0,l=320<(n=a<n?a:n<320?320:n)-2*l?10:0;var d=a<r?a:r,_=a<n?a:n;openModalWindow('<span id="spinner" class="loader_img loader_user_report absolute_center" title="'+evo_js_lang_loading+'"></span>',d+"px",_+"px",!0,evo_js_lang_crop_profile_pic,[evo_js_lang_crop,"btn-primary"],!0);var u=jQuery("div.modal-dialog div.modal-body").length?jQuery("div.modal-dialog div.modal-body"):jQuery("#overlay_page"),c=parseInt(u.css("paddingTop")),p=parseInt(u.css("paddingRight")),h=parseInt(u.css("paddingBottom")),y=parseInt(u.css("paddingLeft")),v=(jQuery("div.modal-dialog div.modal-body").length?parseInt(u.css("min-height")):_-100)-(c+h),f={user_ID:e,file_ID:t,aspect_ratio:i,content_width:d-(y+p),content_height:v,display_mode:"js",crumb_user:evo_js_crumb_user};return evo_js_is_backoffice?(f.ctrl="user",f.user_tab="crop",f.user_tab_from=o):(f.blog=evo_js_blog,f.disp="avatar",f.action="crop"),jQuery.ajax({type:"POST",url:evo_js_user_crop_ajax_url,data:f,success:function(e){openModalWindow(e,d+"px",_+"px",!0,evo_js_lang_crop_profile_pic,[evo_js_lang_crop,"btn-primary"])}}),!1}function user_report(e,t){openModalWindow('<span class="loader_img loader_user_report absolute_center" title="'+evo_js_lang_loading+'"></span>',"auto","",!0,evo_js_lang_report_user,[evo_js_lang_report_this_user_now,"btn-danger"],!0);var o={action:"get_user_report_form",user_ID:e,crumb_user:evo_js_crumb_user};return evo_js_is_backoffice?(o.is_backoffice=1,o.user_tab=t):o.blog=evo_js_blog,jQuery.ajax({type:"POST",url:evo_js_user_report_ajax_url,data:o,success:function(e){openModalWindow(e,"auto","",!0,evo_js_lang_report_user,[evo_js_lang_report_this_user_now,"btn-danger"])}}),!1}function user_contact_groups(e){return openModalWindow('<span class="loader_img loader_user_report absolute_center" title="'+evo_js_lang_loading+'"></span>',"auto","",!0,evo_js_lang_contact_groups,evo_js_lang_save,!0),jQuery.ajax({type:"POST",url:evo_js_user_contact_groups_ajax_url,data:{action:"get_user_contact_form",blog:evo_js_blog,user_ID:e,crumb_user:evo_js_crumb_user},success:function(e){openModalWindow(e,"auto","",!0,evo_js_lang_contact_groups,evo_js_lang_save)}}),!1}function evo_rest_api_request(url,params_func,func_method,method){var params=params_func,func=func_method;"function"==typeof params_func&&(func=params_func,params={},method=func_method),void 0===method&&(method="GET"),jQuery.ajax({contentType:"application/json; charset=utf-8",type:method,url:restapi_url+url,data:params}).then(function(data,textStatus,jqXHR){"object"==typeof jqXHR.responseJSON&&eval(func)(data,textStatus,jqXHR)})}function evo_rest_api_print_error(e,t,o){if("string"!=typeof t&&void 0===t.code&&(t=void 0===t.responseJSON?t.statusText:t.responseJSON),void 0===t.code)var a='<h4 class="text-danger">Unknown error: '+t+"</h4>";else{a='<h4 class="text-danger">'+t.message+"</h4>";o&&(a+="<div><b>Code:</b> "+t.code+"</div><div><b>Status:</b> "+t.data.status+"</div>")}evo_rest_api_end_loading(e,a)}function evo_rest_api_start_loading(e){jQuery(e).addClass("evo_rest_api_loading").append('<div class="evo_rest_api_loader">loading...</div>')}function evo_rest_api_end_loading(e,t){jQuery(e).removeClass("evo_rest_api_loading").html(t).find(".evo_rest_api_loader").remove()}function evo_link_initialize_fieldset(o){if(0<jQuery("#"+o+"attachments_fieldset_table").length){var e=jQuery("#"+o+"attachments_fieldset_table").height();e=320<e?320:e<97?97:e,jQuery("#"+o+"attachments_fieldset_wrapper").height(e),jQuery("#"+o+"attachments_fieldset_wrapper").resizable({minHeight:80,handles:"s",resize:function(e,t){jQuery("#"+o+"attachments_fieldset_wrapper").resizable("option","maxHeight",jQuery("#"+o+"attachments_fieldset_table").height()),evo_link_update_overlay(o)}}),jQuery(document).on("click","#"+o+"attachments_fieldset_wrapper .ui-resizable-handle",function(){var e=jQuery("#"+o+"attachments_fieldset_table").height(),t=jQuery("#"+o+"attachments_fieldset_wrapper").height()+80;jQuery("#"+o+"attachments_fieldset_wrapper").css("height",e<t?e:t),evo_link_update_overlay(o)})}}function evo_link_update_overlay(e){jQuery("#"+e+"attachments_fieldset_overlay").length&&jQuery("#"+e+"attachments_fieldset_overlay").css("height",jQuery("#"+e+"attachments_fieldset_wrapper").closest(".panel").height())}function evo_link_fix_wrapper_height(e){var t=void 0===e?"":e,o=jQuery("#"+t+"attachments_fieldset_table").height();jQuery("#"+t+"attachments_fieldset_wrapper").height()!=o&&jQuery("#"+t+"attachments_fieldset_wrapper").height(jQuery("#"+t+"attachments_fieldset_table").height())}function evo_link_change_position(o,e,t){var a=o,r=o.value,n=o.id.substr(17);return jQuery.get(e+"anon_async.php?action=set_object_link_position&link_ID="+n+"&link_position="+r+"&crumb_link="+t,{},function(e,t){"OK"==(e=ajax_debug_clear(e))?(evoFadeSuccess(jQuery(a).closest("tr")),jQuery(a).closest("td").removeClass("error"),"cover"==r&&jQuery("select[name=link_position][id!="+o.id+"] option[value=cover]:selected").each(function(){jQuery(this).parent().val("aftermore"),evoFadeSuccess(jQuery(this).closest("tr"))})):(jQuery(a).val(e),evoFadeFailure(jQuery(a).closest("tr")),jQuery(a.form).closest("td").addClass("error"))}),!1}function evo_link_insert_inline(e,t,o,a,r,n){if(null==a&&(a=0),void 0!==n){var i="["+e+":"+t;o.length&&(i+=":"+o),i+="]",void 0!==r&&!1!==r&&(i+=r+"[/"+e+"]");var s=jQuery("#display_position_"+t);0!=s.length&&"inline"!=s.val()?(deferInlineReminder=!0,evo_rest_api_request("links/"+t+"/position/inline",function(e){s.val("inline"),evoFadeSuccess(s.closest("tr")),s.closest("td").removeClass("error"),textarea_wrap_selection(n,i,"",a,window.document)},"POST"),deferInlineReminder=!1):textarea_wrap_selection(n,i,"",a,window.document)}}function evo_link_delete(a,r,n,e){return evo_rest_api_request("links/"+n,{action:e},function(e){if("item"==r||"comment"==r||"emailcampaign"==r||"message"==r){var t=window.b2evoCanvas;if(null!=t){var o=new RegExp("\\[(image|file|inline|video|audio|thumbnail):"+n+":?[^\\]]*\\]","ig");textarea_str_replace(t,o,"",window.document)}}jQuery(a).closest("tr").remove(),evo_link_fix_wrapper_height()},"DELETE"),!1}function evo_link_change_order(l,e,d){return evo_rest_api_request("links/"+e+"/"+d,function(e){var t=jQuery(l).closest("tr"),o=t.find("span[data-order]");if("move_up"==d){var a=o.attr("data-order"),r=jQuery(t.prev()).find("span[data-order]"),n=r.attr("data-order");t.prev().before(t),o.attr("data-order",n),r.attr("data-order",a)}else{a=o.attr("data-order");var i=jQuery(t.next()).find("span[data-order]"),s=i.attr("data-order");t.next().after(t),o.attr("data-order",s),i.attr("data-order",a)}evoFadeSuccess(t)},"POST"),!1}function evo_link_attach(e,t,o,a,r){return evo_rest_api_request("links",{action:"attach",type:e,object_ID:t,root:o,path:a},function(e){void 0===r&&(r="");var t=jQuery("#"+r+"attachments_fieldset_table .results table",window.parent.document),o=jQuery(e.list_content);t.replaceWith(jQuery("table",o)).promise().done(function(e){setTimeout(function(){window.parent.evo_link_fix_wrapper_height()},10)})}),!1}function evo_link_ajax_loading_overlay(){var e=jQuery("#attachments_fieldset_table"),t=!1;return 0==e.find(".results_ajax_loading").length&&(t=jQuery('<div class="results_ajax_loading"><div>&nbsp;</div></div>'),e.css("position","relative"),t.css({width:e.width(),height:e.height()}),e.append(t)),t}function evo_link_refresh_list(e,t,o){var a=evo_link_ajax_loading_overlay();return a&&evo_rest_api_request("links",{action:void 0===o?"refresh":"sort",type:e.toLowerCase(),object_ID:t},function(e){jQuery("#attachments_fieldset_table").html(e.html),a.remove(),evo_link_fix_wrapper_height()}),!1}function evo_link_sort_list(o){var a,r=jQuery("#"+o+"attachments_fieldset_table tbody.filelist_tbody tr");r.sort(function(e,t){var o=parseInt(jQuery("span[data-order]",e).attr("data-order")),a=parseInt(jQuery("span[data-order]",t).attr("data-order"));return(o=o||r.length)<(a=a||r.length)?-1:a<o?1:0}),$.each(r,function(e,t){a=(0===e?jQuery(t).prependTo("#"+o+"attachments_fieldset_table tbody.filelist_tbody"):jQuery(t).insertAfter(a),t)})}function ajax_debug_clear(e){return e=(e=e.replace(/<!-- Ajax response end -->/,"")).replace(/(<div class="jslog">[\s\S]*)/i,""),jQuery.trim(e)}function ajax_response_is_correct(e){return!!e.match(/<!-- Ajax response end -->/)&&""!=(e=ajax_debug_clear(e))}jQuery(document).ready(function(){jQuery("img.loadimg").each(function(){jQuery(this).prop("complete")?(jQuery(this).removeClass("loadimg"),""==jQuery(this).attr("class")&&jQuery(this).removeAttr("class")):jQuery(this).on("load",function(){jQuery(this).removeClass("loadimg"),""==jQuery(this).attr("class")&&jQuery(this).removeAttr("class")})})}),jQuery(document).on("click","a.evo_post_flag_btn",function(){var t=jQuery(this),e=parseInt(t.data("id"));return 0<e&&(t.data("status","inprogress"),jQuery("span",jQuery(this)).addClass("fa-x--hover"),evo_rest_api_request("collections/"+t.data("coll")+"/items/"+e+"/flag",function(e){e.flag?(t.find("span:first").show(),t.find("span:last").hide()):(t.find("span:last").show(),t.find("span:first").hide()),jQuery("span",t).removeClass("fa-x--hover"),setTimeout(function(){t.removeData("status")},500)},"PUT")),!1}),jQuery(document).on("mouseover","a.evo_post_flag_btn",function(){"inprogress"!=jQuery(this).data("status")&&jQuery("span",jQuery(this)).addClass("fa-x--hover")}),jQuery(document).on("keydown","textarea, input",function(e){!e.metaKey&&!e.ctrlKey||13!=e.keyCode&&10!=e.keyCode||jQuery(this).closest("form").submit()});