<?php 
//require_once('curl.php');
$token        = 'e33b478e92726fc877482c199a5a2138';
$domainname   = "http://localhost/lmsspice"; 
$functionname = 'core_files_upload';
echo "<pre>";
print_r($_FILES);
//require_once(__DIR__ . '/../../config.php');
require_once(__DIR__ . '/../../config.php');
require_once($CFG->dirroot . '/webservice/lib.php');

function file_get_unused_draft_itemid1() {
    global $DB, $USER;

    if (isguestuser() or !isloggedin()) {
        // guests and not-logged-in users can not be allowed to upload anything!!!!!!
        print_error('noguest');
    }

    $contextid = context_user::instance(4)->id;

    $fs = get_file_storage();
    $draftitemid = rand(1, 999999999);
    while ($files = $fs->get_area_files($contextid, 'user', 'draft', $draftitemid)) {
        $draftitemid = rand(1, 999999999);
    }

    return $draftitemid;
}
if(isset($_POST['submit'])){

$imagename = $_FILES["fileToUpload"]["name"];
	 
//file_put_contents('MyFile.jpg', base64_decode($_POST['filecontent']));


/*'contextid' => null,
'component' => 'user',
'filearea' => 'draft',
'itemid' => 0,
'filepath' => '/',
'filename' => 'sample.txt',
'filecontent' => base64_encode("Hello Word!"),
'contextlevel' => 'user',
'instanceid' => $userid,*/
//$itemid = 0;
$fs = get_file_storage();
    echo $itemid = file_get_unused_draft_itemid1();


  $params = array('component' => 'user',
                   'filearea' => 'draft', 
				   'itemid' => 0,
				   'filename' => $imagename,
				   'filepath' => $_FILES["fileToUpload"]["tmp_name"],
				   'filecontent' => base64_encode($imagename), 
				   'contextlevel' => 'user', 
				   'instanceid' => 4);    

$ch = curl_init();

curl_setopt($ch, CURLOPT_HEADER, 0); 

curl_setopt($ch, CURLOPT_VERBOSE, 0); 

curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);


$serverurl = $domainname . '/webservice/rest/server.php' . '?wstoken=' . $token . '&wsfunction=' . $functionname . '&moodlewsrestformat=json';

 $ch = curl_init();

$cfile = new CURLFile($_FILES['fileToUpload']['tmp_name'], $_FILES['fileToUpload']['type'], $_FILES['fileToUpload']['name']);

curl_setopt($ch, CURLOPT_URL, $serverurl);
curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "POST");
curl_setopt($ch, CURLOPT_POSTFIELDS, $params);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
$result = curl_exec($ch);
print_r($result);

curl_setopt($ch, CURLOPT_URL, $serverurl);

curl_setopt($ch, CURLOPT_POST, true);

curl_setopt($ch, CURLOPT_POSTFIELDS, $params);

$response = curl_exec($ch);
print_r($response);

$result=json_decode($response, true);

//echo $result['itemid'];

//print_r($result);


//$result['itemid'] = '309293578';
$params2 = array( 'draftitemid' => '309293578','userid' => 2);

$ch2 = curl_init(); 

curl_setopt($ch2, CURLOPT_HEADER, 0);

curl_setopt($ch2, CURLOPT_VERBOSE, 0); 

curl_setopt($ch2, CURLOPT_RETURNTRANSFER, true);
$functionname1 = 'core_user_update_picture';

$serverurl1 = $domainname . '/webservice/rest/server.php' . '?wstoken=' . $token . '&wsfunction=' . $functionname1 . '&moodlewsrestformat=json';

curl_setopt($ch2, CURLOPT_URL, $serverurl1);

curl_setopt($ch2, CURLOPT_POST, true);

curl_setopt($ch2, CURLOPT_POSTFIELDS, $params2);

$response2 = curl_exec($ch2);

print_r($response2);

 }
 
?>