<?php 
require_once("$CFG->libdir/formslib.php");

class scheduler_form extends moodleform {
    function definition() {
        global $DB;
        $mform =& $this->_form;
        // $mform->addElement('header','filterform', get_string('filter', 'block_ceo_elbar'));
        // $mform->setExpanded('filterform', false);

        $categories = $this->getCategories();
        $selectArray = array(0=>'select');
        foreach ($categories as $key => $value) {
            $selectArray[$value->id] = $value->name;
        }

        $mform->addElement('select', 'department',get_string('department', 'block_ceo_elbar'),$selectArray);
        $mform->setDefault('select', 'select');        

        
        $subjects = $this->getsubjects();
        $subjectArray = array(0=>'select');
        if ($subjects) {
            foreach ($subjects as $key => $subject) {
                $subjectArray[$subject->id] = $this->get_category_parent_name($subject->id);
            }
        }

        
        // echo "<pre>";print_r($subjectArray);die;
        $mform->addElement('select', 'subject','Category',$subjectArray);
        $mform->setDefault('subject', 'select');

        $opts = array(
            'startyear' => 2000,
            'stopyear' => date("Y"),
            'timezone'  => 99,
            'optional'  => false,
        );
        $mform->addElement('date_selector', 'fromdate', get_string('from_date', 'block_ceo_elbar'), $opts);
        $defaulttime = strtotime( "-1 month", time());
        $mform->setDefault('fromdate',  $defaulttime);
        $mform->addElement('date_selector', 'todate', get_string('to_date', 'block_ceo_elbar'),$opts);

        $buttonarray=array();
        $buttonarray[] =& $mform->createElement('submit', 'scheduler_form','Download');
        $buttonarray[] =& $mform->createElement('submit', 'scheduler_form_cancel', get_string('reset', 'block_ceo_elbar'));
        $mform->addGroup($buttonarray, 'buttonar', '', array(' '), false);

    }

    function reset() {
        $this->_form->updateSubmission(null, null);
    }
    
     function validation($data, $files) {
        $errors = parent::validation($data, $files);
        if ($data['fromdate'] > time()) {
            $errors['fromdate'] = get_string('startdterror', 'block_ceo_elbar');
        }
        if ($data['todate'] < $data['fromdate']) {
            $errors['todate'] = get_string('todatedterror', 'block_ceo_elbar');
        }
        return $errors;
    }

    function getCategories() {
        global $DB;

        $sql = 'SELECT cc.id,cc.name FROM {course_categories} as cc WHERE cc.visible=1 AND depth = 1 order by id asc';

        return $DB->get_records_sql($sql);
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
    
    function getsubjects() {
        global $DB;
        
        $category = optional_param('category_pie',null,PARAM_TEXT);
        $subject = optional_param('subject',null,PARAM_TEXT);

        $trainingType = '';
        $subjecthtml = '<option value="">select</option>';
        if ( !empty($category)) {

            $sql = 'SELECT cc.* FROM {course_categories} as cc JOIN {course} as c on c.category=cc.id WHERE cc.visible=1 AND c.visible=1 AND cc.path LIKE "/'.$category.'/%" AND cc.idnumber LIKE "el%"';
            $trainingType = $DB->get_records_sql($sql,null);

        }
        return $trainingType;
    }
}


?>