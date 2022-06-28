define(['jquery'], function($) {
	return {
		Init: function() {
			$(document).ready(function() {
				console.log('scheduler js loading');
				$('#id_department').change(function(e){
					var category = $('#id_department').val();	
					console.log('working');
					$.ajax({
						type: 'post',
						url: '../scheduler/ajax.php',
						data: {action:'pie_chart',category:category},
						success: function (data) {
							$('#id_subject').html(data);
						}
					});
				}); 
			});
		}       
	}
});