<?php 
  include('includes/files/header.inc.php');   
  securePage();

  require_once('includes/classes/teacher.class.php');
  require_once('includes/classes/step1results.class.php');
  require_once('includes/classes/step2qset.class.php');
  require_once('includes/classes/step2form.class.php');
  $editMode = false;
  $studentName="";
  $date = date('m-d Y');
  $gradeName='';   

  if(isset($_GET['s2'])){
    
    $s2form = new Step2Form($dbase);
    $s2Data = $s2form->findById((int)$_GET['s2']);

    if($s2Data && !empty($s2Data)){

      if(!canEditStep2Form($s2Data['created_on'])){
        setMessage('You cannot edit this form anymore!','warning');
        header('location:step2-result.php?s2='.$_GET['s2']);
      }else{        
        $editMode = true; //== allow edit mode
        require_once('includes/classes/student.class.php');
        require_once('includes/classes/grade.class.php');
        require_once('includes/classes/subject.class.php');
        $student = new Student($dbase);
        $student = $student->findById($s2Data['student_id']);        
        $studentName = $student['name'];        

        $grade = new Grade($dbase);
        $grade = $grade->findById($s2Data['grade_id']);
        $subject = new Subject($dbase);
        $subject = $subject->findById($s2Data['subject_id']);

        $tname = getTeacherName();

        if(isCoordinatorLoggedIn()){
          require_once('includes/classes/teacher.class.php');
          $teacherData = (new Teacher($dbase))->findById($s2Data['teacher_id']);
          $tname = sprintf("%s %s",$teacherData['fname'],$teacherData['lname']);
        }

        $gradeName = $grade['name'] . ' / ' . $subject['name'];
        
        $date = date('m-d Y',strtotime($s2Data['created_on']));        
        $s2DetailsData = $s2form->getAllDetails($s2Data['id']);
      }
    }

  }

  $langStep2Form = $lang->get(['step2','form']);
?>

  <div class="container">

    <h4 class="mt-3 mb-2 subheading"><?php echo $langStep2Form['heading']?></h4>
    
    <?php if(!$editMode){?>
      <div class="row pt-3" id="step2-selectors">

        <div class="col-sm-12">

          <div class="input-group mb-3">
            <div class="input-group-prepend">
              <span class="input-group-text"><?php echo $langStep2Form['student']?></span>
            </div>
            <select class="form-control" id="selStep2Student">
              <option selected value="0">- Select -</option>
              <?php 
                $students = (new Step1Result($dbase))->getStep1Qualifiers(isCoordinatorLoggedIn() ? "all" : $_SESSION['teacherData']['id']);
                foreach($students as $s){
                  printf('<option value="%d" sname="%s" grade="%s" subject="%s">%s (%s-%s)</option>',$s['id'],$s['name'], $s['grade'], $s['subject'],$s['name'], $s['grade'], $s['subject']);
                }
              ?>
            </select>
          </div>

        </div>

      </div>
      

      <div class="row text-center" id="btn-start">
        <div class="col">
          <button class="btn btn-pink" onclick="showStep2Form()"><?php echo $langStep2Form['btnSearch']?></button>
        </div>
      </div>
    <?php }?>

    <form id="step2Form" method="post" action="forms/step2form.action.php" onSubmit="return step2FormValidation(this)" style="display:<?php echo $editMode ? 'block':'none'?>;">
      <input type="hidden" name="action" value="saveStep2Form" />
      <input type="hidden" name="step2form_id" value="<?php echo $editMode ? $s2Data['id'] : 0?>" />
      <input type="hidden" name="s1r" value="0" />
      <div class="text-left" id="step2-form">

        <div class="row">

          <div class="col-sm-6">
            <div class="input-group mb-3">
              <div class="input-group-prepend">
                <span class="input-group-text"><?php echo $langStep2Form['student']?></span>
              </div>
              <div class="input-group-text"><i><b id="studentName"><?php echo $studentName?></b></i></div>
            </div>
          </div>
          <div class="col-sm-6">
            <div class="input-group mb-3">
              <div class="input-group-prepend">
                <span class="input-group-text"><?php echo $langStep2Form['date']?></span>
              </div>
              <div class="input-group-text"><i><b><?php echo $date?></b></i></div>
            </div>
          </div>

        </div>  

        <div class="row">
          <div class="col-sm-6">
            <div class="input-group mb-3">
              <div class="input-group-prepend">
                <span class="input-group-text"><?php echo $langStep2Form['grade']?></span>
              </div>
              <div class="input-group-text"><i><b id="gradeName"><?php echo $gradeName?></b></i></div>
            </div>
          </div>
          <div class="col-sm-6">
            <div class="input-group mb-3">
              <div class="input-group-prepend">
                <span class="input-group-text"><?php echo $langStep2Form['teacher']?></span>
              </div>
              <div class="input-group-text"><i><b> <?php echo $tname?> </b></i></div>
            </div>
          </div>
        </div>      

      </div>

      <?php 
        $step2qset = new Step2Qset($dbase);
        $qset = $step2qset->getAllQuestions();
        $columns = $step2qset->columns();
      ?>

      <div class="col-sm-12 mt-3 mb-3 subheading text-center"><?php echo $langStep2Form['grid']['heading']?></div>

      <div class="table-responsive col-border">
        <table class="table">
          
          <tr>
            <th><?php echo $langStep2Form['grid']['col1']?></th>
            <th><?php echo $langStep2Form['grid']['col2']?></th>
            
            <?php 
              foreach($columns as $colname=>$prosaName){
                printf('<th>%s</th>',$prosaName);
              }
            ?>
          </tr>
          
          <?php           
          function addChecked($qid,$point){            
            global $s2DetailsData;
            $found = false;            

            foreach($s2DetailsData as $row){
              if($row['id']==$qid && $row['answer']==$point){
                $found = true;
              }
            }
            if($found){
              return 'checked';
            }
            return '';            
          }
          
          foreach($qset as $idx=>$row){  
            printf('<tr class="questionrows" question="%d">
              <td>%d</td>
              <td>%s</td>
              <td class="text-center"><div class="radiocheck %s"><input type="radio" name="points[%d]" value="%f" %s /></div></td>
              <td class="text-center"><div class="radiocheck %s"><input type="radio" name="points[%d]" value="%f" %s /></div></td>
              <td class="text-center"><div class="radiocheck %s"><input type="radio" name="points[%d]" value="%f" %s /></div></td>
              <td class="text-center"><div class="radiocheck %s"><input type="radio" name="points[%d]" value="%f" %s /></div></td>
            </tr>',
            $idx+1,
            $idx+1,
            $row['question'],
            addChecked($row['id'],$row['point1']),
            $row['id'],
            $row['point1'],
            addChecked($row['id'],$row['point1']),
            addChecked($row['id'],$row['point2']),
            $row['id'],
            $row['point2'],
            addChecked($row['id'],$row['point2']),
            addChecked($row['id'],$row['point3']),
            $row['id'],
            $row['point3'],
            addChecked($row['id'],$row['point3']),
            addChecked($row['id'],$row['point4']),
            $row['id'],
            $row['point4'],
            addChecked($row['id'],$row['point4'])
            );
          }?>

          <tr>
            <td colspan="2" class="text-right">Total marks (out of <?php echo count($qset)?>): </td>
            <td colspan="4" class="text-center"><span id="step2Score">0 (0%)</span></td>
          </tr>
        </table>    
      </div>

      <div class="row text-center" id="btn-submit">
        <div class="col pb-4">
          
          <button type="submit" class="btn btn-pink"><?php echo $langStep2Form['btnSubmit']?></button>
          <a href="step2-form.php" class="btn btn-secondary"><?php echo $langStep2Form['btnCancel']?></a>
        </div>
      </div>
    </form>

  </div>  
  <style>
  #step2Score{
    color:#FF2768;         
    font-size: 20px;
    font-weight: bold;
  }

  .radiocheck{   
    margin: auto auto;    
    background-color:#FF2768;     
    opacity: 0.1;
    cursor:pointer;        
    border-radius: 40px;
    width: 40px;
    height: 40px;
  }

  .radiocheck:hover{
    opacity: 0.5;    
  }

  .radiocheck input[type=radio]{
    display:none;
  }

  .radiocheck.checked{
    background-color:#FF2768;     
    opacity: 1;
  }

  .col-border td,.col-border th{
    border-left: 1px Solid #DEE2E6;
  }
  
  .col-border tr:last-child{
    border-bottom: 1px Solid #DEE2E6;
  }

  .col-border table{
    border-right: 1px Solid #DEE2E6;
  }
  </style>
<?php include('includes/files/footer.inc.php');?> 
