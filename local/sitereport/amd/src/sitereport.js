define(['jquery'], function($) {
    return {
        Init: function() {

            $(document).ready(function() {

              
               
                $('#id_type').change(function(){

                    var type = $(this).val();
                    var dpt_id = $('#id_dpt').val();
                    $.ajax({
                        url: "ajax.php",
                        type: 'post',
                        data: {
                            'action': 'option',
                            'type': type,
                            'dpt_id':dpt_id 
                        },
                        success: function(result) {
                            $('#id_subject').html('');
                            if (result) {
                              // console.log(result);
                              $('#id_subject').html(result);
                            }
                        }
                    });
                });
          

           
            });
        }
        
    }
});