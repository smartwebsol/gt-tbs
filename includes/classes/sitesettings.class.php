<?php 

  class SiteSetting{

    protected $id;    
    protected $value;    
    protected $updated_on;
    public $db;
    public $table = 'site_settings';

    public function __construct($db,$data = array()){
      
      $this->db = $db; //== set the database

      $this->setData($data);
    }

    public function getId(){
      return $this->id;
    }

    public function getValue(){
      return $this->value;
    }

    public function setData($data=array()){

      if(isset($data['id'])){
        $this->id = $data['id'];
      }      
      if(isset($data['value'])){
        $this->value = $data['value'];
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

    public function findByName($name){
      $names = array(
        "step1edit" => 1,
        "step1top" => 2,
        "step1disp" => 3,
        "step2edit" => 4
      );

      if(!array_key_exists($name, $names))
      return false;

      $id = $names[$name];
      
      $data = $this->findById($id);
      
      $this->setData($data);
      return true;
    }

    public function save(){      
      $row = $this->db->query('UPDATE '.$this->table.' SET `value`=? WHERE id = ?', $this->value, $this->id);
      $updated = $row->affectedRows();
      if($updated){
        //== if there is something which has been updated
        $this->db->query('UPDATE '.$this->table.' SET updated_on=NOW() WHERE id = ?', $this->id);
      }
      return $updated; 
    }

  }

?>