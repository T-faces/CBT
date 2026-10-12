<?php
namespace App\Controllers\Admin;
use App\Controllers\BaseController;
class SubjectController extends BaseController {
 public function index() {
  $subjects=db_connect()->table('subjects')->orderBy('name')->get()->getResultArray();
  return view('admin/subjects',['title'=>'Mata Pelajaran','subjects'=>$subjects]);
 }
 public function create() {
  $name=trim((string)$this->request->getPost('name')); $code=trim((string)$this->request->getPost('code'));
  if($name==='') return redirect()->back()->with('error','Nama mata pelajaran wajib diisi.');
  try { db_connect()->table('subjects')->insert(['name'=>$name,'code'=>$code?:null]); }
  catch(\Throwable $e) { return redirect()->back()->with('error','Gagal menambah mapel; nama/kode mungkin sudah dipakai.'); }
  return redirect()->to('/admin/subjects')->with('success','Mata pelajaran ditambahkan.');
 }
}
