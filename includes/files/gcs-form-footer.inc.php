<?php 
  require_once('includes/classes/step1qset.class.php');        
      if(isset($_GET['gcs'])){
        require_once('includes/classes/gcsform.class.php');        
        require_once('includes/classes/subject.class.php');
        $gcsform = new GCSForm($dbase);
        $gcsData = $gcsform->findById($_GET['gcs']); 
        
        if($gcsData && !empty($gcsData)){
          $subjectData = (new Subject($dbase))->findById($gcsData['subject_id']);
          $questionData = $gcsform->getAllDetails($gcsData['id']);          
          $qData = array();
          foreach($questionData as $q){
            if(!isset($qData[$q['question_id']])){
              $qData[$q['question_id']] = array();
            }
            array_push($qData[$q['question_id']] , $q['student_id']);
          }

          $fillEditForm = '';
          foreach($qData as $qid=>$students){
            foreach($students as $idx=>$sid){
              if($idx > 2){
                $fillEditForm .= sprintf("addStudentBox($('.qnum-%d .student-box'),1);",$qid);
              }
              $fillEditForm .= sprintf("$('.qnum-%d .studentdd').eq(%d).val(%d);",$qid,$idx,$sid);              
            }
          }          

        }
      }

      $langStep1Form = $lang->get(['step1','form']);
    ?>

<script>  

   const langStep1Form = <?php echo json_encode($langStep1Form)?>;   

   $(document).ready(function(){
    $('#selGrade').bind('change',function(){
      fetchSubjects(this);
    });

    <?php 
      if(isset($subjectData)){
        printf("$('#selGrade').val(%d);",$gcsData['grade_id']);
        $subjectData = array(array("id"=>$subjectData['id'],"name"=>$subjectData['name']));
        printf("populateOptions($('#selSubject'),JSON.parse('%s'));",json_encode($subjectData));
        printf("$('#selSubject').val(%d);",$gcsData['subject_id']);
        printf("$('#selYear').val(%d);",$gcsData['year_id']);
        echo 'searchGCSForm()';
      }
    ?>
   });


   function fetchSubjects(ele){
      $('#gcs-selectors').block({message: langStep1Form['subjectFetch']});
      $.post('forms/xhr.action.php',{action:'fetchSubjects',gid: ele.value},function(data){
        $('#gcs-selectors').unblock();
        populateOptions($('#selSubject'),JSON.parse(data));
      });
   }

   function populateOptions(sel,options){     

    let opts = options.map(o => `<option value="${o.id}">${o.name}</option>`);

    opts = '<option value="0"> - Select - </option>' + opts.join('');

    $(sel).html(opts);
   }

  const qset  = <?php echo json_encode((new Step1QSet($dbase))->getAllQuestions())?>;

  let gcsFormData = {};
  

  function searchGCSForm(){
    let data = {
      action:'searchGCS',    
      tid: parseInt($('#tid').val()),   
      gid: parseInt($('#selGrade').val()), 
      sid: parseInt($('#selSubject').val()), 
      year: parseInt($('#selYear').val())
    };

    if(data.gid <= 0){
      alert(langStep1Form['errors']['noGrade']);
      $('#selGrade').focus();
      return;
    }
    if(data.sid <= 0){
      alert(langStep1Form['errors']['noSubject']);
      $('#selSubject').focus();
      return;
    }
    if(data.year <= 0){
      alert(langStep1Form['errors']['noYear']);
      $('#selYear').focus();
      return;
    }

    $('#gcs-selectors').parent().block({message: langStep1Form['searchText']});

    $.post('forms/xhr.action.php',data, function(data){
      $('#gcs-selectors').parent().unblock();
      gcsFormData = JSON.parse(data);
      initGCSForm();
    });
  }

  


  function initGCSForm(){

    if(<?php echo (int)!isset($subjectData)?> && gcsFormData.exists){
      $('#recexists').modal();
      $('#aResultLink').attr('href','/gcs-result.php?gcs='+gcsFormData.gcsform_id);
      if(gcsFormData.editable){
        $('#aEditLink').attr('href','/gcs-form.php?gcs='+gcsFormData.gcsform_id).show();
      }else{
        $('#aEditLink').hide();
      }
    }else{
      createGCSForm();      
      <?php if(isset($fillEditForm)){
        echo $fillEditForm;
      }?>
      
    }
  }

  

  function createGCSForm(){

    $('#btn-start').hide();
    $('#gcs-selectors').block({message: null});
    $('#btn-submit').fadeIn();
    
    const div = $('#gcs-form');

    div.append(`<div class="alert alert-info" style="margin:10px;">${langStep1Form['note']}</div>`);

    var qtext;

    for(var qnum in qset){          
      qtext = qset[qnum];
      div.append(`
        <div class="row qset">
          <div class="col-sm-1 qnum">${qnum}. </div>  
          <div class="col-sm-11 qtext qnum-${qnum}">
            ${qtext}
            <div class="row student-box" qnum="${qnum}"></div>
          </div>  
        </div>    
      `);
    }

    $('.student-box').each(function(){
      addStudentBox($(this),0);
      addStudentBox($(this),0);
      addStudentBox($(this),0);       
    });

    $('#gcs-form').fadeIn();
  }


  function addStudentBox(sbox, canDelete){

    var deleteBtn = '';

    if(canDelete){
      deleteBtn = `<span class="btn btn-danger text-center del-student" onclick="$(this).parent().remove();">-</span>`;
    }

    sbox.find('.btn-addmore').remove();
    var qnum = parseInt($(sbox).attr('qnum'));
    //var dd = $(sbox).find('.studentdd').length;
    sbox.append(`
      <div class="col-sm-4 p-2">
        <select class="form-control studentdd" name="student[${qnum}][]">
          <option value="0"> - Select - </option>
          ` + gcsFormData.students.map(s => `<option value="${s.id}">${s.name}</option>`) + `
        </select>
        ` + deleteBtn + `
      </div>      
      <div class="col-sm-4 text-left btn-addmore p-2">
        <button class="btn btn-pink btn-sm">${langStep1Form['btnAdd']}</button>
      </div>
    `);
    sbox.find('.btn-addmore button').click(function(){
      addStudentBox(sbox,1);
    });
  }

  function saveAndSubmitStep1(frm){
    if(confirm(langStep1Form['onSubmit']['preSubmit'])){
      frm.declareResult.value=1;
      gcsFormValidation(frm);
      frm.submit();
    }
  }


  function gcsFormValidation(frm){    
    frm.grade_id.value=gcsFormData.grade_id;
    frm.subject_id.value=gcsFormData.subject_id;
    frm.year_id.value=gcsFormData.year_id;
    return true;    
  }


</script>  

<style>

  .del-student{
    line-height: 10px;
    padding: 6px!important;
    position: absolute;
    top: 1px;
    left: 1px;
  }

  .qset{
    margin: 10px;
    background-color: #f1f1f1;
    border-radius: 10px;
    padding: 10px;
  }

  .qnum{
    max-width: 40px;
    font-weight: bold;
  }

  .qtext{
    color: #1c1c1c;
    font-size: 20px;
  }
</style>      
