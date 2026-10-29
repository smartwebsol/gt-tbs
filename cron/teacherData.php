<?php 
	ini_set('max_execution_time', '0');
  require_once('../includes/config.inc.php');
  require_once('../includes/classes/teacher.class.php');

  $fpath = _PATH.'docs/2021-data/rcvd/teachers.csv';
  $logpath = _PATH.'logs/import-teacher-2021.log';

  logger(["level"=>"IMPORT START","message" => "  
  ===========================================================
  
  Import File: ".$fpath."
    
  ===========================================================  
  "],$logpath);

  $data = readDataFromCSV($fpath);

  $teacher = new Teacher($dbase);

  $teacherStats = statsObject();

  //$teacherNotToBeMarkedForDeletion = array(); //== array of emails which are not to be marked for deletion

  foreach($data as $i=>$d){
    if($i==0) continue; // skip the header   

    if(!empty($d[0])) {

      $teacherData = array(
        'fname' => $d[0],
        'lname' => $d[1],
        'email' => $d[2]
      );

      logger(["level"=>"IMPORT - Teacher - processing - ","message" => $d[2]],$logpath);

      //array_push($teacherNotToBeMarkedForDeletion,$teacherData['email']);

      $foundData = $teacher->findByEmail($teacherData['email']); //== If email is empty in CSV then find by Adno     

      if(empty($foundData)){

        //== New Data
        $teacherStats->added++;

        $teacher = new Teacher($dbase,$teacherData);
        $save = $teacher->save();       

      }else{

        // Existing Data
        //$teacherStats->found++;
        
        //$studentData['id']=$foundData['id'];
        //$studentData['deleted']=(int)$foundData['deleted'];

        //$student = new Student($dbase,$studentData);
        //$save = $student->save();

        //if($save){
          //== Data updated
          $teacherStats->updated++;
        //}

        //$student->deleteDetails();
      }

    }

    //saveOtherRecords($student,$grade_id ,$d);
  }

  //$teacherStats->deleted = $teacher->markDeletedNotPresent($teacherNotToBeMarkedForDeletion); //== mark student records which are not present in CSV (adno)
  
  logger(["level"=>"IMPORT DONE - ","message" => "
  ============================================================
  IMPORT FINISH

  ".statsToString($teacherStats)."
  ============================================================
  "],$logpath);

?>
