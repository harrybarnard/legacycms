/**
 * LEGACY CMS
 * @author Harry Barnard
 * @version 2.1
 * @copyright Copyright (c) 2010 Harry Barnard (http://harrybarnard.com)
 */

//Show Date in events calendar
function calGo(month,year) {
	dijit.byId("calendarPane").setHref('/events/acalendar/month/' +  month + '/year/' + year + '/');
};