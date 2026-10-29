<?php 
$langStep1Box = $lang->get(['teacherDashboard','step1Box']);
?>
<div class="card card-box">
  <!-- img src="images/school.png" class="card-img-top" alt="G & T Form" -->
  <div class="card-body">
    <h5 class="card-title"><?php echo $langStep1Box['heading']?></h5>
    <div class="card-text text-center" style="margin-top:20%;">
      <?php if(teacherHasFilledAllStep1()){?>
        <div class="alert alert-warning"><?php echo $langStep1Box['completed']?></div>
      <?php }else{?>
        <a href="gcs-form.php" class="btn btn-pink"><?php echo $langStep1Box['btnText']?></a>        
      <?php }?>
    </div>
  </div>
    
</div>