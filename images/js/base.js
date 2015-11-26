(function(){
	$('body').delegate('#logoutlink', 'click', function(){
		$.post('/user/rest/login/' + $('#globaldata').data('uid'), {'method':'DELETE'}, function(data, status){
			if (data.errno) {
				alert(data.error);
			} else {
				window.location.reload();
			}
		}, 'json');
	});
	$('body').delegate('#loginlogodiv', 'mouseenter', function(){
		$('#settingmenu').show();
	});
	$('body').delegate('#loginlogodiv', 'mouseleave', function(){
		$('#settingmenu').hide();
	});
	$.post('/user/rest/agent/',
		{'method':'PUT', 'update': {'screen': {'width':window.screen.width, 'height':window.screen.height}}},
		function(data, status){
	}, 'json');
})();
