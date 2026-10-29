<?php 

  require_once "grade.class.php";

  class Student{

    protected $id;
    protected $adno;
    protected $name;
    protected $email;
    protected $year;
    protected $deleted;
    protected $updated_on;
    public $db;
    public $table = 'students';
    public $table_details = 'student_details';

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
      if(isset($data['adno'])){
        $this->adno = $data['adno'];
      }
      if(isset($data['name'])){
        $this->name = $data['name'];
      }
      if(isset($data['email'])){
        $this->email = $data['email'];
      }
      if(isset($data['year'])){
        $this->year = $data['year'];
      }
      if(isset($data['deleted'])){
        $this->deleted = $data['deleted'];
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
      return null;
    }

    public function findByEmail($email=false){
      if($email){
        $row = $this->db->query('SELECT * FROM '.$this->table.' WHERE email = ?', $email)->fetchArray();
        return $row;
      }
    }

    public function findByAdno($adno=false){
      if($adno){
        $row = $this->db->query('SELECT * FROM '.$this->table.' WHERE adno = ?', $adno)->fetchArray();
        return $row;
      }
    }

    

    public function save(){
      if(empty($this->id)){
        // insert        
        $row = $this->db->query('INSERT INTO '.$this->table.' (adno,name,email,year,updated_on) VALUES (?,?,?,?,now())', $this->adno, $this->name, $this->email, $this->year);        
        $this->id = $this->db->insertId(); //== set the newly inserted id
        return $row->affectedRows();
      }else{
        // update        
        $row = $this->db->query('UPDATE '.$this->table.' SET adno=?, name=?, email=?, year=? WHERE id = ?', $this->adno, $this->name, $this->email, $this->year, $this->id);
        $updated = $row->affectedRows();
        if($updated){
          //== if there is something which has been updated
          $this->db->query('UPDATE '.$this->table.' SET updated_on=NOW() WHERE id = ?', $this->id);
        }
        return $updated;      
      }      
    }

    public function markDeletedAdnoNotPresent($adno=array()){
      $query = sprintf("UPDATE ".$this->table." SET deleted = 1 WHERE adno NOT IN ('%s')",implode("','",$adno));      
      $row = $this->db->query($query);
      return $row->affectedRows();
    }

    public function deleteDetails(){
      $this->db->query('DELETE FROM '.$this->table_details.' WHERE student_id = ?',$this->id);
    }

    public function saveDetails($grade_id, $subject_id, $year_id, $teacher_id){

      if($grade_id && $subject_id && $year_id && $teacher_id){
        $row = $this->db->query('INSERT INTO '.$this->table_details.' (student_id,grade_id,subject_id,year_id,teacher_id) VALUES (?,?,?,?,?)', $this->id, $grade_id, $subject_id, $year_id, $teacher_id);
        return $row->affectedRows();
      }
      return 0;
    }

    public function getStudentData($ary){
      if(!empty($ary)){
        $query=sprintf('SELECT id,name FROM %s WHERE id IN (SELECT student_id FROM %s WHERE 1=1',$this->table,$this->table_details);
        foreach($ary as $k=>$v){
          $query.=" AND ".$k." = ?";
        }
        $query.=") ORDER BY name ASC";
        $row = $this->db->query($query, array_values($ary))->fetchAll();
        return $row;
      }
      return [];
    }

    public function total(){
      $query = $this->db->query('SELECT SUM(1) total FROM '.$this->table.' WHERE deleted=0')->fetchArray();
      return $query['total'];
    }

    public function search($name){
      $grade_table = (new Grade($this->db))->table;
      $query=sprintf('SELECT DISTINCT s1.id,s1.name,g1.name as grade FROM %s AS s1 
      INNER JOIN %s s2 ON (s1.id=s2.student_id)
      INNER JOIN %s g1 ON (g1.id=s2.grade_id) 
      WHERE s1.name like "%%%s%%"', $this->table, $this->table_details,$grade_table, $name);

      return $this->db->query($query)->fetchAll();
    }

  }

?>