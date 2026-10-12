<?php
namespace App\Controllers;
use App\Models\ExamModel;use App\Models\QuestionModel;
class QuestionController extends BaseController {
 public function index($id){$exam=(new ExamModel())->find((int)$id);if(!$exam)throw \CodeIgniter\Exceptions\PageNotFoundException::forPageNotFound();return view('teacher/questions',['title'=>'Bank Soal','exam'=>$exam,'questions'=>(new QuestionModel())->where('exam_id',(int)$id)->findAll()]);}
 public function create($id){
  $id=(int)$id;if(!(new ExamModel())->find($id))throw \CodeIgniter\Exceptions\PageNotFoundException::forPageNotFound();
  $type=$this->request->getPost('question_type');if(!in_array($type,['mcq','essay'],true))$type='mcq';
  $rules=['question_text'=>'required|min_length[2]'];if($type==='mcq')$rules+=['option_a'=>'required','option_b'=>'required','correct_option'=>'required|in_list[A,B,C,D]'];
  if(!$this->validate($rules))return redirect()->back()->withInput()->with('error','Lengkapi teks soal dan opsi wajib.');
  (new QuestionModel())->insert(['exam_id'=>$id,'question_type'=>$type,'question_text'=>trim((string)$this->request->getPost('question_text')),'option_a'=>$this->request->getPost('option_a')?:null,'option_b'=>$this->request->getPost('option_b')?:null,'option_c'=>$this->request->getPost('option_c')?:null,'option_d'=>$this->request->getPost('option_d')?:null,'correct_option'=>$type==='mcq'?$this->request->getPost('correct_option'):null,'points'=>max(1,(float)$this->request->getPost('points'))]);
  return redirect()->to("/guru/exams/$id/questions")->with('success','Soal ditambahkan.');
 }
}