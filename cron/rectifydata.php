<?php require_once('../includes/config.inc.php');

$recs = $dbase->query("select * from students order by email asc, id desc")->fetchAll();

$ary = [];

foreach($recs as $rec){
  if(isset($ary[$rec['email']])){
    $ary[$rec['email']]["old"]=$rec['id'];
  }else{
    $ary[$rec['email']]=array("new"=>$rec['id']);
  }
}


foreach($ary as $email => $rec){
  if($email!='' && $rec['new'] > 0 && $rec['old'] > 0){
    printf('UPDATE step1results SET student_id=%d WHERE student_id=%d;',$rec['new'],$rec['old']);
    echo '<br />';
  }  
}

