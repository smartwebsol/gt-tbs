<?php 
	ini_set('max_execution_time', '6000');
  require_once('../includes/config.inc.php');
  require_once('../includes/classes/subject.class.php');
  //=== csv has 1 column Subject
  $fpath = _PATH.'cron/data-sync/Data - 2024-11-26/subjectData.csv';
  $logpath = _PATH.'logs/import-subject-2024.log';

  logger(["level"=>"IMPORT START","message" => "  
  ===========================================================
  
  Import File: ".$fpath."
    
  ===========================================================  
  "],$logpath);

  $data = readDataFromCSV($fpath);

  $subject = new Subject($dbase);

  $subjectStats = statsObject();

  foreach($data as $i=>$d){
    if($i==0) continue; // skip the header   

    if(!empty($d[0])) {

      logger(["level"=>"IMPORT - Subject - processing - ","message" => $d[0]],$logpath);

      //array_push($teacherNotToBeMarkedForDeletion,$teacherData['email']);

      $foundData = $subject->findByName($d[0]); //== If email is empty in CSV then find by Adno     
      

      if(empty($foundData)){

        //== New Data
        $subjectStats->added++;
        $subject = new Subject($dbase,[
          'name' => $d[0]
        ]);
        $save = $subject->save();       

      }

    }

  }
  

  logger(["level"=>"IMPORT DONE - ","message" => "
  ============================================================
  IMPORT FINISH

  ".statsToString($subjectStats)."
  ============================================================
  "],$logpath);

?>
