<?php 
  include('../includes/config.inc.php');

  switch($_POST['action']){

    case 'step3student':
        require_once('../includes/classes/student.class.php');
        require_once('../includes/classes/step3mark.class.php');
        $s3 = new Step3Mark($dbase);
        echo json_encode($s3->getStudentMarks($_POST['student_id']));
      break;

    case 'step3studentnames':
        require_once('../includes/classes/student.class.php');
        require_once('../includes/classes/step3mark.class.php');
        $s3 = new Step3Mark($dbase);
        echo json_encode($s3->searchStudent($_POST['name']));
      break;


    case 'studentSearch':
        require_once('../includes/classes/student.class.php');
        $student = new Student($dbase);
        echo json_encode($student->search($_POST['name']));
      break;

    case 'searchGCS':

      require_once('../includes/classes/gcsform.class.php');
      require_once('../includes/classes/student.class.php');
      $gcsform = new GCSForm($dbase);
      $ary= array(
        'teacher_id' => (int)$_POST['tid'],
        'grade_id' => (int)$_POST['gid'],
        'subject_id' => (int)$_POST['sid'],
        'year_id' => (int)$_POST['year']
      );
      $rec = $gcsform->findByWhere($ary);
      
      $data = $ary;
      $student = new Student($dbase);
      $data['students'] = $student->getStudentData($ary);

      if(count($rec) == 0){
        $data['exists']=0;        
      }else{
        $data['exists']=1;
        $data['gcsform_id']=$rec[0]['id'];
        $data['editable'] = isCoordinatorLoggedIn() || (canEditGCSForm($rec[0]['created_on']) && !step1ResultsDeclared());
      }
      
      echo json_encode($data);

      break;
    
    case 'fetchSubjects':

      require_once('../includes/classes/grade.class.php');

      $grade = new Grade($dbase);
      $tid = (int)$_SESSION['teacherData']['id'];
      $gid = (int)$_POST['gid'];

      $data = $grade->getAllSubjects($tid,$gid);

      echo json_encode($data);
    
      break;
  }

?>