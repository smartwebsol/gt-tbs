<script>
  $(document).ready(function(){
    $('.radiocheck').bind('click',function(){            
      $(this).parent().parent().find('.radiocheck').removeClass('checked'); //== remove all checked
      $(this).parent().parent().find('input[type=radio]').attr('checked',false);      
      $(this).find('input[type=radio]').attr('checked',true);
      $(this).addClass('checked');       
      syncResult();     
    });
    syncResult();
  });

  function syncResult(){
    var res = 0;
    $('.radiocheck input[type=radio]:checked').each(function(){
      res += parseFloat(this.value);
    });

    $('#step2Score').html(res.toFixed(2) + " (" + ((res.toFixed(2)/($('.radiocheck').length/4))*100).toFixed(2) + " %)");
  }

  function showStep2Form(){
    var sid = $('#selStep2Student').val();
    var opt = $('#selStep2Student option[value='+sid+']');
    var frm = $('#step2Form');

    frm[0].s1r.value=sid; //== set the student ID from step1results announced
    
    if(sid > 0){      
      frm.find('#studentName').html(opt.attr('sname'));
      frm.find('#gradeName').html(opt.attr('grade') + ' / ' + opt.attr('subject'));
      frm.fadeIn();      
      $('#step2-selectors,#btn-start').hide();      
    }else{
      frm.hide();
      frm.find('#studentName,#gradeName').html('');      
      $('#step2-selectors,#btn-start').fadeIn();
      
    }
  }

  function step2FormValidation(frm){
    return true;
    var notAnswered = [];
    $('.questionrows').each(function(){
      if($(this).find('.radiocheck > input[type=radio]:checked').length == 0){
        notAnswered.push($(this).attr('question'));
      }
    });

    if(notAnswered.length > 0){
      alert(`You have not answered ${notAnswered.length} out of ${$('.questionrows').length} questions.\nPlease answer questions ${notAnswered.join(',')}`);
      return false;
    }

    return true;    

  }
</script>
