    <?php

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
 * External Web Service Template
 *
 * @package    localapi
 */
require_once($CFG->libdir . "/externallib.php");
require_once($CFG->dirroot . "/local/api/lib.php");
require_once($CFG->dirroot . '/mod/scorm/lib.php');
require_once($CFG->dirroot . '/mod/scorm/locallib.php');
require_once($CFG->libdir . '/enrollib.php');
require_once($CFG->libdir."/completionlib.php");
require_once($CFG->dirroot.'/cohort/lib.php');


require_once($CFG->libdir . '/gradelib.php');

use core_completion\progress;

class local_api_external extends external_api {

    /**
     * Returns description of method parameters
     * @return external_function_parameters
     */
    public static function test_parameters() {

        return new external_function_parameters(
            array(
                'instanceid' => new external_value(PARAM_INT, 'resource instance id')
            )
        );
    }

    /**
     * Returns welcome message
     * @return string welcome message
     */
    public static function test($instanceid) {
        
        global $DB, $CFG;
        require_once($CFG->dirroot . "/mod/resource/lib.php");

        $params = self::validate_parameters(self::test_parameters(),
                                            array(
                                                'instanceid' => $instanceid
                                            ));
        $warnings = array();

        // Request and permission validation.
        $resource = $DB->get_record('resource', array('id' => $params['instanceid']), '*', MUST_EXIST);
        list($course, $cm) = get_course_and_cm_from_instance($resource, 'resource');

        $context = context_module::instance($cm->id);
        self::validate_context($context);

        require_capability('mod/resource:view', $context);

        // Call the resource/lib API.
        resource_view($resource, $course, $cm, $context);

        $result = array();
        $result['statusCode'] = 'NP01';   
        $result['msg'] = 'Successfully update';   
        $result['status'] = true;
        $result['warnings'] = $warnings;
        return $result;

        
    }

    /**
     * Returns description of method result value
     * @return external_description
     */
    public static function test_returns() {
        return new external_single_structure(
            array(          
                'statusCode'    => new external_value(PARAM_RAW, 'statusCode'),
                'msg'  => new external_value(PARAM_RAW,'msg'),
                'status'    => new external_value(PARAM_BOOL, 'status, true if success'),
                'warnings'  => new external_warnings(),

            )
        );
    } 
    

      /**
     * Returns description of method parameters
     * @return external_function_parameters
     */
    public static function recourse_view_completion_parameters() {

        return new external_function_parameters(
            array(
                'instanceid' => new external_value(PARAM_INT, 'resource instance id')
            )
        );
    }

    /**
     * Returns welcome message
     * @return string welcome message
     */
    public static function recourse_view_completion($instanceid) {
        
        global $DB, $CFG;
        require_once($CFG->dirroot . "/mod/resource/lib.php");

        $params = self::validate_parameters(self::test_parameters(),
                                            array(
                                                'instanceid' => $instanceid
                                            ));
        $warnings = array();

        // Request and permission validation.
        $resource = $DB->get_record('resource', array('id' => $params['instanceid']), '*', MUST_EXIST);
        list($course, $cm) = get_course_and_cm_from_instance($resource, 'resource');

        $context = context_module::instance($cm->id);
        self::validate_context($context);

        require_capability('mod/resource:view', $context);

        // Call the resource/lib API.
        resource_view($resource, $course, $cm, $context);

        $result = array();
        $result['statusCode'] = 'NP01';   
        $result['msg'] = 'Successfully update';   
        $result['status'] = true;
        $result['warnings'] = $warnings;
        return $result;

        
    }

    /**
     * Returns description of method result value
     * @return external_description
     */
    public static function recourse_view_completion_returns() {
        return new external_single_structure(
            array(          
                'statusCode'    => new external_value(PARAM_RAW, 'statusCode'),
                'msg'  => new external_value(PARAM_RAW,'msg'),
                'status'    => new external_value(PARAM_BOOL, 'status, true if success'),
                'warnings'  => new external_warnings(),

            )
        );
    } 
    


     /**
     * Returns description of method parameters
     * @return external_function_parameters
     */
     public static function category_typedata_parameters() {

        return new external_function_parameters(
            array(	

              'type' => new external_value(PARAM_INT, 'Id of the user, 0 for current user', VALUE_DEFAULT, 0)
          )
        );
    }

    /**
     * Returns welcome message
     * @return string welcome message
     */
    public static function category_typedata($type) {

        global $USER,$DB,$PAGE;

        $params = self::validate_parameters(self::category_typedata_parameters(),array('type' => $type));

        $cohort =  cohort_get_user_cohorts ($USER->id);
        $cohortdata = array_values($cohort);

        $departmentName = $cohortdata[0]->idnumber;
        $type = '';
        if($params['type'] == 1 ){
            $type = 'ILT';
        }else{
            $type = 'EL';
        }

        // $sql = "SELECT * FROM {course_categories} WHERE idnumber LIKE '%".$type."%' AND path LIKE CONCAT( '%', ($DB->get_field_sql(SELECT id from {course_categories} WHERE idnumber LIKE '%".$departmentName."%')), '%')";
        $sql = "SELECT * FROM {course_categories} WHERE visible = 1 AND depth = 3 AND idnumber LIKE '%".$type."%' AND path LIKE CONCAT( '%', ($DB->get_field_sql(SELECT id from {course_categories} WHERE idnumber LIKE '%".$departmentName."%')), '%')";

        $data = $DB->get_records_sql($sql,null);
        $fulldata = array();
        foreach($data as $alldata){

            $categorydata = new stdClass;
            $categorydata->id = $alldata->id;
            $categorydata->name = $alldata->name;
            $categorydata->imageurl = api_fileview($alldata->id);
            $categorydata->coursecount = user_course_count($alldata->id, $USER->id);
            $categorydata->is_category = user_is_category($alldata->id,$USER->id);
            $fulldata[$alldata->id] = $categorydata;
        }
      // print_object($data);die;
        if(!empty($data)){

            return   [
                'statusCode' => 'NP01',
                'msg' => 'get completion data successfully',               
                'categorydata' => $fulldata,
                
            ];

        }else{

            return   [
                'statusCode' => 'NP00',
                'msg' => 'Record not found',               
                'categorydata' => [],
                
            ];
        }


        
    }

    /**
     * Returns description of method result value
     * @return external_description
     */
    public static function category_typedata_returns() {
        return new external_single_structure(
            array(          
                'statusCode'=> new external_value(PARAM_RAW, 'statusCode'),
                'msg' => new external_value(PARAM_RAW, 'statusCode message'),					
                'categorydata' => new external_multiple_structure(
                    new external_single_structure(
                       array(
                          'id' => new external_value(PARAM_INT, 'id',VALUE_OPTIONAL),
                          'name' => new external_value(PARAM_RAW, 'name',VALUE_OPTIONAL),
                          'imageurl' => new external_value(PARAM_RAW, 'imageurl',VALUE_OPTIONAL),
                          'coursecount' => new external_value(PARAM_RAW, 'coursecount',VALUE_OPTIONAL),
                          'is_category' => new external_value(PARAM_RAW, 'is_category', VALUE_OPTIONAL)
                   
                      )
                   )
                ),
            )
        );
    } 


      /**
     * Returns description of method parameters
     * @return external_function_parameters
     */
    public static function category_maintypedata_parameters() {

        return new external_function_parameters(
            array(	

              'department' => new external_value(PARAM_RAW)
          )
        );
    }

    /**
     * Returns welcome message
     * @return string welcome message
     */
    public static function category_maintypedata($department) {

        global $USER,$DB,$PAGE;

        $params = self::validate_parameters(self::category_maintypedata_parameters(),array('department' => $department));

        $departmentName = $params['department'];
       

        
        $sql = "SELECT * FROM {course_categories} WHERE depth = 2 AND path LIKE CONCAT( '%', ($DB->get_field_sql(SELECT  id from {course_categories} WHERE idnumber LIKE '%".$departmentName."%')), '%') ORDER BY name DESC";

        $data = $DB->get_records_sql($sql,null);
        $fulldata = array();
        foreach($data as $alldata){

            $categorydata = new stdClass;
            $categorydata->id = $alldata->id;
            $categorydata->name = $alldata->name;
            $categorydata->imageurl = api_fileview($alldata->id);
            $categorydata->coursecount = user_course_count($alldata->id, $USER->id);
            $fulldata[$alldata->id] = $categorydata;
        }
      // print_object($data);die;
        if(!empty($data)){

            return   [
                'statusCode' => 'NP01',
                'msg' => 'get completion data successfully',               
                'categorydata' => $fulldata,
                
            ];

        }else{

            return   [
                'statusCode' => 'NP00',
                'msg' => 'Record not found',               
                'categorydata' => [],
                
            ];
        }


        
    }

    /**
     * Returns description of method result value
     * @return external_description
     */
    public static function category_maintypedata_returns() {
        return new external_single_structure(
            array(          
                'statusCode'=> new external_value(PARAM_RAW, 'statusCode'),
                'msg' => new external_value(PARAM_RAW, 'statusCode message'),					
                'categorydata' => new external_multiple_structure(
                    new external_single_structure(
                       array(
                          'id' => new external_value(PARAM_INT, 'id',VALUE_OPTIONAL),
                          'name' => new external_value(PARAM_RAW, 'name',VALUE_OPTIONAL),
                          'imageurl' => new external_value(PARAM_RAW, 'imageurl',VALUE_OPTIONAL),
                          'coursecount' => new external_value(PARAM_RAW, 'coursecount',VALUE_OPTIONAL),
                    //   'path' => new external_value(PARAM_RAW, 'lastname',VALUE_OPTIONAL),
                    //   'depth' => new external_value(PARAM_RAW, 'email',VALUE_OPTIONAL),
                    //   'timemodified' => new external_value(PARAM_RAW, 'userimage',VALUE_OPTIONAL)
                      )
                   )
                ),
            )
        );
    } 



      /**
     * Returns description of method parameters
     * @return external_function_parameters
     */
      public static function category_subtypedata_parameters() {

        return new external_function_parameters(
            array(	

              'id' => new external_value(PARAM_INT, 'Id of the user, 0 for current user', VALUE_DEFAULT, 0)
          )
        );
    }

    /**
     * Returns welcome message
     * @return string welcome message
     */
    public static function category_subtypedata($id) {

        global $USER,$DB,$PAGE;

        $params = self::validate_parameters(self::category_subtypedata_parameters(),array('id' => $id));


        $sql = "SELECT DISTINCT cc.* FROM {course} c 
        JOIN {enrol} en ON en.courseid = c.id 
        JOIN {course_categories} cc ON cc.id = c.category
        JOIN {user_enrolments} ue ON ue.enrolid = en.id 
        WHERE ue.userid = ".$USER->id." AND c.visible = 1  AND cc.path LIKE '%".$params['id']."%'";

        $data = $DB->get_records_sql($sql,null);
        //print_object($data);die; 
        $fulldata = array();
        foreach($data as $alldata){

            $categorydata = new stdClass;
            $categorydata->id = $alldata->id;
            $categorydata->name = $alldata->name;
            $categorydata->imageurl = api_fileview($alldata->id);
            $categorydata->coursecount = user_course_count($alldata->id, $USER->id);
            $categorydata->path = $alldata->path;
            $categorydata->depth = $alldata->depth;
            $categorydata->timemodified = $alldata->timemodified;
            $fulldata[$alldata->id] = $categorydata;
        }
        if(!empty($fulldata)){

            return   [
                'statusCode' => 'NP01',
                'msg' => 'get completion data successfully',               
                'categorydata' => $fulldata,
                
            ];

        }else{

            return   [
                'statusCode' => 'NP00',
                'msg' => 'Record not found',               
                'categorydata' => [],
                
            ];
        }


        
    }

    /**
     * Returns description of method result value
     * @return external_description
     */
    public static function category_subtypedata_returns() {
        return new external_single_structure(
            array(          
                'statusCode'=> new external_value(PARAM_RAW, 'statusCode'),
                'msg' => new external_value(PARAM_RAW, 'statusCode message'),					
                'categorydata' => new external_multiple_structure(
                    new external_single_structure(
                       array(
                          'id' => new external_value(PARAM_INT, 'id',VALUE_OPTIONAL),
                          'name' => new external_value(PARAM_RAW, 'name',VALUE_OPTIONAL),
                          'imageurl' => new external_value(PARAM_RAW, 'imageurl',VALUE_OPTIONAL),
                          'coursecount' => new external_value(PARAM_RAW, 'coursecount',VALUE_OPTIONAL),
                          'path' => new external_value(PARAM_RAW, 'lastname',VALUE_OPTIONAL),
                          'depth' => new external_value(PARAM_RAW, 'email',VALUE_OPTIONAL),
                          'timemodified' => new external_value(PARAM_RAW, 'userimage',VALUE_OPTIONAL)
                      )
                   )
                ),
            )
        );
    } 



    /**
     * Returns description of method parameters
     * @return external_function_parameters
     */
    public static function user_picture_remove_parameters() {

        return new external_function_parameters(
            array(	
               'delete' => new external_value(PARAM_BOOL, 'If we should delete the user picture', VALUE_DEFAULT, false),
               'userid' => new external_value(PARAM_INT, 'Id of the user, 0 for current user', VALUE_DEFAULT, 0)
           )
        );
    }

    /**
     * Returns welcome message
     * @return string welcome message
     */
    public static function user_picture_remove($delete,$userid) {

        global $USER,$DB,$PAGE;

        $params = self::validate_parameters(self::user_picture_remove_parameters(),array('userid' => $userid,'delete'=>$delete));

        $context = context_system::instance();
        self::validate_context($context);



        if (empty($params['userid']) or $params['userid'] == $USER->id) {
            $user = $USER;
            require_capability('moodle/user:editownprofile', $context);
        } else {
            $user = core_user::get_user($params['userid'], '*', MUST_EXIST);
            core_user::require_active_user($user);
            $personalcontext = context_user::instance($user->id);

            require_capability('moodle/user:editprofile', $personalcontext);
            if (is_siteadmin($user) and !is_siteadmin($USER)) {  // Only admins may edit other admins.
                throw new moodle_exception('useradmineditadmin');
            }
        }



        $filemanageroptions = array(
            'maxbytes' => $CFG->maxbytes,
            'subdirs' => 0,
            'maxfiles' => 1,
            'accepted_types' => 'optimised_image'
        );
        $user->deletepicture = $params['delete'];
        $user->imagefile = $params['draftitemid'];
        $success = core_user::update_picture($user, $filemanageroptions);

        $result = array(
            'success' => $success,
            'warnings' => array(),
        );
        $result['msg'] = 'Successfully Remove';
        $result['statusCode'] = 'NP01';
        if ($success) {
            $userpicture = new user_picture(core_user::get_user($user->id));
            $userpicture->size = 1; 

            $result['profileimageurl'] = $userpicture->get_url($PAGE)->out(false);
            
        }

        $userpictures = new user_picture($USER);
        $userpictures->size = 1; 
        $profileimageurls = $userpictures->get_url($PAGE);

        return $result;

    }

    /**
     * Returns description of method result value
     * @return external_description
     */
    public static function user_picture_remove_returns() {
        return new external_single_structure(
            array(
                'statusCode'=> new external_value(PARAM_RAW, 'statusCode'),
                'msg' => new external_value(PARAM_RAW, 'statusCode message'),
                'success' => new external_value(PARAM_BOOL, 'True if the image was updated, false otherwise.'),
                'profileimageurl' => new external_value(PARAM_URL, 'New profile user image url', VALUE_OPTIONAL),
                
            )
        );
    }


  /**
     * Returns description of method parameters
     * @return external_function_parameters
     */
  public static function user_leaderboard_parameters() {

    return new external_function_parameters(
        array(	'userid' => new external_value(PARAM_INT, 'userid', VALUE_DEFAULT, 0),

    )
    );
}

    /**
     * Returns welcome message
     * @return string welcome message
     */
    public static function user_leaderboard($userid) {

        global $USER,$DB,$PAGE;

        $params = self::validate_parameters(self::user_leaderboard_parameters(),array('userid' => $userid));

        $userpictures = new user_picture($USER);
        $userpictures->size = 1; 
        $profileimageurls = $userpictures->get_url($PAGE);

        $userid = $params['userid'];
        $depid = get_user_department_id($userid);

        $getRanksql = "SELECT COUNT(*) AS 'rank' FROM {local_user_leaderboard} WHERE departmentid =". $depid ." AND finalscore>=(SELECT finalscore FROM {local_user_leaderboard} WHERE userid=".$USER->id." AND finalscore != 0) ";	
        $Rank = $DB->get_record_sql($getRanksql, null);
        $finalscoresql = $DB->get_record('local_user_leaderboard',array('userid'=>$USER->id));
        $finalscore = $finalscoresql->finalscore;	
        $datA = all_leaderboard($userid);
        $alluserscore = array();
        foreach($datA as $userdata){

            if($userdata->finalscore){

             $alluserscore[] = $userdata; 
         }
     }

     if($Rank->rank){

        $rank = $Rank->rank;
        if($rank > 2 ){
            $rank = $rank-1;
        }
    }else{
        $rank = 'N/A';
    }
		//print_object($alluserscore);die;	
    return   [
        'statusCode' => 'NP01',
        'msg' => 'get completion data successfully',
        'mydata' => [
            'userid' =>$USER->id,
            'firstname' => $USER->firstname,
            'lastname' => $USER->lastname,
            'email'	   => $USER->email,
            'dep' => get_user_department_name($USER->id),
            'imageurl' => $profileimageurls->out(false),								
            'rank' => (string)$rank,
            'score' => (string)$finalscore,

        ],
        'alluserscore' => $alluserscore,

    ];

}

    /**
     * Returns description of method result value
     * @return external_description
     */
    public static function user_leaderboard_returns() {
        return new external_single_structure(
            array(          
                'statusCode'=> new external_value(PARAM_RAW, 'statusCode'),
                'msg' => new external_value(PARAM_RAW, 'statusCode message'),
                'mydata'=> new external_single_structure(
                 array(
                     'userid' => new external_value(PARAM_INT, 'userid',VALUE_OPTIONAL),
                     'firstname' => new external_value(PARAM_RAW, 'statusCode message'),
                     'lastname' => new external_value(PARAM_RAW, 'statusCode message'),
                     'email' => new external_value(PARAM_RAW, 'statusCode message'),
                     'dep' => new external_value(PARAM_RAW, 'dep',VALUE_OPTIONAL),
                     'imageurl' => new external_value(PARAM_RAW, 'statusCode message'),
                     'score' => new external_value(PARAM_RAW, 'statusCode message'),
                     'rank' => new external_value(PARAM_RAW, 'statusCode message'),
                 )),
                'alluserscore' => new external_multiple_structure(
                    new external_single_structure(
                       array(
                          'userid' => new external_value(PARAM_INT, 'userid',VALUE_OPTIONAL),
                          'dep' => new external_value(PARAM_RAW, 'dep',VALUE_OPTIONAL),
                          'rank' => new external_value(PARAM_INT, 'rank',VALUE_OPTIONAL),
                          'firstname' => new external_value(PARAM_RAW, 'firstname',VALUE_OPTIONAL),
                          'lastname' => new external_value(PARAM_RAW, 'lastname',VALUE_OPTIONAL),
                          'email' => new external_value(PARAM_RAW, 'email',VALUE_OPTIONAL),
                          'userimage' => new external_value(PARAM_RAW, 'userimage',VALUE_OPTIONAL),
                          'finalgrade' => new external_value(PARAM_RAW, 'finalgrade',VALUE_OPTIONAL),

                      )
                   )
                ),

            )
        );
    }


    /*-------------------------- 16-6-2021-----------------------*/

	 /**
     * Returns description of method parameters
     * @return external_function_parameters
     */
    public static function game_score_parameters() {

        return new external_function_parameters(
            array(	'userid' => new external_value(PARAM_INT, 'userid', VALUE_DEFAULT, 0),
              'url' => new external_value(PARAM_TEXT, 'url', VALUE_DEFAULT, null),
          )
        );
    }

    /**
     * Returns welcome message
     * @return string welcome message
     */
    public static function game_score($userid,$url) {

        global $USER,$DB;

        $params = self::validate_parameters(self::game_score_parameters(),array('userid' => $userid,'url'=>$url));


        $userid = $params['userid'];
        $url =  $params['url'];
        $parameters = '/'.$params['userid'].'.json';

        $newurl = str_replace('.json',$parameters , $url);

        $data =  file_get_contents($newurl);


        $gamedata = array();
        $gamescore = 0;

        if($data !== 'null'){

         $arrayData = json_decode($data);	
         $gamescore = $arrayData->user_score;
					//$gamescore = 20;

         $gamedata[$url]  = $gamescore;
					//echo $gamescore.'180';

     }else{

         $gamedata[$url] =  0;
         $gamescore = 0;
					//echo $gamescore.'186';
     }


     $checkdata = $DB->get_record('local_user_leaderboard',array('userid'=>$userid));

     if(!empty($checkdata)){

       if(!empty($checkdata->gamedata)){

        $arraygamedata = json_decode($checkdata->gamedata);
        $sumscore = 0;
        $currentuserscore = 0;
        foreach($arraygamedata as $gkey => $gdata){

         if($gkey == $url){

             $gamedata[$url] = $gamescore;
             $currentuserscore = $gdata;
             $sumscore += $gamescore;  
         }
					//echo $gdata; 
         $sumscore += $gdata;  
     }
     if(empty($checkdata->gamescore)){
        $sumscore += $gamescore;
    }
    $gamedata[$url] = $gamescore;

    $finalgamescore = $sumscore - $currentuserscore;

    $alldata = array_merge((array)$arraygamedata,$gamedata);
				//print_object($gamedata); 


    $sql = "UPDATE {local_user_leaderboard} SET gamedata = '" .json_encode($alldata)."',gamescore = $finalgamescore, updatetime = ".time()." WHERE userid = $userid ";


					//$DB->execute($sql,null);

    return ['statusCode'=>'NP01','msg'=>'Update Score successfully'];
}else{

    $gamedata[$url]  =  $gamescore;

    $sql = "UPDATE {local_user_leaderboard} SET gamedata = '" .json_encode($gamedata)."', gamescore =".$gamescore.", updatetime = ".time()." WHERE userid = $userid ";


					//$DB->execute($sql,null);

    return ['statusCode'=>'NP01','msg'=>'Update Score successfully'];
}





}else{
   $gamedata = json_encode($gamedata) ;
   $insertObject = new stdClass;
   $insertObject->userid = $userid;
   $insertObject->departmentid = get_user_department_id($userid);
   $insertObject->gamedata = $gamedata;
   $insertObject->gamescore = $gamescore;
   $insertObject->lmsscore = user_profile_score($userid);
   $insertObject->finalscore = user_profile_score($userid) + $gamescore;
   $insertObject->createtime = time();
   $insertObject->updatetime = time();

   $insertdata = $DB->insert_record('local_user_leaderboard', $insertObject, $returnid=true, $bulk=false);

   return ['statusCode'=>'NP01','msg'=>'Add Score successfully'];
}



}

    /**
     * Returns description of method result value
     * @return external_description
     */
    public static function game_score_returns() {
        return new external_single_structure(
            array(          

                'statusCode'=> new external_value(PARAM_RAW, 'statusCode'),
                'msg' => new external_value(PARAM_RAW, 'statusCode message')

            )
        );
    }
    /*-------------------------- 16-6-2021-----------------------*/

     /**
     * Returns description of method parameters
     * @return external_function_parameters
     */
     public static function category_detail_parameters() {

        return new external_function_parameters(
            array('userid' => new external_value(PARAM_INT, 'userid', VALUE_DEFAULT, 0))
        );
    }

    /**
     * Returns welcome message
     * @return string welcome message
     */
    public static function category_detail($userid) {
        global $USER;

        //Parameter validation
        //REQUIRED
        $params = self::validate_parameters(self::category_detail_parameters(),
            array('userid' => $userid));
        $data =  get_user_category_detail($params['userid']);

        if($data['category'][0]->id != NULL)        {

            return ['cetegorydata' => $data['category'],'statusCode'=>'NP01','msg'=>'Fetch data successfully'];
        }else{
           // die('adasd');
            return ['cetegorydata' => $data['category'],'statusCode'=>'NP00','msg'=>'Library not found'];
        }
        
       // return ['cetegorydata' => $data['category']];
    }

    /**
     * Returns description of method result value
     * @return external_description
     */
    public static function category_detail_returns() {
        return new external_single_structure(
            array(
                'cetegorydata' => new external_multiple_structure(
                    new external_single_structure(
                        array(
                            'id' => new external_value(PARAM_RAW, 'id'),
                            'name' => new external_value(PARAM_RAW, 'name', VALUE_OPTIONAL),
                            'categoey_sortname' => new external_value(PARAM_RAW, 'categoey_sortname',VALUE_OPTIONAL),
                            'imageurl' => new external_value(PARAM_RAW, 'user create/update status message'),
                            'coursecount' => new external_value(PARAM_RAW, 'user create/update status message'),
                        )
                    )
                ),
                'statusCode'=> new external_value(PARAM_RAW, 'statusCode'),
                'msg' => new external_value(PARAM_RAW, 'statusCode message')
            )
        );
    }


      /**
     * Returns description of method parameters
     * @return external_function_parameters
     */
      public static function my_course_detail_parameters() {

        return new external_function_parameters(
            array('userid' => new external_value(PARAM_INT, 'userid', VALUE_DEFAULT, 0))
        );
    }

    /**
     * Returns welcome message
     * @return string welcome message
     */
    public static function my_course_detail($userid) {
        global $USER ,$DB;

        //Parameter validation
        //REQUIRED
        $lastaccess = time();
        $params = self::validate_parameters(self::my_course_detail_parameters(),  array('userid' => $userid));

        $data =  get_user_category_detail($params['userid']);

        $DB->execute("UPDATE {user} SET  lastaccess =  '{$lastaccess}'  WHERE id = '{$USER->id}'");

        if($data['courses'][0]['id'] )        {

            return ['mycourse' => $data['courses'],'statusCode'=>'NP01','msg'=>'Fetch data successfully'];
        }else{

            return ['mycourse' => $data['courses'],'statusCode'=>'NP00','msg'=>'Course not found'];
        }

            //return ['mycourse' => $data['courses']];
    }

    /**
     * Returns description of method result value
     * @return external_description
     */
    public static function my_course_detail_returns() {
        return new external_single_structure(
            array(
                'mycourse' => new external_multiple_structure(
                    new external_single_structure(
                        array(
                            'id' => new external_value(PARAM_RAW, 'id'),
                            'fullname' => new external_value(PARAM_RAW, 'fullname'),
                            'shortname' => new external_value(PARAM_RAW, 'shortname'),
                            'summary' => new external_value(PARAM_RAW, 'summary'),
                            'sortorder' => new external_value(PARAM_RAW, 'sortorder'),
                            'visible' => new external_value(PARAM_RAW, 'visible'),
                            'imageurl' => new external_value(PARAM_RAW, 'imageurl'),
                            'categoryname' => new external_value(PARAM_RAW, 'categoryname'),
                            'categoey_sortname' => new external_value(PARAM_RAW, 'categoey_sortname',VALUE_OPTIONAL),
                            'courseprogress' => new external_value(PARAM_RAW, 'courseprogress'),
                            'is_favourite' => new external_value(PARAM_RAW, 'is_favourite'),
                            'coursestatus' => new external_value(PARAM_RAW, 'coursestatus'),
                            'lastaccess' => new external_value(PARAM_RAW, 'lastaccess'),
                            'parent_department' => new external_value(PARAM_RAW, 'parent_department'),
                        )
                    )
                ),
                'statusCode'=> new external_value(PARAM_RAW, 'statusCode'),
                'msg' => new external_value(PARAM_RAW, 'statusCode message')
            )
        );
    }


     /**
     * Returns description of method parameters
     * @return external_function_parameters
     */
     public static function course_content_parameters() {

        return new external_function_parameters(
            array('courseid' => new external_value(PARAM_INT, 'courseid', VALUE_DEFAULT, 0),
                'userid' => new external_value(PARAM_INT, 'userid', VALUE_DEFAULT, 0))
        );
    }

    /**
     * Returns welcome message
     * @return string welcome message
     */
    public static function course_content($courseid,$userid) {

        global $CFG, $DB;
        require_once($CFG->dirroot . "/course/lib.php");
        require_once($CFG->libdir . '/completionlib.php');


        //Parameter validation
        //REQUIRED
        $params = self::validate_parameters(self::course_content_parameters(),
            array('courseid' => $courseid,'userid'=>$userid,));

        $userid = $params['userid'];        
        $course = $DB->get_record('course', array('id' => $params['courseid']), '*', MUST_EXIST);

        if ($course->id != SITEID) {
            // Check course format exist.
            if (!file_exists($CFG->dirroot . '/course/format/' . $course->format . '/lib.php')) {
                throw new moodle_exception('cannotgetcoursecontents', 'webservice', '', null,
                    get_string('courseformatnotfound', 'error', $course->format));
            } else {
                require_once($CFG->dirroot . '/course/format/' . $course->format . '/lib.php');
            }
        }

        // now security checks
        $context = context_course::instance($course->id, IGNORE_MISSING);
       /*  try {
            self::validate_context($context);
        } catch (Exception $e) {
            $exceptionparam = new stdClass();
            $exceptionparam->message = $e->getMessage();
            $exceptionparam->courseid = $course->id;
            throw new moodle_exception('errorcoursecontextnotvalid', 'webservice', '', $exceptionparam);
        } */

        $canupdatecourse = has_capability('moodle/course:update', $context);

        //create return value
        $coursecontents = array();

        if ($canupdatecourse or $course->visible
            or has_capability('moodle/course:viewhiddencourses', $context)) {

            //retrieve sections
            $modinfo = get_fast_modinfo($course);
            $sections = $modinfo->get_section_info_all();
            $coursenumsections = course_get_format($course)->get_last_section_number();
            $stealthmodules = array();   // Array to keep all the modules available but not visible in a course section/topic.

            $completioninfo = new completion_info($course);

            //for each sections (first displayed to last displayed)
            $modinfosections = $modinfo->get_sections();
            foreach ($sections as $key => $section) {

                // This becomes true when we are filtering and we found the value to filter with.
                $sectionfound = false;

                // Filter by section id.
                if (!empty($filters['sectionid'])) {
                    if ($section->id != $filters['sectionid']) {
                        continue;
                    } else {
                        $sectionfound = true;
                    }
                }

                // Filter by section number. Note that 0 is a valid section number.
                if (isset($filters['sectionnumber'])) {
                    if ($key != $filters['sectionnumber']) {
                        continue;
                    } else {
                        $sectionfound = true;
                    }
                }
                $General =  get_section_name($course, $section);
                if($General != 'General'){
                // reset $sectioncontents
                // $sectionvalues = array();
                // $sectionvalues['id'] = $section->id;
                // $sectionvalues['name'] = get_section_name($course, $section);
                // $sectionvalues['visible'] = $section->visible;

                // $options = (object) array('noclean' => true);
                // list($sectionvalues['summary'], $sectionvalues['summaryformat']) =
                //         external_format_text($section->summary, $section->summaryformat,
                //                 $context->id, 'course', 'section', $section->id, $options);
                // $sectionvalues['section'] = $section->section;
                // $sectionvalues['hiddenbynumsections'] = $section->section > $coursenumsections ? 1 : 0;
                // $sectionvalues['uservisible'] = $section->uservisible;
                // if (!empty($section->availableinfo)) {
                //     $sectionvalues['availabilityinfo'] = \core_availability\info::format_info($section->availableinfo, $course);
                // }

                    $sectioncontents = array();

                // For each module of the section.
                    if (empty($filters['excludemodules']) and !empty($modinfosections[$section->section])) {

                     $totalscore = 0;
                     $total_earned = 0;
                     foreach ($modinfosections[$section->section] as $cmid) {
                        $cm = $modinfo->cms[$cmid];

                        // Stop here if the module is not visible to the user on the course main page:
                        // The user can't access the module and the user can't view the module on the course page.
                        if (!$cm->uservisible && !$cm->is_visible_on_course_page()) {
                            continue;
                        }
                        
                        // This becomes true when we are filtering and we found the value to filter with.
                        $modfound = false;

                        // Filter by cmid.
                        if (!empty($filters['cmid'])) {
                            if ($cmid != $filters['cmid']) {
                                continue;
                            } else {
                                $modfound = true;
                            }
                        }

                        // Filter by module name and id.
                        if (!empty($filters['modname'])) {
                            if ($cm->modname != $filters['modname']) {
                                continue;
                            } else if (!empty($filters['modid'])) {
                                if ($cm->instance != $filters['modid']) {
                                    continue;
                                } else {
                                    // Note that if we are only filtering by modname we don't break the loop.
                                    $modfound = true;
                                }
                            }
                        }
						//if($cm->modname != 'scorm'){
                        $module = array();

                        $modcontext = context_module::instance($cm->id);

						//*** Start Display Custom Fields ****						
						//$coursecontextid = context_course::instance($COURSE->id);
                        $customfield = $DB->get_record_sql('SELECT id FROM {customfield_field} WHERE ' . $DB->sql_compare_text('shortname') . ' = ' . $DB->sql_compare_text(':shortname'), ['shortname' => 'displaymode']);
                        $myfielddata = $DB->get_record("customfield_data", array("fieldid" => $customfield->id, "contextid" => $modcontext->id));
                        $display_mode = (int)$myfielddata->value; 
                        //*** Start Display Custom Fields
                        
                        //common info (for people being able to see the module or availability dates)

                        $module['id'] = $cm->id;
                        $module['name'] = external_format_string($cm->name, $modcontext->id);
                        $module['instance'] = $cm->instance;
                        $module['modname'] = (string) $cm->modname;
                        $module['modplural'] = (string) $cm->modplural;
                        $module['modicon'] = $cm->get_icon_url()->out(false);
                        $module['indent'] = $cm->indent;
                        $module['onclick'] = $cm->onclick;
                        $module['afterlink'] = $cm->afterlink;
                        $module['customdata'] = json_encode($cm->customdata);
                        $module['completion'] = $cm->completion;
                        $module['noviewlink'] = plugin_supports('mod', $cm->modname, FEATURE_NO_VIEW_LINK, false);
                        $module['scormurl'] = false;
                        $module['is_module_complete'] = is_module_complete($cm->id,$userid); 
                        $module['display_mode'] = $display_mode;
                        // Check module completion.
                        $completion = $completioninfo->is_enabled($cm);
                        if ($completion != COMPLETION_DISABLED) {
                            $completiondata = $completioninfo->get_data($cm, true);
                            $module['completiondata'] = array(
                                'state'         => $completiondata->completionstate,
                                'timecompleted' => $completiondata->timemodified,
                                'overrideby'    => $completiondata->overrideby,
                                'valueused'     => core_availability\info::completion_value_used($course, $cm->id)
                            );
                        }

                        $module['description'] = empty(strip_tags($cm->content))?' ':strip_tags($cm->content);

                        if (!empty($cm->showdescription) or $module['noviewlink']) {
                            // We want to use the external format. However from reading get_formatted_content(), $cm->content format is always FORMAT_HTML.

                            // $options = array('noclean' => true);
                            // list($module['description'], $descriptionformat) = external_format_text($cm->content,
                            //     FORMAT_HTML, $modcontext->id, $cm->modname, 'intro', $cm->id, $options);
                        }

                        //url of the module
                        $url = $cm->url;
                        if ($url) { //labels don't have url
                        $module['url'] = $url->out(false).'&method=mobile';
                    }

                    $canviewhidden = has_capability('moodle/course:viewhiddenactivities',
                        context_module::instance($cm->id));
                        //user that can view hidden module should know about the visibility
                    $module['visible'] = $cm->visible;
                    $module['visibleoncoursepage'] = $cm->visibleoncoursepage;
                    $module['uservisible'] = $cm->uservisible;
                    if (!empty($cm->availableinfo)) {
                        $module['availabilityinfo'] = \core_availability\info::format_info($cm->availableinfo, $course);
                    }

                    if($cm->modname == "scorm")
                    {

                        $data =  grade_get_grades($courseid, 'mod', 'scorm', $cm->instance, array($userid));



                        foreach($data as $info){

                            foreach($info as $gradedata){

                                $grades = $gradedata->grades;

                            }
                        }
						  //
						  //print_object($grades);die;
                        if(!empty($grades[$userid]->str_grade) && ($grades[$userid]->str_grade == '-'))
                        {

                          $grades[$userid]->str_grade = "0";
                      }

                      if(is_module_complete($cm->id,$userid) == 1)
                      {
                         $module['modicon'] = $CFG->wwwroot."/pix/module_icon_complete.png";
                     }
                     else{
                         $module['modicon'] = $CFG->wwwroot."/pix/module_icon_incomplete.png";
                     }
                           //$module['score'] = (string)$grades[$userid]->str_grade; 
                     $module['score'] = (string)round($grades[$userid]->str_grade); 

                     $module['maxscore'] = (string)get_scrom_maxscore($cm->id);
                     $totalscore += get_scrom_maxscore($cm->id);

						   // ** 17th March
                     $module['timecreated'] =  $cm->added;
                     $completion_status = $completioninfo->get_data($cm, true);

                     $module['timemodified'] = empty($completion_status->timemodified)? $cm->added : $completion_status->timemodified;

                     $total_earned +=  number_format((float)$grades[$userid]->str_grade, 2, '.', '');

                 }

                        // Availability date (also send to user who can see hidden module).
                 if ($CFG->enableavailability && ($canviewhidden || $canupdatecourse)) {
                    $module['availability'] = $cm->availability;
                }

                        // Return contents only if the user can access to the module.
                if ($cm->uservisible) {

                    $baseurl = 'webservice/pluginfile.php';

                            // Call $modulename_export_contents (each module callback take care about checking the capabilities).
                    require_once($CFG->dirroot . '/mod/' . $cm->modname . '/lib.php');
                    $getcontentfunction = $cm->modname.'_export_contents';
                    if (function_exists($getcontentfunction)) {
                        $contents = $getcontentfunction($cm, $baseurl);
                        $module['contentsinfo'] = array(
                            'filescount' => count($contents),
                            'filessize' => 0,
                            'lastmodified' => 0,
                            'mimetypes' => array(),
                        );


                        foreach ($contents as $content) {

                                    // Check repository file (only main file).
                            if (!isset($module['contentsinfo']['repositorytype'])) {
                                $module['contentsinfo']['repositorytype'] =
                                isset($content['repositorytype']) ? $content['repositorytype'] : '';
                            }
                            if (isset($content['filesize'])) {
                                $module['contentsinfo']['filessize'] += $content['filesize'];
                            }
                            if (isset($content['timemodified']) &&
                                ($content['timemodified'] > $module['contentsinfo']['lastmodified'])) {

                                $module['contentsinfo']['lastmodified'] = $content['timemodified'];
                        }
                        if (isset($content['mimetype'])) {
                            $module['contentsinfo']['mimetypes'][$content['mimetype']] = $content['mimetype'];
                        }
                        if($cm->modname != 'scorm'){

                          $module['type'] = $content['type'];
                          $module['filename'] = $content['filename'];
                          $module['filepath'] = $content['filepath'];
                          $module['filesize'] = $content['filesize'];
                          $module['fileurl'] = $content['fileurl'];
                          $module['timecreated'] = $content['timecreated'];
                          $module['timemodified'] = empty($content['timemodified'])?$content['timecreated']:$content['timemodified'];
                          $module['sortorder'] = $content['sortorder'];
                          $module['userid'] = $content['userid'];
                          $module['author'] = $content['author'];
                          $module['license'] = $content['license'];
                          $module['mimetype'] = $content['mimetype'];

									$defaltdata = $DB->get_record('local_resourcescore',array('courseid'=>$courseid));//print_object($defaltdata);	


                                    if($content['mimetype'] == 'application/pdf')
                                    {

                                        if(is_module_complete($cm->id,$userid) == 1)
                                        {
                                            $module['score'] = (string)$pdfscore = empty($defaltdata->pdf)?0:$defaltdata->pdf;

                                            $total_earned += $pdfscore = empty($defaltdata->pdf)?0:$defaltdata->pdf;

                                        }else{
                                            $module['score'] = '0';
                                            $total_earned += 0;
                                        }

                                        $module['maxscore'] = (string)$pdfscore = empty($defaltdata->pdf)?0:$defaltdata->pdf;
                                        $totalscore += $pdfscore = empty($defaltdata->pdf)?0:$defaltdata->pdf;
                                    }elseif($content['mimetype'] == "video/mp4")
                                    {
                                        if(is_module_complete($cm->id,$userid) == 1)
                                        {
                                            $module['score'] = (string)$videoscore = empty($defaltdata->video)?0:$defaltdata->video;
                                            $total_earned += $videoscore = empty($defaltdata->video)?0:$defaltdata->video;
                                        }else{
                                            $module['score'] = '0';
                                            $total_earned += 0;
                                        }
                                        $module['maxscore'] = (string)$videoscore = empty($defaltdata->video)?0:$defaltdata->video;
                                        $totalscore += $videoscore = empty($defaltdata->video)?0:$defaltdata->video;
                                    }elseif($cm->modname== "url")
                                    {
                                        if(is_module_complete($cm->id,$userid) == 1)
                                        {
                                            $module['score'] = (string)$urloscore = empty($defaltdata->url)?0:$defaltdata->url;
                                            $total_earned += $urloscore = empty($defaltdata->url)?0:$defaltdata->url;
                                        }else{
                                            $module['score'] = '0';
                                            $total_earned +=0;
                                        }
                                        $module['maxscore'] = (string)$urloscore = empty($defaltdata->url)?0:$defaltdata->url;
                                        $totalscore += $urloscore = empty($defaltdata->url)?0:$defaltdata->url;
                                    }elseif($content['mimetype'] == "audio/mp3")
                                    {
                                        if(is_module_complete($cm->id,$userid) == 1)
                                        {
                                            $module['score'] = (string)$audioscore = empty($defaltdata->audio)?0:$defaltdata->audio;
                                            $total_earned += $audioscore = empty($defaltdata->audio)?0:$defaltdata->audio;
                                        }else{
                                            $module['score'] = '0';
                                            $total_earned +=0;
                                        }
                                        $module['maxscore'] = (string)$audioscore = empty($defaltdata->audio)?0:$defaltdata->audio;
                                        $totalscore += $audioscore = empty($defaltdata->audio)?0:$defaltdata->audio;
                                    }elseif($cm->modname == "page")
                                    {
                                        if(is_module_complete($cm->id,$userid) == 1)
                                        {
                                            $module['score'] = (string)$pagescore = empty($defaltdata->page)?0:$defaltdata->page;
                                            $total_earned += $pagescore = empty($defaltdata->page)?0:$defaltdata->page;

                                        }else{
                                            $module['score'] = '0';
                                            $total_earned += 0;
                                        }
                                        $module['maxscore'] = (string)$pagescore = empty($defaltdata->page)?0:$defaltdata->page;
                                        $totalscore += $pagescore = empty($defaltdata->page)?0:$defaltdata->page;
                                    }

                                    if(is_module_complete($cm->id,$userid) == 1)
                                    {
                                        $module['modicon'] = $CFG->wwwroot."/pix/module_icon_complete.png";
                                    }
                                    else
                                    {
                                        $module['modicon'] = $CFG->wwwroot."/pix/module_icon_incomplete.png";
                                    }

                                }else{



                                }

                                $module['downloadlink'] = "";
                            }

                            if (empty($filters['excludecontents']) and !empty($contents)) {
                                $module['contents'] = $contents;
                            } else {
                                $module['contents'] = array();
                            }

                        }
                    }

                        // Assign result to $sectioncontents, there is an exception,
                        // stealth activities in non-visible sections for students go to a special section.
                    if (!empty($filters['includestealthmodules']) && !$section->uservisible && $cm->is_stealth()) {
                        $stealthmodules[] = $module;
                    } else {

                        $sectioncontents[] = $module;
                    }

                        // If we just did a filtering, break the loop.
                    if ($modfound) {
                        break;
                    }


                }

            }
				//echo "<pre>";
				//print_object($sectioncontents);
            $sectionvalues['modules'] = $sectioncontents;
            if(empty($sectioncontents))
            {
                $statusCode = 'NP00';
                $msg = 'No Data Found';
            }
            else
            {
              $statusCode = 'NP01';
              $msg = 'Fetch Data Successfully';
          }
                // assign result to $coursecontents
          $coursecontents[$key] = $sectionvalues;

                // Break the loop if we are filtering.
          if ($sectionfound) {
            break;
        }
    }

            // Now that we have iterated over all the sections and activities, check the visibility.
            // We didn't this before to be able to retrieve stealth activities.
    foreach ($coursecontents as $sectionnumber => $sectioncontents) {
        $section = $sections[$sectionnumber];
                // Show the section if the user is permitted to access it, OR if it's not available
                // but there is some available info text which explains the reason & should display.
        $showsection = $section->uservisible ||
        ($section->visible && !$section->available &&
            !empty($section->availableinfo));

        if (!$showsection) {
            unset($coursecontents[$sectionnumber]);
            continue;
        }

                // Remove modules information if the section is not visible for the user.
        if (!$section->uservisible) {
            $coursecontents[$sectionnumber]['modules'] = array();
        }
    }

            // Include stealth modules in special section (without any info).
    if (!empty($stealthmodules)) {
        $coursecontents[] = array(
            'id' => -1,
            'name' => '',
            'summary' => '',
            'summaryformat' => FORMAT_MOODLE,
            'modules' => $stealthmodules,
            'statusCode' => 'NP00',
            'msg'   => 'Fetch Data Successfully'

        );
    }
}
}

$categoey_data = get_category_detail($course->category);
     // print_object($sectioncontents['modules']);die;


	  //var_dump(progress::get_course_progress_percentage($course, $userid));
return [ 
    'statusCode'=>$statusCode,
    'msg' => $msg,
    'course_details' => [

        'courseid' => $course->id,
        'coursename' => $course->fullname,
        'coursecategoryname' => $categoey_data->name,
        'category_sortname' => $categoey_data->idnumber,
        'courseimgurl'=>get_course_image($course->id),
        'date' =>get_last_access_course($course->id, $userid),
        'coursedescription' => strip_tags($course->summary),
                                            'points_earned' => round($total_earned),//(int)progress::get_course_progress_percentage($course, $userid),
                                            'total_points' => $totalscore,
                                            'is_favourite' => is_favourite($course->id,$userid),
                                            'modules' => $sectioncontents['modules'],
                                            //'section'=>$coursecontents
                                        ],

                                    ];
                                }

    /**
     * Returns description of method result value
     * @return external_description
     */
    public static function course_content_returns() {
        return new external_single_structure(
            array(
                'statusCode'=> new external_value(PARAM_RAW, 'statusCode'),
                'msg' => new external_value(PARAM_RAW, 'statusCode message'),
                'course_details' => new external_single_structure(
                    array(
                        'coursename'=>new external_value(PARAM_RAW, 'coursename'),
                        'coursecategoryname'=>new external_value(PARAM_RAW, 'coursecategoryname'),
                        'category_sortname'=>new external_value(PARAM_RAW, 'category_sortname',VALUE_OPTIONAL),
                        'courseimgurl'=>new external_value(PARAM_RAW, 'courseimgurl'),
                        'date' =>new external_value(PARAM_RAW, 'date'),
                        'coursedescription' =>new external_value(PARAM_RAW, 'coursedescription'),
                        'points_earned' => new external_value(PARAM_RAW, 'points_earned'),
                        'total_points' => new external_value(PARAM_RAW, 'total_points'),
                        'is_favourite' => new external_value(PARAM_RAW, 'is_favourite'),
                        'modules' => new external_multiple_structure(
                            new external_single_structure(
                                array(
                                    'id' => new external_value(PARAM_INT, 'activity id'),
                                    'url' => new external_value(PARAM_URL, 'activity url', VALUE_OPTIONAL),
                                    'name' => new external_value(PARAM_RAW, 'activity module name'),
                                    'instance' => new external_value(PARAM_INT, 'instance id', VALUE_OPTIONAL),
                                    'description' => new external_value(PARAM_RAW, 'activity description',VALUE_OPTIONAL),
                                    'visible' => new external_value(PARAM_INT, 'is the module visible', VALUE_OPTIONAL),
                                    'uservisible' => new external_value(PARAM_BOOL, 'Is the module visible for the user?',
                                        VALUE_OPTIONAL),
                                    'availabilityinfo' => new external_value(PARAM_RAW, 'Availability information.',
                                        VALUE_OPTIONAL),
                                    'visibleoncoursepage' => new external_value(PARAM_INT, 'is the module visible on course page',
                                        VALUE_OPTIONAL),
                                    'modicon' => new external_value(PARAM_URL, 'activity icon url'),
                                    'modname' => new external_value(PARAM_PLUGIN, 'activity module type'),
                                    'modplural' => new external_value(PARAM_TEXT, 'activity module plural name'),
                                    'availability' => new external_value(PARAM_RAW, 'module availability settings', VALUE_OPTIONAL),
                                    'indent' => new external_value(PARAM_INT, 'number of identation in the site'),
                                    'onclick' => new external_value(PARAM_RAW, 'Onclick action.', VALUE_OPTIONAL),
                                    'afterlink' => new external_value(PARAM_RAW, 'After link info to be displayed.',
                                        VALUE_OPTIONAL),
                                    'customdata' => new external_value(PARAM_RAW, 'Custom data (JSON encoded).', VALUE_OPTIONAL),
                                    'noviewlink' => new external_value(PARAM_BOOL, 'Whether the module has no view page',
                                        VALUE_OPTIONAL),
                                    'scormurl' => new external_value(PARAM_BOOL, 'Whether the module has no view page',
                                        VALUE_OPTIONAL),
                                    'is_module_complete' => new external_value(PARAM_INT, 'Whether the module has no view page',
                                        VALUE_OPTIONAL),
                                    'display_mode' => new external_value(PARAM_INT, 'Whether the module has no view page',
                                        VALUE_OPTIONAL),		
                                    'completion' => new external_value(PARAM_INT, 'Type of completion tracking:
                                        0 means none, 1 manual, 2 automatic.', VALUE_OPTIONAL),

                                    'type'=> new external_value(PARAM_TEXT, 'a file or a folder or external link',VALUE_OPTIONAL),
                                    'filename'=> new external_value(PARAM_FILE, 'filename',VALUE_OPTIONAL),
                                    'filepath'=> new external_value(PARAM_PATH, 'filepath',VALUE_OPTIONAL),
                                    'filesize'=> new external_value(PARAM_INT, 'filesize',VALUE_OPTIONAL),
                                    'fileurl' => new external_value(PARAM_URL, 'downloadable file url', VALUE_OPTIONAL),
                                    'content' => new external_value(PARAM_RAW, 'Raw content, will be used when type is content', VALUE_OPTIONAL),
                                    'timecreated' => new external_value(PARAM_INT, 'Time created',VALUE_OPTIONAL),
                                    'timemodified' => new external_value(PARAM_INT, 'Time modified',VALUE_OPTIONAL),
                                    'sortorder' => new external_value(PARAM_INT, 'Content sort order',VALUE_OPTIONAL),
                                    'mimetype' => new external_value(PARAM_RAW, 'File mime type.', VALUE_OPTIONAL),
                                    'downloadlink' => new external_value(PARAM_RAW, 'download file', VALUE_OPTIONAL),
                                    'score' => new external_value(PARAM_RAW, 'score', VALUE_OPTIONAL),
                                    'maxscore' => new external_value(PARAM_RAW, 'maxscore', VALUE_OPTIONAL),


                                )
), 'list of module'
), 

                                            // 'section' => new external_multiple_structure(
                                            //     new external_single_structure(
                                            //     array(
                                            //         // 'id' => new external_value(PARAM_INT, 'Section ID'),
                                            //         // 'name' => new external_value(PARAM_TEXT, 'Section name'),
                                            //         // 'visible' => new external_value(PARAM_INT, 'is the section visible', VALUE_OPTIONAL),
                                            //         // 'summary' => new external_value(PARAM_RAW, 'Section description'),
                                            //         // 'summaryformat' => new external_format_value('summary'),
                                            //         // 'section' => new external_value(PARAM_INT, 'Section number inside the course', VALUE_OPTIONAL),
                                            //         // 'hiddenbynumsections' => new external_value(PARAM_INT, 'Whether is a section hidden in the course format',
                                            //         //                                             VALUE_OPTIONAL),
                                            //         // 'uservisible' => new external_value(PARAM_BOOL, 'Is the section visible for the user?', VALUE_OPTIONAL),
                                            //         // 'availabilityinfo' => new external_value(PARAM_RAW, 'Availability information.', VALUE_OPTIONAL),

                                            //         'modules' => new external_multiple_structure(
                                            //                 new external_single_structure(
                                            //                     array(
                                            //                         'id' => new external_value(PARAM_INT, 'activity id'),
                                            //                         'url' => new external_value(PARAM_URL, 'activity url', VALUE_OPTIONAL),
                                            //                         'name' => new external_value(PARAM_RAW, 'activity module name'),
                                            //                         'instance' => new external_value(PARAM_INT, 'instance id', VALUE_OPTIONAL),
                                            //                         'description' => new external_value(PARAM_RAW, 'activity description', VALUE_OPTIONAL),
                                            //                         'visible' => new external_value(PARAM_INT, 'is the module visible', VALUE_OPTIONAL),
                                            //                         'uservisible' => new external_value(PARAM_BOOL, 'Is the module visible for the user?',
                                            //                             VALUE_OPTIONAL),
                                            //                         'availabilityinfo' => new external_value(PARAM_RAW, 'Availability information.',
                                            //                             VALUE_OPTIONAL),
                                            //                         'visibleoncoursepage' => new external_value(PARAM_INT, 'is the module visible on course page',
                                            //                             VALUE_OPTIONAL),
                                            //                         'modicon' => new external_value(PARAM_URL, 'activity icon url'),
                                            //                         'modname' => new external_value(PARAM_PLUGIN, 'activity module type'),
                                            //                         'modplural' => new external_value(PARAM_TEXT, 'activity module plural name'),
                                            //                         'availability' => new external_value(PARAM_RAW, 'module availability settings', VALUE_OPTIONAL),
                                            //                         'indent' => new external_value(PARAM_INT, 'number of identation in the site'),
                                            //                         'onclick' => new external_value(PARAM_RAW, 'Onclick action.', VALUE_OPTIONAL),
                                            //                         'afterlink' => new external_value(PARAM_RAW, 'After link info to be displayed.',
                                            //                             VALUE_OPTIONAL),
                                            //                         'customdata' => new external_value(PARAM_RAW, 'Custom data (JSON encoded).', VALUE_OPTIONAL),
                                            //                         'noviewlink' => new external_value(PARAM_BOOL, 'Whether the module has no view page',
                                            //                             VALUE_OPTIONAL),
                                            //                             'scormurl' => new external_value(PARAM_BOOL, 'Whether the module has no view page',
                                            //                             VALUE_OPTIONAL),
                                            //                         'completion' => new external_value(PARAM_INT, 'Type of completion tracking:
                                            //                             0 means none, 1 manual, 2 automatic.', VALUE_OPTIONAL),
                                            //                         // 'completiondata' => new external_single_structure(
                                            //                         //     array(
                                            //                         //         'state' => new external_value(PARAM_INT, 'Completion state value:
                                            //                         //             0 means incomplete, 1 complete, 2 complete pass, 3 complete fail'),
                                            //                         //         'timecompleted' => new external_value(PARAM_INT, 'Timestamp for completion status.'),
                                            //                         //         'overrideby' => new external_value(PARAM_INT, 'The user id who has overriden the
                                            //                         //             status.'),
                                            //                         //         'valueused' => new external_value(PARAM_BOOL, 'Whether the completion status affects
                                            //                         //             the availability of another activity.', VALUE_OPTIONAL),
                                            //                         //     ), 'Module completion data.', VALUE_OPTIONAL
                                            //                         // ),
                                            //                         'type'=> new external_value(PARAM_TEXT, 'a file or a folder or external link'),
                                            //                         'filename'=> new external_value(PARAM_FILE, 'filename'),
                                            //                         'filepath'=> new external_value(PARAM_PATH, 'filepath'),
                                            //                         'filesize'=> new external_value(PARAM_INT, 'filesize'),
                                            //                         'fileurl' => new external_value(PARAM_URL, 'downloadable file url', VALUE_OPTIONAL),
                                            //                         'content' => new external_value(PARAM_RAW, 'Raw content, will be used when type is content', VALUE_OPTIONAL),
                                            //                         'timecreated' => new external_value(PARAM_INT, 'Time created'),
                                            //                         'timemodified' => new external_value(PARAM_INT, 'Time modified'),
                                            //                         'sortorder' => new external_value(PARAM_INT, 'Content sort order'),
                                            //                         'mimetype' => new external_value(PARAM_RAW, 'File mime type.', VALUE_OPTIONAL),
											// 						'downloadlink' => new external_value(PARAM_RAW, 'download file', VALUE_OPTIONAL),
                                            //                         //'contents' => new exte rnal_multiple_structure(
                                            //                         //       new external_single_structure(
                                            //                         //           array(
                                            //                         //               // content info
                                            //                         //               'type'=> new external_value(PARAM_TEXT, 'a file or a folder or external link'),
                                            //                         //               'filename'=> new external_value(PARAM_FILE, 'filename'),
                                            //                         //               'filepath'=> new external_value(PARAM_PATH, 'filepath'),
                                            //                         //               'filesize'=> new external_value(PARAM_INT, 'filesize'),
                                            //                         //               'fileurl' => new external_value(PARAM_URL, 'downloadable file url', VALUE_OPTIONAL),
                                            //                         //               'content' => new external_value(PARAM_RAW, 'Raw content, will be used when type is content', VALUE_OPTIONAL),
                                            //                         //               'timecreated' => new external_value(PARAM_INT, 'Time created'),
                                            //                         //               'timemodified' => new external_value(PARAM_INT, 'Time modified'),
                                            //                         //               'sortorder' => new external_value(PARAM_INT, 'Content sort order'),
                                            //                         //               'mimetype' => new external_value(PARAM_RAW, 'File mime type.', VALUE_OPTIONAL),
                                            //                         //               'isexternalfile' => new external_value(PARAM_BOOL, 'Whether is an external file.',
                                            //                         //                 VALUE_OPTIONAL),
                                            //                         //               'repositorytype' => new external_value(PARAM_PLUGIN, 'The repository type for external files.',
                                            //                         //                 VALUE_OPTIONAL),

                                            //                         //               // copyright related info
                                            //                         //               'userid' => new external_value(PARAM_INT, 'User who added this content to moodle'),
                                            //                         //               'author' => new external_value(PARAM_TEXT, 'Content owner'),
                                            //                         //               'license' => new external_value(PARAM_TEXT, 'Content license'),
                                            //                         //               'tags' => new external_multiple_structure(
                                            //                         //                    \core_tag\external\tag_item_exporter::get_read_structure(), 'Tags',
                                            //                         //                         VALUE_OPTIONAL
                                            //                         //                ),
                                            //                         //           )
                                            //                         //       ), VALUE_DEFAULT, array()
                                            //                         //   ),
                                            //                         // 'contentsinfo' => new external_single_structure(
                                            //                         //     array(
                                            //                         //         'filescount' => new external_value(PARAM_INT, 'Total number of files.'),
                                            //                         //         'filessize' => new external_value(PARAM_INT, 'Total files size.'),
                                            //                         //         'lastmodified' => new external_value(PARAM_INT, 'Last time files were modified.'),
                                            //                         //         'mimetypes' => new external_multiple_structure(
                                            //                         //             new external_value(PARAM_RAW, 'File mime type.'),
                                            //                         //             'Files mime types.'
                                            //                         //         ),
                                            //                         //         'repositorytype' => new external_value(PARAM_PLUGIN, 'The repository type for
                                            //                         //             the main file.', VALUE_OPTIONAL),
                                            //                         //     ), 'Contents summary information.', VALUE_OPTIONAL
                                            //                         // ),
                                            //                     )
                                            //                 ), 'list of module'
                                            //             ), 

                                            //     )
                                            //    )
                                            //         )
)
),


)
);
}


	  /**
     * Returns description of method parameters
     * @return external_function_parameters
     */
    public static function search_mycourse_parameters() {

        return new external_function_parameters(
            array(
                'userid' => new external_value(PARAM_INT, 'userid', VALUE_DEFAULT, 0),
                'search' => new external_value(PARAM_TEXT, 'search', VALUE_DEFAULT, NULL)
            )
        );
    }

    /**
     * Returns welcome message
     * @return string welcome message
     */
    public static function search_mycourse($userid,$search) {

        global $CFG, $DB;

        $params = self::validate_parameters(self::search_mycourse_parameters(),array('userid' => $userid,'search'=>$search));
        
        $userid = $params['userid'];
        $search = $params['search'];
        
        $data = search_my_course($userid,$search,null);
        if(empty($data['courses'])){
            return ['mycourse' => $data['courses'],'statusCode'=>'NP00','msg'=>'Course not found'];
        }else{
            return ['mycourse' => $data['courses'],'statusCode'=>'NP01','msg'=>'Fetch data successfully'];   
        }
    } 

    /**
     * Returns description of method result value
     * @return external_description
     */
    public static function search_mycourse_returns() {

        return new external_single_structure(
            array(
                'mycourse' => new external_multiple_structure(
                    new external_single_structure(
                        array(
                            'id' => new external_value(PARAM_RAW, 'id'),
                            'fullname' => new external_value(PARAM_RAW, 'fullname'),
                            'shortname' => new external_value(PARAM_RAW, 'shortname'),
                            'sortorder' => new external_value(PARAM_RAW, 'sortorder'),
                            'visible' => new external_value(PARAM_RAW, 'visible'),
                            'imageurl' => new external_value(PARAM_RAW, 'imageurl'),
                            'categoryname' => new external_value(PARAM_RAW, 'categoryname'),
                            'courseprogress' => new external_value(PARAM_RAW, 'courseprogress'),
                            'is_favourite' => new external_value(PARAM_RAW, 'is_favourite'),
                            'coursestatus' => new external_value(PARAM_RAW, 'coursestatus'),
                            'lastaccess' => new external_value(PARAM_RAW, 'lastaccess'),
                        )
                    )
                ),
                'statusCode'=> new external_value(PARAM_RAW, 'statusCode'),
                'msg' => new external_value(PARAM_RAW, 'statusCode message')
            )
        );
    }

	 /**
     * Returns description of method parameters
     * @return external_function_parameters
     */
    public static function mycourse_by_categoryid_parameters() {

        return new external_function_parameters(
            array(
                'userid' => new external_value(PARAM_INT, 'userid', VALUE_DEFAULT, 0),
                'search' => new external_value(PARAM_TEXT, 'search', VALUE_DEFAULT, NULL),
                'categoryid' => new external_value(PARAM_INT, 'categoryid', VALUE_DEFAULT, 0),
            )
        );
    }

    /**
     * Returns welcome message
     * @return string welcome message
     */
    public static function mycourse_by_categoryid($userid,$search,$categoryid) {

        global $CFG, $DB;

        $params = self::validate_parameters(self::mycourse_by_categoryid_parameters(),
            array(
                'userid' => $userid,
                'search'=>$search,
                'categoryid' =>$categoryid));
        
        $userid = $params['userid'];
        $search = $params['search'];
        $categoryid = $params['categoryid'];
        
        $data = search_my_course($userid,$search,$categoryid);
        if(empty($data['courses'])){
            return ['mycourse' => $data['courses'],'statusCode'=>'NP00','msg'=>'Course not found'];
        }else{
            return ['mycourse' => $data['courses'],'statusCode'=>'NP01','msg'=>'Fetch data successfully'];   
        }
    }

    /**
     * Returns description of method result value
     * @return external_description
     */
    public static function mycourse_by_categoryid_returns() {

        return new external_single_structure(
            array(
                'mycourse' => new external_multiple_structure(
                    new external_single_structure(
                        array(
                            'id' => new external_value(PARAM_RAW, 'id'),
                            'fullname' => new external_value(PARAM_RAW, 'fullname'),
                            'shortname' => new external_value(PARAM_RAW, 'shortname'),
                            'sortorder' => new external_value(PARAM_RAW, 'sortorder'),
                            'visible' => new external_value(PARAM_RAW, 'visible'),
                            'imageurl' => new external_value(PARAM_RAW, 'imageurl'),
                            'categoryname' => new external_value(PARAM_RAW, 'categoryname'),
                            'categoey_sortname' => new external_value(PARAM_RAW, 'categoey_sortname',VALUE_OPTIONAL),
                            'courseprogress' => new external_value(PARAM_RAW, 'courseprogress'),
                            'is_favourite' => new external_value(PARAM_RAW, 'is_favourite'),
                            'coursestatus' => new external_value(PARAM_RAW, 'coursestatus'),
                            'lastaccess' => new external_value(PARAM_RAW, 'lastaccess'),
                            'parent_department' => new external_value(PARAM_RAW, 'parent_department'),
                        )
                    )
                ),
                'statusCode'=> new external_value(PARAM_RAW, 'statusCode'),
                'msg' => new external_value(PARAM_RAW, 'statusCode message')
            )
        );
    }



      /**
     * Returns description of method parameters
     * @return external_function_parameters
     */
      public static function mycourses_status_data_parameters() {

        return new external_function_parameters(
            array('userid' => new external_value(PARAM_INT, 'userid', VALUE_DEFAULT, 0))
        );
    }

    /**
     * Returns welcome message
     * @return string welcome message
     */
    public static function mycourses_status_data($userid) {
        global $USER;

        //Parameter validation
        //REQUIRED
        $params = self::validate_parameters(self::mycourses_status_data_parameters(),  array('userid' => $userid));
        $data =  mycourses_status_data($params['userid']);


        return ['notstarted' => $data['notstarted'],'complete'=>$data['complete'],'pending'=>$data['pending'],'statusCode'=>'NP01','msg'=>'Fetch data successfully'];
        


    }

    /**
     * Returns description of method result value
     * @return external_description
     */
    public static function mycourses_status_data_returns() {
        return new external_single_structure(
            array(
                'notstarted' => new external_multiple_structure(
                    new external_single_structure(
                        array(
                            'id' => new external_value(PARAM_RAW, 'id'),
                            'fullname' => new external_value(PARAM_RAW, 'fullname'),
                            'shortname' => new external_value(PARAM_RAW, 'shortname'),
                            'sortorder' => new external_value(PARAM_RAW, 'sortorder'),
                            'visible' => new external_value(PARAM_RAW, 'visible'),
                            'imageurl' => new external_value(PARAM_RAW, 'imageurl'),
                            'categoryname' => new external_value(PARAM_RAW, 'categoryname'),
                            'categoey_sortname' => new external_value(PARAM_RAW, 'categoey_sortname',VALUE_OPTIONAL),
                            'courseprogress' => new external_value(PARAM_RAW, 'courseprogress'),
                            'is_favourite' => new external_value(PARAM_RAW, 'is_favourite'),
                            'lastaccess' => new external_value(PARAM_RAW, 'lastaccess'),
                        )
                    )
                ),
                'complete' => new external_multiple_structure(
                    new external_single_structure(
                        array(
                            'id' => new external_value(PARAM_RAW, 'id'),
                            'fullname' => new external_value(PARAM_RAW, 'fullname'),
                            'shortname' => new external_value(PARAM_RAW, 'shortname'),
                            'sortorder' => new external_value(PARAM_RAW, 'sortorder'),
                            'visible' => new external_value(PARAM_RAW, 'visible'),
                            'imageurl' => new external_value(PARAM_RAW, 'imageurl'),
                            'categoryname' => new external_value(PARAM_RAW, 'categoryname'),
                            'categoey_sortname' => new external_value(PARAM_RAW, 'categoey_sortname',VALUE_OPTIONAL),
                            'courseprogress' => new external_value(PARAM_RAW, 'courseprogress'),
                            'is_favourite' => new external_value(PARAM_RAW, 'is_favourite'),
                            'lastaccess' => new external_value(PARAM_RAW, 'lastaccess'),
                        )
                    )
                ),
                'pending' => new external_multiple_structure(
                    new external_single_structure(
                        array(
                            'id' => new external_value(PARAM_RAW, 'id'),
                            'fullname' => new external_value(PARAM_RAW, 'fullname'),
                            'shortname' => new external_value(PARAM_RAW, 'shortname'),
                            'sortorder' => new external_value(PARAM_RAW, 'sortorder'),
                            'visible' => new external_value(PARAM_RAW, 'visible'),
                            'imageurl' => new external_value(PARAM_RAW, 'imageurl'),
                            'categoryname' => new external_value(PARAM_RAW, 'categoryname'),
                            'categoey_sortname' => new external_value(PARAM_RAW, 'categoey_sortname',VALUE_OPTIONAL),
                            'courseprogress' => new external_value(PARAM_RAW, 'courseprogress'),
                            'is_favourite' => new external_value(PARAM_RAW, 'is_favourite'),
                            'lastaccess' => new external_value(PARAM_RAW, 'lastaccess'),
                        )
                    )
                ),
                'statusCode'=> new external_value(PARAM_RAW, 'statusCode'),
                'msg' => new external_value(PARAM_RAW, 'statusCode message')
            )
        );
    }


	 /**
     * Returns description of method parameters
     * @return external_function_parameters
     */
    public static function get_favourit_course_data_parameters() {

        return new external_function_parameters(
            array(
                'userid' => new external_value(PARAM_INT, 'userid', VALUE_DEFAULT, 0)

            )
        );
    }

    /**
     * Returns welcome message
     * @return string welcome message
     */
    public static function get_favourit_course_data($userid) {


        $params = self::validate_parameters(self::get_favourit_course_data_parameters(),
            array(
                'userid' => $userid,
            ));


        $userid = $params['userid'];      
        
        $data = get_favourit_course($userid);
        // print_object($data['favourite']);die;
        if(empty($data['favourite'])){
            return ['favourit' => $data['favourite'],'statusCode'=>'NP00','msg'=>'Course not found'];
        }else{
            return ['favourit' => $data['favourite'],'statusCode'=>'NP01','msg'=>'Fetch data successfully'];   
        }
    }

    /**
     * Returns description of method result value
     * @return external_description
     */
    public static function get_favourit_course_data_returns() {

        return new external_single_structure(
            array(
                'favourit' => new external_multiple_structure(
                    new external_single_structure(
                        array(
                            'id' => new external_value(PARAM_RAW, 'id'),
                            'fullname' => new external_value(PARAM_RAW, 'fullname'),
                            'shortname' => new external_value(PARAM_RAW, 'shortname'),
                            'sortorder' => new external_value(PARAM_RAW, 'sortorder'),
                            'visible' => new external_value(PARAM_RAW, 'visible'),
                            'imageurl' => new external_value(PARAM_RAW, 'imageurl'),
                            'categoryname' => new external_value(PARAM_RAW, 'categoryname'),
                            'categoey_sortname' => new external_value(PARAM_RAW, 'categoey_sortname',VALUE_OPTIONAL),
                            'courseprogress' => new external_value(PARAM_RAW, 'courseprogress'),
                            'lastaccess' => new external_value(PARAM_RAW, 'lastaccess'),
                            'is_course_complete' => new external_value(PARAM_RAW, 'is_course_complete'),
                            'parent_department' => new external_value(PARAM_RAW, 'parent_department'),
                        )
                    )
                ),
                'statusCode'=> new external_value(PARAM_RAW, 'statusCode'),
                'msg' => new external_value(PARAM_RAW, 'statusCode message')
            )
        );
    }




     /**
     * Describes the parameters for get_scorms_by_courses.
     *
     * @return external_function_parameters
     * @since Moodle 3.0
     */
     public static function get_scorms_by_courses_parameters() {
        return new external_function_parameters (
            array(
                'courseids' =>   new external_value(PARAM_INT, 'course id'),
                'cmid' =>   new external_value(PARAM_INT, 'cmid')
                
            )
        );
    }

    /**
     * Returns a list of scorms in a provided list of courses,
     * if no list is provided all scorms that the user can view will be returned.
     *
     * @param array $courseids the course ids
     * @return array the scorm details
     * @since Moodle 3.0
     */
    public static function get_scorms_by_courses($courseids = array(),$cmid) {
        global $CFG;
        
        $returnedscorms = array();
        $warnings = array();

        $params = self::validate_parameters(self::get_scorms_by_courses_parameters(), array('courseids' => $courseids,'cmid'=>$cmid));
        $params['courseids'] = array($params['courseids']);
        $cmid = $params['cmid'];
        $courses = array();
        if (empty($params['courseids'])) {
            $courses = enrol_get_my_courses();
            $params['courseids'] = array_keys($courses);
        }
        
        // Ensure there are courseids to loop through.
        if (!empty($params['courseids'])) {

            list($courses, $warnings) = external_util::validate_courses($params['courseids'], $courses);

            // Get the scorms in this course, this function checks users visibility permissions.
            // We can avoid then additional validate_context calls.
            $scorms = get_all_instances_in_courses("scorm", $courses);

            $fs = get_file_storage();
            foreach ($scorms as $scorm) {

                $context = context_module::instance($scorm->coursemodule);
                if($cmid == $scorm->coursemodule){
                // Entry to return.
                    $module = array();

                // First, we return information that any user can see in (or can deduce from) the web interface.
                    $module['id'] = $scorm->id;
                    $module['coursemodule'] = $scorm->coursemodule;
                    $module['course'] = $scorm->course;
                    $module['name']  = external_format_string($scorm->name, $context->id);
                    $options = array('noclean' => true);
                    list($module['intro'], $module['introformat']) =
                    external_format_text($scorm->intro, $scorm->introformat, $context->id, 'mod_scorm', 'intro', null, $options);
                    $module['introfiles'] = external_util::get_area_files($context->id, 'mod_scorm', 'intro', false, false);

                // Check if the SCORM open and return warnings if so.
                    list($open, $openwarnings) = scorm_get_availability_status($scorm, true, $context);

                    if (!$open) {
                        foreach ($openwarnings as $warningkey => $warningdata) {
                            $warnings[] = array(
                                'item' => 'scorm',
                                'itemid' => $scorm->id,
                                'warningcode' => $warningkey,
                                'message' => get_string($warningkey, 'scorm', $warningdata)
                            );
                        }
                    } else {
                        $module['packagesize'] = 0;
                    // SCORM size.
                        if ($scorm->scormtype === SCORM_TYPE_LOCAL or $scorm->scormtype === SCORM_TYPE_LOCALSYNC) {
                            if ($packagefile = $fs->get_file($context->id, 'mod_scorm', 'package', 0, '/', $scorm->reference)) {
                                $module['packagesize'] = $packagefile->get_filesize();
                            // Download URL.
                                $module['packageurl'] = moodle_url::make_webservice_pluginfile_url(
                                    $context->id, 'mod_scorm', 'package', 0, '/', $scorm->reference)->out(false);
                            }
                        }

                        $module['protectpackagedownloads'] = get_config('scorm', 'protectpackagedownloads');

                        $viewablefields = array('version', 'maxgrade', 'grademethod', 'whatgrade', 'maxattempt', 'forcecompleted',
                            'forcenewattempt', 'lastattemptlock', 'displayattemptstatus', 'displaycoursestructure',
                            'sha1hash', 'md5hash', 'revision', 'launch', 'skipview', 'hidebrowse', 'hidetoc', 'nav',
                            'navpositionleft', 'navpositiontop', 'auto', 'popup', 'width', 'height', 'timeopen',
                            'timeclose', 'displayactivityname', 'scormtype', 'reference');

                    // Check additional permissions for returning optional private settings.
                        if (has_capability('moodle/course:manageactivities', $context)) {

                            $additionalfields = array('updatefreq', 'options', 'completionstatusrequired', 'completionscorerequired',
                              'completionstatusallscos', 'autocommit', 'timemodified', 'section', 'visible',
                              'groupmode', 'groupingid');
                            $viewablefields = array_merge($viewablefields, $additionalfields);

                        }

                        foreach ($viewablefields as $field) {
                            $module[$field] = $scorm->{$field};
                        }
                    }

                    $returnedscorms[] = $module;
                }
            }
        }

        $result = array();
        $result['scorms'] = $returnedscorms;
        $result['warnings'] = $warnings;

        if(empty($returnedscorms)){
            $result['statusCode'] = 'NP00';
            $result['msg'] = 'data not found';
        }else{
            $result['statusCode'] = 'NP01';
            $result['msg'] = 'Fetch data successfully';
        }

        return $result;
    }

    /**
     * Describes the get_scorms_by_courses return value.
     *
     * @return external_single_structure
     * @since Moodle 3.0
     */
    public static function get_scorms_by_courses_returns() {

        return new external_single_structure(
            array(
                'scorms' => new external_multiple_structure(
                    new external_single_structure(
                        array(
                            'id' => new external_value(PARAM_INT, 'SCORM id'),
                            'coursemodule' => new external_value(PARAM_INT, 'Course module id'),
                            'course' => new external_value(PARAM_INT, 'Course id'),
                            'name' => new external_value(PARAM_RAW, 'SCORM name'),
                            'intro' => new external_value(PARAM_RAW, 'The SCORM intro'),
                            'introformat' => new external_format_value('intro'),
                            'introfiles' => new external_files('Files in the introduction text', VALUE_OPTIONAL),
                            'packagesize' => new external_value(PARAM_INT, 'SCORM zip package size', VALUE_OPTIONAL),
                            'packageurl' => new external_value(PARAM_URL, 'SCORM zip package URL', VALUE_OPTIONAL),
                            'version' => new external_value(PARAM_NOTAGS, 'SCORM version (SCORM_12, SCORM_13, SCORM_AICC)',
                                VALUE_OPTIONAL),
                            'maxgrade' => new external_value(PARAM_INT, 'Max grade', VALUE_OPTIONAL),
                            'grademethod' => new external_value(PARAM_INT, 'Grade method', VALUE_OPTIONAL),
                            'whatgrade' => new external_value(PARAM_INT, 'What grade', VALUE_OPTIONAL),
                            'maxattempt' => new external_value(PARAM_INT, 'Maximum number of attemtps', VALUE_OPTIONAL),
                            'forcecompleted' => new external_value(PARAM_BOOL, 'Status current attempt is forced to "completed"',
                                VALUE_OPTIONAL),
                            'forcenewattempt' => new external_value(PARAM_INT, 'Controls re-entry behaviour',
                                VALUE_OPTIONAL),
                            'lastattemptlock' => new external_value(PARAM_BOOL, 'Prevents to launch new attempts once finished',
                                VALUE_OPTIONAL),
                            'displayattemptstatus' => new external_value(PARAM_INT, 'How to display attempt status',
                                VALUE_OPTIONAL),
                            'displaycoursestructure' => new external_value(PARAM_BOOL, 'Display contents structure',
                                VALUE_OPTIONAL),
                            'sha1hash' => new external_value(PARAM_NOTAGS, 'Package content or ext path hash', VALUE_OPTIONAL),
                            'md5hash' => new external_value(PARAM_NOTAGS, 'MD5 Hash of package file', VALUE_OPTIONAL),
                            'revision' => new external_value(PARAM_INT, 'Revison number', VALUE_OPTIONAL),
                            'launch' => new external_value(PARAM_INT, 'First content to launch', VALUE_OPTIONAL),
                            'skipview' => new external_value(PARAM_INT, 'How to skip the content structure page', VALUE_OPTIONAL),
                            'hidebrowse' => new external_value(PARAM_BOOL, 'Disable preview mode?', VALUE_OPTIONAL),
                            'hidetoc' => new external_value(PARAM_INT, 'How to display the SCORM structure in player',
                                VALUE_OPTIONAL),
                            'nav' => new external_value(PARAM_INT, 'Show navigation buttons', VALUE_OPTIONAL),
                            'navpositionleft' => new external_value(PARAM_INT, 'Navigation position left', VALUE_OPTIONAL),
                            'navpositiontop' => new external_value(PARAM_INT, 'Navigation position top', VALUE_OPTIONAL),
                            'auto' => new external_value(PARAM_BOOL, 'Auto continue?', VALUE_OPTIONAL),
                            'popup' => new external_value(PARAM_INT, 'Display in current or new window', VALUE_OPTIONAL),
                            'width' => new external_value(PARAM_INT, 'Frame width', VALUE_OPTIONAL),
                            'height' => new external_value(PARAM_INT, 'Frame height', VALUE_OPTIONAL),
                            'timeopen' => new external_value(PARAM_INT, 'Available from', VALUE_OPTIONAL),
                            'timeclose' => new external_value(PARAM_INT, 'Available to', VALUE_OPTIONAL),
                            'displayactivityname' => new external_value(PARAM_BOOL, 'Display the activity name above the player?',
                                VALUE_OPTIONAL),
                            'scormtype' => new external_value(PARAM_ALPHA, 'SCORM type', VALUE_OPTIONAL),
                            'reference' => new external_value(PARAM_NOTAGS, 'Reference to the package', VALUE_OPTIONAL),
                            'protectpackagedownloads' => new external_value(PARAM_BOOL, 'Protect package downloads?',
                                VALUE_OPTIONAL),
                            'updatefreq' => new external_value(PARAM_INT, 'Auto-update frequency for remote packages',
                                VALUE_OPTIONAL),
                            'options' => new external_value(PARAM_RAW, 'Additional options', VALUE_OPTIONAL),
                            'completionstatusrequired' => new external_value(PARAM_INT, 'Status passed/completed required?',
                                VALUE_OPTIONAL),
                            'completionscorerequired' => new external_value(PARAM_INT, 'Minimum score required', VALUE_OPTIONAL),
                            'completionstatusallscos' => new external_value(PARAM_INT, 'Require all scos to return completion status', VALUE_OPTIONAL),
                            'autocommit' => new external_value(PARAM_BOOL, 'Save track data automatically?', VALUE_OPTIONAL),
                            'timemodified' => new external_value(PARAM_INT, 'Time of last modification', VALUE_OPTIONAL),
                            'section' => new external_value(PARAM_INT, 'Course section id', VALUE_OPTIONAL),
                            'visible' => new external_value(PARAM_BOOL, 'Visible', VALUE_OPTIONAL),
                            'groupmode' => new external_value(PARAM_INT, 'Group mode', VALUE_OPTIONAL),
                            'groupingid' => new external_value(PARAM_INT, 'Group id', VALUE_OPTIONAL),
                        ), 'SCORM'
)
),
    'warnings' => new external_warnings(),
    'statusCode'=> new external_value(PARAM_RAW, 'statusCode'),
    'msg' => new external_value(PARAM_RAW, 'statusCode message')
)
);
}



    /**
     * Returns description of method parameters
     *
     * @return external_function_parameters
     */
    public static function set_fav_courses_parameters() {
        return new external_function_parameters(

            array(
                'id' => new external_value(PARAM_INT, 'course ID'),
                'favourite' => new external_value(PARAM_BOOL, 'favourite status')
            )
        );
    }

    /**
     * Set the course favourite status for an array of courses.
     *
     * @param  array $courses List with course id's and favourite status.
     * @return array Array with an array of favourite courses.
     */
    public static function set_fav_courses($courses,$favourite) {
        global $USER,$DB;

        $params = self::validate_parameters(self::set_fav_courses_parameters(),
            array(
                'id' => $courses,'favourite'=>$favourite
            )
        );
       // print_r($params);die;
        $warnings = [];

        $ufservice = \core_favourites\service_factory::get_service_for_user_context(\context_user::instance($USER->id));

        $data = $DB->get_record('course',array('id'=>$params['id']));

        if(empty($data)){
            return [

                'statusCode' => 'NP00',
                'msg' => 'please enter valid course id'
            ];
        }

        $warning = [];

        $favouriteexists = $ufservice->favourite_exists('core_course', 'courses', $params['id'],
            \context_course::instance($params['id']));

        if ($params['favourite']) {
            if (!$favouriteexists) {
                try {
                    $ufservice->create_favourite('core_course', 'courses', $params['id'],
                        \context_course::instance($params['id']));
                } catch (Exception $e) {
                    $warning['courseid'] = $params['id'];
                    if ($e instanceof moodle_exception) {
                        $warning['warningcode'] = $e->errorcode;
                    } else {
                        $warning['warningcode'] = $e->getCode();
                    }
                    $warning['message'] = $e->getMessage();
                    $warnings[] = $warning;
                    $warnings[] = $warning;
                }
            } else {
                $warning['courseid'] = $params['id'];
                $warning['warningcode'] = 'coursealreadyfavourited';
                $warning['message'] = 'Course already favourited';
                $warnings[] = $warning;
            }
        } else {
            if ($favouriteexists) {
                try {
                    $ufservice->delete_favourite('core_course', 'courses', $params['id'],
                        \context_course::instance($params['id']));
                } catch (Exception $e) {
                    $warning['courseid'] = $params['id'];
                    if ($e instanceof moodle_exception) {
                        $warning['warningcode'] = $e->errorcode;
                    } else {
                        $warning['warningcode'] = $e->getCode();
                    }
                    $warning['message'] = $e->getMessage();
                    $warnings[] = $warning;
                    $warnings[] = $warning;
                }
            } else {
                $warning['courseid'] = $params['id'];
                $warning['warningcode'] = 'cannotdeletefavourite';
                $warning['message'] = 'Could not delete favourite status for course';
                $warnings[] = $warning;
            }
        }
        
        if(empty($warnings)){
            return [

                'statusCode' => 'NP01',
                'msg' =>  'Set data successfully'
            ];
        }else{

            return [

                'statusCode' => 'NP00',
                'msg' =>  $warning['message']
            ];
        }
    }

    /**
     * Returns description of method result value
     *
     * @return external_description
     */
    public static function set_fav_courses_returns() {
        return new external_single_structure(
            array(

                'statusCode'=> new external_value(PARAM_RAW, 'statusCode'),
                'msg' => new external_value(PARAM_RAW, 'statusCode message')
            )
        );
    }


    
    /**
     * Returns description of method parameters
     *
     * @return external_function_parameters
     */
    public static function course_completion_parameters() {
        return new external_function_parameters(

            array(
                'courseid' => new external_value(PARAM_INT, 'course ID'),
                'userid' => new external_value(PARAM_INT, 'course ID')

            )
        );
    }

    /**
     * Set the course favourite status for an array of courses.
     *
     * @param  array $courses List with course id's and favourite status.
     * @return array Array with an array of favourite courses.
     */
    public static function course_completion($courses,$userid) {
        global $USER;

        $params = self::validate_parameters(self::course_completion_parameters(),
            array(
                'courseid' => $courses,'userid' => $userid 
            )
        );
       // print_r($params);die;
        $courseobject = get_course($params['courseid']);
        $completion =  progress::get_course_progress_percentage($courseobject, $params['userid']);
        
        if(empty($completion)){
            return [
                'courseid'=> $params['courseid'],
                'completion' => $completion,
                'statusCode' => 'NP00',
                'msg' => 'Not start course'
            ];
        }else{

            return [
                'courseid'=> $params['courseid'],
                'completion' => $completion,
                'statusCode' => 'NP01',
                'msg' => 'get completion data successfully'
            ];
        }
    }

    /**
     * Returns description of method result value
     *
     * @return external_description
     */
    public static function course_completion_returns() {
        return new external_single_structure(
            array(
                'courseid' => new external_value(PARAM_RAW, 'courseid'),
                'completion' => new external_value(PARAM_RAW, 'completion'),
                'statusCode'=> new external_value(PARAM_RAW, 'statusCode'),
                'msg' => new external_value(PARAM_RAW, 'statusCode message')
            )
        );
    }




     /**
     * Describes the parameters for update_activity_completion_status_manually.
     *
     * @return external_function_parameters
     * @since Moodle 2.9
     */
     public static function activity_completion_status_manually_parameters() {
        return new external_function_parameters (
            array(
                'cmid' => new external_value(PARAM_INT, 'course module id'),
                'completed' => new external_value(PARAM_BOOL, 'activity completed or not'),
            )
        );
    }

    /**
     * Update completion status for the current user in an activity, only for activities with manual tracking.
     * @param  int $cmid      Course module id
     * @param  bool $completed Activity completed or not
     * @return array            Result and possible warnings
     * @since Moodle 2.9
     * @throws moodle_exception
     */
    public static function activity_completion_status_manually($cmid,  $completed) {

        // Validate and normalize parameters.
        $params = self::validate_parameters(self::activity_completion_status_manually_parameters(),
            array('cmid' => $cmid, 'completed' => $completed));
        $cmid = $params['cmid'];
        $completed = $params['completed'];

        $warnings = array();

        $context = context_module::instance($cmid);
        self::validate_context($context);
        require_capability('moodle/course:togglecompletion', $context);

        list($course, $cm) = get_course_and_cm_from_cmid($cmid);

        // Set up completion object and check it is enabled.
        $completion = new completion_info($course);
        if (!$completion->is_enabled()) {
            throw new moodle_exception('completionnotenabled', 'completion');
        }

        // Check completion state is manual.
        if ($cm->completion != COMPLETION_TRACKING_MANUAL) {
            throw new moodle_exception('cannotmanualctrack', 'error');
        }
 
        $targetstate = ($completed) ? COMPLETION_COMPLETE : COMPLETION_INCOMPLETE;
        $completion->update_state($cm, $targetstate);

        $result = array();

        $completed = $params['completed'];
        if($completed == 1)
        {
            $result['msg'] = 'Complete your activity';
        }else
        {
            $result['msg'] = 'Incomplete your activity';
        } 
        $result['statusCode'] = 'NP01';       
        $result['status'] = true;
        $result['warnings'] = $warnings;
        return $result;
    }

    /**
     * Describes the update_activity_completion_status_manually return value.
     *
     * @return external_single_structure
     * @since Moodle 2.9
     */
    public static function activity_completion_status_manually_returns() {

        return new external_single_structure(
            array(
                'statusCode'    => new external_value(PARAM_RAW, 'statusCode'),
                'msg'  => new external_value(PARAM_RAW,'msg'),
                'status'    => new external_value(PARAM_BOOL, 'status, true if success'),
                'warnings'  => new external_warnings(),
            )
        );
    }


 /**
     * Returns description of method parameters
     * @return external_function_parameters
     */
 public static function leaderboard_parameters() {

    return new external_function_parameters(
        array('timeslot' => new external_value(PARAM_TEXT, 'search', VALUE_DEFAULT, NULL),)
    );
}

    /**
     * Returns welcome message
     * @return string welcome message
     */
    public static function leaderboard($timeslot) {
        global $USER;

        $params = self::validate_parameters(self::leaderboard_parameters(),
            array('timeslot'=>$timeslot));

        $timeslot = $params['timeslot'];

        $userlist = user_leaderboard($USER->id,$timeslot);
        if($userlist){
            return   [
                'statusCode' => 'NP01',
                'msg' => 'get completion data successfully',
                'allData' => [
                    'maxgrade' => $userlist->maxgrade,
                    'mygrade' => $userlist->mydata,
                    'usergrade' => $userlist->userreport,
                    'grade_day' => $userlist->gradedata_day,                        
                    'mydata_day' => $userlist->mydata_day,
                    'grade_week' => $userlist->gradedata_week,
                    'mydata_week'=>$userlist->mydata_week,
                    'grade_month' => $userlist->gradedata_month,
                    'mydata_month' => $userlist->mydata_month,
                    'grade_quarter' => $userlist->gradedata_quarter,
                    'mydata_quarter' => $userlist->mydata_quarter
                ]
            ];
        }else{

			/*$userlist = new stdClass();  
			$userlist->maxgrade = array('' => '' );
                $userlist->mydata = array('' => '' ); 
                $userlist->userreport = array('' => '' );
                $userlist->gradedata_day = array('' => '' );                      
                $userlist->mydata_day= array('' => '' ); 
               $userlist->gradedata_week= array('' => '' );
               $userlist->mydata_week= array('' => '' ); 
               $userlist->gradedata_month= array('' => '' );
               $userlist->mydata_month= array('' => '' ); 
               $userlist->gradedata_quarter= array('' => '' );
               $userlist->mydata_quarter= array('' => '' );*/

               $asd = array("userid"=>$USER->id,
                "dep"=> "",
                "rank"=> 0,
                "firstname"=> "",
                "lastname"=> "",
                "email"=> "",
                "userimage"=> "",
                "finalgrade"=> "0");
               return   [
                'statusCode' => 'NP00',
                'msg' => 'No Data Found',
                'allData' => [                      
                  'maxgrade' => 0,
                  'mygrade' => $asd,
                  'usergrade' => [],
                  'grade_day' => [],                        
                  'mydata_day' => $asd,
                  'grade_week' => [],
                  'mydata_week'=> $asd,
                  'grade_month' => [],
                  'mydata_month' => $asd,
                  'grade_quarter' => [],
                  'mydata_quarter' => $asd
              ]
          ];
      }
  }

   /**
     * Returns description of method result value
     * @return external_description
     */
   public static function leaderboard_returns() {
    return new external_single_structure(
        array(
            'statusCode'    => new external_value(PARAM_RAW, 'statusCode'),
            'msg'  => new external_value(PARAM_RAW,'msg'),
            'allData' =>new external_single_structure(
             array(
                 'maxgrade' => new external_value(PARAM_RAW, 'maxgrade',VALUE_OPTIONAL),
                 'mygrade' =>new external_single_structure(
                     array(
                        'userid' => new external_value(PARAM_INT, 'userid',VALUE_OPTIONAL),
                        'dep' => new external_value(PARAM_RAW, 'dep',VALUE_OPTIONAL),
                        'rank' => new external_value(PARAM_INT, 'rank',VALUE_OPTIONAL),
                        'firstname' => new external_value(PARAM_RAW, 'firstname',VALUE_OPTIONAL),
                        'lastname' => new external_value(PARAM_RAW, 'lastname',VALUE_OPTIONAL),
                        'email' => new external_value(PARAM_RAW, 'email',VALUE_OPTIONAL),
                        'userimage' => new external_value(PARAM_RAW, 'userimage',VALUE_OPTIONAL),
                        'finalgrade' => new external_value(PARAM_RAW, 'finalgrade',VALUE_OPTIONAL),
                        'badge1' => new external_value(PARAM_RAW, 'badge1',VALUE_OPTIONAL),
                        'badgeunlock1' => new external_value(PARAM_RAW, 'badgeunlock1',VALUE_OPTIONAL), 
                        'badge2' => new external_value(PARAM_RAW, 'badge2',VALUE_OPTIONAL),
                        'badgeunlock2' => new external_value(PARAM_RAW, 'badgeunlock2',VALUE_OPTIONAL),
                        'badge3' => new external_value(PARAM_RAW, 'badge3',VALUE_OPTIONAL),
                        'badgeunlock3' => new external_value(PARAM_RAW, 'badgeunlock3',VALUE_OPTIONAL), 
                        'badge4' => new external_value(PARAM_RAW, 'badge4',VALUE_OPTIONAL),
                        'badgeunlock4' => new external_value(PARAM_RAW, 'badgeunlock4',VALUE_OPTIONAL), 
                        'badge5' => new external_value(PARAM_RAW, 'badge2',VALUE_OPTIONAL),
                        'badgeunlock5' => new external_value(PARAM_RAW, 'badgeunlock5',VALUE_OPTIONAL),


                    )
                 ),
                 'usergrade' =>new external_multiple_structure(
                     new external_single_structure(
                        array(
                           'userid' => new external_value(PARAM_INT, 'userid',VALUE_OPTIONAL),
                           'dep' => new external_value(PARAM_RAW, 'dep',VALUE_OPTIONAL),
                           'rank' => new external_value(PARAM_INT, 'rank',VALUE_OPTIONAL),
                           'firstname' => new external_value(PARAM_RAW, 'firstname',VALUE_OPTIONAL),
                           'lastname' => new external_value(PARAM_RAW, 'lastname',VALUE_OPTIONAL),
                           'email' => new external_value(PARAM_RAW, 'email',VALUE_OPTIONAL),
                           'userimage' => new external_value(PARAM_RAW, 'userimage',VALUE_OPTIONAL),
                           'finalgrade' => new external_value(PARAM_RAW, 'finalgrade',VALUE_OPTIONAL),

                       )
                    )
                 ),
                 'grade_day' =>new external_multiple_structure(
                    new external_single_structure(
                       array(
                          'userid' => new external_value(PARAM_INT, 'userid',VALUE_OPTIONAL),
                          'dep' => new external_value(PARAM_RAW, 'dep',VALUE_OPTIONAL),
                          'rank' => new external_value(PARAM_INT, 'rank',VALUE_OPTIONAL),
                          'firstname' => new external_value(PARAM_RAW, 'firstname',VALUE_OPTIONAL),
                          'lastname' => new external_value(PARAM_RAW, 'lastname',VALUE_OPTIONAL),
                          'email' => new external_value(PARAM_RAW, 'email',VALUE_OPTIONAL),
                          'userimage' => new external_value(PARAM_RAW, 'userimage',VALUE_OPTIONAL),
                          'finalgrade' => new external_value(PARAM_RAW, 'finalgrade',VALUE_OPTIONAL),

                      )
                   )
                ),
                 'mydata_day' =>new external_single_structure(
                    array(
                       'userid' => new external_value(PARAM_INT, 'userid',VALUE_OPTIONAL),
                       'dep' => new external_value(PARAM_RAW, 'dep',VALUE_OPTIONAL),
                       'rank' => new external_value(PARAM_INT, 'rank',VALUE_OPTIONAL),
                       'firstname' => new external_value(PARAM_RAW, 'firstname',VALUE_OPTIONAL),
                       'lastname' => new external_value(PARAM_RAW, 'lastname',VALUE_OPTIONAL),
                       'email' => new external_value(PARAM_RAW, 'email',VALUE_OPTIONAL),
                       'userimage' => new external_value(PARAM_RAW, 'userimage',VALUE_OPTIONAL),
                       'finalgrade' => new external_value(PARAM_RAW, 'finalgrade',VALUE_OPTIONAL),

                   )
                ),
                 'grade_week' =>new external_multiple_structure(
                    new external_single_structure(
                       array(
                          'userid' => new external_value(PARAM_INT, 'userid',VALUE_OPTIONAL),
                          'dep' => new external_value(PARAM_RAW, 'dep',VALUE_OPTIONAL),
                          'rank' => new external_value(PARAM_INT, 'rank',VALUE_OPTIONAL),
                          'firstname' => new external_value(PARAM_RAW, 'firstname',VALUE_OPTIONAL),
                          'lastname' => new external_value(PARAM_RAW, 'lastname',VALUE_OPTIONAL),
                          'email' => new external_value(PARAM_RAW, 'email',VALUE_OPTIONAL),
                          'userimage' => new external_value(PARAM_RAW, 'userimage',VALUE_OPTIONAL),
                          'finalgrade' => new external_value(PARAM_RAW, 'finalgrade',VALUE_OPTIONAL),

                      )
                   )
                ),
                 'mydata_week' =>new external_single_structure(
                    array(
                       'userid' => new external_value(PARAM_INT, 'userid',VALUE_OPTIONAL),
                       'dep' => new external_value(PARAM_RAW, 'dep',VALUE_OPTIONAL),
                       'rank' => new external_value(PARAM_INT, 'rank',VALUE_OPTIONAL),
                       'firstname' => new external_value(PARAM_RAW, 'firstname',VALUE_OPTIONAL),
                       'lastname' => new external_value(PARAM_RAW, 'lastname',VALUE_OPTIONAL),
                       'email' => new external_value(PARAM_RAW, 'email',VALUE_OPTIONAL),
                       'userimage' => new external_value(PARAM_RAW, 'userimage',VALUE_OPTIONAL),
                       'finalgrade' => new external_value(PARAM_RAW, 'finalgrade',VALUE_OPTIONAL),

                   )
                ),
                 'grade_month' =>new external_multiple_structure(
                    new external_single_structure(
                       array(
                          'userid' => new external_value(PARAM_INT, 'userid',VALUE_OPTIONAL),
                          'dep' => new external_value(PARAM_RAW, 'dep',VALUE_OPTIONAL),
                          'rank' => new external_value(PARAM_INT, 'rank',VALUE_OPTIONAL),
                          'firstname' => new external_value(PARAM_RAW, 'firstname',VALUE_OPTIONAL),
                          'lastname' => new external_value(PARAM_RAW, 'lastname',VALUE_OPTIONAL),
                          'email' => new external_value(PARAM_RAW, 'email',VALUE_OPTIONAL),
                          'userimage' => new external_value(PARAM_RAW, 'userimage',VALUE_OPTIONAL),
                          'finalgrade' => new external_value(PARAM_RAW, 'finalgrade',VALUE_OPTIONAL),

                      )
                   )
                ),
                 'mydata_month' =>new external_single_structure(
                    array(
                       'userid' => new external_value(PARAM_INT, 'userid',VALUE_OPTIONAL),
                       'dep' => new external_value(PARAM_RAW, 'dep',VALUE_OPTIONAL),
                       'rank' => new external_value(PARAM_INT, 'rank',VALUE_OPTIONAL),
                       'firstname' => new external_value(PARAM_RAW, 'firstname',VALUE_OPTIONAL),
                       'lastname' => new external_value(PARAM_RAW, 'lastname',VALUE_OPTIONAL),
                       'email' => new external_value(PARAM_RAW, 'email',VALUE_OPTIONAL),
                       'userimage' => new external_value(PARAM_RAW, 'userimage',VALUE_OPTIONAL),
                       'finalgrade' => new external_value(PARAM_RAW, 'finalgrade',VALUE_OPTIONAL),

                   )
                ),
                 'grade_quarter' =>new external_multiple_structure(
                    new external_single_structure(
                       array(
                          'userid' => new external_value(PARAM_INT, 'userid',VALUE_OPTIONAL),
                          'dep' => new external_value(PARAM_RAW, 'dep',VALUE_OPTIONAL),
                          'rank' => new external_value(PARAM_INT, 'rank',VALUE_OPTIONAL),
                          'firstname' => new external_value(PARAM_RAW, 'firstname',VALUE_OPTIONAL),
                          'lastname' => new external_value(PARAM_RAW, 'lastname',VALUE_OPTIONAL),
                          'email' => new external_value(PARAM_RAW, 'email',VALUE_OPTIONAL),
                          'userimage' => new external_value(PARAM_RAW, 'userimage',VALUE_OPTIONAL),
                          'finalgrade' => new external_value(PARAM_RAW, 'finalgrade',VALUE_OPTIONAL),

                      )
                   )
                ),
                 'mydata_quarter' =>new external_single_structure(
                    array(
                       'userid' => new external_value(PARAM_INT, 'userid',VALUE_OPTIONAL),
                       'dep' => new external_value(PARAM_RAW, 'dep',VALUE_OPTIONAL),
                       'rank' => new external_value(PARAM_INT, 'rank',VALUE_OPTIONAL),
                       'firstname' => new external_value(PARAM_RAW, 'firstname',VALUE_OPTIONAL),
                       'lastname' => new external_value(PARAM_RAW, 'lastname',VALUE_OPTIONAL),
                       'email' => new external_value(PARAM_RAW, 'email',VALUE_OPTIONAL),
                       'userimage' => new external_value(PARAM_RAW, 'userimage',VALUE_OPTIONAL),
                       'finalgrade' => new external_value(PARAM_RAW, 'finalgrade',VALUE_OPTIONAL),

                   )
                ),
             )
)    
)
);
}



 /**
     * Returns description of method parameters
     * @return external_function_parameters
     */
 public static function leaderboard_by_category_parameters() {

    return new external_function_parameters(
        array('categoryid' => new external_value(PARAM_INT, 'categoryid', VALUE_DEFAULT, 0),)
    );
}

    /**
     * Returns welcome message
     * @return string welcome message
     */
    public static function leaderboard_by_category($categoryid) {
        global $USER;

        $params = self::validate_parameters(self::leaderboard_by_category_parameters(),
            array('categoryid'=>$categoryid));

        $categoryid = $params['categoryid'];

        $userlist = leaderboard_by_categoryid($categoryid,$USER->id);

            //print_object($userlist); die;
			//**** 18 FEB 2021
        if($userlist){
         return   [
            'statusCode' => 'NP01',
            'msg' => 'get completion data successfully',
            'allData' => [
                'maxgrade' => $userlist->maxgrade,
                'mygrade' => $userlist->mydata,
                'usergrade' => $userlist->userreport,
                'grade_day' => $userlist->gradedata_day,                        
                'mydata_day' => $userlist->mydata_day,
                'grade_week' => $userlist->gradedata_week,
                'mydata_week'=>$userlist->mydata_week,
                'grade_month' => $userlist->gradedata_month,
                'mydata_month' => $userlist->mydata_month,
                'grade_quarter' => $userlist->gradedata_quarter,
                'mydata_quarter' => $userlist->mydata_quarter
            ]
        ];
    }else{

       $asd = array("userid"=>$USER->id,
        "dep"=> "",
        "rank"=> 0,
        "firstname"=> "",
        "lastname"=> "",
        "email"=> "",
        "userimage"=> "",
        "finalgrade"=> "0");
       return   [
        'statusCode' => 'NP00',
        'msg' => 'No Data Found',
        'allData' => [                      
          'maxgrade' => 0,
          'mygrade' => $asd,
          'usergrade' => [],
          'grade_day' => [],                        
          'mydata_day' => $asd,
          'grade_week' => [],
          'mydata_week'=> $asd,
          'grade_month' => [],
          'mydata_month' => $asd,
          'grade_quarter' => [],
          'mydata_quarter' => $asd
      ]
  ];
			//**** 18 FEB 2021			
}
}

   /**
     * Returns description of method result value
     * @return external_description
     */
   public static function leaderboard_by_category_returns() {
    return new external_single_structure(
        array(
            'statusCode'    => new external_value(PARAM_RAW, 'statusCode'),
            'msg'  => new external_value(PARAM_RAW,'msg'),
            'allData' =>new external_single_structure(
             array(
                 'maxgrade' => new external_value(PARAM_RAW, 'maxgrade',VALUE_OPTIONAL),
                 'mygrade' =>new external_single_structure(
                     array(
                        'userid' => new external_value(PARAM_INT, 'userid',VALUE_OPTIONAL),
                        'dep' => new external_value(PARAM_RAW, 'dep',VALUE_OPTIONAL),
                        'rank' => new external_value(PARAM_INT, 'rank',VALUE_OPTIONAL),
                        'firstname' => new external_value(PARAM_RAW, 'firstname',VALUE_OPTIONAL),
                        'lastname' => new external_value(PARAM_RAW, 'lastname',VALUE_OPTIONAL),
                        'email' => new external_value(PARAM_RAW, 'email',VALUE_OPTIONAL),
                        'userimage' => new external_value(PARAM_RAW, 'userimage',VALUE_OPTIONAL),
                        'finalgrade' => new external_value(PARAM_RAW, 'finalgrade',VALUE_OPTIONAL),

                    )
                 ),
                 'usergrade' =>new external_multiple_structure(
                     new external_single_structure(
                        array(
                           'userid' => new external_value(PARAM_INT, 'userid',VALUE_OPTIONAL),
                           'dep' => new external_value(PARAM_RAW, 'dep',VALUE_OPTIONAL),
                           'rank' => new external_value(PARAM_INT, 'rank',VALUE_OPTIONAL),
                           'firstname' => new external_value(PARAM_RAW, 'firstname',VALUE_OPTIONAL),
                           'lastname' => new external_value(PARAM_RAW, 'lastname',VALUE_OPTIONAL),
                           'email' => new external_value(PARAM_RAW, 'email',VALUE_OPTIONAL),
                           'userimage' => new external_value(PARAM_RAW, 'userimage',VALUE_OPTIONAL),
                           'finalgrade' => new external_value(PARAM_RAW, 'finalgrade',VALUE_OPTIONAL),

                       )
                    )
                 ),
                 'grade_day' =>new external_multiple_structure(
                    new external_single_structure(
                       array(
                          'userid' => new external_value(PARAM_INT, 'userid',VALUE_OPTIONAL),
                          'dep' => new external_value(PARAM_RAW, 'dep',VALUE_OPTIONAL),
                          'rank' => new external_value(PARAM_INT, 'rank',VALUE_OPTIONAL),
                          'firstname' => new external_value(PARAM_RAW, 'firstname',VALUE_OPTIONAL),
                          'lastname' => new external_value(PARAM_RAW, 'lastname',VALUE_OPTIONAL),
                          'email' => new external_value(PARAM_RAW, 'email',VALUE_OPTIONAL),
                          'userimage' => new external_value(PARAM_RAW, 'userimage',VALUE_OPTIONAL),
                          'finalgrade' => new external_value(PARAM_RAW, 'finalgrade',VALUE_OPTIONAL),

                      )
                   )
                ),
                 'mydata_day' =>new external_single_structure(
                    array(
                       'userid' => new external_value(PARAM_INT, 'userid',VALUE_OPTIONAL),
                       'dep' => new external_value(PARAM_RAW, 'dep',VALUE_OPTIONAL),
                       'rank' => new external_value(PARAM_INT, 'rank',VALUE_OPTIONAL),
                       'firstname' => new external_value(PARAM_RAW, 'firstname',VALUE_OPTIONAL),
                       'lastname' => new external_value(PARAM_RAW, 'lastname',VALUE_OPTIONAL),
                       'email' => new external_value(PARAM_RAW, 'email',VALUE_OPTIONAL),
                       'userimage' => new external_value(PARAM_RAW, 'userimage',VALUE_OPTIONAL),
                       'finalgrade' => new external_value(PARAM_RAW, 'finalgrade',VALUE_OPTIONAL),

                   )
                ),
                 'grade_week' =>new external_multiple_structure(
                    new external_single_structure(
                       array(
                          'userid' => new external_value(PARAM_INT, 'userid',VALUE_OPTIONAL),
                          'dep' => new external_value(PARAM_RAW, 'dep',VALUE_OPTIONAL),
                          'rank' => new external_value(PARAM_INT, 'rank',VALUE_OPTIONAL),
                          'firstname' => new external_value(PARAM_RAW, 'firstname',VALUE_OPTIONAL),
                          'lastname' => new external_value(PARAM_RAW, 'lastname',VALUE_OPTIONAL),
                          'email' => new external_value(PARAM_RAW, 'email',VALUE_OPTIONAL),
                          'userimage' => new external_value(PARAM_RAW, 'userimage',VALUE_OPTIONAL),
                          'finalgrade' => new external_value(PARAM_RAW, 'finalgrade',VALUE_OPTIONAL),

                      )
                   )
                ),
                 'mydata_week' =>new external_single_structure(
                    array(
                       'userid' => new external_value(PARAM_INT, 'userid',VALUE_OPTIONAL),
                       'dep' => new external_value(PARAM_RAW, 'dep',VALUE_OPTIONAL),
                       'rank' => new external_value(PARAM_INT, 'rank',VALUE_OPTIONAL),
                       'firstname' => new external_value(PARAM_RAW, 'firstname',VALUE_OPTIONAL),
                       'lastname' => new external_value(PARAM_RAW, 'lastname',VALUE_OPTIONAL),
                       'email' => new external_value(PARAM_RAW, 'email',VALUE_OPTIONAL),
                       'userimage' => new external_value(PARAM_RAW, 'userimage',VALUE_OPTIONAL),
                       'finalgrade' => new external_value(PARAM_RAW, 'finalgrade',VALUE_OPTIONAL),

                   )
                ),
                 'grade_month' =>new external_multiple_structure(
                    new external_single_structure(
                       array(
                          'userid' => new external_value(PARAM_INT, 'userid',VALUE_OPTIONAL),
                          'dep' => new external_value(PARAM_RAW, 'dep',VALUE_OPTIONAL),
                          'rank' => new external_value(PARAM_INT, 'rank',VALUE_OPTIONAL),
                          'firstname' => new external_value(PARAM_RAW, 'firstname',VALUE_OPTIONAL),
                          'lastname' => new external_value(PARAM_RAW, 'lastname',VALUE_OPTIONAL),
                          'email' => new external_value(PARAM_RAW, 'email',VALUE_OPTIONAL),
                          'userimage' => new external_value(PARAM_RAW, 'userimage',VALUE_OPTIONAL),
                          'finalgrade' => new external_value(PARAM_RAW, 'finalgrade',VALUE_OPTIONAL),

                      )
                   )
                ),
                 'mydata_month' =>new external_single_structure(
                    array(
                       'userid' => new external_value(PARAM_INT, 'userid',VALUE_OPTIONAL),
                       'dep' => new external_value(PARAM_RAW, 'dep',VALUE_OPTIONAL),
                       'rank' => new external_value(PARAM_INT, 'rank',VALUE_OPTIONAL),
                       'firstname' => new external_value(PARAM_RAW, 'firstname',VALUE_OPTIONAL),
                       'lastname' => new external_value(PARAM_RAW, 'lastname',VALUE_OPTIONAL),
                       'email' => new external_value(PARAM_RAW, 'email',VALUE_OPTIONAL),
                       'userimage' => new external_value(PARAM_RAW, 'userimage',VALUE_OPTIONAL),
                       'finalgrade' => new external_value(PARAM_RAW, 'finalgrade',VALUE_OPTIONAL),

                   )
                ),
                 'grade_quarter' =>new external_multiple_structure(
                    new external_single_structure(
                       array(
                          'userid' => new external_value(PARAM_INT, 'userid',VALUE_OPTIONAL),
                          'dep' => new external_value(PARAM_RAW, 'dep',VALUE_OPTIONAL),
                          'rank' => new external_value(PARAM_INT, 'rank',VALUE_OPTIONAL),
                          'firstname' => new external_value(PARAM_RAW, 'firstname',VALUE_OPTIONAL),
                          'lastname' => new external_value(PARAM_RAW, 'lastname',VALUE_OPTIONAL),
                          'email' => new external_value(PARAM_RAW, 'email',VALUE_OPTIONAL),
                          'userimage' => new external_value(PARAM_RAW, 'userimage',VALUE_OPTIONAL),
                          'finalgrade' => new external_value(PARAM_RAW, 'finalgrade',VALUE_OPTIONAL),

                      )
                   )
                ),
                 'mydata_quarter' =>new external_single_structure(
                    array(
                       'userid' => new external_value(PARAM_INT, 'userid',VALUE_OPTIONAL),
                       'dep' => new external_value(PARAM_RAW, 'dep',VALUE_OPTIONAL),
                       'rank' => new external_value(PARAM_INT, 'rank',VALUE_OPTIONAL),
                       'firstname' => new external_value(PARAM_RAW, 'firstname',VALUE_OPTIONAL),
                       'lastname' => new external_value(PARAM_RAW, 'lastname',VALUE_OPTIONAL),
                       'email' => new external_value(PARAM_RAW, 'email',VALUE_OPTIONAL),
                       'userimage' => new external_value(PARAM_RAW, 'userimage',VALUE_OPTIONAL),
                       'finalgrade' => new external_value(PARAM_RAW, 'finalgrade',VALUE_OPTIONAL),

                   )
                ),
             )
)					   
)
);
}


    /**
     * Returns description of method parameters
     * @return external_function_parameters
     */
    public static function profile_parameters() {

        return new external_function_parameters(
            array('userid' => new external_value(PARAM_TEXT, 'userid', VALUE_DEFAULT, 0))
        );
    }

    /**
     * Returns welcome message
     * @return string welcome message
     */
    public static function profile($userid) {

        global $USER,$PAGE,$OUTPUT,$DB,$CFG;
		//print_object($USER);


        $params = self::validate_parameters(self::profile_parameters(),array('userid' => $userid));

        $userpictures = new user_picture($USER);
        $userpictures->size = 1000; 
        $profileimageurls = $userpictures->get_url($PAGE);
				//	print_object( $OUTPUT->user_picture($USER, array('size'=>50)));die;
        $dep = get_user_department_name($USER->id);

        $depid = 	get_user_department_id($USER->id);  

        $coursecount = get_my_course_count($USER->id);


						//print_object($leaderboard->mydata);die;
        if($dep == null)
          $dep = "NA";



      $exrtafieled = get_user_profile_data($USER->id);
        
      if($exrtafieled['designation']->data){

       $degination = $exrtafieled['designation']->data;
   }else{
       $degination = 'None';
   }

   $gamescoreurl = 'https://spicelearn-games-score.herokuapp.com/get-games-score?deptid='.get_user_department_id($USER->id).'&uid='.$USER->id;
   $gamedata =  file_get_contents($gamescoreurl);
   $GameObjectData = json_decode($gamedata);

       // print_object($GameObjectData->total_games_score);

   $LmsScore = user_profile_score($USER->id);
			//print_object($LmsScore);die;
   $checkdata = $DB->get_record('local_user_leaderboard',array('userid'=>$USER->id));
   $finalscore = 0;

   if(empty($checkdata)){

       $insertObject = new stdClass; 
       $insertObject->userid = $USER->id;
       $insertObject->departmentid = get_user_department_id($USER->id);
       $insertObject->gamedata = '';
       $insertObject->gamescore = $GameObjectData->total_games_score;
       $insertObject->lmsscore = $LmsScore;
       $insertObject->finalscore = '';
       $insertObject->createtime = time();
       $insertObject->updatetime = TIME();

       $insertdata = $DB->insert_record('local_user_leaderboard', $insertObject, $returnid=true, $bulk=false);

       $finalscore = $LmsScore;

   }else{
    $finalscore = $LmsScore + $GameObjectData->total_games_score;
    if(empty($GameObjectData->total_games_score)){

        $sql = "UPDATE {local_user_leaderboard} SET lmsscore = $LmsScore,finalscore = $finalscore, updatetime = ".time()." WHERE userid = ".$USER->id ;

    }else{

        $sql = "UPDATE {local_user_leaderboard} SET lmsscore = $LmsScore,gamescore=$GameObjectData->total_games_score,finalscore = $finalscore, updatetime = ".time()." WHERE userid = ".$USER->id ;
    }
      

       $DB->execute($sql,null);

   }




   $getRanksql = "SELECT COUNT(*) AS 'rank' FROM {local_user_leaderboard} WHERE departmentid =". $depid ." AND finalscore>=(SELECT finalscore FROM {local_user_leaderboard} WHERE userid=".$USER->id." AND finalscore != 0)";

   $Rank = $DB->get_record_sql($getRanksql, null);

   if($Rank->rank){

      $rank = $Rank->rank; 
      if($rank > 2){
        $rank = $rank - 1;
    }
}else{
  $rank = 'N/A';
}

$bdata = get_all_badges($USER->id);   
$allbadges = array();

foreach($bdata as $badges_data){

    $allbadgesData = array();

    $allbadgesData['id'] = $badges_data->id;
    $allbadgesData['url'] = $CFG->wwwroot.'/pix/frame218.png';
    $allbadgesData['badgeunlock'] = false; 	

    if(round($finalscore) > $badges_data->score){
        $allbadgesData['url'] = $badges_data->url;
        $allbadgesData['badgeunlock'] = true; 
    }elseif(round($finalscore) > $badges_data->score){
        $allbadgesData['url'] = $badges_data->url;
        $allbadgesData['badgeunlock'] = true; 
    }elseif(round($finalscore) > $badges_data->score){
        $allbadgesData['url'] = $badges_data->url;
        $allbadgesData['badgeunlock'] = true; 
    }elseif(round($finalscore) > $badges_data->score){
        $allbadgesData['url'] = $badges_data->url;
        $allbadgesData['badgeunlock'] = true; 
    }elseif(round($finalscore) > $badges_data->score){
        $allbadgesData['url'] = $badges_data->url;
        $allbadgesData['badgeunlock'] = true; 
    }	

    $allbadges[] = $allbadgesData;
}

$context = get_context_instance (CONTEXT_SYSTEM);
$roles = get_user_roles($context, $USER->id, false);
$role = key($roles);
$rolename = $roles[$role]->shortname;

// echo json_encode($rolename);die;
return [
    'status' => 'NP01',
    'msg' => 'Fetch data successfully',
    'profiledata' => [
     'firstname' => $USER->firstname,
     'lastname' => $USER->lastname,
     'email'	   => $USER->email,
     'imageurl' => $profileimageurls->out(false),
     'department' => $dep,
     'rolename' => $rolename,
     'designation'=> $degination,
     'coursecount' => $coursecount,
     'rank' => (string)$rank,
     'score' => (string)$finalscore,
     'badges' => $allbadges,
 ]
];
}

    /**
     * Returns description of method result value
     * @return external_description
     */
    public static function profile_returns() {
        return new external_single_structure(
            array(          
                'status' =>   new external_value(PARAM_RAW, 'status',VALUE_OPTIONAL),
                'msg' => new external_value(PARAM_RAW, 'msg',VALUE_OPTIONAL),
                'profiledata' => new external_single_structure(
                    array(
                        'firstname' => new external_value(PARAM_RAW, 'firstname',VALUE_OPTIONAL),
                        'lastname' => new external_value(PARAM_RAW, 'lastname', VALUE_OPTIONAL),
                        'email' => new external_value(PARAM_RAW, 'email',VALUE_OPTIONAL),
                        'imageurl' => new external_value(PARAM_RAW, 'imageurl',VALUE_OPTIONAL),
                        'department' => new external_value(PARAM_RAW, 'department',VALUE_OPTIONAL),
                        'rolename' => new external_value(PARAM_RAW, 'rolename',VALUE_OPTIONAL),
                        'designation' => new external_value(PARAM_RAW, 'designation',VALUE_OPTIONAL),
                        'coursecount' => new external_value(PARAM_RAW, 'coursecount', VALUE_OPTIONAL),
                        'rank' => new external_value(PARAM_RAW, 'rank',VALUE_OPTIONAL),
                        'score' => new external_value(PARAM_RAW, 'score',VALUE_OPTIONAL),
                        'badges' =>new external_multiple_structure (
                            new external_single_structure(
                             array(

                                'id' => new external_value(PARAM_RAW, 'badge1',VALUE_OPTIONAL),
                                'url' => new external_value(PARAM_RAW, 'badgeunlock1',VALUE_OPTIONAL), 
                                'badgeunlock' => new external_value(PARAM_RAW, 'badge2',VALUE_OPTIONAL),
                            )
                         )
                        ),

                    )
                )

            )
        );
    }


	 /**
     * Returns description of method parameters
     * @return external_function_parameters
     */
    public static function urls_parameters() {

     return new external_function_parameters(
        array('urls' => new external_value(PARAM_TEXT, 'urls', VALUE_DEFAULT, 0))
    );
 }

    /**
     * Returns welcome message
     * @return string welcome message
     */
    public static function urls($userid) {

        global $CFG,$USER,$PAGE;


        
        return [
            'status' => 'NP01',
            'msg' => 'Fetch data successfully',
            'urls' => [
             'aboutus' => 'https://www.spicejet.com/CorporateOverview.aspx',
             'contactus' => 'https://www.spicejet.com/ContactUs.aspx',
             'privacypolicy'	=> $CFG->wwwroot.'/local/api/privacy-policy.html',
             'terms' => $CFG->wwwroot.'/local/api/terms-and-conditions.html',
             'help' => 'https://www.spicejet.com/GeneralAirTravelFaq.aspx',
             'url1' => '',
             'url2' => '',
             'url3' => '',
         ]
     ];
 }

    /**
     * Returns description of method result value
     * @return external_description
     */
    public static function urls_returns() {
        return new external_single_structure(
            array(          
                'status' =>   new external_value(PARAM_RAW, 'status',VALUE_OPTIONAL),
                'msg' => new external_value(PARAM_RAW, 'msg',VALUE_OPTIONAL),
                'urls' => new external_single_structure(
                    array(
                        'aboutus' => new external_value(PARAM_RAW, 'aboutus',VALUE_OPTIONAL),
                        'contactus' => new external_value(PARAM_RAW, 'contactus', VALUE_OPTIONAL),
                        'privacypolicy' => new external_value(PARAM_RAW, 'privacypolicy',VALUE_OPTIONAL),
                        'terms' => new external_value(PARAM_RAW, 'terms',VALUE_OPTIONAL),
                        'help' => new external_value(PARAM_RAW, 'help',VALUE_OPTIONAL),
                        'url1' => new external_value(PARAM_RAW, 'url1', VALUE_OPTIONAL),
                        'url2' => new external_value(PARAM_RAW, 'url2',VALUE_OPTIONAL),
                        'url3' => new external_value(PARAM_RAW, 'url3',VALUE_OPTIONAL),
                    )
                )

            )
        );
    }





	// *** Update Profile 04March21 *******************************
	/**
     * Returns description of method parameters
     * @return external_function_parameters
     */
    public static function profileupdate_parameters() {

        return new external_function_parameters(	

            array(
                'userid' => new external_value(PARAM_TEXT, 'userid', VALUE_DEFAULT, 0),				
                'lastname' => new external_value(PARAM_TEXT, 'lastname', VALUE_DEFAULT, NULL)
            )
        );
    }

    /**
     * Returns welcome message
     * @return string welcome message
     */
    public static function profileupdate($userid,$lname) {

        global $USER,$PAGE,$DB;

        $params = self::validate_parameters(self::profileupdate_parameters(),array('userid' => $userid,'lastname'=> $lname));

        $userpictures = new user_picture($USER);
        $userpictures->size = 1; 
        $profileimageurls = $userpictures->get_url($PAGE);

        update_user_profile($USER->id,$lname);
        $userdata = $DB->get_record('user', array('id' => $USER->id));

        $dep = get_user_department_name($USER->id);                

        $coursecount = get_my_course_count($USER->id);

        $leaderboard = user_leaderboard($USER->id,$timeslot);	

						//print_object($leaderboard->mydata);die;

        return [
            'status' => 'NP01',
            'msg' => 'Fetch data successfully',
            'profiledata' => [
             'firstname' => $userdata->firstname,
             'lastname' => $userdata->lastname,
             'email'	   => $userdata->email,
             'imageurl' => $profileimageurls->out(false),
             'department' => $dep,
             'coursecount' => $coursecount,
             'rank' => $leaderboard->mydata['rank'],
             'score' => $leaderboard->mydata['finalgrade'],
         ]
     ];
 }

    /**
     * Returns description of method result value
     * @return external_description
     */
    public static function profileupdate_returns() {
        return new external_single_structure(
            array(          
                'status' =>   new external_value(PARAM_RAW, 'status',VALUE_OPTIONAL),
                'msg' => new external_value(PARAM_RAW, 'msg',VALUE_OPTIONAL),
                'profiledata' => new external_single_structure(
                    array(
                        'firstname' => new external_value(PARAM_RAW, 'firstname',VALUE_OPTIONAL),
                        'lastname' => new external_value(PARAM_RAW, 'lastname', VALUE_OPTIONAL),
                        'email' => new external_value(PARAM_RAW, 'email',VALUE_OPTIONAL),
                        'imageurl' => new external_value(PARAM_RAW, 'imageurl',VALUE_OPTIONAL),
                        'department' => new external_value(PARAM_RAW, 'department',VALUE_OPTIONAL),
                        'coursecount' => new external_value(PARAM_RAW, 'coursecount', VALUE_OPTIONAL),
                        'rank' => new external_value(PARAM_RAW, 'rank',VALUE_OPTIONAL),
                        'score' => new external_value(PARAM_RAW, 'score',VALUE_OPTIONAL),
                    )
                )

            )
        );
    }
    // *** Update Profile 04March21 *******************************	

	//**** Start Reset password (8-APRIL21)
	    /**
     * Describes the parameters for request_password_reset.
     *
     * @return external_function_parameters
     * @since Moodle 3.4
     */
        public static function request_password_reset_parameters() {
            return new external_function_parameters(
                array(
                    'username' => new external_value(core_user::get_property_type('username'), 'User name', VALUE_DEFAULT, ''),
                    'email' => new external_value(core_user::get_property_type('email'), 'User email', VALUE_DEFAULT, ''),
                )
            );
        }

    /**
     * Requests a password reset.
     *
     * @param  string $username user name
     * @param  string $email    user email
     * @return array warnings and success status (including notices and errors while processing)
     * @since Moodle 3.4
     * @throws moodle_exception
     */
    public static function request_password_reset($username = '', $email = '') {
        global $CFG, $PAGE;
        require_once($CFG->dirroot . '/login/lib.php');

        $warnings = array();
        $params = self::validate_parameters(
            self::request_password_reset_parameters(),
            array(
                'username' => $username,
                'email' => $email,
            )
        );

        $context = context_system::instance();
        $PAGE->set_context($context);   // Needed by format_string calls.

        // Check if an alternate forgotten password method is set.
        if (!empty($CFG->forgottenpasswordurl)) {
            throw new moodle_exception('cannotmailconfirm');
        }
//print_r($params);
        /*$errors = core_login_validate_forgot_password_data($params);
		print_r($errors);
        if (!empty($errors)) {
            $status = 'dataerror';
            $notice = '';

            foreach ($errors as $itemname => $message) {
                $warnings[] = array(
                    'item' => $itemname,
                    'itemid' => 0,
                    'warningcode' => 'fielderror',
                    'message' => s($message)
                );
            }
        } else {*/
            list($status, $notice, $url) = core_login_process_password_reset($params['username'], $params['email']);
        //}
            if($status == 'emailpasswordconfirmmaybesent')
            {
                return array(
                    'status' => "NP01",
                    'msg' => strip_tags(str_replace("\n","",$notice)),
                    'warnings' => $warnings,
                );
            }
            else
            {	
                return array(
                    'status' => "NP00",
                    'msg' => strip_tags(str_replace("\n","",$notice)),
                    'warnings' => $warnings,
                );
            }
        }

    /**
     * Describes the request_password_reset return value.
     *
     * @return external_single_structure
     * @since Moodle 3.4
     */
    public static function request_password_reset_returns() {

        return new external_single_structure(
            /*array(
                'status' => new external_value(PARAM_ALPHANUMEXT, 'The returned status of the process:
                    dataerror: Error in the sent data (username or email). More information in warnings field.
                    emailpasswordconfirmmaybesent: Email sent or not (depends on user found in database).
                    emailpasswordconfirmnotsent: Failure, user not found.
                    emailpasswordconfirmnoemail: Failure, email not found.
                    emailalreadysent: Email already sent.
                    emailpasswordconfirmsent: User pending confirmation.
                    emailresetconfirmsent: Email sent.
                '),
                'notice' => new external_value(PARAM_RAW, 'Important information for the user about the process.'),
                'warnings'  => new external_warnings(),
            )*/
            array(
                'status' => new external_value(PARAM_ALPHANUMEXT, 'The returned status of the process:
                    dataerror: Error in the sent data (username or email). More information in warnings field.
                    emailpasswordconfirmmaybesent: Email sent or not (depends on user found in database).
                    emailpasswordconfirmnotsent: Failure, user not found.
                    emailpasswordconfirmnoemail: Failure, email not found.
                    emailalreadysent: Email already sent.
                    emailpasswordconfirmsent: User pending confirmation.
                    emailresetconfirmsent: Email sent.
                    '),
                'msg' => new external_value(PARAM_RAW, 'Important information for the user about the process.'),
                'warnings'  => new external_warnings(),
            )
        );
    }
	//**** End Reset Password (8 APRIL 21)

	//***** Start User Feedback API 08APRIL 2021    
    /**
     * Returns description of method parameters
     *
     * @return external_function_parameters
     */
    public static function feedback_parameters() {
        return new external_function_parameters(

            array(                            
                'userid' => new external_value(PARAM_INT, 'Logged in user ID'),
                'courseid' => new external_value(PARAM_INT, 'Course ID'),
                'comment' => new external_value(PARAM_TEXT, 'Feedback Comment'),
                'category' => new external_value(PARAM_TEXT, 'Feedback category'),
                'byte_code' => new external_value(PARAM_RAW, 'Feedback screenshot')
            )
        );
    }

    /**
     * Set the course favourite status for an array of courses.
     *
     * @param  array $courses List with course id's and favourite status.
     * @return array Array with an array of favourite courses.
     */
    public static function feedback($userid,$courseid,$comment,$category,$byte_code) {
        global $USER, $CFG, $DB;

        require_once("$CFG->libdir/gdlib.php");
        require_once($CFG->dirroot . '/webservice/lib.php');

        $params = self::validate_parameters(self::feedback_parameters(),
            array(
                'userid' => $userid,'courseid' => $courseid,'comment' => $comment,'category' => $category,'byte_code' => $byte_code
            )
        );    

 //****** 7APRIL 2021 Feedback
        $couse_name = get_course_name($params['courseid']);
        $userdata = $DB->get_record('user', array('id' => $params['userid']));
        $itemid = 0; 
        $context = context_user::instance($params['userid']);
        $fs = get_file_storage();
        $files = array();
        $fs = get_file_storage();
        if ($itemid <= 0) {
            $itemid = file_get_unused_draft_itemid();}
            $byte_code = $params['byte_code'];	
            $byte_code = str_replace("[","",$byte_code);
            $byte_code = str_replace("]","",$byte_code);
            $code = explode(",",$byte_code);
            $image = implode(array_map('chr',$code));
            $image_name = md5(uniqid($itemid, true));
            $filename = $image_name . '.' . 'png';
            $path = $CFG->dirroot."/local/api/feedback/".$filename;
            file_put_contents($path, $image);
//$ok = mail("sanjeev.kumar@netprophetsglobal.com","testing","purpose");
//*** Mail Body
// Recipient 
            $to = 'arvindk.sharma@netprophetsglobal.com,ayush@netprophetsglobal.com,sanjeev.kumar@netprophetsglobal.com,er.sanjeev.php@gmail.com';  
// Sender 
            $from = $userdata->email;
            $fromName = $userdata->firstname; 
// Email subject 
            $subject = 'Feedback From SpiceLearn';  
// Email body content 


            $htmlContent = ' 
            <p>Student Name: '.$userdata->firstname.'</p>
            <p>Student Email Id: '.$userdata->email.'</p>
            <p>Category: '.$params["category"].'</p>
            <p>Course: '.$couse_name.'</p> 
            <p>Comment: '.$params["comment"].'</p>';

// Header for sender info 
            $headers = "From: $fromName"." <".$from.">"; 

// Boundary  
            $semi_rand = md5(time());  
            $mime_boundary = "==Multipart_Boundary_x{$semi_rand}x";  

// Headers for attachment  
            $headers = "\nMIME-Version: 1.0\n" . "Content-Type: multipart/mixed;\n" . " boundary=\"{$mime_boundary}\""; 

// Multipart boundary  
            $message = "--{$mime_boundary}\n" . "Content-Type: text/html; charset=\"UTF-8\"\n" . 
            "Content-Transfer-Encoding: 7bit\n\n" . $htmlContent . "\n\n";  

// Preparing attachment 
            if(!empty($path) > 0){ 
                if(is_file($path)){ 
                    $message .= "--{$mime_boundary}\n"; 
                    $fp =    @fopen($path,"rb"); 
                    $data =  @fread($fp,filesize($path)); 

                    @fclose($fp); 
                    $data = chunk_split(base64_encode($data)); 
                    $message .= "Content-Type: application/octet-stream; name=\"".basename($path)."\"\n" .  
                    "Content-Description: ".basename($path)."\n" . 
                    "Content-Disposition: attachment;\n" . " filename=\"".basename($path)."\"; size=".filesize($path).";\n" .  
                    "Content-Transfer-Encoding: base64\n\n" . $data . "\n\n"; 
                } 
            } 
            $message .= "--{$mime_boundary}--"; 
            $returnpath = "-f" . $from; 

// Send email 
            mail("ayush@netprophetsglobal.com","testing","purpose");

            $mail = mail($to, $subject, $message, $headers, $returnpath);  
 //email_to_user($to, $from, $subject, $message, $message, ", ", true);
// Email sending status 
//$mail?"<h1>Email Sent Successfully!</h1>":"<h1>Email sending failed.</h1>";
        //unlink($path);
        //*** Mail Body
		//****** 7APRIL 2021
            if($mail == true){
		// Capture Feedback Data.
                $data = new stdClass();
                $data->userid = $params['userid'];
                $data->courseid = $params['userid'];
                $data->feedback_comment = $params['comment'];
                $data->feedback_category = $params['category'];
                $data->attachment = $filename;      
                $data->id = $DB->insert_record('feedback_mobile', $data);	

                return [           
                    'statusCode' => 'NP01',
                    'msg' => 'Feedback email sent successfully.'
                ];

            }else{

              return [                    
                'statusCode' => 'NP00',
                'msg' => 'There is something wrong!'
            ];       
        }
    }
    /**
     * Returns description of method result value
     *
     * @return external_description
     */
    public static function feedback_returns() {
        return new external_single_structure(
            array(              
                'statusCode'=> new external_value(PARAM_RAW, 'statusCode'),
                'msg' => new external_value(PARAM_RAW, 'statusCode message')
            )
        );
    }

	//***** End User Feedback API 08APRIL 2021 

    //custome certificate created by zeeshan 31-03-2022

    public static function custom_get_certificate_parameters() {
        return new external_function_parameters(
            array(
                'coursemoduleid' => new external_value(PARAM_RAW, 'coursemoduleid',VALUE_OPTIONAL),
            )
        );
    }
    public static function custom_get_certificate($coursemoduleid) {
        global $CFG, $USER, $DB;
        
        $params = self::validate_parameters(self::custom_get_certificate_parameters(),array('coursemoduleid' => $coursemoduleid));

        $returndata = array();
        $query_course_module = $DB->get_record('course_modules', array('id' => $coursemoduleid));

        if ($query_course_module) {

            $certificateid = $query_course_module->instance;
            $certificate = $DB->get_record_sql("SELECT * FROM {customcert} WHERE id='".$certificateid."'");  
            $issue = 0;
            $cert_url = '';
            $certificate_issue = $DB->get_record_sql("SELECT * FROM {customcert} as c INNER JOIN {customcert_issues} as ci on c.id=ci.customcertid  WHERE c.id=$certificate->id");
            if ($certificate_issue) {
                $issue = 1;
                $cert_url = $CFG->wwwroot.'/local/api/download-certificate.php?id='.$certificate->id.'&downloadown=true&userid='.$USER->id;
            }
            $certificate->certificate_issue = $issue; 
            $certificate->certificate_url = $cert_url;
            $data[] = array(
                'id'=>$certificate->id,
                'course'=>$certificate->course,
                'templateid'=>$certificate->templateid,
                'name'=>$certificate->name,
                'intro'=>$certificate->intro,
                'introformat'=>$certificate->introformat,
                'timecreated'=>$certificate->timecreated,
                'timemodified'=>$certificate->timemodified,
                'certificate_issue'=>$certificate->certificate_issue,
                'certificate_url'=>$certificate->certificate_url
            );

            $result = array(
               'success' => true,
               'message' => 'get successfully',
               'warnings' => array(),
               'data' => $data,
           );
        }else{
            $result = array(
               'success' => false,
               'message' => 'not found',
               'warnings' => array(),
               'data' => array(),
           );
        }
        return $result;
    }
    public static function custom_get_certificate_returns() {
        return new external_single_structure(
            array(
                'success' => new external_value(PARAM_RAW, 'True if the user was get false otherwise'),
                'message' => new external_value(PARAM_RAW, 'message'),
                'warnings'  => new external_warnings(),
                'data' =>new external_multiple_structure(
                    new external_single_structure(
                        array(
                            'id' => new external_value(PARAM_RAW, 'id'),                 
                            'course' => new external_value(PARAM_RAW, 'course id'), 
                            'templateid' => new external_value(PARAM_RAW, 'summary of course'),                  
                            'name' => new external_value(PARAM_RAW, ' category name of course'), 
                            'intro' => new external_value(PARAM_RAW, ' image of course'),  
                            'introformat' => new external_value(PARAM_RAW, 'enrolled_users'),
                            'timecreated' => new external_value(PARAM_RAW, 'enrolled_users'),
                            'timemodified' => new external_value(PARAM_RAW, 'enrolled_users'),
                            'certificate_issue' => new external_value(PARAM_RAW, 'enrolled_users'),
                            'certificate_url' => new external_value(PARAM_RAW, 'enrolled_users'),
                        )
                    )
                ),'data',VALUE_DEFAULT, array()
            )
        );
    }

    public static function ilt_course_content_parameters() {

        return new external_function_parameters(
            array(
                'courseid' => new external_value(PARAM_INT, 'courseid', VALUE_DEFAULT, 0)
            )
        );
    }

    public static function ilt_course_content($courseid) {
        global $CFG, $DB,$PAGE,$USER;
        require_once($CFG->dirroot . "/course/externallib.php");
        $params = self::validate_parameters(self::ilt_course_content_parameters(),
            array('courseid' => $courseid));

        $coursedata = core_course_external::get_course_contents($courseid,array());
        $coursedataarray = array();
        foreach ($coursedata as $key => $value) {
            foreach ($value['modules'] as $key =>  $value2) {
                $sql = "SELECT gg.id,gg.finalgrade,gg.rawgrademax FROM {grade_grades} gg
                    INNER JOIN {user} u ON u.id = gg.userid AND u.deleted = 0
                    INNER JOIN {grade_items} gi ON gi.id = gg.itemid
                    INNER JOIN {course} c ON c.id = gi.courseid
                    WHERE gg.finalgrade IS NOT NULL AND gi.itemtype = 'mod' AND u.id=$USER->id AND gi.iteminstance='".$value2['instance']."' and gi.courseid=$courseid";
                $activitygrade = $DB->get_record_sql($sql);
                // if ($activitygrade) {
                    $finalgrade = $activitygrade->finalgrade;
                    $rawgrademax = $activitygrade->rawgrademax;
                // }
                $value['modules'][$key]['finalgrade'] = $finalgrade;
                $value['modules'][$key]['rawgrademax'] = $rawgrademax;
            }
            $coursedataarray[] = $value;
        }
        
        
        $sql = "SELECT gg.id,c.fullname, gi.courseid, gg.userid, gg.finalgrade,gg.rawgrademax FROM {grade_grades} gg
                INNER JOIN {user} u ON u.id = gg.userid AND u.deleted = 0
                INNER JOIN {grade_items} gi ON gi.id = gg.itemid
                INNER JOIN {course} c ON c.id = gi.courseid
                WHERE gg.finalgrade IS NOT NULL AND gi.itemtype = 'course' AND u.id=$USER->id and gi.courseid=$courseid";
        $coursegrade = $DB->get_record_sql($sql);
        $coursedetails = array();
        if ($coursegrade) {
            $coursedetails = array('courseid'=>$courseid,'coursegrade'=>$coursegrade->finalgrade,'grademax'=>$coursegrade->rawgrademax);
        }else{
            $coursedetails = array('courseid'=>$courseid,'coursegrade'=>00,'grademax'=>00);
        }

        $result = array(
            'statusCode' => 'NP01',
            'msg' => 'Fetch data successfully',
            'coursedetails' => $coursedetails,
            'content' => $coursedataarray,
        );
        // echo json_encode($result); die;
        return $result;

    }

    public static function ilt_course_content_returns() {
        $completiondefinition = \core_completion\external\completion_info_exporter::get_read_structure(VALUE_DEFAULT, []);

        return new external_single_structure(
            array(          
                'statusCode'=> new external_value(PARAM_RAW, 'statusCode'),
                'msg' => new external_value(PARAM_RAW, 'statusCode message'),
                'coursedetails' => new external_single_structure(
                    array(
                        'courseid' => new external_value(PARAM_RAW, 'courseid',VALUE_OPTIONAL),
                        'coursename' => new external_value(PARAM_RAW, 'coursename', VALUE_OPTIONAL),
                        'coursegrade' => new external_value(PARAM_RAW, 'coursegrade',VALUE_OPTIONAL),
                        'grademax' => new external_value(PARAM_RAW, 'grademax',VALUE_OPTIONAL),
                    )
                ),'data',VALUE_OPTIONAL, array(),                   
                'content' => new external_multiple_structure(
                    new external_single_structure(
                       array(
                    'id' => new external_value(PARAM_INT, 'Section ID'),
                    'name' => new external_value(PARAM_RAW, 'Section name'),
                    'visible' => new external_value(PARAM_INT, 'is the section visible', VALUE_OPTIONAL),
                    'summary' => new external_value(PARAM_RAW, 'Section description'),
                    'summaryformat' => new external_format_value('summary'),
                    'section' => new external_value(PARAM_INT, 'Section number inside the course', VALUE_OPTIONAL),
                    'hiddenbynumsections' => new external_value(PARAM_INT, 'Whether is a section hidden in the course format',
                        VALUE_OPTIONAL),
                    'uservisible' => new external_value(PARAM_BOOL, 'Is the section visible for the user?', VALUE_OPTIONAL),
                    'availabilityinfo' => new external_value(PARAM_RAW, 'Availability information.', VALUE_OPTIONAL),
                    'modules' => new external_multiple_structure(
                        new external_single_structure(
                            array(
                                'id' => new external_value(PARAM_INT, 'activity id'),
                                'url' => new external_value(PARAM_URL, 'activity url', VALUE_OPTIONAL),
                                'name' => new external_value(PARAM_RAW, 'activity module name'),
                                'finalgrade' => new external_value(PARAM_RAW, 'activity module name',VALUE_OPTIONAL),
                                'rawgrademax' => new external_value(PARAM_RAW, 'activity module name',VALUE_OPTIONAL),
                                'instance' => new external_value(PARAM_INT, 'instance id', VALUE_OPTIONAL),
                                'contextid' => new external_value(PARAM_INT, 'Activity context id.', VALUE_OPTIONAL),
                                'description' => new external_value(PARAM_RAW, 'activity description', VALUE_OPTIONAL),
                                'visible' => new external_value(PARAM_INT, 'is the module visible', VALUE_OPTIONAL),
                                'uservisible' => new external_value(PARAM_BOOL, 'Is the module visible for the user?',
                                    VALUE_OPTIONAL),
                                'availabilityinfo' => new external_value(PARAM_RAW, 'Availability information.',
                                    VALUE_OPTIONAL),
                                'visibleoncoursepage' => new external_value(PARAM_INT, 'is the module visible on course page',
                                    VALUE_OPTIONAL),
                                'modicon' => new external_value(PARAM_URL, 'activity icon url'),
                                'modname' => new external_value(PARAM_PLUGIN, 'activity module type'),
                                'modplural' => new external_value(PARAM_TEXT, 'activity module plural name'),
                                'availability' => new external_value(PARAM_RAW, 'module availability settings', VALUE_OPTIONAL),
                                'indent' => new external_value(PARAM_INT, 'number of identation in the site'),
                                'onclick' => new external_value(PARAM_RAW, 'Onclick action.', VALUE_OPTIONAL),
                                'afterlink' => new external_value(PARAM_RAW, 'After link info to be displayed.',
                                    VALUE_OPTIONAL),
                                'customdata' => new external_value(PARAM_RAW, 'Custom data (JSON encoded).', VALUE_OPTIONAL),
                                'noviewlink' => new external_value(PARAM_BOOL, 'Whether the module has no view page',
                                    VALUE_OPTIONAL),
                                'completion' => new external_value(PARAM_INT, 'Type of completion tracking:
                                    0 means none, 1 manual, 2 automatic.', VALUE_OPTIONAL),
                                'completiondata' => $completiondefinition,
                                'dates' => new external_multiple_structure(
                                    new external_single_structure(
                                        array(
                                            'label' => new external_value(PARAM_TEXT, 'date label'),
                                            'timestamp' => new external_value(PARAM_INT, 'date timestamp'),
                                        )
                                    ),
                                    VALUE_DEFAULT,
                                    []
                                ),
                                'contents' => new external_multiple_structure(
                                  new external_single_structure(
                                      array(
                                                  // content info
                                          'type'=> new external_value(PARAM_TEXT, 'a file or a folder or external link'),
                                          'filename'=> new external_value(PARAM_FILE, 'filename'),
                                          'filepath'=> new external_value(PARAM_PATH, 'filepath'),
                                          'filesize'=> new external_value(PARAM_INT, 'filesize'),
                                          'fileurl' => new external_value(PARAM_URL, 'downloadable file url', VALUE_OPTIONAL),
                                          'content' => new external_value(PARAM_RAW, 'Raw content, will be used when type is content', VALUE_OPTIONAL),
                                          'timecreated' => new external_value(PARAM_INT, 'Time created'),
                                          'timemodified' => new external_value(PARAM_INT, 'Time modified'),
                                          'sortorder' => new external_value(PARAM_INT, 'Content sort order'),
                                          'mimetype' => new external_value(PARAM_RAW, 'File mime type.', VALUE_OPTIONAL),
                                          'isexternalfile' => new external_value(PARAM_BOOL, 'Whether is an external file.',
                                            VALUE_OPTIONAL),
                                          'repositorytype' => new external_value(PARAM_PLUGIN, 'The repository type for external files.',
                                            VALUE_OPTIONAL),

                                                  // copyright related info
                                          'userid' => new external_value(PARAM_INT, 'User who added this content to moodle'),
                                          'author' => new external_value(PARAM_TEXT, 'Content owner'),
                                          'license' => new external_value(PARAM_TEXT, 'Content license'),
                                          'tags' => new external_multiple_structure(
                                             \core_tag\external\tag_item_exporter::get_read_structure(), 'Tags',
                                             VALUE_OPTIONAL
                                         ),
                                      )
                                  ), VALUE_DEFAULT, array()
                              ),
                                'contentsinfo' => new external_single_structure(
                                    array(
                                        'filescount' => new external_value(PARAM_INT, 'Total number of files.'),
                                        'filessize' => new external_value(PARAM_INT, 'Total files size.'),
                                        'lastmodified' => new external_value(PARAM_INT, 'Last time files were modified.'),
                                        'mimetypes' => new external_multiple_structure(
                                            new external_value(PARAM_RAW, 'File mime type.'),
                                            'Files mime types.'
                                        ),
                                        'repositorytype' => new external_value(PARAM_PLUGIN, 'The repository type for
                                            the main file.', VALUE_OPTIONAL),
                                    ), 'Contents summary information.', VALUE_OPTIONAL
                                ),
                            )
                        ), 'list of module'
                    )
                )
                   )
                ),
            )
        );  
    }

    public static function get_quiz_questions_parameters() {
    return new external_function_parameters(
        array(
            "uniqueid" => new external_value(PARAM_INT, 'Attempt unique ID')

        )
    );
}


public static function get_quiz_questions($uniqueid) {
    global $CFG, $DB,$PAGE;
        require_once($CFG->dirroot.'/repository/lib.php');  // needed for za file_rewrite_pluginfile_urls
        $returndata = array("status"=>0, "message"=>"Invalid session", "data"=>array());

            $qry="select q.id as 'qid',qa.id as 'id',q.name as 'name', q.questiontext as 'questiontext', q.qtype as 'qtype', qa.slot as 'slot' from {question_attempts} as qa left join {question} as q on qa.questionid = q.id where qa.questionusageid=?";
            // echo $qry;die;
            $query_fetch_question = $DB->get_records_sql($qry, array($uniqueid));
            $fetch_question_data = array();
            $qtype=""; 
            $questioncheck=0;
            foreach($query_fetch_question as $rs_fetch_question)
            {
                $questioncheck = 1;
                $qtype=$rs_fetch_question->qtype;
                $question_id=$rs_fetch_question->qid;
                $slot=$rs_fetch_question->slot;
                $rs_fetch_question->questiontext = $rs_fetch_question->questiontext;
                if($qtype=="truefalse")
                {
                    $query_fetch_options = $DB->get_records_sql("select an.id , an.answer as 'option' , an.answerformat , an.fraction as 'answerfraction' ,CASE WHEN an.fraction =0 THEN 'incorrect' WHEN an.fraction =1 THEN 'correct' END AS 'answer' from {question_attempts} as qa left join {question} as q on qa.questionid = q.id left join {question_answers} as an on q.id = an.question where qa.questionusageid=$uniqueid and qa.slot=$slot");
                    $allanswer=array();
                    $optionValue_Arr=array("False","True");
                    $optionValue = 0;
                    foreach($query_fetch_options as $rs_fetch_options)
                    {
                        $rs_fetch_options->value = array_search($rs_fetch_options->option,$optionValue_Arr);
                        $rs_fetch_options->option = $rs_fetch_options->option;
                        $allanswer[]=$rs_fetch_options;
                    }
                    $rs_fetch_question->allanswer=$allanswer;
                } elseif($qtype=="multichoice")
                {
                    $query_fetch_optstep=$DB->get_records_sql("SELECT qasd.value FROM `{question_attempts}`as qa LEFT JOIN {question_attempt_steps} as qas on qa.id = qas.questionattemptid LEFT JOIN {question_attempt_step_data} as qasd on qasd.attemptstepid = qas.id WHERE qa.questionusageid = $uniqueid and `slot` = $slot and qasd.name='_order'");
                    $answerseq="";
                    foreach($query_fetch_optstep as $rs_fetch_optstep)
                    {
                        $answerseq=$rs_fetch_optstep->value;
                    }

                    $answerseqdata=explode(",",$answerseq);
                    $answerseqdataArr=array();
                    $optionValue = 0;
                    if($choice = $DB->get_record("qtype_multichoice_options", array("questionid"=>$rs_fetch_question->qid))){
                        $rs_fetch_question->single = $choice->single;
                        $rs_fetch_question->shuffleanswers = $choice->shuffleanswers;
                        // $optionValue = 1;
                    }
                    foreach ($answerseqdata as $key => $value) {
                        $query_fetch_options = $DB->get_record_sql("select an.id , an.answer as 'option' , an.answerformat , an.fraction as 'answerfraction' ,CASE WHEN an.fraction =0 THEN 'incorrect' WHEN an.fraction =1 THEN 'correct' END AS 'answer' from {question_answers} as an  where an.id=".$value);
                        $query_fetch_options->value=$optionValue;

                        $query_fetch_options->option = $query_fetch_options->option;
                        $answerseqdataArr[] = $query_fetch_options;
                        $optionValue++;
                    }
                    // $rs_fetch_question->answerseq=explode(",",$answerseq);
                    $rs_fetch_question->allanswer=$answerseqdataArr;
                } else if($qtype=="match")
                {
                    unset($rs_fetch_question->allanswer);
                    $query_fetch_choiceorder=$DB->get_records_sql("SELECT qasd.value FROM `{question_attempts}`as qa LEFT JOIN {question_attempt_steps} as qas on qa.id = qas.questionattemptid LEFT JOIN {question_attempt_step_data} as qasd on qasd.attemptstepid = qas.id  WHERE qa.questionusageid = $uniqueid and `slot` = $slot and qasd.name='_choiceorder'");
                    $choiceorder="";
                    foreach($query_fetch_choiceorder as $rs_fetch_choiceorder)
                    {
                        $choiceorder=$rs_fetch_choiceorder->value;
                    }
                    $query_fetch_stemorder=$DB->get_records_sql("SELECT qasd.value FROM `{question_attempts}`as qa LEFT JOIN {question_attempt_steps} as qas on qa.id = qas.questionattemptid LEFT JOIN {question_attempt_step_data} as qasd on qasd.attemptstepid = qas.id WHERE qa.questionusageid = $uniqueid and `slot` = $slot and qasd.name='_stemorder'");
                    $stemorder="";
                    foreach($query_fetch_stemorder as $rs_fetch_stemorder)
                    {
                        $stemorder=$rs_fetch_stemorder->value;
                    }
                    $choiceorderData = explode(",",$choiceorder);
                     // print_r($choiceorderData);
                    $choiceorderDataArr= array();
                    $ans_data = new stdClass();
                    $optionValue = 0;
                    $ans_data->id="0";
                    $ans_data->answertext="choose...";
                    $ans_data->value=$optionValue;
                    $choiceorderDataArr[] = $ans_data;
                    foreach ($choiceorderData as $key => $value) {
                        $optionValue ++;
                        $qry_matchoption = $DB->get_record_sql("SELECT * from  {qtype_match_subquestions} as qms where qms.id=".$value);
                        $ans_data = new stdClass();
                        $ans_data->id=$qry_matchoption->id;
                        $ans_data->answertext=html_entity_decode(strip_tags($qry_matchoption->answertext));
                        $ans_data->value=$optionValue;
                        $choiceorderDataArr[] = $ans_data;
                    }

                    $stemorderData = explode(",",$stemorder);
                     // print_r($stemorderData);
                    $stemorderDataArr= array();
                    $match_o_value=0;
                    foreach ($stemorderData as $key => $value) {
                        $qry_matchoption = $DB->get_records_sql("SELECT  qmsq.id as qmsqid, qa.questionusageid as quesattemptid,qc.contextid as contextid,qmsq.id as id, qmsq.questionid as questionid, qmsq.questiontext as questiontext, qmsq.questiontextformat as questiontextformat, qmsq.answertext as answertext  FROM {qtype_match_subquestions} as qmsq  INNER JOIN {question} as q ON qmsq.questionid=q.id  INNER JOIN {question_categories} as qc ON qc.id=q.category  INNER JOIN {question_attempts} as qa ON qa.questionid=q.id where qmsq.id=".$value);
                        $stemoption = "";
                        foreach ($qry_matchoption as $key => $value) {
                            $questions=$value->questiontext; //@@PUGLINFILE@@
                            $quesattemptid = $value->quesattemptid;
                            $qtypecontext = $value->contextid;
                            $qmsqid = $value->qmsqid;
                            $questionfile=$DB->get_records_sql("SELECT * FROM  {files}  WHERE contextid=$qtypecontext ");
                            foreach($questionfile as $image){
                                $file='pluginfile.php';
                                $cntxid=$image->contextid;
                                $component=$image->component;
                                $filearea=$image->filearea;
                                $itemid=$image->itemid;
                                $filepath=$image->filepath;
                                $filename=$image->filename;
                            }
                            // $value->questiontext1=file_rewrite_pluginfile_urls($questions,$file,$cntxid,$component,$filearea,$quesattemptid.'/1/'.$qmsqid);
                            $fileurl = $CFG->wwwroot."/".$file."/".$cntxid."/".$component."/".$filearea."/".$quesattemptid.'/'.$slot.'/'.$qmsqid;
                            // $fileurl = htmlcontenturl_login($token, $fileurl);
                            // $decodedqtext=replacepluginURL($questions,$fileurl);
                            $value->questiontext=$value->questiontext;
                            $value->value = "sub".$match_o_value;
                            $match_o_value++;
                            $stemoption=$value;
                        }


                        
                        $stemorderDataArr[] = $stemoption;
                    }

                    $rs_fetch_question->choiceorder=$choiceorderDataArr;
                    $rs_fetch_question->stemorder=$stemorderDataArr;
                }
                $fetch_question_data[] =  $rs_fetch_question;

            }
            if($questioncheck == 1){
                $returndata["status"] = 1;
                $returndata["message"] = "question found";
                $returndata['data'] = $fetch_question_data;
            } else {
                $returndata['status'] = 0;
                $returndata['message'] = "question not found";
            }
        return $returndata;
    }


public static function get_quiz_questions_returns() {
    return new external_single_structure(
        array(                    
            'status' => new external_value(PARAM_INT, 'review of course', VALUE_DEFAULT,''), 
            'message' => new external_value(PARAM_RAW, 'average rating of course',VALUE_DEFAULT,''),
            'data' => new external_multiple_structure(
                new external_single_structure(
                    array(                    
                        'qid' => new external_value(PARAM_RAW, 'level of course'),                  
                        'id' => new external_value(PARAM_RAW, 'online of course'),
                        'name' => new external_value(PARAM_RAW, 'schedule of course'),
                        'questiontext' => new external_value(PARAM_RAW, 'duration of course'),
                        'qtype' => new external_value(PARAM_RAW, 'duration of course'),
                        'slot' => new external_value(PARAM_RAW, 'duration of course'),
                        'single' => new external_value(PARAM_RAW, 'single',VALUE_DEFAULT,''),
                        'shuffleanswers' => new external_value(PARAM_RAW, 'shuffleanswers',VALUE_DEFAULT,''),
                        'allanswer' => new external_multiple_structure(
                            new external_single_structure(
                                array(                    
                                    'id' => new external_value(PARAM_RAW, 'level of course'), 
                                    'option' => new external_value(PARAM_RAW, 'level of course',VALUE_DEFAULT,''), 
                                    'answerformat' => new external_value(PARAM_RAW, 'level of course',VALUE_DEFAULT,''), 
                                    'answerfraction' => new external_value(PARAM_RAW, 'level of course',VALUE_DEFAULT,''), 
                                    'answer' => new external_value(PARAM_RAW, 'level of course',VALUE_DEFAULT,''), 
                                    'value' => new external_value(PARAM_RAW, 'level of course',VALUE_DEFAULT,''), 
                                )
                            ), VALUE_DEFAULT, array()
                        ),
                        'choiceorder' => new external_multiple_structure(
                            new external_single_structure(
                                array(                    
                                    'id' => new external_value(PARAM_RAW, 'level of course'), 
                                    'answertext' => new external_value(PARAM_RAW, 'level of course',VALUE_DEFAULT,''), 
                                    'value' => new external_value(PARAM_RAW, 'level of course',VALUE_DEFAULT,''), 
                                )
                            ), VALUE_DEFAULT, array()
                        ),
                        'stemorder' => new external_multiple_structure(
                            new external_single_structure(
                                array(                    
                                    'id' => new external_value(PARAM_RAW, 'level of course'),
                                    'qmsqid' => new external_value(PARAM_RAW, 'level of course',VALUE_DEFAULT,''), 
                                    'quesattemptid' => new external_value(PARAM_RAW, 'level of course',VALUE_DEFAULT,''),
                                    'contextid' => new external_value(PARAM_RAW, 'level of course',VALUE_DEFAULT,''), 
                                    'questionid' => new external_value(PARAM_RAW, 'level of course',VALUE_DEFAULT,''), 
                                    'questiontext' => new external_value(PARAM_RAW, 'level of course', VALUE_DEFAULT,''), 
                                    'questiontextformat' => new external_value(PARAM_RAW, 'level of course'), 
                                    'answertext' => new external_value(PARAM_RAW, 'level of course', VALUE_DEFAULT,''), 
                                    'questiontext1' => new external_value(PARAM_RAW, 'level of course',VALUE_DEFAULT,''), 
                                    'value' => new external_value(PARAM_RAW, 'level of course',VALUE_DEFAULT,''),  
                                )
                            ), VALUE_DEFAULT, array()
                        )
                    )
                )
            )
        )
);
}

 public static function submit_quiz_saveanswers_parameters() {
        return new external_function_parameters(
            array(
                'questionatmpid' => new external_value(PARAM_TEXT, 'questionatmpid'),
                'answer_data' => new external_value(PARAM_TEXT, 'answer_data')
            )
        );
    }

    public static function submit_quiz_saveanswers($questionatmpid, $answer_data) {
        global $CFG, $DB,$PAGE,$USER;
        $returndata = array("status"=>0, "message"=>"Invalid session");
            $userid = $USER->id;

            // self::change_loginuser($query->userid);
            $time=time();
            $qry="select max(id) as id,max(sequencenumber) as 'seq' FROM {question_attempt_steps} where questionattemptid=?";
            $query_fetch_seq = $DB->get_records_sql($qry, array($questionatmpid));
            $qas_seq=0;
            $qas_id="";
            foreach($query_fetch_seq as $rs_fetch_seq)
            {
                $qas_seq=$rs_fetch_seq->seq;
                $qas_id=$rs_fetch_seq->id;
            }

            $get_qtype = $DB->get_record_sql("SELECT q.id, q.qtype as 'qtype' FROM  {question_attempts} AS qsa INNER JOIN {question} AS q ON qsa.`questionid` = q.id WHERE qsa.id =".$questionatmpid);
            if(!empty($get_qtype))
            {
                $question_type = $get_qtype->qtype;
                if($question_type == "match"){
                    $answer_data = json_decode($answer_data, true);
                    // print_r($answer_data);
                    if(is_array($answer_data) && sizeof($answer_data) > 0){
                        $qasd_val=array();
                        if($qas_seq !="")
                        {
                            $query_fetch_seq_val = $DB->get_records_sql("SELECT * FROM {question_attempt_step_data} WHERE attemptstepid=$qas_id order by id");
                            foreach($query_fetch_seq_val as $rs_fetch_seq_val)
                            {
                                $qasd_val[]=$rs_fetch_seq_val->value;
                            }
                            // if(sizeof($qasd_val) >= sizeof($answer_data)){
                            $ans_optioncheck = 0;
                            $atmp_state = "complete";
                            foreach ($answer_data as $key => $answer) {
                                    // print_r($answer);
                                if($answer['value'] == 0){
                                    $atmp_state="invalid";
                                }
                            }
                            if(sizeof($qasd_val) != sizeof($answer_data)){
                                $atmp_state="invalid";
                            }
                            $qas_seq++;
                            $rec_insert= new stdClass();
                            $rec_insert->questionattemptid= $questionatmpid;
                            $rec_insert->sequencenumber= $qas_seq;
                            $rec_insert->state= $atmp_state;
                            $rec_insert->timecreated= $time;
                            $rec_insert->userid= $userid;
                                // print_r($rec_insert);
                            $q_a_steps = "dummy";
                            $q_a_steps = $DB->insert_record('question_attempt_steps', $rec_insert, true);
                            foreach ($answer_data as $key => $answer) {
                                    // print_r($answer);
                                if($answer['key'] == "" || $answer['value'] == ""): continue; endif;
                                $rec_insert1= new stdClass();
                                $rec_insert1->attemptstepid= $q_a_steps;
                                $rec_insert1->name= $answer['key'];
                                $rec_insert1->value= $answer['value'];
                                    // print_r($rec_insert1);
                                $q_a_s_data = $DB->insert_record('question_attempt_step_data', $rec_insert1, true);
                            }
                            $returndata['status'] = 1;
                            $returndata['message'] = 'Answer updated';
                            /*  $status = 0;
                            $message = "call Sushil";*/
                           /* } else {
                                $status = 0;
                                $message = "missing andwer data";
                            }*/
                        }
                    } else {
                        $returndata['status'] = 0;
                        $returndata['message'] = "Not sufficient answerdata";
                    }
                } else if($question_type == "truefalse" || $question_type == "multichoice" || $question_type == "shortanswer" || $question_type == "numerical"){
                    $qasd_val="";
                    if($qas_seq !="")
                    {
                        $query_fetch_seq_val = $DB->get_records_sql("SELECT value FROM {question_attempt_step_data} WHERE attemptstepid=$qas_id and name='answer'");
                        foreach($query_fetch_seq_val as $rs_fetch_seq_val)
                        {
                            $qasd_val=$rs_fetch_seq_val->value;
                        }
                    }
                    if($qasd_val != $answer_data)
                    {
                        $qas_seq++;
                        $rec_insert= new stdClass();
                        $rec_insert->questionattemptid= $questionatmpid;
                        $rec_insert->sequencenumber= $qas_seq;
                        $rec_insert->state= 'complete';
                        $rec_insert->timecreated= $time;
                        $rec_insert->userid= $userid;
                        // print_r($rec_insert);
                        $q_a_steps = $DB->insert_record('question_attempt_steps', $rec_insert, true);
                        $choice = $DB->get_record("qtype_multichoice_options", array("questionid"=>$get_qtype->id));
                        // var_dump($choice);
                        if($question_type == "multichoice" && $choice &&  $choice->single == 0){
                            $allanswer = explode(",", $answer_data);
                            print_r($allanswer);
                            foreach ($allanswer as $key => $value) {
                              $rec_insert1= new stdClass();
                              $rec_insert1->attemptstepid= $q_a_steps;
                              $rec_insert1->name= 'choice'.$value;
                              $rec_insert1->value= 1;
                                // print_r($rec_insert1);
                              $q_a_s_data = $DB->insert_record('question_attempt_step_data', $rec_insert1, true);
                          }
                          $returndata['status'] = 1;
                          $returndata['message'] = 'Answer updated';
                          return $returndata;
                      }
                      $rec_insert1= new stdClass();
                      $rec_insert1->attemptstepid= $q_a_steps;
                      $rec_insert1->name= 'answer';
                      $rec_insert1->value= $answer_data;
                      $q_a_s_data = $DB->insert_record('question_attempt_step_data', $rec_insert1, true);
                      $returndata['status'] = 1;
                      $returndata['message'] = 'Answer updated';
                  } else {
                    $returndata['status'] = 1;
                    $returndata['message'] = 'Answer not updated';
                }

            } else {
                $returndata['status'] = 0;
                $returndata['message'] = 'Invalid Question type';
            }
        } else {
            $returndata['status'] = 0;
            $returndata['message'] = 'Invalid Question type';
        }
    return $returndata;
}

    public static function submit_quiz_saveanswers_returns() {
        return new external_single_structure(
            array(                    
                'status' => new external_value(PARAM_INT, 'review of course', VALUE_DEFAULT,''), 
                'message' => new external_value(PARAM_RAW, 'average rating of course',VALUE_DEFAULT,''),     
            )
        );
    }

     public static function get_user_role_parameters() {
        return new external_function_parameters(
            array(
                'courseid' => new external_value(PARAM_RAW, 'courseid'),
                'username' => new external_value(PARAM_RAW, 'username')
            )
        );
    }

     public static function get_user_role($courseid,$username) {
        global $CFG, $DB,$PAGE,$USER;
        $params = self::validate_parameters(self::get_user_role_parameters(),
            array('courseid' => $courseid,'username'=>$username));

        $roles = array();
        if ($userid = $DB->get_record('user', array('username' => $username),'id')) {
            // echo json_encode($userid);die;
            $rolenamearray = array();
            $context = get_context_instance(CONTEXT_COURSE, $courseid, false);

            if ($context) {
                $roles = get_user_roles($context, $userid->id, true);

                $statusCode = 'NP01';
                $msg = 'Record Found';
        
            }else{
                 $statusCode = 'NP00';
                $msg = 'Record not found';  
            }
    
        }else{
            $statusCode = 'NP01';
            $msg = 'Invalid Email ID';
        }
        // print_object($rolenamearray);die;

        return  [
                'statusCode' => $statusCode,
                'msg' => $msg,               
                'roles' => $roles,        
            ];
    }

     public static function get_user_role_returns() {
        return new external_single_structure(
            array(          
                'statusCode'=> new external_value(PARAM_RAW, 'statusCode'),
                'msg' => new external_value(PARAM_RAW, 'statusCode message'),                   
                'roles' => new external_multiple_structure(
                    new external_single_structure(
                       array(
                          'shortname' => new external_value(PARAM_RAW, 'name',VALUE_OPTIONAL)
                      )
                   )
                ),
            )
        );
    }

    public static function get_enrolled_user_parameters() {
        return new external_function_parameters(
            array(
                'courseid' => new external_value(PARAM_RAW, 'courseid')
            )
        );
    }

     public static function get_enrolled_user($courseid) {
        global $CFG, $DB,$PAGE,$USER;
        $params = self::validate_parameters(self::get_enrolled_user_parameters(),
            array('courseid' => $courseid));

        $enrol_users_array = array();
        $coursecentext = context_course::instance($courseid);
        
        if ($coursecentext) {
            $enrol_users = get_enrolled_users($coursecentext);
            foreach ($enrol_users as $key => $value) {
                
                $exrtafieled = get_user_profile_data($value->id);
                if($exrtafieled['designation']->data){
                   $degination = $exrtafieled['designation']->data;
                }else{
                   $degination = 'None';
                }
                $value->degination = $degination;

                $enrol_users_array[] = $value;
            }
            $statusCode = 'NP01';
            $msg = 'Record Found';
    
        }else{
             $statusCode = 'NP00';
            $msg = 'Record not found';  
        }
        // echo json_encode($enrol_users);die;
    

        return  [
                'statusCode' => $statusCode,
                'msg' => $msg,               
                'users' => $enrol_users_array,        
            ];
    }

     public static function get_enrolled_user_returns() {
        return new external_single_structure(
            array(          
                'statusCode'=> new external_value(PARAM_RAW, 'statusCode'),
                'msg' => new external_value(PARAM_RAW, 'statusCode message'),                   
                'users' => new external_multiple_structure(
                    new external_single_structure(
                       array(
                          'id' => new external_value(PARAM_RAW, 'id',VALUE_OPTIONAL),
                          'username' => new external_value(PARAM_RAW, 'username',VALUE_OPTIONAL),
                          'firstname' => new external_value(PARAM_RAW, 'firstname',VALUE_OPTIONAL),
                          'lastname' => new external_value(PARAM_RAW, 'lastname',VALUE_OPTIONAL),
                          'email' => new external_value(PARAM_RAW, 'email',VALUE_OPTIONAL),
                          'degination' => new external_value(PARAM_RAW, 'degination',VALUE_OPTIONAL)
                      )
                   )
                ),
            )
        );
    }

    public static function local_mod_attendance_add_session_parameters() {
        return new external_function_parameters(
            array(
                'attendanceid' => new external_value(PARAM_INT, 'attendance instance id'),
                'description' => new external_value(PARAM_RAW, 'description', VALUE_DEFAULT, ''),
                'sessiontime' => new external_value(PARAM_INT, 'session start timestamp'),
                'duration' => new external_value(PARAM_INT, 'session duration (seconds)', VALUE_DEFAULT, 0),
                'groupid' => new external_value(PARAM_INT, 'group id', VALUE_DEFAULT, 0),
                'addcalendarevent' => new external_value(PARAM_BOOL, 'add calendar event', VALUE_DEFAULT, true),
            )
        );
    }

     public static function local_mod_attendance_add_session(int $attendanceid, $description, int $sessiontime, int $duration, int $groupid,
     bool $addcalendarevent) {
        global $USER, $DB,$CFG;
        
        require_once($CFG->dirroot . "/mod/attendance/externallib.php");

        $params = self::validate_parameters(self::local_mod_attendance_add_session_parameters(), array(
        'attendanceid' => $attendanceid,
        'description' => $description,
        'sessiontime' => $sessiontime,
        'duration' => $duration,
        'groupid' => $groupid,
        'addcalendarevent' => $addcalendarevent,
        ));
        $attendance_add = mod_attendance_external::add_session($attendanceid, $description,$sessiontime,$duration,$groupid,$addcalendarevent);

        echo json_encode($attendance_add);die;
        
        if ($attendance_add) {

            $statusCode = 'NP01';
            $msg = 'Record Found';
    
        }else{
             $statusCode = 'NP00';
            $msg = 'Record not found';  
        }
    
        return  [
                'statusCode' => $statusCode,
                'msg' => $msg,               
                'users' => $enrol_users_array,        
            ];
    }

     public static function local_mod_attendance_add_session_returns() {
        return new external_single_structure(
            array(          
                'statusCode'=> new external_value(PARAM_RAW, 'statusCode'),
                'msg' => new external_value(PARAM_RAW, 'statusCode message'),                   
                'users' => new external_multiple_structure(
                    new external_single_structure(
                       array(
                          'id' => new external_value(PARAM_RAW, 'id',VALUE_OPTIONAL),
                          'username' => new external_value(PARAM_RAW, 'username',VALUE_OPTIONAL),
                          'firstname' => new external_value(PARAM_RAW, 'firstname',VALUE_OPTIONAL),
                          'lastname' => new external_value(PARAM_RAW, 'lastname',VALUE_OPTIONAL),
                          'email' => new external_value(PARAM_RAW, 'email',VALUE_OPTIONAL),
                          'degination' => new external_value(PARAM_RAW, 'degination',VALUE_OPTIONAL)
                      )
                   )
                ),
            )
        );
    }

    public static function local_get_courseid_parameters() {
        return new external_function_parameters(
            array(
                'scormid' => new external_value(PARAM_INT, 'scorm')
            )
        );
    }

     public static function local_get_courseid($scormid) {
        global $USER, $DB,$CFG;
        
        require_once($CFG->dirroot . "/mod/attendance/externallib.php");

        $params = self::validate_parameters(self::local_get_courseid_parameters(), array(
        'scormid' => $scormid,
        ));

        $courseid = $DB->get_record('course_modules', array('id' => $scormid));
        // echo json_encode($courseid->course);die;
        
        if ($courseid) {

            $statusCode = 'NP01';
            $msg = 'Record Found';
            $courseid = $courseid->course;
    
        }else{
             $statusCode = 'NP00';
            $msg = 'Record not found';  
        }
    
        return  [
                'statusCode' => $statusCode,
                'msg' => $msg,               
                'courseid' => $courseid,        
            ];
    }

     public static function local_get_courseid_returns() {
        return new external_single_structure(
            array(          
                'statusCode'=> new external_value(PARAM_RAW, 'statusCode'),
                'msg' => new external_value(PARAM_RAW, 'statusCode message'),                   
                'courseid' => new external_value(PARAM_RAW, 'courseid',VALUE_OPTIONAL),
            )
        );
    }

    public static function local_get_originalcourseid_parameters() {
        return new external_function_parameters(
            array(
                'courseid' => new external_value(PARAM_INT, 'scorm')
            )
        );
    }

     public static function local_get_originalcourseid($courseid) {
        global $USER, $DB,$CFG;
        
        require_once($CFG->dirroot . "/mod/attendance/externallib.php");

        $params = self::validate_parameters(self::local_get_originalcourseid_parameters(), array(
        'courseid' => $courseid,
        ));

        $originalcourseid = $DB->get_record('course', array('id' => $courseid));
        // echo json_encode($originalcourseid);die;
        if ($originalcourseid) {

            $statusCode = 'NP01';
            $msg = 'Record Found';
            $courseid = $originalcourseid->originalcourseid;
    
        }else{
             $statusCode = 'NP00';
            $msg = 'Record not found';  
        }
    
        return  [
                'statusCode' => $statusCode,
                'msg' => $msg,               
                'courseid' => $courseid,        
            ];
    }

     public static function local_get_originalcourseid_returns() {
        return new external_single_structure(
            array(          
                'statusCode'=> new external_value(PARAM_RAW, 'statusCode'),
                'msg' => new external_value(PARAM_RAW, 'statusCode message'),                   
                'courseid' => new external_value(PARAM_RAW, 'courseid',VALUE_OPTIONAL),
            )
        );
    }
}