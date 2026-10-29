<?php 

  require_once('student.class.php');
  require_once('grade.class.php');
  require_once('subject.class.php');  
  require_once('step2form.class.php');  
  require_once('teacher.class.php');

  class Step3Mark{

    protected $id;    
    protected	$grade_id;    
    protected $student_id;    
    protected $year_id;
    protected $exam_type;
    protected $subject_id;
    protected $score;
    protected $created_on;
    protected $updated_on;
    public $db;
    public $table = 'step3marks';    

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
      if(isset($data['grade_id'])){
        $this->grade_id = $data['grade_id'];
      }
      if(isset($data['year_id'])){
        $this->year_id = $data['year_id'];
      }      
      if(isset($data['exam_type'])){
        $this->exam_type = $data['exam_type'];
      }
      if(isset($data['subject_id'])){
        $this->subject_id = $data['subject_id'];
      }      
      if(isset($data['score'])){
        $this->score = $data['score'];
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

    public function findAllStep3StudentDetails($ary = array(),$sort=false){
      $table_student = (new Student($this->db))->table;
      $table_student_details = (new Student($this->db))->table_details;      
      $table_grade = (new Grade($this->db))->table;
      $table_subject = (new Subject($this->db))->table;
      $table_s2form = (new Step2Form($this->db))->table;
      $cols = "s.name studentname,
      s.email studentemail,
      g.name gradename,
      sub.name subjectname,
      g.id gid,
      sd.year_id as yid,
      sub.id subid,
      s3.score,
      s3.exam_type,
      s3.id s3id,
      s2f.*";

      $query=sprintf("SELECT %s FROM %s s2f 
      INNER JOIN %s s ON (s2f.student_id=s.id AND s.deleted=0)  
      INNER JOIN %s g ON (s2f.grade_id=g.id) 
      INNER JOIN %s sd ON (s.id=sd.student_id)
      INNER JOIN %s sub ON (sub.id=sd.subject_id) 
      LEFT JOIN %s s3 ON (s2f.student_id=s3.student_id AND s2f.year_id=s3.year_id AND sd.subject_id=s3.subject_id)",
      $cols,
      $table_s2form,
      $table_student,
      $table_grade,
      $table_student_details,
      $table_subject,
      $this->table);

      $query.=" WHERE s2f.step3=1";

      if(!empty($ary)){
        foreach($ary as $k=>$v){
          if($k=='subject_id'){
            $query.=" AND sd.".$k." = ?";
          }
          else if($k=='exam_type'){
            $query.=" AND s3.".$k." = ?";
          }
          else if($k!='sort'){
            $query.=" AND s2f.".$k." = ?";
          }
        }
      }

      if($sort){
        $query.= sprintf(" ORDER BY %s %s,studentname asc",$sort['by'],$sort['dir']);
        unset($ary['sort']);//== removes the sort parameter passed
      }     

      $row = $this->db->query($query, array_values($ary))->fetchAll();
      return $row;      
    }

    

    public function insert(){
      if(!$this->id){
        $row = $this->db->query('INSERT INTO '.$this->table.' (student_id,grade_id,subject_id,year_id,exam_type,score,created_on,updated_on) VALUES (?,?,?,?,?,?,NOW(),NOW())', $this->student_id, $this->grade_id, $this->subject_id, $this->year_id,$this->exam_type,$this->score);
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

    public function deleteData($where){
      $query = sprintf('DELETE FROM %s WHERE 1=1',$this->table);
      $names = array_keys($where);
      foreach($names as $k){
        $query.=sprintf(' AND %s=?',$k);
      }
      $ary = array_values($where);
      $this->db->query($query,$ary);
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

    public function examTypes(){
      return array(
        1 => 'CEM',
        2 => 'MIDYIS',
        3 => 'GL',
        4 => 'ALIS'
      );
    }

    public function examColors(){
      return array(
        1 => 'rgba(255, 0, 0, 0.5)',
        2 => 'rgba(255, 255, 0, 0.5)',
        3 => 'rgba(255, 0, 255, 0.5)',
        4 => 'rgba(0, 0, 255, 0.5)'
      );
    }

    public function totalStudents(){
      $query = $this->db->query('SELECT count(DISTINCT student_id,grade_id,year_id) total FROM '.$this->table)->fetchArray();
      return $query['total'];
    }

    public function step3graph(){
      $res = $this->db->query('SELECT exam_type,count(DISTINCT student_id,grade_id,year_id) total FROM '.$this->table.' GROUP BY exam_type ORDER BY exam_type')->fetchAll();      
      $ret = array(0,0,0,0);
      foreach($res as $r){
        $ret[$r['exam_type']-1]=$r['total'];
      }
      return $ret;
    }

    public function searchStudent($name){
      $grade_table = (new Grade($this->db))->table;
      $student = new Student($this->db);
      
      $query=sprintf('SELECT DISTINCT st.id,st.name,g1.name as grade FROM %s AS s3 
      INNER JOIN %s s2 ON (s3.student_id=s2.student_id)
      INNER JOIN %s g1 ON (g1.id=s2.grade_id) 
      INNER JOIN %s st ON (st.id=s3.student_id)
      WHERE st.name like "%%%s%%"', $this->table, $student->table_details,$grade_table, $student->table,$name);


      return $this->db->query($query)->fetchAll();
    }

    public function getStudentMarks($stdid){
      $subject_table = (new Subject($this->db))->table;
      $query=sprintf("select s3.exam_type,s3.score,sub.name as subject,s3.year_id 
        from %s s3 
        INNER JOIN %s sub ON (sub.id=s3.subject_id)
        where s3.student_id=%d ORDER BY exam_type",$this->table,$subject_table,$stdid);
      $res = $this->db->query($query)->fetchAll();
      $extype = $this->examTypes();
      $ret = array("datasets" => array(), "labels" => array_values($extype));
      $excol = $this->examColors();

      $exfound=array();
      
      foreach($res as $r){
        $exfound[$r['exam_type']]=$r['exam_type'];
        if(!isset($ret['datasets'][$r['subject']])){
          $ret['datasets'][$r['subject']]=array(
            "label" => $r['subject'],
            "backgroundColor"=> $excol[$r['exam_type']],
            "stack" =>  'Subject '.$r['subject'],
            "data" => array_fill(0,count($extype),0)
          );
        }
        
        $ret['datasets'][$r['subject']]['data'][$r['exam_type']-1]=(float)$r['score'];
      }

      $diff = array_keys(array_diff_key($extype,$exfound));
      
      $ret['datasets']=array_values($ret['datasets']);

      
      foreach($ret['datasets'] as $idx=>$st){         
        foreach($diff as $d){
          unset($ret['datasets'][$idx]['data'][$d-1]);
        }
        $ret['datasets'][$idx]['data'] = array_values($ret['datasets'][$idx]['data']);
      }

      foreach($diff as $d){
        unset($ret['labels'][$d-1]);        
      }
      $ret['labels']=array_values($ret['labels']);

      return $ret;
    }


  }
?>