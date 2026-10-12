<?php
namespace App\Controllers;
use App\Models\ExamModel; use App\Models\AttemptModel; use App\Models\AnswerModel; use App\Models\QuestionModel;
class ResultController extends BaseController {
 public function exam($examId) {
  $exam=(new ExamModel())->find((int)$examId); if(!$exam) throw \CodeIgniter\Exceptions\PageNotFoundException::forPageNotFound();
  $attempts=db_connect()->table('exam_attempts a')->select('a.*,u.full_name,u.username')
   ->join('users u','u.id=a.user_id')->where('a.exam_id',(int)$examId)->orderBy('a.submitted_at','DESC')->get()->getResultArray();
  $answers=db_connect()->table('answers an')->select('an.*,q.question_text,q.points,q.question_type,u.full_name,ea.exam_id')
   ->join('questions q','q.id=an.question_id')->join('exam_attempts ea','ea.id=an.attempt_id')
   ->join('users u','u.id=ea.user_id')->where('ea.exam_id',(int)$examId)->where('q.question_type','essay')->get()->getResultArray();
  return view('teacher/results',['title'=>'Rekap Nilai','exam'=>$exam,'attempts'=>$attempts,'essayAnswers'=>$answers]);
 }
 public function csv($examId) {
  $exam=(new ExamModel())->find((int)$examId); if(!$exam) throw \CodeIgniter\Exceptions\PageNotFoundException::forPageNotFound();
  $rows=db_connect()->table('exam_attempts a')->select('u.username,u.full_name,a.status,a.started_at,a.submitted_at,a.objective_score,a.essay_pending')
   ->join('users u','u.id=a.user_id')->where('a.exam_id',(int)$examId)->orderBy('u.full_name')->get()->getResultArray();
  $out=fopen('php://temp','r+'); fputcsv($out,['Username','Nama','Status','Mulai','Kumpul','Nilai Objektif','Esai Pending']);
  foreach($rows as $r) fputcsv($out,$r); rewind($out); $csv=stream_get_contents($out); fclose($out);
  return $this->response->setHeader('Content-Type','text/csv; charset=UTF-8')->setHeader('Content-Disposition','attachment; filename="rekap-ujian-'.$examId.'.csv"')->setBody("\xEF\xBB\xBF".$csv);
 }
 public function gradeEssay($answerId) {
  if(!in_array(session()->get('role'),['admin','guru'],true)) return redirect()->to('/');
  $m=new AnswerModel(); $answer=$m->find((int)$answerId); if(!$answer) throw \CodeIgniter\Exceptions\PageNotFoundException::forPageNotFound();
  $q=(new QuestionModel())->find($answer['question_id']); if(!$q||$q['question_type']!=='essay') return redirect()->back()->with('error','Jawaban bukan esai.');
  $score=(float)$this->request->getPost('score'); if($score<0||$score>(float)$q['points']) return redirect()->back()->with('error','Nilai di luar rentang poin.');
  $m->update($answer['id'],['score'=>$score,'is_correct'=>null]);
  $attempt=(new AttemptModel())->find($answer['attempt_id']);
  $pending=(new AnswerModel())->where('attempt_id',$attempt['id'])->where('score',null)->countAllResults()>0;
  (new AttemptModel())->update($attempt['id'],['essay_pending'=>$pending?1:0]);
  return redirect()->back()->with('success','Nilai esai disimpan.');
 }
}
