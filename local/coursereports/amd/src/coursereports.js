 define(['jquery'], function($) {
    return {
        Init: function() {
            
            $(document).ready(function(){

               
			   $('#course-filter').change(function(){
							
						courseid = $(this).val();
					$.ajax({
                            url: "ajax.php",
                            type: 'post',
                            data: {
                                'action':'coursereport',
                                'courseid': courseid,
                                'cohort': '',
                                
                            },
                            success: function(result) {

                               if(result){
                                 $('.corsereports-table').html(result)  
									if(courseid){
										$('.pagination').hide();
									}else{
											$('.pagination').show();
									}
							  }
							   
                            }
                        });
						
					});

                    $('#submit-leaderboard-filter').click(function(){
						$('.leaderboard-table').hide();
                        $('.loader').show();
                        var departmentid = $('#leaderboard-department-filter').val();
                        var name = $('#leaderboard-name').val();
                        var email = $('#leaderboard-email').val();
						
					$.ajax({
                            url: "ajax.php",
                            type: 'post',
                            data: {
                                'action':'leaderboard',
                                'name': name,
                                'cohort': departmentid,
                                'email':email
                                
                            },
                            success: function(result) {

                                $('.loader').hide();
                                $('.leaderboard-table').show(); 

                               if(result){
                                 $('.leaderboard-table').html(result);
							  }
							   
                            }
                        });
						
					});

                    $('#leaderboard-department-filter').change(function(){
						$('.leaderboard-table').hide();
                        $('.loader').show();
						departid = $(this).val();
                        var name = $('#leaderboard-name').val();
                        var email = $('#leaderboard-email').val();
					$.ajax({
                            url: "ajax.php",
                            type: 'post',
                            data: {
                                'action':'leaderboard',
                                'cohort': departid,
                                'email':email,
                                'name': name

                                
                                
                            },
                            success: function(result) {
                                $('.loader').hide();
                                $('.leaderboard-table').show(); 
                               if(result){
                                 $('.leaderboard-table').html(result)  
									
							  }
                             
							   
                            }
                        });
						
					});



                    $('#userwisereport-department-filter').change(function(){
						$('.userwisereport-table').html('');
                        $('.loader').show();
						departid = $(this).val();
                        var name = $('#userwise-name').val();
                      //  var email = $('#leaderboard-email').val();
					$.ajax({
                            url: "ajax.php",
                            type: 'post',
                            data: {
                                'action':'userwisereport',
                                'cohort': departid,
                               // 'email':email,
                                'name': name

                                
                                
                            },
                            success: function(result) {
                                $('.loader').hide();
                                
                               if(result){
                                 $('.userwisereport-table').html(result)  
									
							  }
                             
							   
                            }
                        });
						
					});


                    $('#submit-userwise-filter').click(function(){
						$('.userwisereport-table').html('');
                        $('.loader').show();
						departid = $('#userwisereport-department-filter').val();
                        var name = $('#userwise-name').val();
                      //  var email = $('#leaderboard-email').val();
					$.ajax({
                            url: "ajax.php",
                            type: 'post',
                            data: {
                                'action':'userwisereport',
                                'cohort': departid,
                               // 'email':email,
                                'name': name

                                
                                
                            },
                            success: function(result) {
                                $('.loader').hide();
                                
                               if(result){
                                 $('.userwisereport-table').html(result)  
									
							  }
                             
							   
                            }
                        });
						
					});
                   
            });

            
        }
    }
});