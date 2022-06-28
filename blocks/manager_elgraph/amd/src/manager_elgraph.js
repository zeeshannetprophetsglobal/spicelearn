define(['jquery'], function($) {
	return {
		Init: function() {

			$(document).ready(function() {
				
				console.log('manager elgraph js loading');
				window.onbeforeunload = () => {}
				$("#id_category").change(function() {
					console.log('change working');
				     this.form.submit();
				});

				$("#id_manager_elgraph_submit,#id_manager_elgraph_cancel").unbind().click(function(){
					 var currenurl = $(this).closest("form").attr("action");
					 var formid = $(this).closest("form").attr("id");
					 var updateurl = currenurl+'#id_manager_elgraph_form';
					 $(this).closest("form").attr('action', updateurl).submit();
					console.log(formid+updateurl);
				});

				$("#id_manager_elpie_submit,#id_manager_elpie_cancel").unbind().click(function(){
					 var currenurl = $(this).closest("form").attr("action");
					 var formid = $(this).closest("form").attr("id");
					 var updateurl = currenurl+'#id_manager_elpie_filter';
					 $(this).closest("form").attr('action', updateurl).submit();
					console.log(formid+updateurl);
				});

			});
		}       
	}
});