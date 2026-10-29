<?php 
  include('includes/files/header.inc.php'); 
  securePage();
  require_once('includes/classes/gcsform.class.php');
  $gcsform = new GCSForm($dbase);  
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

  if(!isCoordinatorLoggedIn()){
    $search['teacher_id'] = (int)$_SESSION['teacherData']['id'];   
    $prefillCond = " WHERE teacher_id=".(int)$_SESSION['teacherData']['id'];
  }

  $rows = $gcsform->findByWhere($search,$search['sort']);

  $langStep1List = $lang->get(['step1','list']);  
  ?>

<div class="container">

  <h4 class="mt-3 mb-4 subheading"><?php echo $langStep1List['heading']?></h4>

  <script>
  function sortColumn(by,dir){
    $('#search_sort_by').val(by);
    $('#search_sort_dir').val(dir);
    $('#searchform')[0].submit();
    return false;
  }
  </script>
  <form action="<?php echo _URL?>gcs-list.php" method="post" id="searchform">
  <input type="hidden" id="search_sort_by" name="search[sort][by]" value="<?php echo $search['sort']['by']?>" />
  <input type="hidden" id="search_sort_dir" name="search[sort][dir]" value="<?php echo $search['sort']['dir']?>" />
  <div class="row">
    
    <div class="col-sm-3">

      <div class="input-group mb-3">
        <div class="input-group-prepend">
          <span class="input-group-text"><?php echo $langStep1List['search']['year']?></span>
        </div>
        <select class="form-control" name="search[year_id]">
          <option value="">All</option>
          <?php           
          $years = $gcsform->getAllYears($prefillCond);
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

    <div class="col-sm-4">

      <div class="input-group mb-3">
        <div class="input-group-prepend">
          <span class="input-group-text"><?php echo $langStep1List['search']['grade']?></span>
        </div>
        <select class="form-control" name="search[grade_id]">
          <option value="">All</option>
          <?php 
          $grades = $gcsform->getAllGrades($prefillCond);
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

    <div class="col-sm-4">

      <div class="input-group mb-3">
        <div class="input-group-prepend">
          <span class="input-group-text"><?php echo $langStep1List['search']['subject']?></span>
        </div>
        <select class="form-control" name="search[subject_id]">
          <option value="">All</option>
          <?php 
          $subjects = $gcsform->getAllSubjects($prefillCond);
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

    <div class="col-sm-1">
      <button type="submit" class="btn btn-pink">Search</button>
    </div>

  </div>
  </form>

  <div class="table-responsive">
    <table class="table table-hover">
      
      <?php 
      if(count($rows)==0){
        printf('<div class="alert alert-info text-center">%s</div>',$langStep1List['grid']['noresult']);
      }else{?>

      <tr>
        <th><?php echo $langStep1List['grid']['col1']?></th>
        <th><?php echo sortColumn($langStep1List['grid']['col2'],'gradename',$search)?></th>
        <th><?php echo sortColumn($langStep1List['grid']['col3'],'subjectname',$search)?></th>        
        <th><?php echo sortColumn($langStep1List['grid']['col4'],'year_id',$search)?></th>
        <?php if(isCoordinatorLoggedIn()){?>
          <th><?php echo $langStep1List['grid']['col5']?></th>
        <?php }?>
        <th><?php echo sortColumn($langStep1List['grid']['col6'],'created_on',$search)?></th>
        <th></th>
      </tr>
      
      <?php foreach($rows as $idx=>$row){  
        $gcsform1 = new GCSForm($dbase,$row);        
        $s1rdec = $gcsform1->isResultDeclared();

        $actions = sprintf("<a href='%sgcs-result.php?gcs=%d' class='btn btn-pink btn-sm'>%s</a>",_URL,$row[id],$langStep1List['grid']['result']);
        
        if(isCoordinatorLoggedIn() || (canEditGCSForm($row['created_on']) && !$s1rdec)){
          $actions.=sprintf(" <a href='%sgcs-form.php?gcs=%d' class='btn btn-pink btn-sm'>%s</a>",_URL,$row[id],$langStep1List['grid']['edit']);
        }

          printf("<tr>
            <td>%d</td>
            <td>%s</td>
            <td>%s</td>
            <td>%d</td>
            <td>%s</td>
            <td>%s</td>
          </tr>",
          $idx+1,
          $row['gradename'],
          $row['subjectname'],
          $row['year_id'],
          (isCoordinatorLoggedIn() ? $row['tfname'].' '.$row['tlname']."</td><td>".$row['updated_on'] : $row['updated_on']),
          $actions);
        }

      }?>
    </table>
  </div>

</div>

<?php include('includes/files/footer.inc.php'); ?>