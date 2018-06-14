/**
 * LEGACY CMS
 * @author Harry Barnard
 * @version 2.1
 * @copyright Copyright (c) 2010 Harry Barnard (http://harrybarnard.com)
 */

google.load("swfobject", "2.1");

function _run() {
	var videoID = vidID;
    var params = { allowScriptAccess: "always" , allowfullscreen: "true"};
    var atts = { id: "ytPlayer" };
    swfobject.embedSWF("http://www.youtube.com/v/" + videoID + "&enablejsapi=2&rel=0&fs=1&showinfo=0&playerapiid=player1",
    	"videoDiv", "608", "369", "8", null, null, params, atts);
}
    
google.setOnLoadCallback(_run);
      
//Load page in comments pane
function videosGo(url) {
	dijit.byId("videosPane").setHref(url);
};
