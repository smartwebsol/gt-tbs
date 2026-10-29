<?php 
  require_once('../includes/config.inc.php');
  require_once('../includes/classes/gcsform.class.php');

  $gcsform = new GCSForm($dbase);
  $gcsform->declareResultsForExpiredForms((int)getSetting('step1edit'));
  ?>