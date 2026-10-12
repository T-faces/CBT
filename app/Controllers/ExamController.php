<?php
namespace App\Controllers;
use App\Models\ExamModel;
class ExamController extends BaseController {
 public function index(){return view('teacher/exams',['title'=>'Kelola Ujian','exams'=>(new ExamModel())->orderBy('created_at','DESC')->findAll()]);}
 public function new(){return view('teacher/exam_form',['title'=>'Buat Ujian','subjects'=>db_connect()->table('subjects')->orderBy('name')->get()->getResultArray(),'classes'=>db_connect()->table('school_classes')->orderBy('name')->get()->getResultArray()]);}
 public function create(){
  $rules=['title'=>'required|min_length[3]|max_length[180]','duration_minutes'=>'required|is_natural_no_zero|less_than_equal_to[300]','max_attempts'=>'required|is_natural_no_zero|less_than_equal_to[5]'];
  if(!$this->validate($rules))return redirect()->back()->withInput()->with('error','Periksa nama ujian, durasi, dan batas percobaan.');
  $start=$this->request->getPost('start_at');$end=$this->request->getPost('end_at');
  if($start&&$end&&strtotime($end)<=strtotime($start))return redirect()->back()->withInput()->with('error','Waktu selesai harus setelah waktu mulai.');
  $pin=trim((string)$this->request->getPost('access_pin'));
  (new ExamModel())->insert(['title'=>trim((string)$this->request->getPost('title')),'description'=>trim((string)$this->request->getPost('description')),'subject_id'=>$this->request->getPost('subject_id')?:null,'class_id'=>$this->request->getPost('class_id')?:null,'duration_minutes'=>(int)$this->request->getPost('duration_minutes'),'start_at'=>$start?date('Y-m-d H:i:s',strtotime($start)):null,'end_at'=>$end?date('Y-m-d H:i:s',strtotime($end)):null,'access_pin'=>$pin?:null,'shuffle_questions'=>$this->request->getPost('shuffle_questions')?1:0,'shuffle_options'=>$this->request->getPost('shuffle_options')?1:0,'max_attempts'=>(int)$this->request->getPost('max_attempts'),'status'=>$this->request->getPost('status')==='published'?'published':'draft','created_by'=>(int)session()->get('user_id')]);
  return redirect()->to('/guru/exams')->with('success','Ujian dibuat. Tambahkan soal pada bank soal.');
 }
}