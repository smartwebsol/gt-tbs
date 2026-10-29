<?php


if(isset($_POST['username']) && isset($_POST['password'])){
    
    $adServer = "LDAP://172.16.0.7:389"; //"ldap://british-school.org:389";//"LDAP://172.16.0.7:389";
    $domain = "british-school.org";
    $connString="DC=british-school,DC=org";
    $ldap_password = $_POST['password'];
    $ldap_username = $_POST['username'];
    
    $ldap_connection = ldap_connect($adServer);

    if(!$ldap_connection){
        die("no such server ".$adServer);
    }else{
        printf('%s connected...',$adServer);        
    }

    $domainAndUsername = $ldap_username."@".$domain;
    
    ldap_set_option($ldap_connection, LDAP_OPT_PROTOCOL_VERSION, 3);
    ldap_set_option($ldap_connection, LDAP_OPT_REFERRALS, 0);

    $bind = ldap_bind($ldap_connection, $domainAndUsername,$ldap_password);

    if ($bind) {
        $dn = "DC=british-school,DC=org";
        $filter="(samaccountname=nmatthews)";
        $justthese = array("givenname", "sn", "mail", "userprincipalname", "name", "distinguishedname", "displayname");

        $sr=ldap_search($ldap_connection, $dn, $filter, $justthese);
        $info = ldap_get_entries($ldap_connection, $sr);

        echo $info[0]['mail'][0];
        

        @ldap_close($ldap);
    } else {
        die('Server inaccessible!');
    }

}else{
?>
    <form action="/login.php" method="POST">
        <label for="username">Username: </label><input id="username" type="text" name="username" value="bsproject" /> 
        <label for="password">Password: </label><input id="password" type="password" name="password" value="Br!t!$#2012" />        
        <input type="submit" name="submit" value="Submit" />
    </form>
<?php } ?> 