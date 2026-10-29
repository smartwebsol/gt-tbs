<?php 
  include('includes/files/header.inc.php'); 
  securePage();
  require_once('includes/classes/step2form.class.php');
  require_once('includes/classes/step2qset.class.php');
  $s2qCount = (new Step2QSet($dbase))->total()['total'];
  
  $s2form = new Step2Form($dbase);

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

  $rows = $s2form->findByWhere($search,$search['sort']);  
  
  $langStep2List = $lang->get(['step2','list']);
  $s2rdec = step2ResultsDeclared();
  ?>

<div class="container">

  <h4 class="mt-3 mb-4 subheading"><?php echo $langStep2List['heading']?></h4>

  <script>
  function sortColumn(by,dir){
    $('#search_sort_by').val(by);
    $('#search_sort_dir').val(dir);
    $('#searchform')[0].submit();
    return false;
  }
  </script>
  <form action="<?php echo _URL?>step2-list.php" method="post" id="searchform">
  <input type="hidden" id="search_sort_by" name="search[sort][by]" value="<?php echo $search['sort']['by']?>" />
  <input type="hidden" id="search_sort_dir" name="search[sort][dir]" value="<?php echo $search['sort']['dir']?>" />
  <div class="row">
    
    <div class="col-sm-3">

      <div class="input-group mb-3">
        <div class="input-group-prepend">
          <span class="input-group-text"><?php echo $langStep2List['search']['year']?></span>
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

    <div class="col-sm-4">

      <div class="input-group mb-3">
        <div class="input-group-prepend">
          <span class="input-group-text"><?php echo $langStep2List['search']['grade']?></span>
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

    <div class="col-sm-4">

      <div class="input-group mb-3">
        <div class="input-group-prepend">
          <span class="input-group-text"><?php echo $langStep2List['search']['subject']?></span>
        </div>
        <select class="form-control" name="search[subject_id]">
          <option value="">All</option>
          <?php 
          $subjects = $s2form->getAllSubjects($prefillCond);
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
        printf('<div class="alert alert-info text-center">%s</div>',$langStep2List['grid']['noresult']);
      }else{?>

      <tr>
        <th><?php echo $langStep2List['grid']['col1']?></th>
        <th><?php echo sortColumn($langStep2List['grid']['col2'],'studentname',$search)?></th>
        <th><?php echo sortColumn($langStep2List['grid']['col3'],'gradename',$search)?></th>
        <th><?php echo sortColumn($langStep2List['grid']['col4'],'subjectname',$search)?></th>        
        <th><?php echo sortColumn($langStep2List['grid']['col5'],'year_id',$search)?></th>
        <th><?php echo sortColumn($langStep2List['grid']['col6'],'result',$search)?></th>        
        <?php if(isCoordinatorLoggedIn()){?>
          <th><?php echo $langStep2List['grid']['col7']?></th>
        <?php }?>
        <th><?php echo sortColumn($langStep2List['grid']['col8'],'created_on',$search)?></th>
        <th></th>
      </tr>
      
      <?php foreach($rows as $idx=>$row){          

        $actions = sprintf("<a href='%sstep2-result.php?s2=%d' class='btn btn-pink btn-sm'>%s</a>",_URL,$row[id],$langStep2List['grid']['result']);
        
        if(canEditStep2Form($row['created_on']) && !$s2rdec){
          $actions.=sprintf(" <a href='%sstep2-form.php?s2=%d' class='btn btn-pink btn-sm'>%s</a>",_URL,$row[id],$langStep2List['grid']['edit']);
        }

        $actions.= sprintf(" <a href='%sforms/step3.action.php?s3=%d&id=%d' class='btn btn-%s btn-sm mt-1 mb-1'>%s</a>",_URL,!$row['step3'],$row[id],$row['step3'] ? 'danger' : 'success',$langStep2List['grid']['step3-'.$row['step3']]);
        

          printf("<tr>
            <td>%d</td>
            <td class='step3-%d'>%s</td>
            <td>%s</td>
            <td>%s</td>
            <td>%d</td>
            <td class='text-right'>%0.2f %%</td>
            <td>%s</td>            
            <td>%s</td>
          </tr>",
          $idx+1,
          $row['step3'],
          $row['studentname'],
          $row['gradename'],
          $row['subjectname'],
          $row['year_id'],
          ($row['result']/$s2qCount)*100,
          (isCoordinatorLoggedIn() ? $row['tfname'].' '.$row['tlname']."</td><td>".$row['updated_on'] : $row['updated_on']),
          $actions);
        }

      }?>
    </table>
  </div>

</div>

<style>
td.step3-1{
  background-color: #44A745;
  color: #fff;
}
</style>

<?php include('includes/files/footer.inc.php'); ?>