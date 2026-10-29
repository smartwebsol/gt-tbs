<?php
include('../includes/config.inc.php');
require_once('../includes/classes/step2form.class.php');
require_once('../includes/classes/step3mark.class.php');
$step2form = new Step2Form($dbase);
$s3mark = new Step3Mark($dbase);

if(!isCoordinatorLoggedIn()){
  setMessage($langStep2onSubmit['error'],'danger');
  header('location:../index.php');
}

if(isset($_GET['s3']) && $_GET['id'] > 0){    
  $langStep2onSubmit = $lang->get(['step2','form','onSubmit']);  
  $s2Data = $step2form->findById((int)$_GET['id']);  
  $s3 = (int)$_GET['s3'];
  $step2form = new Step2Form($dbase,$s2Data);  
  $step2form->update(array('step3'=> $s3));  
  
  if($s3==0){ //== means disqualify
    $s3mark->deleteData(array(
      'student_id'=>$s2Data['student_id'],      
      'year_id'=>$s2Data['year_id']
    ));
  }
  
  setMessage($langStep2onSubmit['s3-success'],'success');
  header('location:../step2-list.php'); 
}

if(isset($_POST['action'])){
  $langStep3 = $lang->get(['step3']);

  if($_POST['action']=='savescore'){    
    foreach($_POST['scoremode'] as $prefix=>$sm){
      $et = (int)$_POST['exam_type'][$prefix];
      $sc = (float)$_POST['score'][$prefix];

      $data = explode('_',$prefix);

      if($et > 0 && $sc > 0){

        if($sm=='new'){          
          $data = array(
            'student_id' => (int)$data[0],
            'year_id' => (int)$data[1],
            'grade_id' => (int)$data[2],
            'subject_id' => (int)$data[3],
            'exam_type' => $et,
            'score' => $sc
          );
          $s3mark = new Step3Mark($dbase,$data);  
          
          $s3mark->insert();
        }else if($sm=='old'){
          $data = $s3mark->findById((int)$_POST['s3id'][$prefix]);
          $s3mark = new Step3Mark($dbase,$data);  
          $s3mark->update(array(
            'exam_type' => $et,
            'score' => $sc
          ));
        }
      }
    }

    setMessage($langStep3['grid']['onsuccess'],'success');
    header('location:../step3.php'); 
  }

}



?>