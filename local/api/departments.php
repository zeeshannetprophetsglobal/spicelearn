<?php
require_once('../../config.php');
$alldepts = $DB->get_records('cohort');
$allcohort = array();
foreach($alldepts as $dept)
 {
     $allcohort[] = array("dept_id"=>$dept->id,"dept_name"=>$dept->name);	 
}
$data = [];
$data['All_Departments'] = $allcohort;
echo json_encode($data);
?>