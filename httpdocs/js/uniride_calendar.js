$(document).ready(function() {
		
		$('#calendar').fullCalendar({
			editable: true,
			events: "calendar/getcalendarentries"
		});
		
	});