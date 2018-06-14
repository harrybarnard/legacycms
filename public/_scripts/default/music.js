/**
 * LEGACY CMS
 * @author Harry Barnard
 * @version 2.1
 * @copyright Copyright (c) 2010 Harry Barnard (http://harrybarnard.com)
 */

var wimpyWindow;
var winOpen=0;
function wimpyPopPlayer(wimpyPopPage,theWidth,theHeight) {
   wimpyWindow = window.open(wimpyPopPage,'wimpyMP3player','width='+theWidth+',height='+theHeight);
   winOpen=1;
}
function wimpyIsOpen(){
   if (winOpen==1){
     if (wimpyWindow.closed){
       return false;
     } else {
       return true;
     }
   } else {
     return false;
   }
}
function wimpyPlaylist(theplaylist) {
	wimpyWindow.wimpy_loadExternalPlaylist(thePlaylist);
}
function wimpyPlaylistTrack(theTrack) {
	wimpyWindow.wimpy_gotoTrack(theTrack);
}
function wimpyPopAndPlay(startOnLoad, theTrack, thePlaylist){
   if(wimpyIsOpen()){
	 wimpyPlaylist(thePlaylist);
     wimpyPlaylistTrack(theTrack);
   } else {
	 wimpyPopPlayer('/music/player/','485','180');
	 wimpyPlaylist(thePlaylist);
	 wimpyPlaylistTrack(theTrack);
   }
}

function openMusicPlayer(url,id,width,height) {
    var left   = (screen.width  - width)/2;
    var top    = (screen.height - height)/2;
    var params = 'width='+width+', height='+height;
    params += ', top='+top+', left='+left;
    params += ', directories=no';
    params += ', location=no';
    params += ', menubar=no';
    params += ', resizable=no';
    params += ', scrollbars=no';
    params += ', status=no';
    params += ', toolbar=no';
    wimpyWindow = window.open(url,id, params);
    if (window.focus) {wimpyWindow.focus()}
    return false;
}