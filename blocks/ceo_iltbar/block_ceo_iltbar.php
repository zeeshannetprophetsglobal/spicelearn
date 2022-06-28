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
 * @package    block_ceo_iltbar
 * @author     Ayush <aayush.yahoo@gmail.com>
 */

defined('MOODLE_INTERNAL') || die();

class block_ceo_iltbar extends block_base {
    /**
     * block initializations
     */
    public function init() {
        $this->title = get_string('pluginname', 'block_ceo_iltbar');
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
        $html .= $this->eco_piechat_element();
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

        global $CFG,$OUTPUT,$DB,$PAGE;
        
        $PAGE->requires->jquery();
        $PAGE->requires->js_call_amd('block_ceo_iltbar/iltbar','Init');

        require_once("date_filter.php");
        $cancle = optional_param('ceo_iltbar_cancel',"", PARAM_RAW);

        $fromdatetime = strtotime( "-31 days", strtotime(date('Y-m-d')));
        $enddatetime = strtotime(date('Y-m-d'));

        $filterData = [];
        $mform = new date_filter();

        $html = '';
        $html .= html_writer::div('', '', array('id' => 'id_ceo_iltbar_form'));
        $html .= html_writer::div('', '', array('id' => 'id_ceo_iltpie_form'));
        
        if ($cancle) {
            
            $mform->reset();

        } else if ($formData = (array)$mform->get_data()) {

            $fromdatetime = $formData['fromdate'];
            $enddatetime = $formData['todate'];
            $enddatetime = strtotime( "+23 hours", $enddatetime);

        }
        
        $filterData['start_date'] = $fromdatetime;
        $filterData['end_date'] = $enddatetime;

        $sql = 'SELECT * FROM  {course_categories} WHERE depth = 1';

        $depratment = $DB->get_records_sql($sql,null);
        $first_key = key($depratment); 
        $dptArray = array();
        $CourseArray = array();
        foreach($depratment as $dpt){

            $courseCount = $this->get_iltcourse_count($dpt->id,$filterData);
            if($courseCount){
                $CourseArray[] = $courseCount;
                $dptArray[] = $dpt->name;
            }
        }
        $html .= html_writer::tag('h3','Trainings Conducted',array('class'=>'chart-heading'));
        $html .= '<i class="icon fas fa-info-circle fa-fw iconhelp icon-pre"></i>';
        $html .= html_writer::tag('span','Total number of Training conducted across department');
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
       // $html .= '<a href="/blocks/ceo_iltbar/view.php?dep_id='.$first_key.'" class="btn btn-secondary">View More</a>';
       $viewmoreurl = new moodle_url($CFG->wwwroot.'/blocks/ceo_iltbar/view.php',array('dep_id'=>$first_key));
       $html .= html_writer::link($viewmoreurl,get_string('deep_dive', 'block_ceo_iltbar'),array('class'=>"btn btn-secondary viewmore"));

        return $html;
    }

    public function get_iltcourse_count($categotryid,$filterData){

        global $DB;
        
        $sql = 'SELECT c.* FROM {course} as c JOIN {course_categories} as cc ON c.category = cc.id WHERE '; 
        
        $sql .= 'c.visible= 1 and cc.visible=1 and cc.idnumber LIKE "%ilt%" AND cc.path LIKE "%'.$categotryid.'%"';
        if(!empty($filterData)){
        
            $sql .= ' AND (c.startdate BETWEEN "'.$filterData["start_date"].'" AND "'.$filterData["end_date"].'")';
        }
        // echo $sql;die;
        $coursedata = $DB->get_records_sql($sql,null);
       
        return count($coursedata);
    }

    
    public function eco_piechat_element(){

        global $CFG,$OUTPUT,$DB,$PAGE;

        $PAGE->requires->jquery();
        $PAGE->requires->js_call_amd('block_ceo_iltbar/iltbar','Init');

        require_once("date_filter.php");
        $mform = new ilt_pie_chart_form1();
        $cancle = optional_param('ceo_iltpie_cancel',"", PARAM_RAW);
        $fromdate = optional_param('fromdate',"", PARAM_RAW);
        $todate = optional_param('todate',"", PARAM_RAW);
        $html = '';

        $fromdatetime = strtotime( "-31 days", strtotime(date('Y-m-d')));
        $todatetime = strtotime(date('Y-m-d'));

        $subjectfilter = '';
        $categoryfilter = '';


        if ($cancle) {

            $mform->reset();
        
        } else if ($fromform = $mform->get_data()) {

            if ($fromform->trainingType!=0 and $fromform->trainingType != '') {
                $subjectfilter = ' and cc.id = '.$fromform->trainingType;
            }
            if ($fromform->department_piechart!=0 and $fromform->department_piechart != '') {
                $categoryfilter = ' and cc.path LIKE "%'.$fromform->department_piechart.'%"';
            }
            $fromdatetime = $fromform->fromdate;
            $todatetime = $fromform->todate;
          
        }
      
       // echo $fromdatetime;die;
        $quizsql = "SELECT DISTINCT CONCAT(gi.iteminstance) FROM {course} as c JOIN {course_categories} as cc ON c.category = cc.id JOIN {course_modules} as cm ON cm.course = c.id JOIN {grade_items} as gi ON gi.courseid = c.id WHERE c.visible= 1 and cc.visible=1 and cc.idnumber LIKE '%ILT%' AND gi.itemname LIKE '%final%' AND gi.itemmodule = 'quiz' AND c.startdate BETWEEN $fromdatetime AND $todatetime ".$categoryfilter.$subjectfilter;
        //  echo $quizsql;die;
        $passsql = "SELECT DISTINCT qg.* FROM {quiz_grades} as qg JOIN {grade_items} as gi ON qg.quiz = gi.iteminstance WHERE quiz IN($quizsql) AND qg.grade >= gi.gradepass AND qg.timemodified BETWEEN $fromdatetime AND  $todatetime";
       
        $failsql = "SELECT DISTINCT qg.* FROM {quiz_grades} as qg JOIN {grade_items} as gi ON qg.quiz = gi.iteminstance WHERE quiz IN($quizsql) AND qg.grade < gi.gradepass AND qg.timemodified BETWEEN $fromdatetime AND  $todatetime";

      //  $notatteptsql = "SELECT  * FROM {grade_grades} WHERE itemid IN($quizsql) AND finalgrade IS NULL";

        $notatteptsql = "SELECT DISTINCT ra.userid 
        FROM mdl_user u
        INNER JOIN mdl_role_assignments ra ON ra.userid = u.id
        INNER JOIN mdl_context ct ON ct.id = ra.contextid
        JOIN mdl_quiz as q ON q.course=ct.instanceid
        LEFT JOIN mdl_quiz_attempts as qa ON qa.userid=ra.userid AND qa.quiz=q.id
        WHERE q.id IN($quizsql) and ra.roleid=5 and qa.id is null";
        // echo $notatteptsql;
        $passdata = count($DB->get_records_sql($passsql,null));
        $faildata = count($DB->get_records_sql($failsql,null));
        $notatteptdata = count($DB->get_records_sql($notatteptsql,null));

        $value = array($notatteptdata,$faildata,$passdata);

        $piechart = new core\chart_pie();
        $piechart->set_doughnut(true);
        $seriesDisplay =  new core\chart_series('Total Students', $value);
        $seriesDisplay->set_colors(['#F93450','#FFD600','#6184F0']);
        $piechart->add_series($seriesDisplay); // On pie charts we just need to set one series.
        $piechart->set_labels(['Not Attempted', 'Fail','Passed']);
        
        $html .= html_writer::tag('h3','ILT Result',array('class'=>'chart-heading'));
        $html .= '<i class="icon fas fa-info-circle fa-fw iconhelp icon-pre"></i>';
        $html .= html_writer::tag('span','Department wise pass/fail segregation');
        if(array_sum($value) != 0){
            $html .= $OUTPUT->render($piechart);
            }else{
                $html .= html_writer::tag('h1','No record found',array('align'=>'center'));
            }
        $html .= $mform->render();

        return $html;
        
    }

}
