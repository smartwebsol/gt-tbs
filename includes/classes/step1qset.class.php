<?php 

class Step1QSet{

  protected $id;
  protected $question;  
  protected $status;
  protected $updated_on;    
  public $db;
  public $table = 'step1qset';

  public function __construct($db,$data = array()){
    
    $this->db = $db; //== set the database

    $this->setData($data);
  }

  public function getId(){
    return $this->id;
  }

  public function setData($data=array()){

    if(isset($data['id'])){
      $this->id = $data['id'];
    }
    if(isset($data['question'])){
      $this->question = $data['question'];
    }        
    if(isset($data['status'])){
      $this->status = $data['status'];
    }
    if(isset($data['updated_on'])){
      $this->updated_on = $data['updated_on'];
    }
  }
  
  public function findById($id=false){
    if($id){
      $row = $this->db->query('SELECT * FROM '.$this->table.' WHERE id = ?',$id)->fetchArray();
      return $row;
    }
    return false;
  }

  public function getAllQuestions(){
    $row = $this->db->query('SELECT * FROM '.$this->table.' WHERE status=1')->fetchAll();
    $qset = array();
    foreach($row as $r){
      $qset[$r['id']]=$r['question'];
    }
    return $qset;
  }

  public function totalQuestions(){
    return $this->db->query('SELECT * FROM '.$this->table.' WHERE status=1')->numRows();
  }



}
?>