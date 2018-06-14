/**
 * LEGACY CMS
 * @author Harry Barnard
 * @version 2.1
 * @copyright Copyright (c) 2010 Harry Barnard (http://harrybarnard.com)
 */

function sameCheck(dojoTxt1, dojoTxt2) {
	return dojoTxt1.getValue() == dojoTxt2.getValue();
}

function countryCheck(country){
	if (country == "United Kingdom") {
		dijit.byId("postcode").attr("required",true);
	} else {
	    dijit.byId("postcode").attr("required",false);
	}
}

function aliasCheck(alias) {
	dijit.byId("alias").validate(false);
	dojo.xhrPost({
		url: "/settings/aliascheck",
	    load: function(response, ioArgs){
			dijit.byId("aliasResponse").attr("content",response);
	},
	error: function(response, ioArgs){
		dijit.byId("ajaxResponse").attr("content","<p class=\"Spacer\"><\/p><div class=\"cErr\">An error occurred, with response: " + response + "<\/div>");
	    return response;
	},
	    
	content: {'alias': alias},
    handleAs: "text"
	});
};

function passwordCheck(password) {
	dojo.xhrPost({
		url: "/settings/passwordcheck",
	    load: function(response, ioArgs){
			dijit.byId("passwordResponse").attr("content",response);
	},
	error: function(response, ioArgs){
		dijit.byId("ajaxResponse").attr("content","<p class=\"Spacer\"><\/p><div class=\"cErr\">An error occurred, with response: " + response + "<\/div>");
	    return response;
	},
	    
	content: {'password': password},
    handleAs: "text"
	});
};

function toggleCheckbox(box,id) {
	if(dijit.byId(box).checked) {
		dijit.byId(box).attr("checked",false);
		dojo.removeClass(id, "yBG");
	} else {
		dijit.byId(box).attr("checked",true);
		dojo.addClass(id, "yBG");
	}
} 
