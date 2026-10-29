<?php 

  class Subject{

    protected $id;    
    protected $name;    
    protected $updated_on;
    public $db;
    public $table = 'subjects';

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
      if(isset($data['name'])){
        $this->name = $data['name'];
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

    public function findByName($name=false){      
      if(!empty($name)){
        $row = $this->db->query('SELECT * FROM '.$this->table.' WHERE name = ?', $name)->fetchArray();
        return $row;
      }
      return false;
    }

    

    public function save(){
      if(empty($this->id)){        
        // look for existing
        $data = $this->findByName($this->name);        
        if($data && !empty($data)){
          //== found record, copy id and updated_on and do nothing
          $this->id=$data['id'];
          $this->updated_on=$data['updated_on'];
          return 0;
        }else if(!empty($this->name)){          
          // insert
          $row = $this->db->query('INSERT INTO '.$this->table.' (name,updated_on) VALUES (?,now())', $this->name);
          $this->id = $this->db->insertId(); //== set the newly inserted id
          return $row->affectedRows();
        }
      }   
    }

    public function total(){
      $query = $this->db->query('SELECT count(id) total FROM '.$this->table)->fetchArray();
      return $query['total'];
    }

    public function getAll(){
      return $this->db->query('SELECT * FROM '.$this->table)->fetchAll();
    }

  }

?>