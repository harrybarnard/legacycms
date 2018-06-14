/**
 * LEGACY CMS
 * @author Harry Barnard
 * @version 2.1
 * @copyright Copyright (c) 2010 Harry Barnard (http://harrybarnard.com)
 */

// Refresh comments pane and update tab
function showComments(ajax) {
	if (ajax == '1') {
		dijit.byId("commentsResponse").refresh();
		commentTab(tagType,slaveID);
	}
};

function deleteComment(id,ajax) {
	dojo.xhrGet({
		url: "/admin/comments/delete/id/" + id + "/ajax/" + ajax,
	    load: function(data){
		  dijit.byId("ajaxDialog").attr("content",data);
	      dijit.byId("ajaxDialog").attr("title","Comment Deleted");
	      dijit.byId('ajaxDialog').show();
	      showComments(ajax);
	    }
	});
};

function approveComment(id,status,ajax) {
	dojo.xhrGet({
		url: "/admin/comments/approve/id/" + id + "/status/" + status + "/ajax/" + ajax,
	    load: function(data){
		  dijit.byId("ajaxDialog").attr("content",data);
		  dijit.byId("ajaxDialog").attr("title","Comment Approval");
	      dijit.byId('ajaxDialog').show();
	      showComments(ajax);
	    }
	});
};

//Show details of current Article
function commentTab(type,slave) {
	dojo.xhrGet({
		url: "/admin/comments/tab?type=" + type + "&slave=" + slave,
	    load: function(data){
		dijit.byId("CommentsPane").controlButton.attr("label",data);
	}
	});
};

