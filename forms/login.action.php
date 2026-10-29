<?php 
  include('../includes/config.inc.php');
  $langLoginonSubmit = $lang->get(['login','onSubmit']);

  $userFound1 = false;
  $userFound2 = false;

  if($_SERVER['HTTP_HOST']=='gt.british-school.org'){

    if(isset($_POST['username']) && isset($_POST['password'])){

      $adServer = _LDAPSERVER;
      $domain = _LDAPDOMAIN;
      $connString=_LDAPSEARCH;
      //$ldap_username = _LDAPUSER;
      
      $ldap_password = $_POST['password'];

      $username = $_POST['username'];
      
      
      $ldap_connection = ldap_connect($adServer);

      if(!$ldap_connection){
        setMessage($langLoginonSubmit['unreachable'],'danger');      
          //die("no such server ".$adServer);
      }else{
          //printf('%s connected...',$adServer);
        $domainAndUsername = $username."@".$domain;      
        ldap_set_option($ldap_connection, LDAP_OPT_PROTOCOL_VERSION, 3);
        ldap_set_option($ldap_connection, LDAP_OPT_REFERRALS, 0);
        
        $bind = ldap_bind($ldap_connection, $domainAndUsername,$ldap_password);

        if ($bind) {          
          $filter=sprintf("(samaccountname=%s)",$username);
          $justthese = array("givenname", "sn", "mail", "userprincipalname", "name", "distinguishedname", "displayname");

          $sr=ldap_search($ldap_connection, $connString, $filter, $justthese);
          $info = ldap_get_entries($ldap_connection, $sr);

          if($info && $info['count'] > 0){            
            $userFound1 = $info[0]['mail'][0]; //== email address to be matched in database
            $userFound2 = $info[0]['userprincipalname'][0]; //== email address to be matched in database            
          }else{
            setMessage($langLoginonSubmit['nouser'],'danger');  
          }
            

          @ldap_close($ldap);
        } else {
            setMessage($langLoginonSubmit['invalid'],'danger');
            //die('Server inaccessible!');
        }
      }
    }

  }else{
    $userFound1 = $_POST['username'];
  }

  if($userFound1 || $userFound2){

    require('../includes/classes/teacher.class.php');
    $teacher = new Teacher($dbase);
    $tData = $teacher->findByEmail($userFound1);
    if(!empty($tData)){      
      $_SESSION['teacherData'] = $tData;
      setMessage($langLoginonSubmit[isCoordinatorLoggedIn() ? 'success-coord' : 'success'],'success');      
    }else{
      $tData = $teacher->findByEmail($userFound2);
      if(!empty($tData)){
        $_SESSION['teacherData'] = $tData;
        setMessage($langLoginonSubmit[isCoordinatorLoggedIn() ? 'success-coord' : 'success'],'success');        
      }else{      
        setMessage($langLoginonSubmit['nouserindb'],'danger');
      }
    }

  }

  header('location:../index.php');
  
?>