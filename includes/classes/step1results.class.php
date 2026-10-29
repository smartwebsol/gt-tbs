<?php 

  require_once('student.class.php');
  require_once('grade.class.php');
  require_once('subject.class.php');
  require_once('step2form.class.php');

  class Step1Result{

    protected $id;    
    protected $gcsforms_id;
    protected $student_id;
    protected $teacher_id;
    protected	$grade_id;
    protected $subject_id;
    protected $year_id;
    protected $created_on;    
    public $db;
    public $table = 'step1results';

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
      if(isset($data['student_id'])){
        $this->student_id = $data['student_id'];
      }      
      if(isset($data['gcsforms_id'])){
        $this->gcsforms_id = $data['gcsforms_id'];
      }
      if(isset($data['teacher_id'])){
        $this->teacher_id = $data['teacher_id'];
      }
      if(isset($data['grade_id'])){
        $this->grade_id = $data['grade_id'];
      }
      if(isset($data['subject_id'])){
        $this->subject_id = $data['subject_id'];
      }
      if(isset($data['year_id'])){
        $this->year_id = $data['year_id'];
      }
      if(isset($data['created_on'])){
        $this->created_on = $data['created_on'];
      }
    }
    
    public function findById($id=false){
      if($id){
        $row = $this->db->query('SELECT * FROM '.$this->table.' WHERE id = ?',$id)->fetchArray();
        return $row;
      }
      return false;
    }

    public function insert(){
      if(!$this->id){
        $row = $this->db->query('INSERT INTO '.$this->table.' (gcsforms_id,student_id,teacher_id,grade_id,subject_id,year_id,created_on) VALUES (?,?,?,?,?,?,NOW())', $this->gcsforms_id,$this->student_id, $this->teacher_id, $this->grade_id, $this->subject_id, $this->year_id);
        $this->id = $this->db->insertId(); //== set the newly inserted id          
        return $row->affectedRows();
      }
      return 0;
    }

    public function deleteAll($teacher_id,$year){
      $row = $this->db->query('DELETE FROM '.$this->table.' WHERE teacher_id=? AND year_id=?',$teacher_id,$year);
      return $row->affectedRows();
    }

    public function getStep1Qualifiers($teacher_id){
      $tabstudent = (new Student($this->db))->table;
      $tabgrade = (new Grade($this->db))->table;
      $tabsubject = (new Subject($this->db))->table;
      $tabs2form = (new Step2Form($this->db))->table;
      $query = sprintf("SELECT sr.id,s.name, g.name grade, sub.name as `subject` FROM %s sr 
      INNER JOIN %s s ON s.id=sr.student_id INNER JOIN %s g ON g.id=sr.grade_id 
      INNER JOIN %s sub ON sub.id=sr.subject_id  
      LEFT JOIN %s s2f on (s2f.student_id=sr.student_id AND 
      s2f.grade_id=sr.grade_id AND s2f.subject_id=sr.subject_id AND 
      s2f.year_id=sr.year_id AND s2f.teacher_id=sr.teacher_id) 
      WHERE s2f.id IS NULL",$this->table,$tabstudent,$tabgrade,$tabsubject,$tabs2form);

      if($teacher_id!="all"){
        $query.=sprintf(" AND sr.teacher_id=%d",$teacher_id);
      }

      return $this->db->query($query)->fetchAll();
    }

    public function total(){
      $query = $this->db->query('SELECT count(DISTINCT gcsforms_id) total FROM '.$this->table)->fetchArray();
      return $query['total'];
    }

    public function totalStudents(){
      $query = $this->db->query('SELECT count(DISTINCT student_id,gcsforms_id) total FROM '.$this->table)->fetchArray();
      return $query['total'];
    }
  }
  ?>