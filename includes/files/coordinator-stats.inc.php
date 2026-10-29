<?php 
  require_once(_PATH.'includes/classes/teacher.class.php');
  require_once(_PATH.'includes/classes/grade.class.php');
  require_once(_PATH.'includes/classes/subject.class.php');
  require_once(_PATH.'includes/classes/student.class.php');
  require_once(_PATH.'includes/classes/gcsform.class.php');
  require_once(_PATH.'includes/classes/step1results.class.php');
  require_once(_PATH.'includes/classes/step2form.class.php');
  $teacher = new Teacher($dbase);  
  $grade = new Grade($dbase);
  $subject = new Subject($dbase);
  $student = new Student($dbase);
  $gcsform = new GCSForm($dbase);
  $step1result = new Step1Result($dbase);
  $step2form = new Step2Form($dbase);
  $stats = array(
    'stats1' => $teacher->total(),
    'stats2' => $grade->total(),
    'stats3' => $subject->total(),
    'stats4' => $student->total(),
    'stats5' => $gcsform->total(),
    'stats6' => $step1result->total(),
    'stats7' => $step2form->total()
  );

  $langCoBox = $lang->get(['teacherDashboard','coordinatorBox']);
?>

<div class="card card-box" style="max-width: 100%;">
  <!-- img src="images/class.jpg" class="card-img-top" alt="Teacher Statistics" -->
  <div class="card-body">
    <h5 class="card-title"><?php echo $langCoBox['heading']?><a href="/stats.php"><span class="fa fa-cog"></span></a></h5>
    <p class="card-text">
      <?php foreach($stats as $k=>$v){?>
        <div class="row">  
          <div class="col p-2 text-right"><?php echo $langCoBox[$k]?> : </div>
          <div class="col p-2"><span class="values p-1 m-1"><?php echo $v?></span></div>
        </div>  
      <?php }?>
      

    </p>

  </div>
</div>

<style>
.card-box .values{
  border-radius: 5px;
  border: 1px dotted #FF2768;
  background-color: #efefef;
  color: #555;
  font-weight: bold;
}
</style>