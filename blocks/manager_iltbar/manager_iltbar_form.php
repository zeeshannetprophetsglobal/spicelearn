<?php 
require_once("$CFG->libdir/formslib.php");
// require_once($CFG->dirroot.'/cohort/lib.php');

class manager_iltbar_form extends moodleform {
    
    function definition() {
        global $DB;
        $mform =& $this->_form;
        // $mform->addElement('header','manager_ilt_form_old', "Filter");
        // $mform->setExpanded('manager_ilt_form_old', false);

        $opts = array(
            'startyear' => 2000,
            'stopyear' => date("Y"),
            'timezone'  => 99,
            'optional'  => false,
        );
        $mform->addElement('date_selector', 'fromdate', "From Date", $opts);
        $defaulttime = strtotime( "-1 month", time());
        $mform->setDefault('fromdate',  $defaulttime);
        $mform->addElement('date_selector', 'todate', "To Date", array('optional' => false));

        $buttonarray=array();
        $buttonarray[] =& $mform->createElement('submit', 'manager_ilt_submit', "Search");
        $buttonarray[] =& $mform->createElement('submit', 'manager_ilt_cancel', 'Reset');
        $mform->addGroup($buttonarray, 'buttonar', '', array(' '), false);

    }

    
    function reset() {
        $this->_form->updateSubmission(null, null);
    } 

    function validation($data, $files) {
        $errors = parent::validation($data, $files);
        if ($data['fromdate'] > time()) {
            $errors['fromdate'] = get_string('startdterror', 'block_ceo_iltbar');
        }
       if ($data['todate'] < $data['fromdate']) {
            $errors['todate'] = get_string('todatedterror', 'block_ceo_iltbar');
        }
        return $errors;
    }
}


class manager_iltpie1_form extends moodleform {
    
    function definition() {
        global $DB;
        $mform =& $this->_form;
        // $mform->addElement('header','manager_iltpie_form_old', "Filter");
        // $mform->setExpanded('manager_iltpie_form_old', false);

        $categories = $this->getCategories();
        $selectArray = array(0=>'All');
        foreach ($categories as $key => $value) {
            $selectArray[$value->id] =  $this->get_category_parent_name($value->id);
        }

        $mform->addElement('select', 'ilt_category',"Training Type",$selectArray);
        $mform->setDefault('select', 'All');  

        $opts = array(
            'startyear' => 2000,
            'stopyear' => date("Y"),
            'timezone'  => 99,
            'optional'  => false,
        );
        $mform->addElement('date_selector', 'fromdate', "From Date", $opts);
        $defaulttime = strtotime( "-31 days", time());
        $mform->setDefault('fromdate',  $defaulttime);
        $mform->addElement('date_selector', 'todate', "To Date",  $opts);

        $buttonarray=array();
        $buttonarray[] =& $mform->createElement('submit', 'manager_iltpie_submit', "Search");
        $buttonarray[] =& $mform->createElement('submit', 'manager_iltpie_cancel', 'Reset');
        $mform->addGroup($buttonarray, 'buttonar', '', array(' '), false);

    }
    function getCategories() {
            global $DB,$USER;
        
        $cohort =  cohort_get_user_cohorts($USER->id);
        $cohortdata = array_values($cohort);
        $trainingType = array();
        if ($cohortdata) {
            $parent_cate = $DB->get_record_sql("SELECT id FROM  {course_categories} WHERE name LIKE '%".$cohortdata[0]->name."%'");
            $sql = 'SELECT DISTINCT cc.* FROM {course_categories} as cc JOIN {course} as c on c.category=cc.id WHERE c.visible=1 AND cc.path LIKE "/'.$parent_cate->id.'/%" AND cc.idnumber LIKE "ILT%"';
            $trainingType = $DB->get_records_sql($sql,null);
        }
        return $trainingType;
    }

    function reset() {
        $this->_form->updateSubmission(null, null);
    }

     function validation($data, $files) {
        $errors = parent::validation($data, $files);
        if ($data['fromdate'] > time()) {
            $errors['fromdate'] = get_string('startdterror', 'block_ceo_iltbar');
        }
        if ($data['todate'] < $data['fromdate']) {
            $errors['todate'] = get_string('todatedterror', 'block_ceo_iltbar');
        }
        return $errors;
    }

    function get_category_parent_name($category){
    
        global $DB;
        $sql = 'SELECT * FROM  {course_categories} WHERE id = "'.$category.'"';
        $categoryData = $DB->get_records_sql($sql,null);
        $name = $categoryData[$category]->name;
        if($categoryData[$category]->depth < 4){
            return $name;
        }else{
            return $name = $this->get_category_parent_name($categoryData[$category]->parent) . '/'.$name;
        }
    }
}

?>