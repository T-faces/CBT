<?php
namespace App\Controllers;
use App\Models\ExamModel; use App\Models\QuestionModel; use App\Models\AttemptModel; use App\Models\AnswerModel; use App\Models\ActivityLogModel;
class StudentExamController extends BaseController {
 public function index() {
  $exams=(new ExamModel())->where('status','published')->orderBy('start_at','ASC')->findAll();
  return view('student/exams',['title'=>'Ujian Tersedia','exams'=>$exams]);
 }
 public function start($examId) {
  if(session()->get('role')!=='siswa') return redirect()->to('/')->with('error','Halaman ini khusus peserta didik.');
  $exam=(new ExamModel())->where('status','published')->find((int)$examId);
  if(!$exam) throw \CodeIgniter\Exceptions\PageNotFoundException::forPageNotFound('Ujian tidak tersedia.');
  $now=time();
  if(($exam['start_at']&&$now<strtotime($exam['start_at']))||($exam['end_at']&&$now>strtotime($exam['end_at']))) return redirect()->to('/siswa/exams')->with('error','Ujian belum dimulai atau sudah berakhir.');
  $pin=trim((string)$this->request->getGet('pin'));
  if(!empty($exam['access_pin']) && !hash_equals((string)$exam['access_pin'],$pin)) return view('student/pin',['title'=>'PIN Ujian','exam'=>$exam]);
  $am=new AttemptModel();
  $attempt=$am->where('exam_id',$exam['id'])->where('user_id',session()->get('user_id'))->where('status','in_progress')->first();
  if(!$attempt) {
   $count=$am->where('user_id',session()->get('user_id'))->where('exam_id',$exam['id'])->countAllResults();
   if($count >= (int)$exam['max_attempts']) return redirect()->to('/siswa/exams')->with('error','Batas percobaan ujian tercapai.');
   $id=$am->insert(['exam_id'=>$exam['id'],'user_id'=>session()->get('user_id'),'started_at'=>date('Y-m-d H:i:s'),'last_seen_at'=>date('Y-m-d H:i:s'),'status'=>'in_progress']);
   $attempt=$am->find($id);
   $qs=(new QuestionModel())->where('exam_id',$exam['id'])->findAll();
   if((int)$exam['shuffle_questions']) shuffle($qs);
   $db=db_connect(); $pos=1;
   foreach($qs as $q) {
    $opts=['A','B','C','D']; $available=[];
    foreach($opts as $key) if(!empty($q['option_'.strtolower($key)])) $available[]=$key;
    if((int)$exam['shuffle_options']) shuffle($available);
    $db->table('attempt_questions')->insert(['attempt_id'=>$id,'question_id'=>$q['id'],'position_no'=>$pos++,'option_order'=>json_encode($available)]);
   }
  }
  if($attempt['status']!=='in_progress') return redirect()->to('/siswa/exams')->with('error','Ujian ini sudah dikumpulkan.');
  if(strtotime($attempt['started_at'])+(int)$exam['duration_minutes']*60<=time()) { $this->finalize($attempt); return redirect()->to('/siswa/exams')->with('error','Waktu habis; ujian dikumpulkan.'); }
  $rows=db_connect()->table('attempt_questions aq')->select('q.*,aq.position_no,aq.option_order')->join('questions q','q.id=aq.question_id')->where('aq.attempt_id',$attempt['id'])->orderBy('aq.position_no')->get()->getResultArray();
  $saved=[]; foreach((new AnswerModel())->where('attempt_id',$attempt['id'])->findAll() as $a) $saved[$a['question_id']]=$a;
  foreach($rows as &$q) $q['option_order']=json_decode($q['option_order']??'[]',true)?:['A','B','C','D']; unset($q);
  return view('student/take_exam',['title'=>$exam['title'],'exam'=>$exam,'attempt'=>$attempt,'questions'=>$rows,'saved'=>$saved,'deadline'=>strtotime($attempt['started_at'])+(int)$exam['duration_minutes']*60]);
 }
 public function saveAnswer($attemptId) {
  $attempt=(new AttemptModel())->find((int)$attemptId);
  if(!$attempt||(int)$attempt['user_id']!==(int)session()->get('user_id')||$attempt['status']!=='in_progress') return $this->response->setStatusCode(403)->setJSON(['ok'=>false,'csrfHash'=>csrf_hash()]);
  $exam=(new ExamModel())->find($attempt['exam_id']);
  if(strtotime($attempt['started_at'])+(int)$exam['duration_minutes']*60<=time()) { $this->finalize($attempt); return $this->response->setStatusCode(410)->setJSON(['ok'=>false,'message'=>'Waktu habis','csrfHash'=>csrf_hash()]); }
  $qid=(int)$this->request->getPost('question_id'); $q=(new QuestionModel())->where('id',$qid)->where('exam_id',$exam['id'])->first();
  if(!$q) return $this->response->setStatusCode(400)->setJSON(['ok'=>false,'csrfHash'=>csrf_hash()]);
  $value=trim((string)$this->request->getPost('answer_text')); $data=['attempt_id'=>$attempt['id'],'question_id'=>$qid,'answer_text'=>$value,'selected_option_key'=>null,'is_correct'=>null,'score'=>null];
  if($q['question_type']==='mcq') {
   $aq=db_connect()->table('attempt_questions')->where('attempt_id',$attempt['id'])->where('question_id',$qid)->get()->getRowArray();
   $order=json_decode($aq['option_order']??'[]',true)?:['A','B','C','D'];
   if(!in_array($value,$order,true)) $value='';
   $data['answer_text']=$value; $data['selected_option_key']=$value;
   $data['is_correct']=$value!==''&&$value===$q['correct_option']?1:0;
   $data['score']=$data['is_correct']?(float)$q['points']:0;
  }
  $m=new AnswerModel(); $old=$m->where('attempt_id',$attempt['id'])->where('question_id',$qid)->first();
  if($old) $m->update($old['id'],$data); else $m->insert($data);
  (new AttemptModel())->update($attempt['id'],['last_seen_at'=>date('Y-m-d H:i:s')]);
  (new ActivityLogModel())->insert(['user_id'=>session()->get('user_id'),'event_type'=>'answer_saved','details'=>'attempt='.$attempt['id'].';question='.$qid,'ip_address'=>$this->request->getIPAddress()]);
  return $this->response->setJSON(['ok'=>true,'saved_at'=>date('H:i:s'),'csrfHash'=>csrf_hash()]);
 }
 public function heartbeat($attemptId) {
  $attempt=(new AttemptModel())->find((int)$attemptId);
  if(!$attempt||(int)$attempt['user_id']!==(int)session()->get('user_id')||$attempt['status']!=='in_progress') return $this->response->setStatusCode(403)->setJSON(['ok'=>false,'csrfHash'=>csrf_hash()]);
  (new AttemptModel())->update($attempt['id'],['last_seen_at'=>date('Y-m-d H:i:s')]);
  return $this->response->setJSON(['ok'=>true,'csrfHash'=>csrf_hash()]);
 }
 public function submit($attemptId) {
  $attempt=(new AttemptModel())->find((int)$attemptId);
  if(!$attempt||(int)$attempt['user_id']!==(int)session()->get('user_id')) return redirect()->to('/siswa/exams')->with('error','Percobaan tidak valid.');
  if($attempt['status']==='in_progress') $this->finalize($attempt);
  return redirect()->to('/siswa/exams')->with('success','Ujian berhasil dikumpulkan.');
 }
 private function finalize(array $attempt): void {
  $answers=(new AnswerModel())->where('attempt_id',$attempt['id'])->findAll(); $score=0; $pending=false;
  foreach($answers as $a) { if($a['score']!==null)$score+=(float)$a['score']; if($a['score']===null)$pending=true; }
  (new AttemptModel())->update($attempt['id'],['submitted_at'=>date('Y-m-d H:i:s'),'status'=>'submitted','objective_score'=>$score,'essay_pending'=>$pending?1:0]);
 }
}
