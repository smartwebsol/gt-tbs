<?php 
  include('includes/files/header.inc.php');
  securePage();
  $langStats = $lang->get(['stats']);
?>

  <div class="container">

    <ul class="nav nav-pills nav-justified" role="tablist">
      <li class="nav-item active"><a class="nav-link active" data-toggle="pill" href="#pill1"><?php echo $langStats['tab1']?></a></li>
      <li class="nav-item"><a class="nav-link" data-toggle="pill" href="#pill2"><?php echo $langStats['tab2']?></a></li>      
      <li class="nav-item"><a class="nav-link" data-toggle="pill" href="#pill3"><?php echo $langStats['tab3']?></a></li>      
    </ul>

    <div class="tab-content chart-container">
      <div id="pill1" class="tab-pane container fade show active">        
        <canvas id="generic-stats"></canvas>
      </div>
      <div id="pill2" class="tab-pane container fade">     
        <div class="text-center alert alert-info mt-2">
          Below statistics is for how many students scored in these exams.
        </div>
        <canvas id="step3-stats"></canvas>
      </div>      
      <div id="pill3" class="tab-pane container fade">   
        <div class="text-center alert alert-info mt-2">
          Search and select a student (by <u>Name</u> "containing") to look at score graph.
        </div>     
        <input id="studentSearch" placeholder="Type Student's name" class="form-control mt-3 mb-3" />
        <canvas id="specific-stats"></canvas>
      </div>      
    </div>

  
  </div>    

  <style>
  .chart-container {
    border: 0px Solid #000;
    position: relative;
    margin: auto;        
    margin: 0px auto;
  }
  </style>

<?php include('includes/files/footer.inc.php')?>