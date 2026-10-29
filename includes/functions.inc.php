<?php

function sortColumn($label,$key='',$search=false){
  if($key==''){
    return $label;
  }

  $same = $search && $search['sort'] && $search['sort']['by']==$key;

  $cls='';

  $dir=($same && $search['sort']['dir']=="asc") ? "desc" : "asc";

  if($same){
    $cls= "fa fa-sort-".($dir=="desc" ? 'down' : 'up'); 
  }
  
  return sprintf('<a href="#" onclick="return sortColumn(\'%s\',\'%s\')">%s</a> <span class="%s" />',$key,$dir,$label,$cls);
}

function getSetting($name){
  global $dbase;
  require_once('classes/sitesettings.class.php');
  $ss = new SiteSetting($dbase); 
  if($ss->findByName($name)){
    return $ss->getValue();
  }
  return "";
}

function getPointName($point = null){
  global $dbase;
  if(is_null($point))
    return '';
  else{
    require_once('classes/step2qset.class.php');
    $cols = (new Step2QSet($dbase))->columns();    
    $colp = $point*4;
    if($point < 1){
      $colp +=1;
    }
    return $cols["point".$colp];
  }

}

function step2ResultsDeclared(){
  return false;
}

function teacherHasFilledAllStep2(){
  global $dbase;
  require_once('classes/teacher.class.php');
  $teacher = new Teacher($dbase,$_SESSION['teacherData']);
  return $teacher->hasFilledAllStep2(getCurrentYear());
}

function canEditStep2Form($strtime){
  $ptime = strtotime($strtime) + ((int)getSetting('step2edit') * 86400);    
  return time() < $ptime;
}

function step1ResultsDeclared(){
    global $dbase;
    require_once('classes/teacher.class.php');
    $teacher = new Teacher($dbase,$_SESSION['teacherData']);
    return $teacher->step1ResultsDeclared(getCurrentYear());
  }

  function getCurrentYear(){
    return date('Y');
  }

  function teacherHasFilledAllStep1(){
    global $dbase;
    require_once('classes/teacher.class.php');
    $teacher = new Teacher($dbase,$_SESSION['teacherData']);
    return $teacher->hasFilledAllStep1(getCurrentYear());
  }

  function canEditGCSForm($strtime){
    if(isCoordinatorLoggedIn()){
      return true; //== coordinator can edit
    }
    $ptime = strtotime($strtime) + ((int)getSetting('step1edit') * 86400);    
    return time() < $ptime;
  }

  function flashMessage(){
    if(isset($_SESSION['flash']) && count($_SESSION['flash']) > 0){
      foreach($_SESSION['flash'] as $m){
        printf('<div class="alert alert-%s">%s</div>',$m['type'],$m['message']);
      }
      unset($_SESSION['flash']);
    }
  }


  /*
  $type can be 'success','info','warning','danger'
  */
  function setMessage($message,$type='info'){
    if(!isset($_SESSION['flash'])){
      $_SESSION['flash']=array();      
    }
    array_push($_SESSION['flash'],array("type"=>$type,"message"=>$message));
  }


  function getAllGrades($tid){
    global $dbase;
    require_once('classes/teacher.class.php');
    $teacher = new Teacher($dbase);
    $tdata = $teacher->findById($tid);
    $teacher = new Teacher($dbase,$tdata);
    return $teacher->getAllGrades();
  }

  function getTeacherName(){
    return sprintf('%s %s',$_SESSION['teacherData']['fname'],$_SESSION['teacherData']['lname']);
  }

  function getTeacherId(){
    return $_SESSION['teacherData']['id'];
  }

  function showTeacherName(){
    echo getTeacherName();
  }

  function securePage(){
    $teacherPages = array(
      '/gcs-form.php',
      '/gcs-result.php',
      '/gcs-list.php',
      '/step2-form.php',
      '/step2-list.php',
      '/step2-result.php'      
    );

    $coordinatorPages = array(
      '/settings.php',
      '/stats.php',
      '/step3.php'
    );

    if(in_array($_SERVER['SCRIPT_NAME'],$teacherPages)){
      if(!isTeacherLoggedIn()){
        header('location:index.php'); //== go to index to login
      }
    }

    if(in_array($_SERVER['SCRIPT_NAME'],$coordinatorPages)){
      if(!isCoordinatorLoggedIn()){
        header('location:index.php'); //== go to index to login
      }
    }
  }

  function isCoordinatorLoggedIn(){    
    if(isset($_SESSION['teacherData'])){
      return in_array($_SESSION['teacherData']['email'],_COORD_EMAIL);
    }
    return false;
  }

  function isTeacherLoggedIn(){    
    return isset($_SESSION['teacherData']);
  }

  function logoutTeacher(){    
    unset($_SESSION['teacherData']);
  }

  function logger(array $data, $logFile = "error.log"){        
    $message="%clock% : %level% %message%";
    $data['clock']=date('d-m-Y H:i:s');
    foreach ($data as $key => $val) {
      $message = str_replace("%{$key}%", $val, $message);    
    }
    $message .= PHP_EOL;
    return file_put_contents($logFile, $message, FILE_APPEND);  
  }

  function statsObject(){
    $stats = new stdClass();
    $stats->added = 0;
    $stats->found = 0;
    $stats->updated = 0;
    $stats->deleted = 0;

    return $stats;
  }

  function statsToString($stats){
      return sprintf("
      Added: %d, 
      Found: %d, 
      Updated: %d, 
      Deleted: %d",
      $stats->added,
      $stats->found,
      $stats->updated,
      $stats->deleted);    
  }

  

  function readDataFromCSV($fpath){
    $file = fopen($fpath,"r");
    $data = array();
    while(! feof($file)){
      array_push($data,fgetcsv($file));
    }    
    fclose($file);
    return $data;
  }
?>