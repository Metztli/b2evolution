/* This includes 4 files: jquery.colorbox.js, voting.js, jquery.touchswipe.js, colorbox.init.js */

!function(u,s,e){var t,c,h,d,p,f,g,i,w,v,m,b,x,_,o,y,T,j,k,Q,C,S,I,E,r,D,H,O,L,n,a,M,W,l={transition:"elastic",speed:300,width:!1,initialWidth:"600",innerWidth:!1,minWidth:!1,maxWidth:!1,height:!1,initialHeight:"450",innerHeight:!1,minHeight:!1,maxHeight:!1,scalePhotos:!0,scrolling:!0,inline:!1,html:!1,iframe:!1,fastIframe:!0,photo:!1,href:!1,title:!1,rel:!1,preloading:!0,current:"image {current} of {total}",previous:"previous",next:"next",close:"close",openNewWindowText:"open in new window",open:!1,returnFocus:!0,loop:!0,slideshow:!1,slideshowAuto:!0,slideshowSpeed:2500,slideshowStart:"start slideshow",slideshowStop:"stop slideshow",onOpen:!1,onLoad:!1,onComplete:!1,onCleanup:!1,onClosed:!1,overlayClose:!0,escKey:!0,arrowKey:!0,top:!1,bottom:!1,left:!1,right:!1,fixed:!1,data:!1,displayVoting:!1,votingUrl:""},$="colorbox",P="cbox",z=P+"Element",N=P+"_open",A=P+"_load",R=P+"_complete",F=P+"_cleanup",U=P+"_closed",V=P+"_purge",K=!u.support.opacity;function X(e,t,o){return o=s.createElement("div"),e&&(o.id=P+e),o.style.cssText=t||"",u(o)}function Y(e,t){return Math.round((/%/.test(e)?("x"===t?f.width():f.height())/100:1)*parseInt(e,10))}function B(e){return T.photo||/\.(gif|png|jp(e|g|eg)|bmp|ico|webp|avif|jxr|svg)(?:\?([^#]*))?(?:#(\.*))?$/i.test(e)}function q(e){for(e in T=u.extend({},u.data(r,$)))u.isFunction(T[e])&&"on"!==e.substring(0,2)&&(T[e]=T[e].call(r));T.rel=T.rel||r.rel||"nofollow",T.href=T.href||u(r).attr("href"),T.title=T.title||r.title,"string"==typeof T.href&&(T.href=u.trim(T.href))}function G(e,t){t&&t.call(r),u.event.trigger(e)}function Z(e){if(!n){if(r=e,j={},q(),p=u(r),D=0,"nofollow"!==T.rel&&(p=u("."+z).filter(function(){return(u.data(this,$).rel||this.rel)===T.rel}),-1===(D=p.index(r))&&(p=p.add(r),D=p.length-1)),!O){if(O=L=!0,c.show(),T.returnFocus)try{r.blur(),u(r).one(U,function(){try{this.focus()}catch(e){}})}catch(e){}t.css({cursor:T.overlayClose?"pointer":"auto"}).show(),T.w=Y(T.initialWidth,"x"),T.h=Y(T.initialHeight,"y"),W.position(),G(N,T.onOpen),y.add(v).hide(),o.html(T.close).show(),function(){var e,t,o,n=P+"Slideshow_",i="click."+P;T.slideshow&&p[1]?(t=function(){b.text(T.slideshowStop).one(i,o),c.removeClass(n+"off").addClass(n+"on"),e=setInterval(function(){O&&(T.loop||D!=p.length-1)||o(),W.next()},T.slideshowSpeed)},o=function(){clearInterval(e),b.text(T.slideshowStart).one(i,t),c.removeClass(n+"on").addClass(n+"off")},T.slideshowAuto?t():o()):c.removeClass(n+"off "+n+"on")}()}W.load(!0)}}(W=u.fn[$]=u[$]=function(e,t){var o=this;if(e=e||{},!o[0]){if(o.selector)return o;o=u("<a/>"),e.open=!0}return t&&(e.onComplete=t),o.each(function(){u.data(this,$,u.extend({},u.data(this,$)||l,e)),u(this).addClass(z)}),(u.isFunction(e.open)&&e.open.call(o)||e.open)&&Z(o[0]),o}).init=function(){f=u(e),c=X().attr({id:$,class:K?P+"IE":""}),t=X("Overlay").hide(),h=X("Wrapper"),d=X("Content").append(g=X("LoadedContent","width:0; height:0; overflow:hidden"),w=X("LoadingOverlay").add(X("LoadingGraphic")),v=X("Title"),$infoBar=X("InfoBar").append($nav=X("Navigation").append(_=X("Previous"),m=X("Current"),x=X("Next")),$voting=X("Voting"),b=X("Slideshow"),o=X("Close"),$open=X("Open"))),h.append(d),i=X(!1,"position:absolute; width:9999px; visibility:hidden; display:none"),u("body").prepend(t,c.append(h,i)),$voting.data("voting_positions_done",0),previous_title="",d.children().on("mouseenter",function(){u(this).addClass("hover")}).on("mouseleave",function(){u(this).removeClass("hover")}).addClass("hover"),k=d.outerHeight(!0)-d.height(),Q=d.outerWidth(!0)-d.width(),S=g.outerHeight(!0),I=g.outerWidth(!0),C=S,E=o.height()+4,c.css({"padding-bottom":k,"padding-right":Q}).hide(),x.on("click",function(){W.next()}),_.on("click",function(){W.prev()}),o.on("click",function(){W.close()}),$open.on("click",function(){W.close()}),y=x.add(_).add(m).add(b),d.children().removeClass("hover"),t.on("click",function(){T.overlayClose&&W.close()}),u(s).bind("keydown."+P,function(e){var t=e.keyCode;O&&T.escKey&&27===t&&(e.preventDefault(),W.close()),O&&T.arrowKey&&p[1]&&(37===t?(e.preventDefault(),_.trigger("click")):39===t&&(e.preventDefault(),x.trigger("click")))})},W.remove=function(){c.add(t).remove(),u("."+z).removeData($).removeClass(z)},W.position=function(e,t){var o=null==j.pw||T.w>j.pw?T.w:j.pw,n=null==j.ph||T.h>j.ph?T.h:j.ph,i=u("#colorbox .voting_wrapper");parseInt(d.css("border-bottom"));$infoBar.css({minHeight:E+"px"}),S=o<=700&&$voting.is(":visible")?(i.addClass("compact"),2*C-3):(i.removeClass("compact"),C);var r=0,a=0;function l(e){d[0].style.width=e.style.width,w[0].style.height=w[1].style.height=d[0].style.height=e.style.height}f.unbind("resize."+P),c.hide(),T.fixed?c.css({position:"fixed"}):(r=f.scrollTop(),a=f.scrollLeft(),c.css({position:"absolute"})),!1!==T.right?a+=Math.max(f.width()-o-I-Q-Y(T.right,"x"),0):!1!==T.left?a+=Y(T.left,"x"):a+=Math.round(Math.max(f.width()-o-I-Q,0)/2),!1!==T.bottom?r+=Math.max(s.documentElement.clientHeight-n-S-k-Y(T.bottom,"y"),0):!1!==T.top?r+=Y(T.top,"y"):r+=Math.round(Math.max(s.documentElement.clientHeight-n-S-k,0)/2),c.show(),e=c.width()===o+I&&c.height()===n+S?0:e||0,h[0].style.width=h[0].style.height="9999px",c.dequeue().animate({width:o+I,height:n+S,top:r,left:a},{duration:e,complete:function(){l(this),L=!1,h[0].style.width=o+I+Q+"px",h[0].style.height=n+S+k+"px",t&&t(),setTimeout(function(){f.bind("resize."+P,W.position)},1),W.resizeVoting(),h.parent().width()<380?b.hide():b.show()},step:function(){l(this)}})},W.resize=function(e){if(O){if((e=e||{}).width&&(T.w=Y(e.width,"x")-I-Q),e.innerWidth&&(T.w=Y(e.innerWidth,"x")),g.css({width:T.w}),e.height&&(T.h=Y(e.height,"y")-S-k),e.innerHeight&&(T.h=Y(e.innerHeight,"y")),!e.innerHeight&&!e.height){var t=g.wrapInner("<div style='overflow:auto'></div>").children();T.h=t.outerHeight(),t.replaceWith(t.children())}g.css({height:T.h}),j.pw=T.w,j.ph=T.h,W.position("none"===T.transition?0:T.speed)}},W.prep=function(e){if(O){var t,s="none"===T.transition?0:T.speed;g.remove(),(g=X("LoadedContent").append(e)).hide().appendTo(i.show()).css({width:(T.w=T.w||g.width(),T.w=T.mw&&T.mw<T.w?T.mw:T.w,T.w=T.minWidth&&T.minWidth>T.w?T.minWidth:T.w,j.pw=null==j.pw||T.w>j.pw?T.w:j.pw,j.pw),overflow:T.scrolling?"auto":"hidden"}).css({height:(T.h=T.h||g.height(),T.h=T.mh&&T.mh<T.h?T.mh:T.h,T.h=T.minHeight&&T.minHeight>T.h?T.minHeight:T.h,j.ph=null==j.ph||T.h>j.ph?T.h:j.ph,j.ph)}).prependTo(d),i.hide(),u(H).css({float:"none"}),t=function(){var e,t,o,n,i,r,a=p.length;function l(){K&&c[0].style.removeProperty("filter")}O&&(r=function(){clearTimeout(M),w.hide(),G(R,T.onComplete)},K&&H&&g.fadeIn(100),v.add(g).show(),1<a?("string"==typeof T.current&&380<g.width()&&m.html(T.current.replace("{current}",D+1).replace("{total}",a)).show(),x[T.loop||D<a-1?"show":"hide"]().html(T.next),_[T.loop||D?"show":"hide"]().html(T.previous),e=D?p[D-1]:p[a-1],o=D<a-1?p[D+1]:p[0],T.slideshow&&380<g.width()&&b.show(),T.preloading&&(n=u.data(o,$).href||o.href,t=u.data(e,$).href||e.href,n=u.isFunction(n)?n.call(o):n,t=u.isFunction(t)?t.call(e):t,B(n)&&(u("<img/>")[0].src=n),B(t)&&(u("<img/>")[0].src=t))):y.hide(),T.iframe?(i=u("<iframe/>").addClass(P+"Iframe")[0],T.fastIframe?r():u(i).one("load",r),i.name=P+ +new Date,i.src=T.href,T.scrolling||(i.scrolling="no"),K&&(i.frameBorder=0,i.allowTransparency="true"),u(i).appendTo(g).one(V,function(){i.src="//about:blank"})):r(),"fade"===T.transition?c.fadeTo(s,1,l):l())},"fade"===T.transition?c.fadeTo(s,0,function(){W.position(0,t)}):W.position(s,t)}},W.load=function(e){var t,o,n=W.prep;H=!(L=!0),r=p[D],e||q(),G(V),G(A,T.onLoad),previous_title=T.title,T.displayVoting&&""!=T.votingUrl&&""!=r.id?(0==$voting.data("voting_positions_done")&&(0==S&&(S=g.outerHeight(!0)),$voting.data("voting_positions_done",1)),$voting.show(),init_voting_bar($voting,T.votingUrl,r.id,!0)):""!=$voting.html()&&($voting.html("").hide(),$voting.data("voting_positions_done",0)),T.h=T.height?Y(T.height,"y")-S-k:T.innerHeight&&Y(T.innerHeight,"y"),T.w=T.width?Y(T.width,"x")-I-Q:T.innerWidth&&Y(T.innerWidth,"x"),T.mw=T.w,T.mh=T.h,T.maxWidth&&(T.mw=Y(T.maxWidth,"x")-I-Q,T.mw=T.w&&T.w<T.mw?T.w:T.mw),T.maxHeight&&(T.mh=Y(T.maxHeight,"y")-S-k,T.mh=T.h&&T.h<T.mh?T.h:T.mh),t=T.href,M=setTimeout(function(){w.show()},100),T.inline?(X().hide().insertBefore(u(t)[0]).one(V,function(){u(this).replaceWith(g.children())}),n(u(t))):T.iframe?n(" "):T.html?n(T.html):B(t)?(u(H=new Image).addClass(P+"Photo").on("error",function(){T.title=!1,n(X("Error").text("This image could not be loaded"))}).on("load",function(){var e;H.onload=null,T.scalePhotos&&(o=function(){H.height-=H.height*e,H.width-=H.width*e},T.mw&&H.width>T.mw&&(e=(H.width-T.mw)/H.width,o()),T.mh&&H.height>T.mh&&(e=(H.height-T.mh)/H.height,o())),T.h&&(H.style.marginTop=Math.max(T.h-H.height,0)/2+"px"),jQuery(H).removeClass("zoomin zoomout"),colorbox_is_zoomed=!1;var s=0,c=0,t=H.naturalWidth>1.1*H.width||H.naturalHeight>1.1*H.height;t&&(H.className=H.className+" zoomin"),!t&&p[1]&&(D<p.length-1||T.loop)&&(H.onclick=function(e){W.next()}),t&&jQuery(H).bind("click dblclick",function(e,t){if(colorbox_is_zoomed)H.className=H.className.replace(/zoomout/,""),H.width=s,H.height=c,jQuery(this).parent().scrollLeft(0).scrollTop(0),jQuery(this).css({position:"relative",top:"0",left:"0"});else{W.resize({width:T.mw,height:T.mh+parseInt(g.css("margin-bottom"))});var o=jQuery(this).offset(),n=void 0!==e.pageX?e.pageX:t.originalEvent.touches[0].pageX,i=void 0!==e.pageY?e.pageY:t.originalEvent.touches[0].pageY,r=(n-o.left)/jQuery(this).width(),a=(i-o.top)/jQuery(this).height();H.className=H.className+" zoomout",u(H).css({position:"static",top:0,left:0,transform:"none"}),s=H.width,c=H.height,H.removeAttribute("width"),H.removeAttribute("height");var l=jQuery(this).parent()[0];jQuery(this).parent().scrollLeft(r*(l.scrollWidth-l.clientWidth)).scrollTop(a*(l.scrollHeight-l.clientHeight))}colorbox_is_zoomed=!colorbox_is_zoomed}),K&&(H.style.msInterpolationMode="bicubic"),setTimeout(function(){n(H)},1)}),setTimeout(function(){H.src=t},1)):t&&i.load(t,T.data,function(e,t,o){n("error"===t?X("Error").text("Request unsuccessful: "+o.statusText):u(this).contents())})},W.next=function(){!L&&p[1]&&(D<p.length-1||T.loop)&&(D=D<p.length-1?D+1:0,W.load())},W.prev=function(){!L&&p[1]&&(D||T.loop)&&(D=D?D-1:p.length-1,W.load())},W.close=function(){O&&!n&&(O=!(n=!0),G(F,T.onCleanup),f.unbind("."+P),t.fadeTo(200,0),c.stop().fadeTo(300,0,function(){c.add(t).css({opacity:1,cursor:"auto"}).hide(),G(V),g.remove(),setTimeout(function(){n=!1,G(U,T.onClosed)},1)}))},W.resizeVoting=function(){var e=u("#colorbox .voting_wrapper"),t=h.parent().width();t<=480?m.hide():m.show(),$infoBar.css({minHeight:E+"px"}),S=t<=700&&$voting.is(":visible")?(e.addClass("compact"),2*C-3):(e.removeClass("compact"),C)},W.element=function(){return u(r)},W.settings=l,a=function(e){0!==e.button&&void 0!==e.button||e.ctrlKey||e.shiftKey||e.altKey||(e.preventDefault(),Z(this))},u(s).on("click","."+z,a),u(W.init)}(jQuery,document,this),jQuery.event.special.dblclick={setup:function(e,t){jQuery(this).bind("touchstart.dblclick",jQuery.event.special.dblclick.handler)},teardown:function(e){jQuery(this).unbind("touchstart.dblclick")},handler:function(e){var t=e.target,o=jQuery(t),n=o.data("lastTouch")||0,i=(new Date).getTime(),r=i-n;20<r&&r<500?(o.data("lastTouch",0),o.trigger("dblclick",e)):o.data("lastTouch",i)}},window.init_voting_bar=function(l,s,e,t){function c(){if("cboxVoting"==l.attr("id")){var e=jQuery("#colorbox").width(),t=l.width();e<t&&jQuery("#colorbox").css({left:jQuery("#colorbox").position().left-Math.round(t-e)/2,width:t})}}if(t&&(l.html('<div class="loading">&nbsp;</div>'),jQuery.ajax({type:"POST",url:s+"&vote_ID="+e,success:function(e){l.html(ajax_debug_clear(e)),jQuery("a.action_icon",l).data("votingPanel",l),c(),window.votingAdjust()}})),null==jQuery(l).data("initialized")){function o(t,e,o,n){var i=l.find("#voting_action"),r=i.length?i.val():s;0<l.find("#votingID").length&&(r+="&vote_ID="+l.find("#votingID").val()),0<l.find("#widgetID").length&&(r+="&widget_ID="+l.find("#widgetID").val()),0<l.find("#skinID").length&&(r+="&skin_ID="+l.find("#skinID").val());var a=l.css("backgroundColor");jQuery(t).is(":checkbox")?jQuery(t).is(":checked")?(r+="&checked=1",window.votingFadeIn(l,o)):(r+="&checked=0",window.votingFadeIn(l,n)):(jQuery(t).removeAttr("id"),window.votingFadeIn(l,o)),jQuery.ajax({type:"POST",url:r+"&vote_action="+e,success:function(e){jQuery(t).is(":checkbox")||(l.html(ajax_debug_clear(e)),c()),window.votingFadeIn(l,a),window.votingAdjust()}})}l.on("click","a.action_icon",function(){return!1}),l.on("click","#votingLike",function(){o(this,"like","#bcffb5")}),l.on("click","#votingNoopinion",function(){o(this,"noopinion","#bbb")}),l.on("click","#votingDontlike",function(){o(this,"dontlike","#ffc9c9")}),l.on("click","#votingInappropriate",function(){o(this,"inappropriate","#dcc","#bbb")}),l.on("click","#votingSpam",function(){o(this,"spam","#dcc","#bbb")}),jQuery(l).data("initialized",!0)}},window.votingFadeIn=function(e,t){var o="transparent"==t||"rgba(0, 0, 0, 0)"==t;if(o){for(var n=e.parent(),i=t;n&&("transparent"==i||"rgba(0, 0, 0, 0)"==i);)i=n.css("backgroundColor"),n=n.parent();"HTML"!=n[0].tagName&&(t=i)}e.animate({backgroundColor:t},200,function(){o&&e.css("background-color","transparent")})},window.votingAdjust=function(){$prev=jQuery("#cboxPrevious"),$wrap=jQuery("#cboxWrapper"),$voting=jQuery("#cboxVoting");$prev.width();var e=$("#colorbox .voting_wrapper"),t=($("#colorbox .voting_wrapper > .btn-group"),$("#colorbox .vote_title_panel"),$("#colorbox .vote_others"),$("#colorbox .separator"),$wrap.parent().width());e.removeClass("compact"),t<=700&&e.addClass("compact")},jQuery(document).ready(function(){"undefined"!=typeof evo_init_comment_voting_config&&jQuery("span[id^=vote_helpful_").each(function(){window.init_voting_bar(jQuery(this),evo_init_comment_voting_config.action_url,jQuery(this).find("#votingID").val(),!1)}),"undefined"!=typeof evo_init_item_voting_config&&jQuery("span[id^=vote_item_").each(function(){window.init_voting_bar(jQuery(this),evo_init_item_voting_config.action_url,jQuery(this).find("#votingID").val(),!1)})}),function(e){"function"==typeof define&&define.amd&&define.amd.jQuery?define(["jquery"],e):e(jQuery)}(function(ie){"use strict";var re="left",ae="right",le="up",se="down",ce="in",ue="out",he="none",de="auto",pe="swipe",fe="pinch",ge="tap",we="doubletap",ve="longtap",me="horizontal",be="vertical",xe="all",_e=10,ye="start",Te="move",je="end",ke="cancel",Qe="ontouchstart"in window,Ce="TouchSwipe";function n(e,a){var t=Qe||!a.fallbackToMouseEvents,o=t?"touchstart":"mousedown",n=t?"touchmove":"mousemove",i=t?"touchend":"mouseup",r=t?null:"mouseleave",l="touchcancel",s=0,c=null,u=0,h=0,d=0,p=1,f=0,g=0,w=null,v=ie(e),m="start",b=0,x=null,_=0,y=0,T=0,j=0,k=0,Q=null;try{v.bind(o,C),v.bind(l,E)}catch(e){ie.error("events not supported "+o+","+l+" on jQuery.swipe")}function C(e){if(!0!==v.data(Ce+"_intouch")&&!(0<ie(e.target).closest(a.excludedElements,v).length)){var t,o=e.originalEvent?e.originalEvent:e,n=Qe?o.touches[0]:o;return m=ye,Qe?b=o.touches.length:e.preventDefault(),g=c=null,p=1,f=d=h=u=s=0,x=function(){for(var e=[],t=0;t<=5;t++)e.push({start:{x:0,y:0},end:{x:0,y:0},identifier:0});return e}(),w=function(){var e={};return e[re]=ee(re),e[ae]=ee(ae),e[le]=ee(le),e[se]=ee(se),e}(),Y(),!Qe||b===a.fingers||a.fingers===xe||N()?(G(0,n),_=ne(),2==b&&(G(1,o.touches[1]),h=d=oe(x[0].start,x[1].start)),(a.swipeStatus||a.pinchStatus)&&(t=L(o,m))):t=!1,!1===t?(L(o,m=ke),t):(q(!0),null)}}function S(e){var t=e.originalEvent?e.originalEvent:e;if(m!==je&&m!==ke&&!B()){var o,n=Z(Qe?t.touches[0]:t);if(y=ne(),Qe&&(b=t.touches.length),m=Te,2==b&&(0==h?(G(1,t.touches[1]),h=d=oe(x[0].start,x[1].start)):(Z(t.touches[1]),d=oe(x[0].end,x[1].end),x[0].end,x[1].end,g=p<1?ue:ce),p=function(e,t){return(t/e*1).toFixed(2)}(h,d),f=Math.abs(h-d)),b===a.fingers||a.fingers===xe||!Qe||N()){if(function(e,t){if(a.allowPageScroll===he||N())e.preventDefault();else{var o=a.allowPageScroll===de;switch(t){case re:(a.swipeLeft&&o||!o&&a.allowPageScroll!=me)&&e.preventDefault();break;case ae:(a.swipeRight&&o||!o&&a.allowPageScroll!=me)&&e.preventDefault();break;case le:(a.swipeUp&&o||!o&&a.allowPageScroll!=be)&&e.preventDefault();break;case se:(a.swipeDown&&o||!o&&a.allowPageScroll!=be)&&e.preventDefault()}}}(e,c=function(e,t){var o=function(e,t){var o=e.x-t.x,n=t.y-e.y,i=Math.atan2(n,o),r=Math.round(180*i/Math.PI);r<0&&(r=360-Math.abs(r));return r}(e,t);return o<=45&&0<=o?re:o<=360&&315<=o?re:135<=o&&o<=225?ae:45<o&&o<135?se:le}(n.start,n.end)),s=function(e,t){return Math.round(Math.sqrt(Math.pow(t.x-e.x,2)+Math.pow(t.y-e.y,2)))}(n.start,n.end),u=te(),function(e,t){t=Math.max(t,J(e)),w[e].distance=t}(c,s),(a.swipeStatus||a.pinchStatus)&&(o=L(t,m)),!a.triggerOnTouchEnd||a.triggerOnTouchLeave){var i=!0;if(a.triggerOnTouchLeave){var r=function(e){var t=(e=ie(e)).offset();return{left:t.left,right:t.left+e.outerWidth(),top:t.top,bottom:t.top+e.outerHeight()}}(this);i=function(e,t){return e.x>t.left&&e.x<t.right&&e.y>t.top&&e.y<t.bottom}(n.end,r)}!a.triggerOnTouchEnd&&i?m=O(Te):a.triggerOnTouchLeave&&!i&&(m=O(je)),m!=ke&&m!=je||L(t,m)}}else L(t,m=ke);!1===o&&L(t,m=ke)}}function I(e){var t=e.originalEvent;return Qe&&0<t.touches.length?(T=ne(),j=event.touches.length+1,!0):(B()&&(b=j),e.preventDefault(),y=ne(),u=te(),$()?L(t,m=ke):a.triggerOnTouchEnd||0==a.triggerOnTouchEnd&&m===Te?L(t,m=je):!a.triggerOnTouchEnd&&V()?M(t,m=je,ge):m===Te&&L(t,m=ke),q(!1),null)}function E(){d=h=_=y=b=0,p=1,Y(),q(!1)}function D(e){var t=e.originalEvent;a.triggerOnTouchLeave&&L(t,m=O(je))}function H(){v.unbind(o,C),v.unbind(l,E),v.unbind(n,S),v.unbind(i,I),r&&v.unbind(r,D),q(!1)}function O(e){var t=e,o=P(),n=W(),i=$();return!o||i?t=ke:!n||e!=Te||a.triggerOnTouchEnd&&!a.triggerOnTouchLeave?!n&&e==je&&a.triggerOnTouchLeave&&(t=ke):t=je,t}function L(e,t){var o=void 0;return A()&&R()||R()?o=M(e,t,pe):(z()&&N()||N())&&!1!==o&&(o=M(e,t,fe)),X()&&K()&&!1!==o?o=M(e,t,we):u>a.longTapThreshold&&s<_e&&a.longTap&&!1!==o?o=M(e,t,ve):1!==b&&Qe||!isNaN(s)&&0!==s||!V()||!1===o||(o=M(e,t,ge)),t===ke&&E(),t===je&&(Qe?0==e.touches.length&&E():E()),o}function M(e,t,o){var n=void 0;if(o==pe){if(v.trigger("swipeStatus",[t,c||null,s||0,u||0,b]),a.swipeStatus&&!1===(n=a.swipeStatus.call(v,e,t,c||null,s||0,u||0,b)))return!1;if(t==je&&A()){if(v.trigger("swipe",[c,s,u,b]),a.swipe&&!1===(n=a.swipe.call(v,e,c,s,u,b)))return!1;switch(c){case re:v.trigger("swipeLeft",[c,s,u,b]),a.swipeLeft&&(n=a.swipeLeft.call(v,e,c,s,u,b));break;case ae:v.trigger("swipeRight",[c,s,u,b]),a.swipeRight&&(n=a.swipeRight.call(v,e,c,s,u,b));break;case le:v.trigger("swipeUp",[c,s,u,b]),a.swipeUp&&(n=a.swipeUp.call(v,e,c,s,u,b));break;case se:v.trigger("swipeDown",[c,s,u,b]),a.swipeDown&&(n=a.swipeDown.call(v,e,c,s,u,b))}}}if(o==fe){if(v.trigger("pinchStatus",[t,g||null,f||0,u||0,b,p]),a.pinchStatus&&!1===(n=a.pinchStatus.call(v,e,t,g||null,f||0,u||0,b,p)))return!1;if(t==je&&z())switch(g){case ce:v.trigger("pinchIn",[g||null,f||0,u||0,b,p]),a.pinchIn&&(n=a.pinchIn.call(v,e,g||null,f||0,u||0,b,p));break;case ue:v.trigger("pinchOut",[g||null,f||0,u||0,b,p]),a.pinchOut&&(n=a.pinchOut.call(v,e,g||null,f||0,u||0,b,p))}}return o==ge?t!==ke&&t!==je||(clearTimeout(Q),K()&&!X()?(k=ne(),Q=setTimeout(ie.proxy(function(){k=null,v.trigger("tap",[e.target]),a.tap&&(n=a.tap.call(v,e,e.target))},this),a.doubleTapThreshold)):(k=null,v.trigger("tap",[e.target]),a.tap&&(n=a.tap.call(v,e,e.target)))):o==we?t!==ke&&t!==je||(clearTimeout(Q),k=null,v.trigger("doubletap",[e.target]),a.doubleTap&&(n=a.doubleTap.call(v,e,e.target))):o==ve&&(t!==ke&&t!==je||(clearTimeout(Q),k=null,v.trigger("longtap",[e.target]),a.longTap&&(n=a.longTap.call(v,e,e.target)))),n}function W(){var e=!0;return null!==a.threshold&&(e=s>=a.threshold),e}function $(){var e=!1;return null!==a.cancelThreshold&&null!==c&&(e=J(c)-s>=a.cancelThreshold),e}function P(){return!a.maxTimeThreshold||!(u>=a.maxTimeThreshold)}function z(){var e=F(),t=U(),o=null===a.pinchThreshold||f>=a.pinchThreshold;return e&&t&&o}function N(){return!!(a.pinchStatus||a.pinchIn||a.pinchOut)}function A(){var e=P(),t=W(),o=F(),n=U();return!$()&&n&&o&&t&&e}function R(){return!!(a.swipe||a.swipeStatus||a.swipeLeft||a.swipeRight||a.swipeUp||a.swipeDown)}function F(){return b===a.fingers||a.fingers===xe||!Qe}function U(){return 0!==x[0].end.x}function V(){return!!a.tap}function K(){return!!a.doubleTap}function X(){if(null==k)return!1;var e=ne();return K()&&e-k<=a.doubleTapThreshold}function Y(){j=T=0}function B(){var e=!1;T&&ne()-T<=a.fingerReleaseThreshold&&(e=!0);return e}function q(e){!0===e?(v.bind(n,S),v.bind(i,I),r&&v.bind(r,D)):(v.unbind(n,S,!1),v.unbind(i,I,!1),r&&v.unbind(r,D,!1)),v.data(Ce+"_intouch",!0===e)}function G(e,t){var o=void 0!==t.identifier?t.identifier:0;return x[e].identifier=o,x[e].start.x=x[e].end.x=t.pageX||t.clientX,x[e].start.y=x[e].end.y=t.pageY||t.clientY,x[e]}function Z(e){var t=function(e){for(var t=0;t<x.length;t++)if(x[t].identifier==e)return x[t]}(void 0!==e.identifier?e.identifier:0);return t.end.x=e.pageX||e.clientX,t.end.y=e.pageY||e.clientY,t}function J(e){if(w[e])return w[e].distance}function ee(e){return{direction:e,distance:0}}function te(){return y-_}function oe(e,t){var o=Math.abs(e.x-t.x),n=Math.abs(e.y-t.y);return Math.round(Math.sqrt(o*o+n*n))}function ne(){return(new Date).getTime()}this.enable=function(){return v.bind(o,C),v.bind(l,E),v},this.disable=function(){return H(),v},this.destroy=function(){return H(),v.data(Ce,null),v},this.option=function(e,t){if(void 0!==a[e]){if(void 0===t)return a[e];a[e]=t}else ie.error("Option "+e+" does not exist on jQuery.swipe.options");return null}}ie.fn.swipe=function(e){var t=ie(this),o=t.data(Ce);if(o&&"string"==typeof e){if(o[e])return o[e].apply(this,Array.prototype.slice.call(arguments,1));ie.error("Method "+e+" does not exist on jQuery.swipe")}else if(!(o||"object"!=typeof e&&e))return function(o){!o||void 0!==o.allowPageScroll||void 0===o.swipe&&void 0===o.swipeStatus||(o.allowPageScroll=he);void 0!==o.click&&void 0===o.tap&&(o.tap=o.click);o=o||{};return o=ie.extend({},ie.fn.swipe.defaults,o),this.each(function(){var e=ie(this),t=e.data(Ce);t||(t=new n(this,o),e.data(Ce,t))})}.apply(this,arguments);return t},ie.fn.swipe.defaults={fingers:1,threshold:75,cancelThreshold:null,pinchThreshold:20,maxTimeThreshold:null,fingerReleaseThreshold:250,longTapThreshold:500,doubleTapThreshold:200,swipe:null,swipeLeft:null,swipeRight:null,swipeUp:null,swipeDown:null,swipeStatus:null,pinchIn:null,pinchOut:null,pinchStatus:null,click:null,tap:null,doubleTap:null,longTap:null,triggerOnTouchEnd:!0,triggerOnTouchLeave:!1,allowPageScroll:"auto",fallbackToMouseEvents:!0,excludedElements:"label, button, input, select, textarea, a, .noSwipe"},ie.fn.swipe.phases={PHASE_START:ye,PHASE_MOVE:Te,PHASE_END:je,PHASE_CANCEL:ke},ie.fn.swipe.directions={LEFT:re,RIGHT:ae,UP:le,DOWN:se,IN:ce,OUT:ue},ie.fn.swipe.pageScroll={NONE:he,HORIZONTAL:me,VERTICAL:be,AUTO:de},ie.fn.swipe.fingers={ONE:1,TWO:2,THREE:3,ALL:xe}});var b2evo_colorbox_params={maxWidth:480<jQuery(window).width()?"95%":"100%",maxHeight:480<jQuery(window).height()?"90%":"100%",slideshow:!0,slideshowAuto:!1};function init_colorbox(e){if("object"==typeof e&&0!=e.length){var t=e.attr("rel").match(/lightbox\[([a-z]+)/i);switch(t=t?t[1]:""){case"p":e.colorbox(b2evo_colorbox_params_post);break;case"c":e.colorbox(b2evo_colorbox_params_cmnt);break;case"user":e.colorbox(b2evo_colorbox_params_user);break;default:e.colorbox(b2evo_colorbox_params)}}}jQuery(document).ready(function(){"undefined"!=typeof b2evo_colorbox_params_post&&"undefined"!=typeof b2evo_colorbox_params_cmnt&&"undefined"!=typeof b2evo_colorbox_params_user&&"undefined"!=typeof b2evo_colorbox_params_other&&(b2evo_colorbox_params_post=jQuery.extend({},b2evo_colorbox_params,b2evo_colorbox_params_post),b2evo_colorbox_params_cmnt=jQuery.extend({},b2evo_colorbox_params,b2evo_colorbox_params_cmnt),b2evo_colorbox_params_user=jQuery.extend({},b2evo_colorbox_params,b2evo_colorbox_params_user),b2evo_colorbox_params=jQuery.extend({},b2evo_colorbox_params,b2evo_colorbox_params_other),jQuery('a[rel^="lightbox"]').each(function(){init_colorbox(jQuery(this))}),jQuery("#colorbox").swipe({swipeLeft:function(e,t,o,n,i){"undefined"!=typeof colorbox_is_zoomed&&colorbox_is_zoomed||jQuery.colorbox.next()},swipeRight:function(e,t,o,n,i){"undefined"!=typeof colorbox_is_zoomed&&colorbox_is_zoomed||jQuery.colorbox.prev()}}),jQuery(document).on("click","#colorbox img.cboxPhoto",function(){jQuery(this).hasClass("zoomout")?jQuery("#colorbox").swipe("disable"):jQuery("#colorbox").swipe("enable")}))});
