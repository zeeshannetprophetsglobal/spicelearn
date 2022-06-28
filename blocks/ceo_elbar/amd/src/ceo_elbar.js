define(['jquery'], function($) {
	return {
		Init: function() {

			$(document).ready(function() {
				console.log('ceo elbar js loading');
					
				window.onbeforeunload = () => {}
				$("#id_category").change(function() {
					console.log('change working');
				     this.form.submit();
				});

				$("#id_elbar_submitbutton,#id_elbar_cancel").unbind().click(function(){
					 var currenurl = $(this).closest("form").attr("action");
					 var formid = $(this).closest("form").attr("id");
					 var updateurl = currenurl+'#ceo_elbar_form';
					 $(this).closest("form").attr('action', updateurl).submit();
					console.log(formid+updateurl);
				});


				$('#id_category_pie').change(function(e){
					var category = $('#id_category_pie').val();	
					console.log('working');
					$.ajax({
						type: 'post',
						url: '../blocks/ceo_elbar/ajax.php',
						data: {action:'pie_chart',category:category},
						success: function (data) {
							$('#id_subject').html(data);
						}
					});
				}); 

				$("#id_ceo_elpiechart_submit,#id_ceo_elpiechart_cancel").unbind().click(function(){
					 var currenurl = $(this).closest("form").attr("action");
					 var formid = $(this).closest("form").attr("id");
					 var updateurl = currenurl+'#ceo_elpiechart_form';
					 $(this).closest("form").attr('action', updateurl).submit();
					console.log(formid+updateurl);
				});
			});
		}       
	}
});