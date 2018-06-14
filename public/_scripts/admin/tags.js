/**
 * LEGACY CMS
 * @author Harry Barnard
 * @version 2.1
 * @copyright Copyright (c) 2010 Harry Barnard (http://harrybarnard.com)
 */

//Refresh Tags Pane
function showTags() {
	dijit.byId("tagsResponse").refresh();
	tagTab(tagType,slaveID);
};

//Add A New Tag
function newTag() { 
	if (tagForm.isValid()) {
		dojo.xhrPost({
			url: "/admin/tags/new",
			load: function(response, ioArgs){
				dijit.byId("ajaxDialog").attr("content",response);
				showTags();
				dijit.byId("ajaxDialog").attr("title","Add Tag");
				dijit.byId('ajaxDialog').show();
	      
				return response;
	    	},
	    	error: function(response, ioArgs){
	    		dijit.byId("tagResponse").setContent('<p class=\"Spacer\"><\/p><div class=\"cErr\">An error occurred, with response: ' + response + '<\/div>');
	    		return response;
	    	},
	    
	    	form:"tagForm"
		});
	}
}

// Delete Tag
function deleteTag(id) {
	dijit.byId("ajaxDialog").setHref('/admin/tags/delete?id=' + id);
	dijit.byId("ajaxDialog").attr("title","Remove Tag");
	dijit.byId('ajaxDialog').show();
	showTags();
};

// Populate Tag Tab
function tagTab(type,slave) {
	dojo.xhrGet({
		url: "/admin/tags/tab?type=" + type + "&slave=" + slave,
	    load: function(data){
		dijit.byId("TagsPane").controlButton.attr("label",data);
	}});
};

dojo.addOnLoad(function(){
	tagTab(tagType,slaveID);
});