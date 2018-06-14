/**
 * LEGACY CMS
 * @author Harry Barnard
 * @version 2.1
 * @copyright Copyright (c) 2010 Harry Barnard (http://harrybarnard.com)
 */

// Show directory in main pane
function exploreFolder(type,folder) {
	dijit.byId("exploreResponse").setHref('/admin/assets/explore?type=' + type + '&folder=' + folder);
};

// Show directory in main pane
function exploreRefresh() {
	dijit.byId("exploreResponse").refresh();
};

// Lightbox
function imageLightbox(title,url,type,width,height) {
	Mediabox.open(url, title, width + ' ' + height);
}

function insertFile(fileUrl)
{
	window.opener.SetUrl(fileUrl);
	window.close();
}

function chooseFile(field,asset,file)
{
	window.opener.SetAsset(field,asset,file);
	window.close();
}

function imagePreview(key) {
	var type = dijit.byId("type").attr("value");
	var width = dijit.byId("width").attr("value");
	var height = dijit.byId("height").attr("value");
	var image = "/admin\/assets\/imagepreview\/key\/" + key + "\/width\/" + width + "\/height\/" + height + "\/type\/" + type + "\/";
	dijit.byId("imagePreview").setHref(image);
}

function imageInsert(key) {
	var type = dijit.byId("type").attr("value");
	var width = dijit.byId("width").attr("value");
	var height = dijit.byId("height").attr("value");
	var url = "\/assets\/thumb\/" + key + "\/" + width + "\/" + height + "\/type\/" + type +"\/";
	insertFile(url);
}

function linkInsert(key) {
	var thisForm = dijit.byId(insertlinkForm);
	var method = thisForm.attr('value').method;
	var url = "\/assets\/" + method + "\/" + key + "\/";
	insertFile(url);
}