function DpDevbar() {
	$('#dp_devbar').on('click', function(ev) {
		ev.stopPropagation();
	});
	$('#dp_devbar .reload-active-tab').on('click', function(ev) {
		ev.preventDefault();
		DeskPRO_Window.reloadSelectedTab();
	});
};

var devbar = new DpDevbar();
