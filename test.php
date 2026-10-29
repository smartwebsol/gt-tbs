<?php 

require('includes/config.inc.php');

$conn = new mysqli(_DBHOST, _DBUSER, _DBPWD, _DBNAME);
if ($conn->connect_error) {
    echo 'Failed to connect to MySQL - ' . $conn->connect_error;
}else{
    echo "MySQLi Connected";
}

echo '<hr />';

$ldap_connection = ldap_connect(_LDAPSERVER);
if(!$ldap_connection){
    echo "no such server ".$adServer;
  }else{
    echo "LDAP Connected";
  }
?>