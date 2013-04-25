$(function() {
		$( "#datePicker" ).datepicker({
											dateFormat: "dd. MM yy",
											monthNames: [ "Januar", "Februar", "März", "April", "Mai", "Juni", "Juli", "August", "September", "Oktober", "November", "Dezember" ],
											dayNamesMin: [ "So", "Mo", "Di", "Mi", "Do", "Fr", "Sa" ],
											firstDay: 1,
											altField: "#date",
											altFormat: "yy-mm-dd"
										});
	});