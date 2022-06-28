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
 * @package    block_manager_elgraph
 * @author     Ayush <aayush.yahoo@gmail.com>
 */

defined('MOODLE_INTERNAL') || die();

class block_manager_elgraph extends block_base {
    /**
     * block initializations
     */
    public function init() {
        $this->title = get_string('pluginname', 'block_manager_elgraph');
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
        $PAGE->requires->js_call_amd('block_manager_elgraph/manager_elgraph','Init');

        require_once("manager_elgraph_form.php");
        require_once($CFG->dirroot.'/cohort/lib.php');
        $html = '';
        $html .= html_writer::div('', '', array('id' => 'id_manager_elgraph_form'));
        $html .= html_writer::div('', '', array('id' => 'id_manager_elpie_filter'));
        $mform = new manager_elgraph_form();
        $cancle = optional_param('manager_elgraph_cancel',"", PARAM_RAW);

        $fromdatetime = strtotime( "-31 days", strtotime(date('Y-m-d')));
        $todatetime = strtotime(date('Y-m-d'));
        
        if ($cancle) {
            $mform->reset();
        } else if ($fromform = $mform->get_data()) {
            $fromdatetime = $fromform->fromdate;
            $todatetime = $fromform->todate;

        }

        $cohort =  cohort_get_user_cohorts($USER->id);
        $cohortdata = array_values($cohort);
        if ($cohortdata) {

            $parent_cate = $DB->get_record_sql("SELECT id FROM  {course_categories} WHERE name LIKE '%".$cohortdata[0]->name."%'");
            if ($parent_cate) {
                $category = ' and cc.idnumber LIKE "%el%" AND cc.path LIKE "%'.$parent_cate->id.'%"';
            }
        }

        $sql = "SELECT cc.id,cc.name,COUNT(c.id) as totalcourse,cc.path FROM {course_categories} as cc LEFT JOIN {course} as c on cc.id=c.category WHERE c.visible= 1 and cc.visible=1 and c.startdate BETWEEN $fromdatetime AND $todatetime $category GROUP by cc.id";
        // print_object($sql);die;
        $cate_courses = $DB->get_records_sql($sql);
        $dptArray = array();
        $CourseArray = array();
        foreach($cate_courses as $cate_course){
            $dptArray[] = $this->get_category_parent_name($cate_course->id);
            $CourseArray[] = $cate_course->totalcourse;

        }
        $html .= html_writer::tag('h3','Course Released',array('class'=>'chart-heading'));
        $html .= '<i class="icon fas fa-info-circle fa-fw iconhelp icon-pre"></i>';
        $html .= html_writer::tag('span','Total number of course released across department');
        foreach($CourseArray as $key => $array_item){
            if($array_item==0){
                unset($CourseArray[$key]);
                unset($dptArray[$key]);
            }
        }
        $CourseArray = array_values($CourseArray);
        $dptArray = array_values($dptArray);
        if (empty($CourseArray)) {
            $html .= '<h2 align="center">No record found</h2>';
        }else{
            $chart = new core\chart_bar();
            $serie = new core\chart_series('Number of courses', $CourseArray);
            $serie->set_color('#FFD600');
            $chart->add_series($serie);
            $chart->set_labels($dptArray);

            $html .= $OUTPUT->render($chart);
        }
        
        $html .= $mform->render();
        return $html;
    }


    public function manager_piechart_element(){

     
        global $CFG,$OUTPUT,$DB,$PAGE,$USER;
        
        $PAGE->requires->jquery();
        $PAGE->requires->js_call_amd('block_manager_elgraph/manager_elgraph','Init');
        require_once($CFG->dirroot.'/local/sitereport/locallib.php');
        require_once("manager_elgraph_form.php");
        require_once($CFG->dirroot.'/cohort/lib.php');
        $mform = new manager_elpie1_form();
        $cancle = optional_param('manager_elpie_cancel',"", PARAM_RAW);
        $fromdate = optional_param('fromdate',"", PARAM_RAW);
        $todate = optional_param('todate',"", PARAM_RAW);
        $html2 = '';
        $subject = '';
        $fromdatetime = strtotime( "-31 days", strtotime(date('Y-m-d')));
        $todatetime = strtotime(date('Y-m-d'));
        

        if ($cancle) {

            $mform->reset();
            
        } else if ($fromform = $mform->get_data()) {
            
            $subject = optional_param('manager_elpie_subject',"", PARAM_RAW);
            $fromdatetime = $fromform->fromdate;
            $todatetime = $fromform->todate;
            
        }

        $CourseArray = array();
        $cohort =  cohort_get_user_cohorts($USER->id);
        $cohortdata = array_values($cohort);
        if ($cohortdata) {

            $parent_cate = $DB->get_record_sql("SELECT id FROM  {course_categories} WHERE name LIKE '%".$cohortdata[0]->name."%'");
            if ($parent_cate) {
                $category = $parent_cate->id;
            }
        }

        $formfilter = ' AND c.visible = 1 AND cc.visible=1 AND c.startdate BETWEEN '.$fromdatetime.' and '.$todatetime;
        if ($subject) {
            $formfilter .= ' AND cc.id='.$subject;
        }else if($category){
            $formfilter .= ' AND cc.idnumber LIKE "%el%" AND cc.path LIKE "%'.$category.'%"';
        }else{
            $formfilter .= ' AND cc.idnumber LIKE "%el%"';
        }

        $sql = 'SELECT c.id FROM {course} as c JOIN {course_categories} as cc ON c.category = cc.id'.$formfilter;
        $courses = $DB->get_records_sql($sql,null);
        // echo $sql;
        $completed = 0;
        $inprogress = 0;
        $notstarted = 0;
        foreach ($courses as $key => $value) {
            $UsersCount = $this->getcategoryreport($value->id);
            $completed += $UsersCount->completed;
            $inprogress += $UsersCount->inprogress;
            $notstarted += $UsersCount->notstarted;
        }

        $html2 .= html_writer::tag('h3','Course consumption',array('class'=>'chart-heading'));
            $html2 .= '<i class="icon fas fa-info-circle fa-fw iconhelp icon-pre"></i>';
        $html2 .= html_writer::tag('span','Department wise trainee consumption report');
        if ($notstarted==0 and $inprogress==0 and $completed == 0) {
            $html2 .= '<h2 align="center">No record found</h2>';
        }else{

            $labels = array('Not Started','In Progress','Completed');
            $value = array($notstarted,$inprogress,$completed);
            $chart = new core\chart_pie();
            $chart->set_doughnut(true);
            $serie = new core\chart_series('Users', $value);
            $serie->set_colors(['#F93450','#FFD600','#6184F0']);
            $chart->add_series($serie);
            $chart->set_labels($labels);
            $html2 .= $OUTPUT->render($chart);
        }

        $html2 .= $mform->render();
        return $html2;
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

    public function getcategoryreport($courseid){
        global $CFG,$DB;

        $sql = "SELECT  
                COUNT(DISTINCT u.id) as totaluser,
                COUNT(DISTINCT IF(ccom.timecompleted is not null,u.id,NULL)) AS completed, 
                COUNT(DISTINCT IF(ccom.timecompleted is null AND ulast.timeaccess is not null,u.id,NULL)) AS inprogress, 
                COUNT(DISTINCT IF(ulast.timeaccess is null AND ccom.timecompleted is null,u.id,NULL)) AS notstarted 
                FROM mdl_user u
                INNER JOIN mdl_role_assignments ra ON ra.userid = u.id
                INNER JOIN mdl_context ct ON ct.id = ra.contextid
                INNER JOIN mdl_course c ON c.id = ct.instanceid
                JOIN mdl_course_categories as cc ON c.category = cc.id 
                INNER JOIN mdl_role r ON r.id = ra.roleid
                LEFT JOIN mdl_course_completions as ccom ON ccom.course=c.id AND ccom.userid=u.id 
                LEFT JOIN mdl_user_lastaccess as ulast ON ulast.userid=u.id AND ulast.courseid=c.id
                WHERE r.id=5 AND c.id=".$courseid;
        $totaluser = $DB->get_record_sql($sql);
        return $totaluser;
    }
}
