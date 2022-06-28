<?php
ini_set("display_errors",1);
error_reporting(E_ALL);
require_once(__DIR__ . '/../../config.php');

global $CFG,$USER;

//global $USER;
//echo $sesskey = $USER->sesskey;
//echo $logout = '<a href="https://spicelearn.inroad.in/login/logout.php?sesskey='.$sesskey.'">Logout</a>';
$name=base64_decode($_REQUEST['username']);
$password=base64_decode($_REQUEST['password']);
$red=$_REQUEST['wantsurl'];
$dashboard = $CFG->wwwroot;
$user = authenticate_user_login($name, $password);
//print_r($user);die;
if(complete_user_login($user))
{   
    //$actual_link = "http://$_SERVER[HTTP_HOST]/login/logout.php?sesskey=".$user->sesskey;	
    //json_encode(['user'=>$user,'logout'=>$actual_link],true);
	redirect($red); 
}
else
{
   echo "not login"; die;
}
?>