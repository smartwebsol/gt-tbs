<?php

class Lang{

  private $lang;

  public function __construct($lang){
    $f = sprintf('%sincludes/lang/%s.json',_PATH,_LANG);
    if(file_exists($f)){
      $this->lang = json_decode(file_get_contents($f),true);      
    }else{
      die('Language not set or incorrect!');
    }
  }

  public function get($arr=[]){
    $tmp = $this->lang;
    foreach($arr as $key){
      $tmp = $tmp[$key];
    }
    return $tmp;
  }

}

?>