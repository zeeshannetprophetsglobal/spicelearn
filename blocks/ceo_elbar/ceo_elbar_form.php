<?php 
require_once("$CFG->libdir/formslib.php");

class ceo_elbar_form extends moodleform {
    function definition() {
        global $DB;
        $mform =& $this->_form;
        // $mform->addElement('header','ceo_elbar_form_old', get_string('date_range', 'block_ceo_elbar'));
        // $mform->setExpanded('ceo_elbar_form_old', false);

        $opts = array(
            'startyear' => 2000,
            'stopyear' => date("Y"),
            'timezone'  => 99,
            'optional'  => false,
        );
        $mform->addElement('date_selector', 'elbar_fromdate', get_string('from_date', 'block_ceo_elbar'), $opts);
        $defaulttime = strtotime( "-1 month", time());
        $mform->setDefault('elbar_fromdate',  $defaulttime);
        $mform->addElement('date_selector', 'elbar_todate', get_string('to_date', 'block_ceo_elbar'), $opts);

        $buttonarray=array();
        $buttonarray[] =& $mform->createElement('submit', 'elbar_submitbutton', get_string('filter_button', 'block_ceo_elbar'));
        $buttonarray[] =& $mform->createElement('submit', 'elbar_cancel', get_string('reset', 'block_ceo_elbar'));
        $mform->addGroup($buttonarray, 'buttonar', '', array(' '), false);

    }

    function reset() {
        $this->_form->updateSubmission(null, null);
    }

    function validation($data, $files) {
        $errors = parent::validation($data, $files);
        if ($data['elbar_fromdate'] > time()) {
            $errors['elbar_fromdate'] = get_string('startdterror', 'block_ceo_elbar');
        }
        if ($data['elbar_todate'] < $data['elbar_fromdate']) {
            $errors['elbar_todate'] = get_string('todatedterror', 'block_ceo_iltbar');
        }
        return $errors;
    }

}


class ceo_elpiechart1_form extends moodleform {
    function definition() {
        global $DB;
        $mform =& $this->_form;
        // $mform->addElement('header','ceo_elpiechart_form_old', get_string('filter', 'block_ceo_elbar'));
        // $mform->setExpanded('ceo_elpiechart_form_old', false);
        
        $categories = $this->getCategories();
        $selectArray = array();
        foreach ($categories as $key => $value) {
            $selectArray[$value->id] = $value->name;
        }
        // var_dump($selectArray);
        $mform->addElement('select', 'category_pie',get_string('department', 'block_ceo_elbar'),$selectArray);
        // $mform->setDefault('category_pie', $selectArray[0]);        

        
        $subjects = $this->getsubjects();
        $subjectArray = array(0=>'All');
        if ($subjects) {
            foreach ($subjects as $key => $subject) {
                $subjectArray[$subject->id] = $this->get_category_parent_name($subject->id);
            }
        }

        
        // echo "<pre>";print_r($subjectArray);die;
        $mform->addElement('select', 'subject',get_string('trainingType', 'block_ceo_elbar'),$subjectArray);
        $mform->setDefault('subject', 'All');
        

        $opts = array(
            'startyear' => 2000,
            'stopyear' => date("Y"),
            'timezone'  => 99,
            'optional'  => false,
        );
        $mform->addElement('date_selector', 'fromdate',get_string('from_date', 'block_ceo_elbar'), $opts);
        $defaulttime = strtotime( "-1 month", time());
        $mform->setDefault('fromdate',  $defaulttime);
        $mform->addElement('date_selector', 'todate', get_string('to_date', 'block_ceo_elbar'),$opts);

        $buttonarray=array();
        $buttonarray[] =& $mform->createElement('submit', 'ceo_elpiechart_submit', get_string('filter_button', 'block_ceo_elbar'));
        $buttonarray[] =& $mform->createElement('submit', 'ceo_elpiechart_cancel', get_string('reset', 'block_ceo_elbar'));
        $mform->addGroup($buttonarray, 'buttonar', '', array(' '), false);

    }

    function getCategories() {
        global $DB;

        $sql = 'SELECT cc.id,cc.name FROM {course_categories} as cc WHERE cc.visible=1 AND  depth = 1 order by id asc';

        return $DB->get_records_sql($sql);
    }

    function getsubjects() {
        global $DB;
        
        $category = optional_param('category_pie',null,PARAM_TEXT);
        $subject = optional_param('subject',null,PARAM_TEXT);
        if (empty($category)) {
            $defaultcate_query = 'SELECT cc.id FROM {course_categories} as cc WHERE cc.visible=1 AND  depth = 1 order by id asc LIMIT 1';
            $category = $DB->get_record_sql($defaultcate_query);
            $category = $category->id;
        }

        $trainingType = '';
        $subjecthtml = '<option value="">select</option>';
        if ( !empty($category)) {

            $sql = 'SELECT DISTINCT cc.* FROM {course_categories} as cc JOIN {course} as c on c.category=cc.id WHERE cc.visible=1 AND c.visible=1 AND cc.path LIKE "/'.$category.'/%" AND cc.idnumber LIKE "el%"';
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
            $errors['fromdate'] = get_string('startdterror', 'block_ceo_elpiechart');
        }
        // print_object($data);
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