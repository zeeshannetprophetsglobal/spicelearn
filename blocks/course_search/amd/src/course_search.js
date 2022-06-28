 define(['jquery'], function ($) {   
    return {
        Init: function() {

            $(document).ready(function() {

                $('#coursename_search').keyup(function(){
                    var coursename = $(this).val();
                    $.ajax({
                        url: "../blocks/course_search/ajax.php",
                        type: 'post',
                        data: {
                            'coursename':coursename                                
                        },
                        success: function(result) {
                            $('#enrolled_course_box').html(result);
                            console.log(result);
                        }
                    });
                });

            });

        }
    }
});