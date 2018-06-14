/**
 * LEGACY CMS
 * @author Harry Barnard
 * @version 2.1
 * @copyright Copyright (c) 2010 Harry Barnard (http://harrybarnard.com)
 */

//Refresh Tags Pane
function showAttachments() {
	dijit.byId("attachmentsResponse").refresh();
	attachmentTab(tagType,slaveID);
};

//Add A New Tag
function newAttachment() { 
	if (attachmentForm.isValid()) {
		dojo.xhrPost({
			url: "/admin/attachments/new",
			load: function(response, ioArgs){
				dijit.byId("ajaxDialog").attr("content",response);
				showAttachments();
				dijit.byId("ajaxDialog").attr("title","Add Attachment");
				dijit.byId('ajaxDialog').show();
	      
				return response;
	    	},
	    	error: function(response, ioArgs){
	    		dijit.byId("attachmentsResponse").setContent('<p class=\"Spacer\"><\/p><div class=\"cErr\">An error occurred, with response: ' + response + '<\/div>');
	    		return response;
	    	},
	    
	    	form:"attachmentForm"
		});
	}
}

// Delete Tag
function deleteAttachment(id) {
	dijit.byId("ajaxDialog").setHref('/admin/attachments/delete?id=' + id);
	dijit.byId("ajaxDialog").attr("title","Remove Attachment");
	dijit.byId('ajaxDialog').show();
	showAttachments();
};

// Populate Tag Tab
function attachmentTab(type,slave) {
	dojo.xhrGet({
		url: "/admin/attachments/tab?type=" + type + "&slave=" + slave,
	    load: function(data){
		dijit.byId("AttachmentsPane").controlButton.attr("label",data);
	}});
};

dojo.addOnLoad(function(){
	attachmentTab(tagType,slaveID);
});