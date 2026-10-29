<?php 

  require_once('student.class.php');
  require_once('grade.class.php');
  require_once('subject.class.php');
  require_once('step1qset.class.php');
  require_once('step1results.class.php');

  class GCSForm{

    protected $id;    
    protected $teacher_id;
    protected	$grade_id;
    protected $subject_id;
    protected $year_id;
    protected $created_on;
    protected $updated_on;
    public $db;
    public $table = 'gcsforms';
    public $table_details = 'gcsform_students';

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
      
      $table_grade = (new Grade($this->db))->table;
      $table_subject = (new Subject($this->db))->table;
      $cols = "gs.*,g.name gradename, sub.name subjectname";
      $query=sprintf('SELECT ++COLNAME++ FROM %s gs INNER JOIN %s g ON g.id=gs.grade_id INNER JOIN %s sub ON sub.id=gs.subject_id',$this->table,$table_grade,$table_subject);

      if(isCoordinatorLoggedIn()){
        require_once('teacher.class.php');
        $table_teacher = (new Teacher($this->db))->table;
        $cols.=" ,t.fname tfname, t.lname tlname";
        $query.=sprintf(' INNER JOIN %s t ON t.id=gs.teacher_id',$table_teacher);          
      }

      $query = str_replace("++COLNAME++",$cols,$query);

      $query.=" WHERE 1=1";

      if(!empty($ary)){
        foreach($ary as $k=>$v){
          if($k!='sort'){
            $query.=" AND gs.".$k." = ?";
          }
        }
      }

      if($sort){
        $query.= sprintf(" ORDER BY %s %s,id asc",$sort['by'],$sort['dir']);
        unset($ary['sort']);//== removes the sort parameter passed
      }


      $row = $this->db->query($query, array_values($ary))->fetchAll();
      return $row;
      
      return null;
    }

    

    public function insert(){
      if(!$this->id){
        $row = $this->db->query('INSERT INTO '.$this->table.' (teacher_id,grade_id,subject_id,year_id,created_on,updated_on) VALUES (?,?,?,?,NOW(),NOW())', $this->teacher_id, $this->grade_id, $this->subject_id, $this->year_id);
        $this->id = $this->db->insertId(); //== set the newly inserted id          
        return $row->affectedRows();
      }else{
        return true;
      }
    }

    public function saveStudent($question_id,$student_id){
      $row = $this->db->query('INSERT INTO '.$this->table_details.' (gcsforms_id,student_id,question_id,question_text,created_on) VALUES (?,?,?,?,NOW())', $this->id, $student_id, $question_id, 'Q');      
      return $row->affectedRows();
    }

    public function getAllDetails($gcsform_id){
      $table_student = (new Student($this->db))->table;
      return $this->db->query('SELECT gs.question_id,s.name,s.id as student_id FROM '.$this->table_details.' gs INNER JOIN '.$table_student.' s ON (s.id=gs.student_id) WHERE gs.gcsforms_id = ? ORDER BY gs.question_id,s.name',$gcsform_id)->fetchAll();      
    }

    public function getResult($gcsform_id){
      $table_student = (new Student($this->db))->table;
      return $this->db->query('select gs.student_id,count(gs.question_id) occ,s.name,s.adno from '.$this->table_details.' gs INNER JOIN '.$table_student.' s ON (s.id=gs.student_id) WHERE gs.gcsforms_id=? group by gs.student_id ORDER BY occ desc,adno asc',$gcsform_id)->fetchAll();
    }

    public function getStep2Qualifiers(){
      $rows = $this->db->query('select gs.student_id,count(gs.question_id) occ from '.$this->table_details.' gs WHERE gs.gcsforms_id=? group by gs.student_id ORDER BY occ desc',$this->id)->fetchAll();
      $occ=array();
      $step2qualifiers = array();
      foreach($rows as $row){
        array_push($occ,$row['occ']);
        $occ = array_unique($occ);
        $len = count($occ);        
        if($len > (int)getSetting('step1top')) break; //== dont iterate in case of more than step1top setting occurrence

        array_push($step2qualifiers,$row['student_id']);
      }
      return $step2qualifiers;
    }

    public function deleteDetails(){
      $row = $this->db->query('delete from '.$this->table_details.' WHERE gcsforms_id = ?',$this->id);
      return $row->affectedRows();
    }

    public function isStep1Completed(){
      $query = $this->db->query('select distinct(question_id) FROM '.$this->table_details.
      ' WHERE gcsforms_id IN (SELECT id FROM '.$this->table.
      ' WHERE teacher_id=? AND grade_id=? AND subject_id=? AND year_id=?)', $this->teacher_id, $this->grade_id, $this->subject_id, $this->year_id);
      
      return ($query->numRows() >= (new Step1QSet($this->db))->totalQuestions());
    }

    public function isResultDeclared(){
        $tabs1r = (new Step1Result($this->db))->table;
        $query = $this->db->query('select id FROM '.$tabs1r.' WHERE gcsforms_id=?', $this->id);
        return $query->numRows() > 0;
    }

    public function declareStep1Results(){
      $this->deletePrevStep1Results();
      $students = $this->getStep2Qualifiers();      
      foreach($students as $student_id){
        $data = array(
          'student_id'=> $student_id,
          'gcsforms_id'=> $this->id,
          'grade_id'=> $this->grade_id,
          'subject_id'=> $this->subject_id,
          'year_id'=> $this->year_id,
          'teacher_id'=> $this->teacher_id
        );
        $step1results = new Step1Result($this->db,$data);
        $step1results->insert();
      }
    }

    public function deletePrevStep1Results(){
      $step1results = new Step1Result($this->db);
      $this->db->query('DELETE FROM '.$step1results->table.' WHERE gcsforms_id=?',$this->id);
    }

    public function declareResultsForExpiredForms($days){

      $query = sprintf('SELECT * FROM %s WHERE created_on < DATE(NOW() - INTERVAL %d DAY) + INTERVAL 0 SECOND',$this->table,$days);
      $rows = $this->db->query($query)->fetchAll();
      foreach($rows as $row){
        $gcsform1 = new GCSForm($this->db,$row);
        if(!$gcsform1->isResultDeclared()){
          $this->declareStep1Results();
        }
      }
      
    }

    public function total(){
      $query = $this->db->query('SELECT count(id) total FROM '.$this->table)->fetchArray();
      return $query['total'];
    }

    public function totalStudents(){
      $query = $this->db->query('SELECT count(distinct student_id) total FROM '.$this->table_details)->fetchArray();
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