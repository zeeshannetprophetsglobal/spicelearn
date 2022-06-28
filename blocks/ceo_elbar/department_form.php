<?php 
require_once("$CFG->libdir/formslib.php");

class department_form extends moodleform {
    function definition() {
        global $DB;
        $mform =& $this->_form;
        // $mform->addElement('header','filterform', get_string('filter', 'block_ceo_elbar'));
        // $mform->setExpanded('filterform', false);
        
        $categories = $this->getCategories();
        foreach ($categories as $key => $value) {
            $selectArray[$value->id] = $value->name;
        }
        
        $mform->addElement('select', 'category',get_string('department', 'block_ceo_elbar'),$selectArray);
        $mform->setDefault('select', 'select');

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
        $buttonarray[] =& $mform->createElement('submit', 'pie_chart_form', get_string('filter_button', 'block_ceo_elbar'));
        $buttonarray[] =& $mform->createElement('submit', 'pie_chart_form_cancel', get_string('reset', 'block_ceo_elbar'));
        $mform->addGroup($buttonarray, 'buttonar', '', array(' '), false);

    }

    function getCategories() {
        global $DB;

        $sql = 'SELECT cc.id,cc.name FROM {course_categories} as cc WHERE cc.visible=1 AND depth = 1';

        return $DB->get_records_sql($sql);
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
}


?>