/**
 * LEGACY CMS
 * @author Harry Barnard
 * @version 2.1
 * @copyright Copyright (c) 2010 Harry Barnard (http://harrybarnard.com)
 */

function sameCheck(dojoTxt1, dojoTxt2) {
	return dojoTxt1.getValue() == dojoTxt2.getValue();
}

function toggleCheckbox(box,id) {
	if(dijit.byId(box).checked) {
		dijit.byId(box).attr("checked",false);
		dojo.removeClass(id, "yBG");
	} else {
		dijit.byId(box).attr("checked",true);
		dojo.addClass(id, "yBG");
	}
} 
