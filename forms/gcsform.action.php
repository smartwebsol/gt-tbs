<?php 
  include('../includes/config.inc.php');
  require('../includes/classes/gcsform.class.php');

  $langStep1onSubmit = $lang->get(['step1','form','onSubmit']);
  

  $data = array(
    'teacher_id'=>(int)$_SESSION['teacherData']['id'],
    'grade_id'=>(int)$_POST['grade_id'],
    'subject_id'=>(int)$_POST['subject_id'],
    'year_id'=>(int)$_POST['year_id']
  );

  $gcsform = new GCSForm($dbase,$data);

  if(isset($_POST['gcsform_id'])){
    
    $gcsdata = $gcsform->findById((int)$_POST['gcsform_id']);

    if(
        (isCoordinatorLoggedIn() || $data['teacher_id']==$gcsdata['teacher_id']) && 
        $data['grade_id']==$gcsdata['grade_id'] && 
        $data['subject_id']==$gcsdata['subject_id'] && 
        $data['year_id']==$gcsdata['year_id']
      ){
        $gcsform = new GCSForm($dbase,$gcsdata);
        $gcsform->deleteDetails();
      }

  }

  $qdata = $_POST['student'];  
  
  if($gcsform->insert()){
    
    $qdata = $_POST['student'];

    foreach($qdata as $qnum => $students){
      //printf("<hr />Question %d)<br /> ",$qnum);
      $students= array_unique($students);
      foreach($students as $rank=>$student){        
        if($student > 0){
          //printf("Rank %d = %s<br />",$rank+1,$student);
          $gcsform->saveStudent($qnum,$student);
        }
      }
    }
    
    if($_POST['declareResult']=='1' || $gcsform->isStep1Completed()){      
      $gcsform->declareStep1Results(); //== declare results automatically once teacher has filled all the questions for a student
    }

    setMessage($langStep1onSubmit['success'],'success');
    header('location:../gcs-form.php');
  }else{
    setMessage($langStep1onSubmit['error'],'danger');
    header('location:../gcs-form.php');
  }
  


  


?>