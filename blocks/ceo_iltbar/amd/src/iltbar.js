define(['jquery'], function($) {
    return {
        Init: function() {

            $(document).ready(function() {

                $('#department').change(function(){ 
                    var departmentVal = $(this).val();
                    let url = '';
                    url = 'view.php?dep_id='+departmentVal;
                    window.location = url;
                    
                });

                $('#department_piechart').change(function(){ 
                    
                    dep_id = $(this).val();
                    $.ajax({
                        url: "ajax.php",
                        type: 'post',
                        data: {
                            'action': 'getTrainingTypes',
                            'id': dep_id
                        },
                        success: function(jsonData) { 
                            if (jsonData) {
                              trainingData = JSON.parse(jsonData);
                              let html = '';
                              $.each(trainingData, function(index, element) {
                                 html += '<option value="' + element.id + '">'+ element.name +'</option>';
                              });     
                              $('#trainingType').html(html)
                            }
                        }
                    });
                });

                $('#filterButton').click(function(){
                    let training_id  = $('#trainingType').val();
                    // let start_date  = $('#start_date').val();
                    // let end_date  = $('#end_date').val();

                    url = 'view2.php?training_id='+training_id;
                    window.location = url;
                });

                $("#id_ceo_iltbar_submit,#id_ceo_iltbar_cancel").unbind().click(function(){
                     var currenurl = $(this).closest("form").attr("action");
                     var formid = $(this).closest("form").attr("id");
                     var updateurl = currenurl+'#id_ceo_iltbar_form';
                     $(this).closest("form").attr('action', updateurl).submit();
                    console.log(formid+updateurl);
                });

                window.onbeforeunload = () => {}
                $("#id_department").change(function() {
                     this.form.submit();
                });


                $("#id_ceo_iltpie_submit,#id_ceo_iltpie_cancel").unbind().click(function(){
                     var currenurl = $(this).closest("form").attr("action");
                     var formid = $(this).closest("form").attr("id");
                     var updateurl = currenurl+'#id_ceo_iltpie_form';
                     $(this).closest("form").attr('action', updateurl).submit();
                    console.log(formid+updateurl);
                });

                 $('#id_department_piechart').change(function(e){
                   console.log('getsubject called');
                    dep_id = $('#id_department_piechart').val();
                    $.ajax({
                        url: "../blocks/ceo_iltbar/ajax.php",
                        type: 'post',
                        data: {
                            'action': 'getTrainingTypes',
                            'id': dep_id
                        },
                        success: function(jsonData) { 
                            if (jsonData) {
                              trainingData = JSON.parse(jsonData);
                              let html = '<option selected value="">All</option>';
                              $.each(trainingData, function(index, element) {
                               html += '<option value="' + element.id + '">'+ element.name +'</option>';
                           });     
                              $('#id_trainingType').html(html)
                          }
                      }
                  });
                }); 
            });
        }
    }
});