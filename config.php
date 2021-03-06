<?php  // Moodle configuration file

unset($CFG);
global $CFG;
$CFG = new stdClass();

$CFG->dbtype    = 'mysqli';
$CFG->dblibrary = 'native';
$CFG->dbhost    = '127.0.0.1';
$CFG->dbname    = 'newspice_db';
$CFG->dbuser    = 'spicelearn_u';
$CFG->dbpass    = 'g4jW7>6tSX';
$CFG->prefix    = 'mdl_';
$CFG->dboptions = array (
  'dbpersist' => 0,
  'dbport' => '',
  'dbsocket' => '',
  'dbcollation' => 'utf8mb4_general_ci',
);
$CFG->dbsession = '0';
$CFG->wwwroot   = 'http://uat.spicelearnweb.xrcstaging.in';
//$CFG->dataroot  = '/home/spicelearn_st/moodledata';
$CFG->dataroot = '/var/www/moodledata_live';
$CFG->admin     = 'admin';

$CFG->directorypermissions = 0777;
header('Access-Control-Allow-Origin: *');
require_once(__DIR__ . '/lib/setup.php');


// There is no php closing tag in this file,
// it is intentional because it prevents trailing whitespace problems!
