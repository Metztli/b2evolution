jQuery(document).ready(function() {

  // cookie period
  var days = 365;
 // var zin = zIndex;
  //var thiss = $("div[id*='tqitem']");
 $(".pagenumbers").draggable({cursor: "move"});
  // load positions and z-index from cookies
 $("div[id*='tqitem']").each( function(){

    $(this).css( "left", 
      $.cookie( "im_" + this.id + "_left") );
    $(this).css( "top", 
      $.cookie( "im_" + this.id + "_top") );
    $(this).css("z-index", 
	  $.cookie( "tqz_" + this.id + "_zIndex") );
  }

  );
  

   // save positions into cookies
  function savePos( event, ui ){
    $.cookie("im_" + this.id + "_left", 
      $(this).css("left"), { path: '/', expires: days });
    $.cookie("im_" + this.id + "_top", 
      $(this).css("top"), { path: '/', expires: days });
//	 $.cookie("im_" + this.id + "_zIndex", 
  //   $(this).css("zIndex"), { path: '/', expires: days });
  };



function savePot(){
      $("div[id*='tqitem']").each(function () {
          $.cookie("tqz_" + $(this).attr("id") + "_zIndex", 
		$(this).css("zIndex"), { path: '/', expires: days });
      })
};
  //  bind event

  $("div[id*='tqitem']").bind('dragstop', savePos);
  $("div[id*='tqitem']").bind('dragstop', savePot);

 


});