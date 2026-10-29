<?php include('includes/config.inc.php')?>
<!doctype html>
<html lang="en">
  <head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <meta name="description" content="">
    <meta name="author" content="">
    <!-- link rel="icon" href="favicon.ico" -->

    <title><?php echo $lang->get(['siteTitle'])?></title>

    <!-- Bootstrap core CSS -->
    <link href="dist/css/bootstrap.min.css" rel="stylesheet">

    <!-- FontAwesome CSS -->
    <link rel="stylesheet" href="https://use.fontawesome.com/releases/v5.7.0/css/all.css" integrity="sha384-lZN37f5QGtY3VHgisS14W3ExzMWZxybE1SJSEsQp9S+oqd12jhcu+A56Ebc1zFSJ" crossorigin="anonymous">

     <!-- JQuery UI CSS -->
     <link href="dist/css/jquery-ui.min.css" rel="stylesheet">

     <style>
       .heading{
          background-color: #FF2768;
          border-radius: 10px;
          padding: 10px;
        }
        .heading .title{
          color: #ffffff;          
          font-size: 15px;
          font-weight: bold;
        }

        .subheading{
          color: #0062A0 !important;          
          font-weight: 700;
        }

        .menu-box{
          text-align:right;
          color: #fff;
        }

        .menu-box a{
          color:#ffc;
          font-size: 12px;
          font-weight: bold;          
        }

        .btn.btn-pink,
        .btn-lg.btn-pink,
        .btn-sm.btn-pink,
        .btn-xs.btn-pink
        {
          background-color: #FF2768;
          color:#fff;
          border-color:#FF2768;
        }

        .btn.btn-pink.focus,.btn.btn-pink:focus{
          background-color: #FF2768;
        }

     </style>  

  </head>
  <body>

    <main role="main">

    <header class="container mb-2">
      <div class="row">
        <div class="col">
          <img src="images/logofinal.png" height="100" />
        </div>  
      </div>
      <div class="heading">         

        <div class="row">
          
          <div class="col-sm-4 title"><?php echo (isTeacherLoggedIn() ? $lang->get(['header','heading','afterLogin']) : $lang->get(['header','heading','beforeLogin']))?></div>
          <?php $menusText = $lang->get(['header','menus'])?>
          <div class="col-sm-8 menu-box">
            <a href="<?php echo _URL?>"><?php echo $menusText['home']?></a>
            <?php if(isCoordinatorLoggedIn()){?>
              | <a href="<?php echo _URL?>settings.php"><?php echo $menusText['settings']?></a>
              | <a href="<?php echo _URL?>step3.php"><?php echo $menusText['step3']?></a>                            
              | <a href="<?php echo _URL?>step2-form.php"><?php echo $menusText['step2form']?></a>
              | <a href="<?php echo _URL?>step2-list.php"><?php echo $menusText['step2list']?></a>
              | <a href="<?php echo _URL?>gcs-form.php"><?php echo $menusText['step1form']?></a>
              | <a href="<?php echo _URL?>gcs-list.php"><?php echo $menusText['step1list']?></a>
              | <a href="<?php echo _URL?>forms/logout.action.php"><?php echo $menusText['logout']?></a>
            <?php } else if(isTeacherLoggedIn()){?>            
              <?php if(step1ResultsDeclared()){?>
              | <a href="<?php echo _URL?>step2-form.php"><?php echo $menusText['step2form']?></a>
              | <a href="<?php echo _URL?>step2-list.php"><?php echo $menusText['step2list']?></a>
              <?php if(!teacherHasFilledAllStep1()){?>
                | <a href="<?php echo _URL?>gcs-form.php"><?php echo $menusText['step1form']?></a>
              <?php }?>
              <?php }else{?>
              | <a href="<?php echo _URL?>gcs-form.php"><?php echo $menusText['step1form']?></a>
              <?php }?>
              | <a href="<?php echo _URL?>gcs-list.php"><?php echo $menusText['step1list']?></a>
              | <a href="<?php echo _URL?>forms/logout.action.php"><?php echo $menusText['logout']?></a>
            <?php }else{?>
              
            <?php }?>  
          </div>

        </div>  

      </div>
    </header>

    <div class="container"><?php flashMessage()?></div>