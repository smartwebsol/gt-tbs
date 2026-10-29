<?php 
  include('includes/files/header.inc.php'); 
  securePage();

  require_once('includes/classes/gcsform.class.php');
  require_once('includes/classes/step1qset.class.php');
  require_once('includes/classes/grade.class.php');
  require_once('includes/classes/subject.class.php');
  $gcsform = new GCSForm($dbase);
  $gcsData = $gcsform->findById($_GET['gcs']);  
  $gradeData= (new Grade($dbase))->findById($gcsData['grade_id']);
  $subjectData= (new Subject($dbase))->findById($gcsData['subject_id']);

  $tname = getTeacherName();

  if(isCoordinatorLoggedIn()){
    require_once('includes/classes/teacher.class.php');
    $teacherData = (new Teacher($dbase))->findById($gcsData['teacher_id']);
    $tname = sprintf("%s %s",$teacherData['fname'],$teacherData['lname']);
  }


  $questionData = $gcsform->getAllDetails($gcsData['id']);
  $step1Data = $gcsform->getResult($gcsData['id']);

  $qData = array();

  foreach($questionData as $q){
    if(!isset($qData[$q['question_id']])){
      $qData[$q['question_id']] = array();
    }   

    array_push($qData[$q['question_id']] , $q['name']);
  }

  $qset = (new Step1QSet($dbase))->getAllQuestions();
  $langStep1Result = $lang->get(['step1','result']);
?>

<div class="container">

  <h4 class="mt-3 mb-2 subheading"><?php echo $langStep1Result['heading']?></h4>

  <hr />
  <div class="row">
    <div class="col">
      <?php echo $langStep1Result['teacher']?><b><?php echo $tname?></b>
    </div>
    <div class="col">
      <?php echo $langStep1Result['grade']?><b><?php echo $gradeData['name']?></b>
    </div>
    <div class="col">
    <?php echo $langStep1Result['subject']?><b><?php echo $subjectData['name']?></b>
    </div>
    <div class="col">
      <?php echo $langStep1Result['year']?><b><?php echo $gcsData['year_id']?></b>
    </div>
  </div>  
  <hr />

  <div class="subheading"><?php echo $langStep1Result['subheading1']?></div>
  <span class="note"><?php printf($langStep1Result['note'],getSetting('step1top'))?></span>

  <div class="table-responsive">
    <table class="table table-striped">

    <?php 
      if(count($step1Data)==0){
        printf('<div class="alert alert-info text-center">%s</div>',$langStep1Result['grid']['noresult']);
      }else{?>

      <tr>
        <th><?php echo $langStep1Result['grid']['col1']?></th>
        <th><?php echo $langStep1Result['grid']['col2']?></th>
        <th><?php echo $langStep1Result['grid']['col3']?></th>
      </tr>
      <?php
        $occ = array(); 
        foreach($step1Data as $idx=>$s1){
          array_push($occ,$s1['occ']);
          $occ = array_unique($occ);
          $len = count($occ);
          
          if($len > (int)getSetting('step1disp')) break; //== dont iterate in case of more than step1disp setting occurrence
          
          printf("<tr>
            <td>%d</td>
            <td class='%s'>%s - (%s)</td>
            <td>%d</td>
          </tr>",
          $idx+1,
          $len <= (int)getSetting('step1top') ? "qualified": '',
          $s1['name'],
          $s1['adno'],
          $s1['occ']);
          
        }
      }?>  
    </table>
  </div>
  
  
  <div class="subheading"><?php echo $langStep1Result['subheading2']?></div>

      <?php foreach($qData as $id=>$students){?>

        <div class="row qboxcont">

          <div class="col">
            <div class="qbox"><b>Question <?php echo ((int)$id)?>.</b> <?php echo $qset[$id]?></div>
            <div class="sboxcont">
              <div class="sbox"><?php echo implode('</div><div class="sbox">', $students)?></div>
            </div>

          </div>

        </div>

      <?php }?>  
        
</div>

<style>
  .qboxcont{
    margin: 10px 0px;
    border-radius:10px;
    border: 1px Solid #f1f1f1;
    padding: 5px 2px;  
  }

  .qbox{
    padding: 3px 0px;
    font-size: 18px;
  }

  .sbox{
    float: left;
    background-color: #f4f4f4;
    margin: 2px 2px;
    padding: 5px 8px;
    border-radius: 5px;
  }

  .note,
  .qualified{
    background-color:rgba(245,38,104,0.8);
    color: #fff;
    font-weight: bold;
  }

  span.note{
    padding: 5px 5px;
    font-size: 12px;
  }
</style>


<?php include('includes/files/footer.inc.php'); ?>