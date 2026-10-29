<?php
include('../includes/config.inc.php');
require_once('../includes/classes/teacher.class.php');
require_once('../includes/classes/gcsform.class.php');
require_once('../includes/classes/step1results.class.php');

$langStep1Box = $lang->get(['teacherDashboard','step1Box']);

$teacher = new Teacher($dbase,$_SESSION['teacherData']);

$step1results = new Step1Result($dbase);
$step1results->deleteAll($teacher->getId(),getCurrentYear());

$gcsforms = $teacher->getAllGCSFormsFilled(getCurrentYear());

foreach($gcsforms as $step1){
  $gcsform = new GCSForm($dbase,$step1);
  $result = $gcsform->getResult($step1['id']);
  foreach($result as $res){
    $data = array(
      'student_id'=> $res['student_id'],
      'gcsforms_id'=> $step1['id'],
      'grade_id'=> $step1['grade_id'],
      'subject_id'=> $step1['subject_id'],
      'year_id'=> $step1['year_id'],
      'teacher_id'=> $step1['teacher_id']
    );
    $step1results = new Step1Result($dbase,$data);
    $step1results->insert();
  }
}

setMessage($langStep1Box['step1CompleteSubmit'],'info');
header('location:../index.php');


?>