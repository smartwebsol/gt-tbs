<?php 
  require_once(_PATH.'includes/classes/teacher.class.php');

  $teacher = new Teacher($dbase,$_SESSION['teacherData']);
  $grades = $teacher->getAllGrades();
  $gradenames = implode(', ',array_map(function($a){return $a['name'];},$grades));
    
  $subjects = $teacher->getAllSubjects(1);
  $students = $teacher->getAllStudents(1);

  $langProfileBox = $lang->get(['teacherDashboard','profileBox']);
?>

<div class="card card-box">
  <!-- img src="images/class.jpg" class="card-img-top" alt="Teacher Statistics" -->
  <div class="card-body">
    <h5 class="card-title"><?php echo $langProfileBox['heading']?></h5>
    <p class="card-text">
      <?php showTeacherName()?><br />
      <?php echo $teacher->getEmail()?>   

      <div class="row">  
        <div class="col-sm-5"><?php echo $langProfileBox['grades']?> (<?php echo count($grades)?>): </div>
        <div class="col"><?php echo $gradenames?></div>
      </div>  
      <div class="row">  
        <div class="col-sm-5"><?php echo $langProfileBox['subjects']?> </div>
        <div class="col"><?php echo $subjects?></div>
      </div>  
      <div class="row">  
        <div class="col-sm-5"><?php echo $langProfileBox['students']?> </div>
        <div class="col"><?php echo $students?></div>
      </div>  

    </p>

  </div>
</div>