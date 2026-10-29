<?php 
  include('includes/files/header.inc.php'); 
  securePage();

  require_once('includes/classes/step2form.class.php');
  require_once('includes/classes/student.class.php');  
  require_once('includes/classes/grade.class.php');  
  require_once('includes/classes/subject.class.php');
  $step2form = new Step2Form($dbase);
  $s2Data = $step2form->findById($_GET['s2']);  
  $gradeData= (new Grade($dbase))->findById($s2Data['grade_id']);
  $subjectData= (new Subject($dbase))->findById($s2Data['subject_id']);
  $studentData= (new Student($dbase))->findById($s2Data['student_id']);

  $tname = getTeacherName();

  if(isCoordinatorLoggedIn()){
    require_once('includes/classes/teacher.class.php');
    $teacherData = (new Teacher($dbase))->findById($s2Data['teacher_id']);
    $tname = sprintf("%s %s",$teacherData['fname'],$teacherData['lname']);
  }

  $s2DetailsData = $step2form->getAllDetails($s2Data['id']);
  $max = count($s2DetailsData);
  
  $langStep2Result = $lang->get(['step2','result']);
?>

<div class="container">

  <h4 class="mt-3 mb-2 subheading"><?php echo $langStep2Result['heading']?></h4>

  <hr />
  <div class="row">    
    <div class="col-sm-6 mb-3">
      <?php echo $langStep2Result['student']?><b class="note" style="font-size:20px"><?php echo $studentData['name']?></b>
    </div>    
    <div class="col-sm-6 mb-3">
      <?php echo $langStep2Result['score']?><b class="note" style="font-size:20px"><?php printf('%0.2f',($s2Data['result']/$max)*100);?>% (<?php echo $s2Data['result']?>/<?php echo $max?>)</b>
    </div>    
    <div class="col-sm-6">
      <?php echo $langStep2Result['teacher']?><b><?php echo $tname?></b>
    </div>
    <div class="col-sm-6">
      <?php echo $langStep2Result['grade']?><b><?php echo $gradeData['name']?> / <?php echo $subjectData['name']?></b>
    </div>
  </div>  
  <hr />

  <div class="subheading"><?php echo $langStep2Result['subheading1']?></div>
  <span class="note"><?php echo $langStep2Result['note']?></span>

  <div class="table-responsive">
    <table class="table table-striped">

    <?php 
      if($max==0){
        printf('<div class="alert alert-info text-center">%s</div>',$langStep2Result['grid']['noresult']);
      }else{?>

      <tr>
        <th><?php echo $langStep2Result['grid']['col1']?></th>
        <th><?php echo $langStep2Result['grid']['col2']?></th>
        <th><?php echo $langStep2Result['grid']['col3']?></th>
      </tr>
      <?php
        //$occ = array(); 
        
        foreach($s2DetailsData as $idx=>$s1){
          //array_push($occ,$s1['occ']);
          //$occ = array_unique($occ);
          //$len = count($occ);
          
          //if($len>3) break; //== dont iterate in case of more than 3 occurrence
          
          printf("<tr>
            <td>%d</td>
            <td>%s</td>
            <td %s>%s</td>
          </tr>",
          $idx+1,          
          $s1['question'],
          is_null($s1['answer']) ? "" : "class='highlighted'",
          getPointName($s1['answer']));
          
        }
      }?>  
    </table>
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
  .highlighted{
    background-color:rgba(245,38,104,0.8);
    color: #fff;
    font-weight: bold;
    padding: 5px 5px;
  }

  .note{
    padding: 5px 5px;
    font-size: 12px;
  }
</style>


<?php include('includes/files/footer.inc.php'); ?>