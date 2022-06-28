<?php 
require_once("$CFG->libdir/formslib.php");

class date_filter extends moodleform {
    function definition() {
        global $DB;
        $mform =& $this->_form;
        // $mform->addElement('header','ceo_iltbar_form_old', "Filter"); 
        // $mform->setExpanded('ceo_iltbar_form_old', false);

        $opts = array(
            'startyear' => 2000,
            'stopyear' => date("Y"),
            'timezone'  => 99,
            'optional'  => false,
        );
        $mform->addElement('date_selector', 'fromdate',get_string('from_date', 'block_ceo_iltbar'), $opts);
        $defaulttime = strtotime( "-1 month", time());
        $mform->setDefault('fromdate',  $defaulttime);
        $mform->addElement('date_selector', 'todate',  get_string('to_date', 'block_ceo_iltbar'), $opts);

        $buttonarray=array();
        $buttonarray[] =& $mform->createElement('submit', 'ceo_iltbar_submit', get_string('filter_button', 'block_ceo_iltbar'));
        $buttonarray[] =& $mform->createElement('submit', 'ceo_iltbar_cancel', get_string('reset', 'block_ceo_iltbar'));
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


class ilt_pie_chart_form1 extends moodleform {
    function definition() {
        global $DB;
        $mform =& $this->_form;
        // $mform->addElement('header','ceo_iltpie_form_old', "Filter");
        // $mform->setExpanded('ceo_iltpie_form_old', false);
        
        $categories = $this->getCategories();
        $selectArray = array(0=>'All');
        foreach ($categories as $key => $value) {
            $selectArray[$value->id] = $value->name;
        }

        $mform->addElement('select', 'department_piechart',"Department",$selectArray);
        $mform->setDefault('select', 'All');        

        $subjects = $this->getsubjects();
        $subjectArray = array(0=>'All');
        if ($subjects) {
            foreach ($subjects as $key => $subject) {
                $subjectArray[$subject->id] = $this->get_category_parent_name($subject->id);
            }
        }


        $mform->addElement('select', 'trainingType',"Training Type",$subjectArray);
        $mform->setDefault('trainingType', 'All');
        
        $opts = array(
            'startyear' => 2000,
            'stopyear' => date("Y"),
            'timezone'  => 99,
            'optional'  => false,
        );
        $mform->addElement('date_selector', 'fromdate', "From Date", $opts);
        $defaulttime = strtotime( "-1 month", time());
        $mform->setDefault('fromdate',  $defaulttime);
        $mform->addElement('date_selector', 'todate', "To Date",$opts);

        $buttonarray=array();
        $buttonarray[] =& $mform->createElement('submit', 'ceo_iltpie_submit', get_string('filter_button', 'block_ceo_iltbar'));
        $buttonarray[] =& $mform->createElement('submit', 'ceo_iltpie_cancel', get_string('reset', 'block_ceo_iltbar'));
        $mform->addGroup($buttonarray, 'buttonar', '', array(' '), false);

    }

    function getCategories() {
        global $DB;

        $sql = 'SELECT cc.id,cc.name FROM {course_categories} as cc WHERE cc.visible=1 AND depth = 1';

        return $DB->get_records_sql($sql);
    }

     function getsubjects() {
        global $DB;
        
        $category = optional_param('department_piechart',null,PARAM_TEXT);
        $subject = optional_param('trainingType',null,PARAM_TEXT);

        $trainingType = '';
        $subjecthtml = '<option value="">select</option>';
        if ( !empty($category)) {

            $sql = 'SELECT cc.* FROM {course_categories} as cc JOIN {course} as c on c.category=cc.id WHERE c.visible=1 AND cc.path LIKE "/'.$category.'/%" AND cc.idnumber LIKE "ILT%"';
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
            $errors['fromdate'] = get_string('startdterror', 'block_ceo_iltpie');
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