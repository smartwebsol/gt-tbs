<?php 
	ini_set('max_execution_time', '0');
  require_once('../includes/config.inc.php');
  require_once('../includes/classes/student.class.php');
  require_once('../includes/classes/grade.class.php');

  function saveOtherRecords($student,$grade_id,$d){
    require_once('../includes/classes/subject.class.php');
    require_once('../includes/classes/teacher.class.php');
    $db = $student->db;

    $subject = new Subject($db,array('name'=> $d[5]));
    $subject->save();    
    $subject_id=$subject->getId();

    $teacherData = array(
      'fname' => $d[6],
      'lname' => $d[7],
      'email' => $d[8]
    );

    if(empty($teacherData['email'])){
      $teacherData = array(
        'fname' => 'n/a',
        'lname' => 'n/a',
        'email' => 'n@a'
      );
    }
    
    $teacher = new Teacher($db);
    $foundData = $teacher->findByEmail($teacherData['email']); //== If email is empty in CSV then find by Adno     
    if(!empty($foundData)){
      $teacherData['id']=$foundData['id'];      
    }
    $teacher = new Teacher($db,$teacherData);
    $teacher->save();
    $teacher_id=$teacher->getId();

    $student->saveDetails($grade_id, $subject_id, (int)$d[9], $teacher_id);
  }

  $fpath = _PATH.'docs/2021-data/rcvd/students.csv';
  $logpath = _PATH.'logs/import-2021.log';

  logger(["level"=>"IMPORT START","message" => "  
  ===========================================================
  
  Import File: ".$fpath."
    
  ===========================================================  
  "],$logpath);

  $data = readDataFromCSV($fpath);

  $student = new Student($dbase); 
  $grade = new Grade($dbase);
  $grade_id=0;

  $studentStats = statsObject();

  $adnoNotToBeMarkedForDeletion = array(); //== array of emails which are not to be marked for deletion

  foreach($data as $i=>$d){
    if($i==0) continue; // skip the header   

    if(!empty($d[0])) {

      $studentData = array(
        'adno' => $d[0],
        'name' => $d[1],
        'email' => $d[2],
        'year' => $d[3]
      );

      logger(["level"=>"IMPORT - Students - processing - ","message" => $d[0]],$logpath);

      array_push($adnoNotToBeMarkedForDeletion,$studentData['adno']);

      $foundData = $student->findByEmail($studentData['email']); //== If email is empty in CSV then find by Adno     

      //$grade = new Grade($dbase,array('name'=> $d[4]));
      //$grade->save();        
      //$grade_id=$grade->getId();

      if(empty($foundData)){

        //== New Data
        $studentStats->added++;

        $student = new Student($dbase,$studentData);
        $save = $student->save();       

      }else{

        // Existing Data
        $studentStats->found++;
        
        $studentData['id']=$foundData['id'];
        //$studentData['deleted']=(int)$foundData['deleted'];

        $student = new Student($dbase,$studentData);
        $save = $student->save();

        if($save){
          //== Data updated
          $studentStats->updated++;
        }

        $student->deleteDetails();
      }

    }

    //saveOtherRecords($student,$grade_id ,$d);
  }

  $studentStats->deleted = $student->markDeletedAdnoNotPresent($adnoNotToBeMarkedForDeletion); //== mark student records which are not present in CSV (adno)
  
  logger(["level"=>"IMPORT DONE - ","message" => "
  ============================================================
  IMPORT FINISH

  ".statsToString($studentStats)."
  ============================================================
  "],$logpath);



  

?>
