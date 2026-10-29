<?php 
require_once('grade.class.php');
require_once('subject.class.php');
require_once('student.class.php');
require_once('gcsform.class.php');
require_once('step1results.class.php');
require_once('step2form.class.php');

  class Teacher{

    protected $id;    
    protected $fname;    
    protected $lname;
    protected $email;
    protected $updated_on;
    public $db;
    public $table = 'teachers';

    public function __construct($db,$data = array()){
      
      $this->db = $db; //== set the database

      $this->setData($data);
    }

    public function getId(){
      return $this->id;
    }
    public function getEmail(){
      return $this->email;
    }

    public function setData($data=array()){

      if(isset($data['id'])){
        $this->id = $data['id'];
      }      
      if(isset($data['fname'])){
        $this->fname = $data['fname'];
      }
      if(isset($data['lname'])){
        $this->lname = $data['lname'];
      }
      if(isset($data['email'])){
        $this->email = $data['email'];
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

    public function findByEmail($email=false){
      if($email){
        $row = $this->db->query('SELECT * FROM '.$this->table.' WHERE email = ?', $email)->fetchArray();
        return $row;
      }
      return false;
    }

    

    public function save(){
      if(empty($this->id)){
        // insert
        $row = $this->db->query('INSERT INTO '.$this->table.' (fname,lname,email,updated_on) VALUES (?, ?, ?, NOW())', $this->fname, $this->lname, $this->email);
        $this->id = $this->db->insertId(); //== set the newly inserted id
        return $row->affectedRows();
      }else{
        // update
        $row = $this->db->query('UPDATE '.$this->table.' SET fname=?,lname=?, email=? WHERE id = ?', $this->fname,$this->lname,$this->email, $this->id);
        $updated = $row->affectedRows();
        if($updated){
          //== if there is something which has been updated
          $this->db->query('UPDATE '.$this->table.' SET updated_on=NOW() WHERE id = ?', $this->id);
        }
        return $updated;      
      }      
    }

    public function getAllGrades(){      
      $tabgrade = (new Grade($this->db))->table;
      $tabsdetails = (new Student($this->db))->table_details;
      $query=sprintf("select id,name from %s WHERE id IN (select distinct(grade_id) from %s where teacher_id=%d) ORDER BY name ASC",$tabgrade,$tabsdetails,$this->id);
      $row = $this->db->query($query)->fetchAll();
      return $row;
    }

    public function getAllSubjects($count=0){  
      $tabsubject = (new Subject($this->db))->table;
      $tabsdetails = (new Student($this->db))->table_details;
      $query=sprintf("select id,name from %s WHERE id IN (select distinct(subject_id) from %s where teacher_id=%d) ORDER BY name ASC",$tabsubject,$tabsdetails,$this->id);
      $query = $this->db->query($query);
      if($count){
        return $query->numRows();
      }
      return $query->fetchAll();
    }

    public function getAllStudents($count=0){  
      $student = new Student($this->db);      
      $query=sprintf("select id,name from %s WHERE id IN (select distinct(student_id) from %s where teacher_id=%d) ORDER BY name ASC",$student->table,$student->table_details,$this->id);
      $query = $this->db->query($query);
      if($count){
        return $query->numRows();
      }
      return $query->fetchAll();
    }

    public function getAllGCSFormsFilled($year){
      $tabgcsforms = (new GCSForm($this->db))->table;
      return $this->db->query('select * FROM '.$tabgcsforms.' WHERE teacher_id=? AND year_id=?',$this->id,$year)->fetchAll();
    }

    public function hasFilledAllStep1($year){      
      $tabsdetails = (new Student($this->db))->table_details;
      $tabgcsforms = (new GCSForm($this->db))->table;
      $rec1 = $this->db->query('select (count(DISTINCT grade_id)*count(DISTINCT subject_id)) as totstep1 FROM '.$tabsdetails.' WHERE teacher_id=?',$this->id)->fetchArray();
      $rec2 = $this->db->query('select distinct(id) gcsid FROM '.$tabgcsforms.' WHERE teacher_id=? AND year_id=?',$this->id,$year)->fetchAll();

      $cond1 = $rec1['totstep1'] == count($rec2); //== Teacher has filled something in all grades and subjects for step1 atleast
      
      if(!$cond1){
        return false;// no all step1 data is not filled
      }

      $gcsform = new GCSForm($this->db);
      $completed = array();

      foreach($rec2 as $row){
        $data=$gcsform->findById($row['gcsid']);            
        $gcsform = new GCSForm($this->db,$data);
        if($gcsform->isStep1Completed()){
          array_push($completed,$row['gcsid']);
        }
      }
      
      return count($completed)==count($rec2);
    }

    public function totalStep1ResultsDeclared($year_id){
      $tabstep1results = (new Step1Result($this->db))->table;
      $query = $this->db->query('SELECT id FROM '.$tabstep1results.' WHERE teacher_id=? AND year_id=?',$this->id,$year_id);
      return $query->numRows();
    }

    public function step1ResultsDeclared($year_id){      
      return $this->totalStep1ResultsDeclared($year_id) > 0;
    }

    public function hasFilledAllStep2($year){ 
      $totals1 = $this->totalStep1ResultsDeclared($year);
      $s2f = new Step2Form($this->db);      
      $rows = $this->db->query('SELECT step2form_id s2id,count(question_id) tot FROM '.
      $s2f->table_details.' WHERE step2form_id IN (select id from '.
      $s2f->table.' WHERE teacher_id=? AND year_id=?) GROUP BY step2form_id',$this->id,$year_id)->fetchAll();
            
      return $totals1 == count($rows);
    }

    public function total(){
      $query = $this->db->query('SELECT count(id) total FROM '.$this->table)->fetchArray();
      return $query['total'];
    }
    

  }

?>