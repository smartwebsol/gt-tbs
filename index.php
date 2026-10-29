<?php include('includes/files/header.inc.php');?>

  <div class="container">

    <?php if(isTeacherLoggedIn()) { ?>
      
      <h4 class="subheading"> Welcome <i><?php showTeacherName()?>,</i></h4>

      <div class="row">

        <div class="col-sm-4 p-1" style="min-width:300px;">
          <?php include('includes/files/teacher-stats.inc.php')?>
        </div>

        <div class="col-sm-4 p-1">
          <?php include('includes/files/step1-box.inc.php');?>
        </div>

        <?php if(step1ResultsDeclared()) { ?>
        <div class="col-sm-4 p-1">
          <?php include('includes/files/step2-box.inc.php');?>
        </div>
        <?php }?>

        <?php if(isCoordinatorLoggedIn()){?>
        <div class="col-sm-12 p-1">
          <?php include('includes/files/coordinator-stats.inc.php')?>
        </div>
        <?php }?>

      </div>

    <?php }else{ 

      include('includes/files/login.inc.php');

    }?>


  </div>  

  <style>
    .card-box{
      min-width: 300px;
      max-width: 300px;      
      height: 100%;
      border-color:#FF2768;      
    }

    .card-box .card-title{
      font-weight:bold;      
    }

    @media only screen and (max-width: 600px) {
      .card-box{
        width:unset;
      }
    }
  </style>  

<?php include('includes/files/footer.inc.php')?>