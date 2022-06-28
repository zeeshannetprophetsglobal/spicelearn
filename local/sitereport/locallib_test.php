<?php
$ecn = "ayush";
$base = "noida";
$zone = null;
$rank = '123';
$l1 = null;
$l2 = null;
define('STUDENT', 5);
define('ZONE', 2);
define('RANK', 1);
define('L1', 3);
define('L2', 5);


department_user_detail($page = 0,$ecn,$base,$zone,$rank,$l1,$l2,$downloaddata = null);

function department_user_detail($page = 0,$ecn,$base,$zone,$rank,$l1,$l2,$downloaddata = null){

    global $USER,$DB;
    //$sql = 'SELECT DISTINCT u.id,u.username,u.firstname,u.lastname,u.email,u.city FROM {user} as u JOIN {cohort_members} AS cm ON u.id = cm.userid JOIN {role_assignments} AS ra ON cm.userid = ra.userid __zone_rank_l1_l2__ WHERE ra.roleid = '.STUDENT.' AND cm.cohortid =7 __where_and_base__';
    $sql = 'SELECT DISTINCT u.id,u.username,u.firstname,u.lastname,u.email,u.city FROM mdl_user as u JOIN mdl_cohort_members AS cm ON u.id = cm.userid JOIN mdl_role_assignments AS ra ON cm.userid = ra.userid __JOIN__  WHERE ra.roleid = '.STUDENT.' AND cm.cohortid =7';

    $table_filter['base'] = ['where_and'=>' AND u.city LIKE  "%'.$base.'%"'];
    $table_filter['ecn'] = ['where_and'=>' AND u.username LIKE  "%'.$ecn.'%"'];
    $table_filter['zone'] = ['join'=>' JOIN mdl_user_info_data as uif ON u.id = uif.userid', 'where_and'=>' AND uif.fieldid = '.ZONE.' AND uif.data LIKE  "%'.$zone.'%"'];
    $table_filter['rank'] = ['join'=>' JOIN mdl_user_info_data as uif ON u.id = uif.userid', 'where_and'=>' AND uif.fieldid = '.RANK.' AND uif.data LIKE  "%'.$rank.'%"'];
    $table_filter['l1'] = ['join'=>'JOIN mdl_user_info_data as uif ON u.id = uif.userid', 'where_and'=>' AND uif.fieldid = '.L1.' AND uif.data LIKE  "%'.$l1.'%"'];
    $table_filter['l2'] = ['join'=>' JOIN mdl_user_info_data as uif ON u.id = uif.userid','where_and'=>' AND uif.fieldid = '.L2.' AND uif.data LIKE  "%'.$l2.'%"'];
    echo "<pre>";print_r($table_filter);echo "</pre>";

    $JOIN__user_info_data = false;
    $join_sql = '';

    foreach($table_filter as $key => $val){
        if(isset($$key)){
            echo $key.'='.$$key;
            $sql = str_replace('__where_and__', $table_filter[$key]['where_and'],$sql);
            if(isset($table_filter[$key]['where_and'])){
                $sql .= $table_filter[$key]['where_and'];
            }
            
            if(isset($table_filter[$key]['join']) && !$JOIN__user_info_data){
               $join_sql = $table_filter[$key]['join'];
               $JOIN__user_info_data  = true;
            }
            
            echo '<br>---------------<br>';
        }
    }
    $sql = str_replace('__JOIN__', $join_sql, $sql);

    // if(!empty($base)){
    //     $sql .= ' ';
    // }elseif(!empty($ecn)){

    //     $sql .= ' AND u.username LIKE  "%'.$ecn.'%"';

    // }elseif(!empty($zone)){

    //     $sql .= ' ';

    // }elseif(!empty($rank)){

    //     $sql .= ' AND uif.fieldid = '.RANK.' AND uif.data LIKE  "%'.$rank.'%"';

    // }elseif(!empty($l1)){
    //     $sql .= ' AND uif.fieldid = '.L1.' AND uif.data LIKE  "%'.$l1.'%"';

    // }elseif(!empty($l2)){

    //     $sql .= ' AND uif.fieldid = '.L2.' AND uif.data LIKE  "%'.$l2.'%"';

    // }else{
    //      $sql .= ' WHERE ra.roleid = '.STUDENT.' AND cm.cohortid =7';
    // }
    echo $sql;
    //$Alldata = $DB->get_records_sql($sql,null,$page,PERPAGE_LIMIT);
    
//     foreach ($Alldata as $index) {
//         $page++;
//         $index->index = $page;
//     }
//    //print_object($Alldata);die;
  
//    return $Alldata;
}