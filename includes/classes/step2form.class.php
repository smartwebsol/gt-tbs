<?php 

  require_once('student.class.php');
  require_once('grade.class.php');
  require_once('subject.class.php');
  require_once('step2qset.class.php');

  class Step2Form{

    protected $id;    
    protected $student_id;
    protected $result;
    protected $teacher_id;
    protected	$grade_id;
    protected $subject_id;
    protected $year_id;
    protected $step3;
    protected $created_on;
    protected $updated_on;
    public $db;
    public $table = 'step2forms';
    public $table_details = 'step2form_details';

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
      if(isset($data['result'])){
        $this->result = $data['result'];
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
      if(isset($data['step3'])){
        $this->step3 = $data['step3'];
      }
      if(isset($data['created_on'])){
        $this->created_on = $data['created_on'];
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

    public function findByWhere($ary = array(),$sort=false){      
        $table_student = (new Student($this->db))->table;
        $table_grade = (new Grade($this->db))->table;
        $table_subject = (new Subject($this->db))->table;
        $cols = "s2.*,s.name as studentname,g.name gradename, sub.name subjectname";
        $query=sprintf('SELECT ++COLNAME++ FROM %s s2 INNER JOIN %s s ON s2.student_id=s.id INNER JOIN %s g ON g.id=s2.grade_id INNER JOIN %s sub ON sub.id=s2.subject_id',
        $this->table,
        $table_student,
        $table_grade,
        $table_subject);

        if(isCoordinatorLoggedIn()){
          require_once('teacher.class.php');
          $table_teacher = (new Teacher($this->db))->table;
          $cols.=" ,t.fname tfname, t.lname tlname";
          $query.=sprintf(' INNER JOIN %s t ON t.id=s2.teacher_id',$table_teacher);          
        }

        $query = str_replace("++COLNAME++",$cols,$query);

        $query.=" WHERE 1=1";
        if(!empty($ary)){
          foreach($ary as $k=>$v){
            if($k!='sort'){
              $query.=" AND s2.".$k." = ?";
            }
          }
        }

        if($sort){
          $query.= sprintf(" ORDER BY %s %s,id asc",$sort['by'],$sort['dir']);
          unset($ary['sort']);//== removes the sort parameter passed
        }     

        $row = $this->db->query($query, array_values($ary))->fetchAll();
        return $row;      
    }

    

    public function insert(){
      if(!$this->id){
        $row = $this->db->query('INSERT INTO '.$this->table.' (student_id,result,teacher_id,grade_id,subject_id,year_id,created_on,updated_on) VALUES (?,?,?,?,?,?,NOW(),NOW())', $this->student_id, $this->result, $this->teacher_id, $this->grade_id, $this->subject_id, $this->year_id);
        $this->id = $this->db->insertId(); //== set the newly inserted id          
        return $row->affectedRows();
      }else{
        return true;
      }
    }

    public function update($fields=array()){      
      if($this->id){
        $query=sprintf('UPDATE %s SET ',$this->table);
        $names = array_keys($fields);
        foreach($names as $k){
          $query.=sprintf(' %s=?,',$k);
        }
        $query.=" updated_on=NOW() WHERE id=?";        

        $ary = array_merge(array_values($fields),[$this->id]);

        $row = $this->db->query($query,$ary);
        return $row->affectedRows();
      }
      return false;
    }

    public function saveAnswer($question_id,$answer){
      $query = sprintf('INSERT INTO %s (step2form_id,question_id,answer) VALUES (%d,%d,%0.2f)', $this->table_details, $this->id, $question_id,$answer);      
      $row = $this->db->query($query);      
      return $row->affectedRows();
    }

    public function getAllDetails($step2form_id){      
      $table_s2qset = (new Step2QSet($this->db))->table;
      return $this->db->query('select s2q.id,s2q.question,s2fd.answer from '.$table_s2qset.' s2q LEFT JOIN '.$this->table_details.' s2fd ON (s2q.id=s2fd.question_id AND s2fd.step2form_id=?)',$step2form_id)->fetchAll();
    }

    public function deleteAnswers(){
      $row = $this->db->query('delete from '.$this->table_details.' WHERE step2form_id = ?',$this->id);
      return $row->affectedRows();
    }

    public function total(){
      $query = $this->db->query('SELECT count(id) total FROM '.$this->table)->fetchArray();
      return $query['total'];
    }

    public function getAllGrades($where=''){
      $table_grade = (new Grade($this->db))->table;
      return $this->db->query('SELECT id,name FROM '.$table_grade.' WHERE id IN (SELECT distinct grade_id FROM '.$this->table.$where.') ORDER BY id ASC')->fetchAll();      
    }

    public function getAllSubjects($where=''){
      $table_subject = (new Subject($this->db))->table;
      return $this->db->query('SELECT id,name FROM '.$table_subject.' WHERE id IN (SELECT distinct subject_id FROM '.$this->table.$where.') ORDER BY name ASC')->fetchAll();      
    }

    public function getAllYears($where=''){      
      return $this->db->query('SELECT distinct year_id FROM '.$this->table.$where.' ORDER BY year_id ASC')->fetchAll();      
    }


  }
?>