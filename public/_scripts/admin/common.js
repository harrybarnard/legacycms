/**
 * LEGACY CMS
 * @author Harry Barnard
 * @version 2.1
 * @copyright Copyright (c) 2010 Harry Barnard (http://harrybarnard.com)
 */

//Toggle Item Visibility
function toggleVis(id,flagit) {
	if (flagit=="1"){
		if (document.layers) document.layers[''+id+''].visibility = "show"
		else if (document.all) document.all[''+id+''].style.visibility = "visible"
		else if (document.getElementById) document.getElementById(''+id+'').style.visibility = "visible"
	}
else
	if (flagit=="0"){
		if (document.layers) document.layers[''+id+''].visibility = "hide"
		else if (document.all) document.all[''+id+''].style.visibility = "hidden"
		else if (document.getElementById) document.getElementById(''+id+'').style.visibility = "hidden"
	}
}

//Toggle Item Display
function toggleDisp(id,flagit) {
	if (flagit=="1"){
		if (document.layers) document.layers[''+id+''].display = "inline"
		else if (document.all) document.all[''+id+''].style.display = "inline"
		else if (document.getElementById) document.getElementById(''+id+'').style.display = "inline"
	}
else
	if (flagit=="0"){
		if (document.layers) document.layers[''+id+''].display = "none"
		else if (document.all) document.all[''+id+''].style.display = "none"
		else if (document.getElementById) document.getElementById(''+id+'').style.display = "none"
	}
}

//Open Asset Store Manager Window
var assetsWindow;

function openAssetMan(type,method,field) {
    var width = 950;
    var height = 650;
    var left = parseInt((screen.availWidth/2) - (width/2));
    var top = parseInt((screen.availHeight/2) - (height/2));
    var windowFeatures = "width=" + width + ",height=" + height + ",left=" + left + ",top=" + top + "screenX=" + left + ",screenY=" + top;
    if(type == "n") {
    	assetsWindow = window.open('/admin/assets/index/method/' + method + '/field/' + field, "assetStore", windowFeatures);
    } else {
    	assetsWindow = window.open('/admin/assets/index/type/' + type + '/method/' + method + '/field/' + field, "assetStore", windowFeatures);
    }
}

function SetAsset(field,asset,file) {
	var filefield = field + 'File';
	var responsefield = field + 'Response';
	var img = "<img src=\"\/admin\/assets\/thumb\/" + asset + "\/180\/180\/type\/resize\" border=\"0\"\/>";
	document.getElementById(field).value = asset;
	dijit.byId(filefield).attr("value",file);
	if (document.getElementById(responsefield)) {
		dijit.byId(responsefield).attr("content",img);
	}
}

//FCK Editor Stuff
function MyFCKClass()
{
	this.UpdateEditorFormValue = function()
    {
		for ( i = 0; i < parent.frames.length; ++i )
        if ( parent.frames[i].FCK )
        parent.frames[i].FCK.UpdateLinkedField();
    };
}
var MyFCKObject = new MyFCKClass();

//Get URL
function goTo(url) {
	document.location.href=url;
}

// Define AJAX Loading Message
var lmessage = "<div style=\"text-align:center; padding:8px;height:100%\"><img src=\"/_styles/admin/images/ajax_loader.gif\" style=\"vertical-align: middle\"/></div>";

//Post Item With Server Side Validation
function postDialog(url,form,title,refresh) {
	dijit.byId("ajaxDialog").attr("content",lmessage);
	dojo.xhrPost({
		url: url,
	    load: function(response, ioArgs){
	      dijit.byId("ajaxDialog").attr("content",response);
	      dijit.byId("ajaxDialog").attr("title",title);
	      if(refresh == '1') {
	    	  dijit.byId("detailsResponse").refresh();
	      }
	      dijit.byId("ajaxDialog").show();
	      
	      return response;
	},
	error: function(response, ioArgs){
		dijit.byId("ajaxDialog").attr("content","<p class=\"Spacer\"><\/p><div class=\"cErr\">An error occurred, with response: " + response + "<\/div>");
	    return response;
	},
	    
	form:form
	});
}

//Post Dialog With Forced Client Validation
function doDialog(url,form,title) {
	var thisForm = dijit.byId(form);
	if (thisForm.isValid()) {
		dojo.xhrPost({
			url: url,
			load: function(response, ioArgs){
				dijit.byId("ajaxDialog").attr("content",response);
				dijit.byId("ajaxDialog").attr("title",title);
				dijit.byId("ajaxDialog").show();
	      
				return response;
		},
		error: function(response, ioArgs){
			dijit.byId("ajaxResponse").attr("content","<p class=\"Spacer\"><\/p><div class=\"cErr\">An error occurred, with response: " + response + "<\/div>");
			return response;
		},
	    
		form:form
		});
	}
}

//Open Dialog Without Post
function getDialog(url,title,refresh) {
	dijit.byId("ajaxDialog").setHref(url);
	dijit.byId("ajaxDialog").attr("title",title);
	//If pane should be refreshed
	if(typeof(refresh) != 'undefined') {
		dijit.byId(refresh).refresh();
	}
	dijit.byId("ajaxDialog")._position(); 
    dijit.byId("ajaxDialog").show();
}

//Toggle Specified Checkbox & Background
function toggleCheckbox(box,id) {
	if(dijit.byId(box).checked) {
		dijit.byId(box).attr("checked",false);
		dojo.removeClass(id, "yBG");
	} else {
		dijit.byId(box).attr("checked",true);
		dojo.addClass(id, "yBG");
	}
}

var hideLoader = function(){
    dojo.fadeOut({
            node:"preloader",
            onEnd: function(){
                    dojo.style("preloader", "display", "none");
            }
    }).play();
};

dojo.addOnLoad(function(){
    dojo.parser.parse();
    hideLoader();
});