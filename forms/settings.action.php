<?php 
  include('../includes/config.inc.php');
  require_once('../includes/classes/sitesettings.class.php');

  $langSettings = $lang->get(['settings']);  

  $ss = new SiteSetting($dbase);

  $arr = array("step1edit","step1top","step1disp","step2edit");
  $saved=false;
  
  foreach($arr as $a){
    if($_POST[$a]!=getSetting($a)){
      if($ss->findByName($a)){
        $ss->setData(array('value'=>(int)$_POST[$a]));
        $ss->save();
        $saved=true;
      }
    }
  }

  if($saved){
    setMessage($langSettings['success'],'success');
  }else{
    setMessage($langSettings['error'],'danger');
  }
  header('location:../settings.php');

?>