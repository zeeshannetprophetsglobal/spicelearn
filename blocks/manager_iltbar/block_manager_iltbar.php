<?php
// This file is part of Moodle - http://moodle.org/
//
// Moodle is free software: you can redistribute it and/or modify
// it under the terms of the GNU General Public License as published by
// the Free Software Foundation, either version 3 of the License, or
// (at your option) any later version.
//
// Moodle is distributed in the hope that it will be useful,
// but WITHOUT ANY WARRANTY; without even the implied warranty of
// MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
// GNU General Public License for more details.
//
// You should have received a copy of the GNU General Public License
// along with Moodle.  If not, see <http://www.gnu.org/licenses/>.

/**
 * Block displaying information about current logged-in user.
 *
 * This block can be used as anti cheating measure, you
 * can easily check the logged-in user matches the person
 * operating the computer.
 *
 * @package    block_manager_iltbar
 * @author     Saurabh Pandey
 */

defined('MOODLE_INTERNAL') || die();

class block_manager_iltbar extends block_base {
    /**
     * block initializations
     */
    public function init() {
        $this->title = get_string('pluginname', 'block_manager_iltbar');
    }

    /**
     * block contents
     *
     * @return object
     */
    public function get_content() {

        if ($this->content !== NULL) {
            return $this->content;
        }

        if (!isloggedin() or isguestuser()) {
            // Only real users can access myprofile block.
            return;
        }


        
        $html = '';
        $html .= html_writer::start_tag('div',array('class'=>'row'));

        $html .= html_writer::start_tag('div',array('class'=>'col-md-6 chart-block'));
        $html .= $this->dpt_admin_front_element();
        $html .= html_writer::end_tag('div');

        $html .= html_writer::start_tag('div',array('class'=>'col-md-6 chart-block'));
        $html .= $this->manager_piechart_element();
        $html .= html_writer::end_tag('div');

        $html .= html_writer::end_tag('div');
        $this->content = new stdClass();
        $this->content->text = $html;
        // $this->content->text = 'Manager Bar Chart';
        $this->content->footer = '';

        return $this->content;
    }

    /**
     * allow the block to have a configuration page
     *
     * @return boolean
     */
    public function has_config() {
        return false;
    }

    /**
     * allow more than one instance of the block on a page
     *
     * @return boolean
     */
    public function instance_allow_multiple() {
        //allow more than one instance on a page
        return false;
    }

    /**
     * allow instances to have their own configuration
     *
     * @return boolean
     */
    function instance_allow_config() {
        //allow instances to have their own configuration
        return false;
    }

    /**
     * instance specialisations (must have instance allow config true)
     *
     */
    public function specialization() {
    }

    /**
     * locations where block can be displayed
     *
     * @return array
     */
    public function applicable_formats() {
        return array('all'=>true);
    }

    /**
     * post install configurations
     *
     */
    public function after_install() {
    }

    /**
     * post delete configurations
     *
     */
    public function before_delete() {
    }

    /**
     * Return the plugin config settings for external functions.
     *
     * @return stdClass the configs for both the block instance and plugin
     * @since Moodle 3.8
     */
    public function get_config_for_external() {
        // Return all settings for all users since it is safe (no private keys, etc..).
        $configs = !empty($this->config) ? $this->config : new stdClass();

        return (object) [
            'instance' => $configs,
            'plugin' => new stdClass(),
        ];
    }

    public function dpt_admin_front_element(){

        global $CFG,$OUTPUT,$DB,$USER,$PAGE;

        $PAGE->requires->jquery();
        $PAGE->requires->js_call_amd('block_manager_iltbar/iltbar','Init');

        $filterData = [];
        $trainingName=[];
        $html = '';

        $html .= html_writer::div('', '', array('id' => 'id_manager_ilt_form'));
        $html .= html_writer::div('', '', array('id' => 'id_manager_iltpie_form'));

        require_once("manager_iltbar_form.php");
        $mform = new manager_iltbar_form();
        
        $cancle = optional_param('manager_ilt_cancel',"", PARAM_RAW);

        $fromdatetime = strtotime( "-31 days", strtotime(date('Y-m-d')));
        $enddatetime = strtotime(date('Y-m-d'));
        
        if ($cancle) {

            $mform->reset();

        } else if ($formData = (array)$mform->get_data()) {

            $fromdatetime = $formData['fromdate'];
            $enddatetime = $formData['todate'];

        }

        $filterData['start_date'] = $fromdatetime;
        $filterData['end_date'] = $enddatetime;


        /* Get Manager Department*/
        require_once($CFG->dirroot.'/cohort/lib.php');
        $cohort =  cohort_get_user_cohorts ($USER->id);
        $cohortdata = array_values($cohort);
        $managerDeaprtment = '';
        $cohort_cate = $DB->get_record_sql("SELECT id FROM  {course_categories} WHERE idnumber='".$cohortdata[0]->idnumber."'");
        // print_object($cohort_cate);die;
        $managerDeaprtment = '';
        if ($cohort_cate) {
            $managerDeaprtment = $cohort_cate->id;
        }
        // print_object($managerDeaprtment);die;
        if ($managerDeaprtment) {
         /* Get Enrolled Users in course*/
         $trainingData = $this->get_ilt_training_type($managerDeaprtment);
        // print_object(count($trainingData));die;
         foreach($trainingData as $training){

            $countOfCourses = $this->get_iltcourse($training->id,$filterData);
            if($countOfCourses){
                $numberOfCourses[] = $countOfCourses;
                if($training->depth > 3 || $training->depth > 4){
                    $trainingName[] = $this->get_category_parent_name($training->id);
                }else{
                    $trainingName[] = $training->name;
                }
            }
            
        }
        
       
        $html .= html_writer::tag('h3','Trainings Conducted',array('class'=>'chart-heading'));
        $html .= '<i class="icon fas fa-info-circle fa-fw iconhelp icon-pre"></i>';
        $html .= html_writer::tag('span','Total number of Training conducted across department');
        /* Bar Chart Display */
        if(count($trainingName)){
            $tempData = $numberOfCourses;
            $nameData = $trainingName;
            $chart = new core\chart_bar();
            $players_series = new core\chart_series('Number of Courses', $tempData);
            $players_series->set_color('#FFD600');
            $chart->add_series($players_series);
            $chart->set_labels($nameData);
            $html .=  $OUTPUT->render($chart);



        }else{
            $html .= '<h1 align="center">No records found</h1>';
        }
    }else{
        $html .= '<h1 align="center">No records found</h1>';
    }

    $html .= $mform->render();

    return $html;
}

/* This function is not in use */
public function get_iltcourse_count($categotryid){

    global $DB;

    $sql = 'SELECT c.* FROM {course} as c JOIN {course_categories} as cc ON c.category = cc.id WHERE c.visible= 1 and cc.visible=1 and cc.idnumber LIKE "%ilt%" AND cc.path LIKE "%'.$categotryid.'%"';

    $coursedata = $DB->get_records_sql($sql,null);

    return count($coursedata);
}

public function get_ilt_training_type($categotryid){
    global $DB;
    $sql = 'SELECT cc.* FROM {course_categories} as cc WHERE 
    cc.path LIKE "%'.$categotryid.'%"  AND cc.idnumber LIKE "ILT%"';
    $trainingType = $DB->get_records_sql($sql,null);

    return $trainingType;
}

public function get_category_parent_name($category){

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

public function get_iltcourse($categotryid,$filterData){

    global $DB;
    $sql = 'SELECT c.* FROM {course} as c WHERE c.visible=1 AND c.category = "'.$categotryid.'"'; 
    if(!empty($filterData['start_date'])){

        $sql .= ' AND (c.startdate BETWEEN "'.$filterData["start_date"].'" AND "'.$filterData["end_date"].'")';
    }
    $coursedata = $DB->get_records_sql($sql,null);
    return count($coursedata);
}

/* This function is not in use */
public function get_enrol_user($categoryId,$filterData){

    global $DB;
    $totaluser = [];    
    $count = 0;
    $courseData = $this->get_iltcourse($categoryId,$filterData);
    foreach($courseData as $course){
        $role = $DB->get_record('role', array('shortname' => 'student'));
        $context = CONTEXT_COURSE::instance($course->id);
        $student = get_role_users($role->id, $context);
        foreach ($student as $key => $value) {
            $count ++;
        }
    }
    return  $count;
    
}

public function manager_piechart_element(){

    global $CFG,$OUTPUT,$DB,$USER,$PAGE;

    $PAGE->requires->jquery();
    $PAGE->requires->js_call_amd('block_manager_iltpie/iltbar','Init');

    $filterData = [];
    require_once("manager_iltbar_form.php");
    $mform = new manager_iltpie1_form();

    $cancle = optional_param('manager_iltpie_cancel',"", PARAM_RAW);
    $subjectfilter = '';
    
    $fromdatetime = strtotime( "-31 days", strtotime(date('Y-m-d')));
    $todatetime = strtotime(date('Y-m-d'));

    if ($cancle) {

        $mform->reset();
        
    } else if ($formData = $mform->get_data()) {

        if ($formData->ilt_category!=0 and $formData->ilt_category != '') {
            $subjectfilter = ' and cc.id = '.$formData->ilt_category;
        }
        $fromdatetime = $formData->fromdate;
        $todatetime = $formData->todate;
        
    }
    
    $cohort =  cohort_get_user_cohorts($USER->id);
    $cohortdata = array_values($cohort);
    $managerDeaprtment = '';
    $cohort_cate = $DB->get_record_sql("SELECT id FROM  {course_categories} WHERE idnumber='".$cohortdata[0]->idnumber."'");
    $depratment = '';
    if ($cohort_cate) {
        $depratment =   '  AND cc.path LIKE "%'.$cohort_cate->id.'%" ';
    }
    

    $quizsql = "SELECT DISTINCT CONCAT(gi.iteminstance) FROM {course} as c JOIN {course_categories} as cc ON c.category = cc.id JOIN {course_modules} as cm ON cm.course = c.id JOIN {grade_items} as gi ON gi.courseid = c.id WHERE c.visible= 1 and cc.visible=1 and cc.idnumber LIKE '%ILT%' AND gi.itemname LIKE '%final%' AND gi.itemmodule = 'quiz' AND c.startdate BETWEEN $fromdatetime AND $todatetime ".$depratment.$subjectfilter;
        // echo $quizsql;die;
    $passsql = "SELECT DISTINCT qg.* FROM {quiz_grades} as qg JOIN {grade_items} as gi ON qg.quiz = gi.iteminstance WHERE quiz IN($quizsql) AND qg.grade >= gi.gradepass AND qg.timemodified BETWEEN $fromdatetime AND  $todatetime";

    $failsql = "SELECT DISTINCT qg.* FROM {quiz_grades} as qg JOIN {grade_items} as gi ON qg.quiz = gi.iteminstance WHERE quiz IN($quizsql) AND qg.grade < gi.gradepass AND qg.timemodified BETWEEN $fromdatetime AND  $todatetime";

    // $notatteptsql = "SELECT  * FROM {grade_grades} WHERE itemid IN($quizsql) AND finalgrade IS NULL";
    $notatteptsql = "SELECT DISTINCT ra.userid 
        FROM mdl_user u
        INNER JOIN mdl_role_assignments ra ON ra.userid = u.id
        INNER JOIN mdl_context ct ON ct.id = ra.contextid
        JOIN mdl_quiz as q ON q.course=ct.instanceid
        LEFT JOIN mdl_quiz_attempts as qa ON qa.userid=ra.userid AND qa.quiz=q.id
        WHERE q.id IN($quizsql) and ra.roleid=5 and qa.id is null";

    $passdata = count($DB->get_records_sql($passsql,null));
    $faildata = count($DB->get_records_sql($failsql,null));
    $notatteptdata = count($DB->get_records_sql($notatteptsql,null));
    $html = '';
    $html .= html_writer::tag('h3','ILT Result',array('class'=>'chart-heading')); 
        $html .= '<i class="icon fas fa-info-circle fa-fw iconhelp icon-pre"></i>';
    $html .= html_writer::tag('span','Department wise pass/fail segregation');  
    $value = array($notatteptdata,$faildata,$passdata);

    $piechart = new core\chart_pie();
    $piechart->set_doughnut(true);
    $seriesDisplay =  new core\chart_series('Students', $value);
    $seriesDisplay->set_colors(['#F93450','#FFD600','#6184F0']);
                $piechart->add_series($seriesDisplay); // On pie charts we just need to set one series.
                $piechart->set_labels(['Not Attempted', 'Failed','Passed' ]);

                if(array_sum($value) != 0){
                   $html .= $OUTPUT->render($piechart);
               }else{
                $html .= html_writer::tag('h1','No record found',array('align'=>'center'));
            }
            $html .= $mform->render();
            return $html;

        }

    }
