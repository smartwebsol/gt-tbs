<?php 
  include('includes/files/header.inc.php');   
  securePage();
  require_once('includes/classes/step2form.class.php');
  require_once('includes/classes/step3mark.class.php');
  require_once('includes/classes/subject.class.php');
  $s2form = new Step2Form($dbase);
  $s3mark = new Step3Mark($dbase);

  $search = array();
  $pagename=basename($_SERVER['SCRIPT_FILENAME']);  

  if(isset($_POST['search'])){
    foreach($_POST['search'] as $k=>$v){
      if($v!=''){
        $search[$k]=$v;
      }
    }

    $_SESSION[$pagename]['search']=$search;
  }

  if(isset($_SESSION[$pagename]['search'])){
    $search = $_SESSION[$pagename]['search'];
  }else{
    $search['year_id']=date('Y');
    $search['sort']['by']='gradename';
    $search['sort']['dir']='asc';
    $_SESSION[$pagename]['search']=$search;
  }

  $rows = $s3mark->findAllStep3StudentDetails($search,$search['sort']);  

  $prefillCond = " WHERE step3=1";

  $date = date('m-d Y');
  $langStep3 = $lang->get(['step3']);
  $examTypes = $s3mark->examTypes();
?>

  <div class="container">

    <h4 class="mt-3 mb-2 subheading"><?php echo $langStep3['heading']?></h4>

    <div class="subheading fade"><?php echo $langStep3['labelImport']?>
      <a href="<?php echo _URL?>forms/download.php?file=s3">
        <?php echo $langStep3['templatecsv']?> 
        <span class="fa fa-file-csv filecss"></span>
      </a>
    </div>  

    <div class="row fade">
      <div class="col">
        <input type="file" class="form-control upload-file" />        
      </div>
      <div class="col mt-1">
        <button class="btn btn-pink"><?php echo $langStep3['submit-import']?></button>
      </div>
    </div>

    <script>
    function sortColumn(by,dir){
      $('#search_sort_by').val(by);
      $('#search_sort_dir').val(dir);
      $('#searchform')[0].submit();
      return false;
    }
    </script>
    <form action="<?php echo _URL?>step3.php" method="post" id="searchform">
    <input type="hidden" id="search_sort_by" name="search[sort][by]" value="<?php echo $search['sort']['by']?>" />
    <input type="hidden" id="search_sort_dir" name="search[sort][dir]" value="<?php echo $search['sort']['dir']?>" />
    <div class="row">
      
      <div class="col-sm-2">

        <div class="input-group mb-3">
          <div class="input-group-prepend">
            <span class="input-group-text"><?php echo $langStep3['search']['year']?></span>
          </div>
          <select class="form-control" name="search[year_id]">
            <option value="">All</option>
            <?php           
            $years = $s2form->getAllYears($prefillCond);
            foreach($years as $y){
              $selected = '';
              if($search['year_id']==$y['year_id']){
                $selected = " selected";
              }
              printf('<option value="%s" %s>%s</option>',$y['year_id'],$selected,$y['year_id']);
            }?>
          </select>
        </div>

      </div>

      <div class="col-sm-3">

        <div class="input-group mb-3">
          <div class="input-group-prepend">
            <span class="input-group-text"><?php echo $langStep3['search']['grade']?></span>
          </div>
          <select class="form-control" name="search[grade_id]">
            <option value="">All</option>
            <?php 
            $grades = $s2form->getAllGrades($prefillCond);
            foreach($grades as $g){
              $selected = '';
              if($search['grade_id']==$g['id']){
                $selected = " selected";
              }
              printf('<option value="%d" %s>%s</option>',$g['id'],$selected,$g['name']);
            }?>
          </select>
        </div>

      </div>

      <div class="col-sm-3">

        <div class="input-group mb-3">
          <div class="input-group-prepend">
            <span class="input-group-text"><?php echo $langStep3['search']['subject']?></span>
          </div>
          <select class="form-control" name="search[subject_id]">
            <option value="">All</option>
            <?php 
            $subjects = (new Subject($dbase))->getAll();
            foreach($subjects as $s){
              $selected = '';
              if($search['subject_id']==$s['id']){
                $selected = " selected";
              }
              printf('<option value="%d" %s>%s</option>',$s['id'],$selected,$s['name']);
            }?>
          </select>
        </div>

      </div>

      <div class="col-sm-3">

        <div class="input-group mb-3">
          <div class="input-group-prepend">
            <span class="input-group-text"><?php echo $langStep3['search']['exam']?></span>
          </div>
          <select class="form-control" name="search[exam_type]">
            <option value="">All</option>
            <?php             
            foreach($examTypes as $k=>$v){
              $selected = '';
              if($search['exam_type']==$k){
                $selected = " selected";
              }
              printf('<option value="%d" %s>%s</option>',$k,$selected,$v);
            }?>
          </select>
        </div>

      </div>

      <div class="col-sm-1">
        <button type="submit" class="btn btn-pink">Search</button>
      </div>

    </div>
    </form>

    <script>
      function validateS3(frm){
        var errEle = false,prefix, et, sc, found=false;
        $(frm).find('.scoremode').each(function(){
          if(!errEle){
            prefix = $(this).attr('prefix');
            et  = $('#exam_type_'+prefix).val();
            sc = $('#score_'+prefix).val();
            if(this.value=='old'){
              if(parseInt(et)==0){
                errEle=$('#exam_type_'+prefix);
              }
              else if(parseFloat(sc) <= 0){
                errEle=$('#score_'+prefix);
              }else{
                found=true;
              }
            }else{
              if(parseInt(et)>0 && sc!=''){
                if(parseFloat(sc) <= 0){
                  errEle=$('#score_'+prefix);
                }else{
                  found=true;
                }
              }
            }
          }
        });

        if(errEle){
          errEle[0].focus();
          alert('Fill the form correctly');
          return false;
        }

        if(!found){
          alert('Please fill form to continue!');
          return false;
        }

        return true;
      }
    </script>


    <form method="post" action="forms/step3.action.php" onsubmit="return validateS3(this)">
      <input type="hidden" name="action" value="savescore" />

      <div class="table-responsive">
        <table class="table table-hover">
          
          <?php 
          if(count($rows)==0){
            printf('<div class="alert alert-info text-center">%s</div>',$langStep3['grid']['noresult']);
          }else{?>

          <tr>
            <th><?php echo $langStep3['grid']['col1']?></th>
            <th><?php echo sortColumn($langStep3['grid']['col2'],'studentname',$search)?></th>
            <th><?php echo sortColumn($langStep3['grid']['col5'],'year_id',$search)?></th>
            <th><?php echo sortColumn($langStep3['grid']['col3'],'gradename',$search)?></th>
            <th><?php echo sortColumn($langStep3['grid']['col4'],'subjectname',$search)?></th>                  
            <th><?php echo sortColumn($langStep3['grid']['col6'],'exam_type',$search)?></th>
            <th><?php echo sortColumn($langStep3['grid']['col7'],'score',$search)?></th>          
          </tr>
          
          <?php 
            foreach($rows as $idx=>$row){             

            $id=$row['s3id'] > 0 ? $row['s3id'] : $row['id'];

            $exam_type_options = '<option value="0"> - SELECT - </option>';

            foreach($examTypes as $k=>$v){              
              $selected = $k==$row['exam_type'] ? 'selected' : '';
              $exam_type_options .= sprintf('<option value="%d" %s>%s</option>',$k,$selected,$v);              
            }

            $prefix = sprintf('%d_%d_%d_%d',$row['student_id'],$row['yid'],$row['gid'],$row['subid']);

            printf("<tr>
                <td>%d</td>              
                <td>%s</td>
                <td>%d</td>
                <td>%s</td>
                <td>%s</td>              
                <td class='w150'>
                  <input type='hidden' name='scoremode[%s]' prefix='%s' class='scoremode' value='%s' />
                  <input type='hidden' name='s3id[%s]' value='%d' />
                  <select id='exam_type_%s' name='exam_type[%s]' class='form-control'>%s</select>
                </td>
                <td class='w150'><input type='text' id='score_%s' name='score[%s]' class='form-control' value='%s' /></td>
              </tr>",
              $idx+1,            
              $row['studentname'],
              $row['year_id'],
              $row['gradename'],
              $row['subjectname'],
              $prefix,
              $prefix,
              $row['s3id'] > 0 ? 'old' : 'new',
              $prefix,
              $row['s3id'] > 0 ? $row['s3id'] : '',
              $prefix,
              $prefix,
              $exam_type_options,
              $prefix,
              $prefix,
              $row['score'] > 0 ? number_format($row['score'],2) : ''
            );

            }

          }?>
          <tr>
          <td colspan="8" class="text-center"><input type="submit" class="btn btn-pink" type="submit" value="<?php echo $langStep3['grid']['save']?> " />
        </table>
      </div>

    </form>


  </div>  

  <style>
  .w150{
    width: 150px;
  }
  .filecss{
    color: #44A745;
    bgcolor: #ffcc00;
    font-size:24px;
  }
  .upload-file{
    padding-bottom: 40px;
    padding-top: 10px;
  }
  </style>
  
<?php include('includes/files/footer.inc.php');?> 
