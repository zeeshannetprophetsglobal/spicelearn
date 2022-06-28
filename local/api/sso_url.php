<?php

    require_once('../../config.php');
   
    
   
	if(!empty($_POST['username'])){
        $username=$_POST['username'];
    }else{
        $username='';
    }
	
	if(!empty($_POST['email'])){
        $email=$_POST['email'];
    }else{
        $email='';
    }
	
    if(!empty($_POST['courseid'])){
        $courseid=$_POST['courseid'];
    }else{
        $courseid='';
    }

    if(!empty($_POST['modname'])){
        $modname=$_POST['modname'];
    }else{
        $modname='';
    }

    if(!empty($_POST['activityid'])){
        $activityid=$_POST['activityid'];
    }else{
        $activityid='';
    }

    if(!empty($_POST['inctanceid'])){
        $inctanceid=$_POST['inctanceid'];
    }else{
        $inctanceid='';
    }

    if(!empty($_POST['scoid'])){
        $scoid=$_POST['scoid'];
    }else{
        $scoid='';
    }


    if(!empty($_POST['url'])){
        $url=$_POST['url'];
    }else{
        $url='';
    }
    
    $ws_login_url=getloginurl($username,$email, $courseid, $modname, $activityid,$url);

    echo $ws_login_url;
    exit();

/**
 * @param   string $useremail Email address of user to create token for.
 * @param   string $firstname First name of user (used to update/create user).
 * @param   string $lastname Last name of user (used to update/create user).
 * @param   string $username Username of user (used to update/create user).
 * @param   string $ipaddress IP address of end user that login request will come from (probably $_SERVER['REMOTE_ADDR']).
 * @param int      $courseid Course id to send logged in users to, defaults to site home.
 * @param int      $modname Name of course module to send users to, defaults to none.
 * @param int      $activityid cmid to send logged in users to, defaults to site home.
 * @return bool|string
 */
function getloginurl($username, $email, $courseid = null, $modname = null, $activityid = null, $url = null) {
    require_once('curl.php');
    global $CFG;
   ob_start();
    $token        = 'c988e5d740b474b9400d892f5d380921';
    $domainname   = "https://spicelearn.inroad.in"; 
    $functionname = 'auth_userkey_request_login_url';
    
    $param = [
        'user' => [
            'username'  => $username,
			'email' 	=> $email
            
        ]
    ];
    
     $serverurl = $domainname . '/webservice/rest/server.php' . '?wstoken=' . $token . '&wsfunction=' . $functionname . '&moodlewsrestformat=json';
   
    $curl = new curl; // The required library curl can be obtained from https://github.com/moodlehq/sample-ws-clients 

    try {
        $resp     = $curl->post($serverurl, $param);  
        print_r($resp)    ;
        exit();
        $resp     = json_decode($resp);
       
        if ($resp && !empty($resp->loginurl)) {
            $loginurl = $resp->loginurl;        
        }
    } catch (Exception $ex) {
        return false;
    }

    if (!isset($loginurl)) {
        return false;
    }

    $path = '';
    if (isset($courseid) && $courseid !=null) {
        $path = '&wantsurl=' . urlencode("$domainname/course/view.php?id=$courseid");
    }else if (isset($url) && $url !=null) {
        $path = '&wantsurl=' . urlencode($url);
    }else if (isset($modname) && $modname!=null ) {
      
        $path = '&wantsurl=' . urlencode("$domainname/mod/$modname/view.php?id=$activityid");   
          
    }
   
    return $loginurl . $path;
}

?>