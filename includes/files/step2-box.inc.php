<?php 
$langStep2Box = $lang->get(['teacherDashboard','step2Box']);
?>
<div class="card card-box">
  <!-- img src="images/school.png" class="card-img-top" alt="G & T Form" -->
  <div class="card-body">
    <h5 class="card-title"><?php echo $langStep2Box['heading']?></h5>
    <div class="card-text text-center" style="margin-top:20%;">
      <?php if(teacherHasFilledAllStep2()){?>
        <div class="alert alert-warning"><?php echo $langStep2Box['completed']?></div>
      <?php }else{?>
        <a href="step2-form.php" class="btn btn-pink"><?php echo $langStep2Box['btnText']?></a>        
      <?php }?>
    </div>
  </div>
    
</div>