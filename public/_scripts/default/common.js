/**
 * LEGACY CMS
 * @author Harry Barnard
 * @version 2.1
 * @copyright Copyright (c) 2010 Harry Barnard (http://harrybarnard.com)
 */

function goTo(url) {
	window.location = url;
};

// JavaScript CSS Switch
var jsoffList = dojo.query(".js-off");
for(var i = 0; i < jsoffList.length; i++ ){
	jsoffList[i].style.display='none';
};

function SimpleSwap(el,which){
	el.src=el.getAttribute(which || "origsrc");
};

function SimpleSwapSetup(){
	var x = document.getElementsByTagName("img");
	for (var i=0;i<x.length;i++){
		var oversrc = x[i].getAttribute("oversrc");
	    if (!oversrc) continue;
	    x[i].oversrc_img = new Image();
	    x[i].oversrc_img.src=oversrc;
	    x[i].onmouseover = new Function("SimpleSwap(this,'oversrc');");
	    x[i].onmouseout = new Function("SimpleSwap(this);");
	    x[i].setAttribute("origsrc",x[i].src);
	}
};
var PreSimpleSwapOnload =(window.onload)? window.onload : function(){};
dojo.addOnLoad(
    function(){
	    PreSimpleSwapOnload(); 
	    SimpleSwapSetup();
});

//Load page in comments pane
function commentsGo(url) {
	dijit.byId("commentsPane").setHref(url);
};
