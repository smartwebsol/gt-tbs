<?php 
  include('includes/files/header.inc.php'); 
  securePage();
  $tname = getTeacherName();
  $tid = getTeacherId();
  if(isset($_GET['gcs'])){
    require_once('includes/classes/gcsform.class.php');        
    $gcsform = new GCSForm($dbase);
    $gcsData = $gcsform->findById((int)$_GET['gcs']);

    if($gcsData && !empty($gcsData)){

      if(isCoordinatorLoggedIn()){
        require_once('includes/classes/teacher.class.php');
        $tid=$gcsData['teacher_id'];
        $teacherData = (new Teacher($dbase))->findById($tid);        
        $tname = sprintf("%s %s",$teacherData['fname'],$teacherData['lname']);
      }

      $gcsform1 = new GCSForm($dbase,$gcsData);
      if(!isCoordinatorLoggedIn() && (!canEditGCSForm($gcsData['created_on']) || $gcsform1->isResultDeclared())){
        setMessage('You cannot edit this form anymore!','warning');
        printf('<script>window.location.href="/gcs-result.php?gcs=%d";</script>',$_GET['gcs']);        
      }
    }
  }

  $langStep1Form = $lang->get(['step1','form']);
?>

  <div class="container">

    <h4 class="mt-3 mb-2 subheading"><?php echo $langStep1Form['heading']?></h4>
    
    <div class="row pt-3" id="gcs-selectors">

      <div class="col-sm-3">

        <div class="input-group mb-3">
          <div class="input-group-prepend">
            <span class="input-group-text"><?php echo $langStep1Form['teacher']?></span>
          </div>
          <div class="input-group-text"><i><b> <?php echo $tname?> </b></i>
          <input type="hidden" id="tid" name="teacher_id" value="<?php echo $tid?>" />
          </div>
        </div>

      </div>

      <div class="col-sm-3">

        <div class="input-group mb-3">
          <div class="input-group-prepend">
            <span class="input-group-text"><?php echo $langStep1Form['grade']?></span>
          </div>
          <select class="form-control" id="selGrade">
            <option selected value="0">- Select -</option>
            <?php 
              $grades = getAllGrades($tid);
              foreach($grades as $g){
                printf('<option value="%d">%s</option>',$g['id'],$g['name']);
              }
            ?>
          </select>
        </div>

      </div>

      <div class="col-sm-3">

        <div class="input-group mb-3">
          <div class="input-group-prepend">
            <span class="input-group-text"><?php echo $langStep1Form['subject']?></span>
          </div>
          <select class="form-control" id="selSubject">
            <option selected value="0">- Select -</option>
          </select>
        </div>

      </div>

      <div class="col-sm-3">

        <div class="input-group mb-3">
          <div class="input-group-prepend">
            <span class="input-group-text"><?php echo $langStep1Form['year']?></span>
          </div>
          <select class="form-control" id="selYear">
            <option selected value="0">- Select -</option>
            <?php 
            $y = 2019;
            while($y <= date('Y')){
              printf('<option value="%d">%d</option>',$y,$y);
              $y++;
            }?>
          </select>
        </div>

      </div>

    </div>
    

    <div class="row text-center" id="btn-start">
      <div class="col">
        <button class="btn btn-pink" onclick="searchGCSForm()"><?php echo $langStep1Form['btnSearch']?></button>
      </div>
    </div>


    <form id="gcsFormForm" method="post" action="forms/gcsform.action.php" onSubmit="return gcsFormValidation(this)">
      <input type="hidden" name="action" value="saveGCSForm" />
      <input type="hidden" name="gcsform_id" value="<?php echo $_GET['gcs']?>" />
      <input type="hidden" name="grade_id" value="0" />
      <input type="hidden" name="subject_id" value="0" />      
      <input type="hidden" name="year_id" value="0" />
      <input type="hidden" name="declareResult" value="0" />
      <div class="text-left" id="gcs-form">

      </div>

      <div class="row text-center" id="btn-submit" style="display: none;">
        <div class="col pb-4">
          <button type="submit" class="btn btn-pink"><?php echo $langStep1Form['btnSave']?></button>
          <button type="button" class="btn btn-danger" onclick="saveAndSubmitStep1(this.form)"><?php echo $langStep1Form['btnSubmit']?></button>
          <a href="gcs-list.php" class="btn btn-secondary"><?php echo $langStep1Form['btnCancel']?></a>
        </div>
      </div>
    </form>

    <div class="modal fade" id="recexists">
      <div class="modal-dialog">
        <div class="modal-content">
        
          <!-- Modal Header -->
          <!-- div class="modal-header">
            <h4 class="modal-title">Modal Heading</h4>
            <button type="button" class="close" data-dismiss="modal">×</button>
          </div -->
          
          <!-- Modal body -->
          <div class="modal-body text-center">
            <?php echo $langStep1Form['dialog']['text']?>
          </div>
          
          <!-- Modal footer -->
          <div class="modal-footer">
            <!-- button type="button" class="btn btn-danger" data-dismiss="modal">Overwrite</button -->
            <a class="btn btn-sm btn-pink" id="aResultLink" href=""><?php echo $langStep1Form['dialog']['result']?></a>
            <a class="btn btn-danger btn-sm " id="aEditLink" href=""><?php echo $langStep1Form['dialog']['edit']?></a>
            <a class="btn btn-info btn-sm" data-dismiss="modal"><?php echo $langStep1Form['dialog']['search']?></a>
          </div>
          
        </div>
      </div>
    </div>

  </div>  

<?php include('includes/files/footer.inc.php');?>
   
