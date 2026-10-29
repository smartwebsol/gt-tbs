<?php
include('../includes/config.inc.php');
require_once('../includes/classes/step1results.class.php');
require_once('../includes/classes/step2form.class.php');


$langStep2onSubmit = $lang->get(['step2','form','onSubmit']);
$teacher_id=(int)$_SESSION['teacherData']['id'];

$result=0;

$points = $_POST['points'];
foreach($points as $qid=>$point){
  $result+=$point;
}

if($_POST['step2form_id']==0){
  $s1r = (int)$_POST['s1r'];
  $step1result = new Step1Result($dbase);
  $row = $step1result->findById($s1r);  

  if(!$row || (!isCoordinatorLoggedIn() && $row['teacher_id']!=$teacher_id)){
    setMessage($langStep2onSubmit['invalid'],'danger');
    header('location:../gcs-form.php');
  }

  $data = array(
    'result'=> (float)sprintf("%0.2f",$result),
    'student_id'=>$row['student_id'],
    'teacher_id'=>$teacher_id,
    'grade_id'=>$row['grade_id'],
    'subject_id'=>$row['subject_id'],
    'year_id'=>$row['year_id']
  );

  $step2form = new Step2Form($dbase,$data);


  if($step2form->insert()){

    //$step2form->deleteAnswers();
    
    foreach($points as $qnum => $point){
      $step2form->saveAnswer((int)$qnum,(float)$point);
    }
  
    setMessage($langStep2onSubmit['success'],'success');
    header('location:../step2-form.php');
  }else{
    setMessage($langStep2onSubmit['error'],'danger');
    header('location:../step2-form.php');
  }

}else if($_POST['step2form_id'] > 0){
  
  $step2form = new Step2Form($dbase);
  $s2Data = $step2form->findById((int)$_POST['step2form_id']);

  if(isCoordinatorLoggedIn() || $teacher_id==$s2Data['teacher_id']){
      $step2form = new Step2Form($dbase,$s2Data);
      $step2form->update(array('result'=> (float)sprintf("%0.2f",$result)));
      $step2form->deleteAnswers();
      foreach($points as $qnum => $point){
        $step2form->saveAnswer((int)$qnum,(float)$point);
      }
      setMessage($langStep2onSubmit['success'],'success');
      header('location:../step2-form.php');
    }else{
      setMessage($langStep2onSubmit['error'],'danger');
      header('location:../step2-form.php');
    }

}



?>