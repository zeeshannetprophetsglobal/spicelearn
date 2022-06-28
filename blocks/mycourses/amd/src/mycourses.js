 define(['jquery'], function ($) {   
        return {
            Init: function() {
    
                $(document).ready(function() {
                    
                    $('#dpt-filtter').change(function(){
                        var categoryid = $(this).val();
                        var status = $('#status-filtter').val();
                        var group = $('.filtter-box').attr('group');
                        $.ajax({
                            url: "ajax.php",
                            type: 'post',
                            data: {
                                'categoryid': categoryid,
                                'status':status,
                                'action':'category_filtter',
                                'group':group
                                
                                },
                            success: function(result) {

                                $('#darkheader').html(result);
                                console.log(result);
                               
                            }
                        });
                    });

                    $('#status-filtter').change(function(){
                        var categoryid = $('#dpt-filtter').val();
                        var status = $(this).val();
                        var group = $('.filtter-box').attr('group');
                        $.ajax({
                            url: "ajax.php",
                            type: 'post',
                            data: {
                                'categoryid': categoryid,
                                'status':status,
                                'action':'category_filtter',
                                'group':group
                                
                                },
                            success: function(result) {

                                $('#darkheader').html(result);
                                console.log(result);
                               
                            }
                        });
                    });

                    $('#coursename').keyup(function(){
                        var categoryid = $('#dpt-filtter').val();
                        var status = $('#status-filtter').val();
                        var group = $('.filtter-box').attr('group');
                        var coursename = $(this).val();
                        $.ajax({
                            url: "ajax.php",
                            type: 'post',
                            data: {
                                'categoryid': categoryid,
                                'status':status,
                                'action':'category_filtter',
                                'group':group,
                                'coursename':coursename
                                
                                },
                            success: function(result) {

                                $('#darkheader').html(result);
                                console.log(result);
                               
                            }
                        });
                    });
                   
                });
    
            }
        }
    });