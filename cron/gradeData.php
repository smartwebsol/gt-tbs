<?php 
	ini_set('max_execution_time', '6000');
  require_once('../includes/config.inc.php');
  require_once('../includes/classes/grade.class.php');
  //=== csv has 1 column Name
  $fpath = _PATH.'cron/data-sync/Data - 2024-11-26/gradeData.csv';
  $logpath = _PATH.'logs/import-grade-2024.log';

  logger(["level"=>"IMPORT START","message" => "  
  ===========================================================
  
  Import File: ".$fpath."
    
  ===========================================================  
  "],$logpath);

  $data = readDataFromCSV($fpath);

  $grade = new Grade($dbase);

  $gradeStats = statsObject();

  foreach($data as $i=>$d){
    if($i==0) continue; // skip the header   

    if(!empty($d[0])) {

      logger(["level"=>"IMPORT - Grade - processing - ","message" => $d[0]],$logpath);

      //array_push($teacherNotToBeMarkedForDeletion,$teacherData['email']);

      $foundData = $grade->findByName($d[0]); //== If email is empty in CSV then find by Adno     
      

      if(empty($foundData)){

        //== New Data
        $gradeStats->added++;
        $grade = new Grade($dbase,[
          'name' => $d[0]
        ]);
        $save = $grade->save();       

      }

    }

  }
  

  logger(["level"=>"IMPORT DONE - ","message" => "
  ============================================================
  IMPORT FINISH

  ".statsToString($gradeStats)."
  ============================================================
  "],$logpath);

?>
