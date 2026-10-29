<?php 

class Step2QSet{

  protected $id;
  protected $question;
  protected $point1;
  protected $point2;
  protected $point3;
  protected $point4;
  protected $status;
  protected $updated_on;    
  public $db;
  public $table = 'step2qset';

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
    if(isset($data['point1'])){
      $this->point1 = $data['point1'];
    }
    if(isset($data['point2'])){
      $this->point2 = $data['point2'];
    }
    if(isset($data['point3'])){
      $this->point3 = $data['point3'];
    }
    if(isset($data['point4'])){
      $this->point4 = $data['point4'];
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

  public function columns(){
    return array(
      'point1' => 'Rarely or Never',
      'point2' => 'Occassionally',
      'point3' => 'Frequently',
      'point4' => 'Always'
    );
  }

  public function getAllQuestions(){
    return $this->db->query('SELECT * FROM '.$this->table.' WHERE status=1')->fetchAll();
  }

  public function total(){
    return $this->db->query('SELECT count(*) total FROM '.$this->table.' WHERE status=1')->fetchArray();
  }



}
?>