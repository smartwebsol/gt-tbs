<?php 
  include('includes/files/header.inc.php'); 
  securePage();  
  $langSettings = $lang->get(['settings']);
  ?>
<div class="container">

  <form class="border p-4" action="forms/settings.action.php" method="post">

  <h4 class="subheading"><?php echo $langSettings['heading']?></h4>

    <div class="row">
      <div class="col-sm-5 text-right">
        <?php echo $langSettings['step1Editable']?>
      </div>
      <div class="col-sm-7 text-left">
        <input type="number" class="form-control setting" name="step1edit" value="<?php echo getSetting('step1edit')?>" />
      </div>
    </div>
    
    <div class="row mt-2">
      <div class="col-sm-5 text-right">
        <?php echo $langSettings['step1Result']?>
      </div>
      <div class="col-sm-7 text-left">
        <input type="number" class="form-control setting" name="step1top" value="<?php echo getSetting('step1top')?>" />
      </div>
    </div>

    <div class="row mt-2">
      <div class="col-sm-5 text-right">
        <?php echo $langSettings['step1Display']?>
      </div>
      <div class="col-sm-7 text-left">
        <input type="number" class="form-control setting" name="step1disp" value="<?php echo getSetting('step1disp')?>" />
      </div>
    </div>

    <div class="row mt-2">
      <div class="col-sm-5 text-right">
        <?php echo $langSettings['step2Editable']?>
      </div>
      <div class="col-sm-7 text-left">
        <input type="number" class="form-control setting" name="step2edit" value="<?php echo getSetting('step2edit')?>" />
      </div>
    </div>

    <div class="row mt-2">
    <div class="col-sm-5 text-right"></div>
      <div class="col-sm-7 text-left">
        <input type="submit" class="btn btn-pink" value="<?php echo $langSettings['submit']?>" />
      </div>
    </div>
  </form>

</div>

<style>
  input.setting{
    max-width: 100px;
  }
</style>