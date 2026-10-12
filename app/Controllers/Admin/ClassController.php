<?php
namespace App\Controllers\Admin;
use App\Controllers\BaseController;
class ClassController extends BaseController {
 public function index() {
  $classes=db_connect()->table('school_classes')->orderBy('name')->get()->getResultArray();
  return view('admin/classes',['title'=>'Data Kelas','classes'=>$classes]);
 }
 public function create() {
  $name=trim((string)$this->request->getPost('name')); $year=trim((string)$this->request->getPost('academic_year'));
  if($name===''||$year==='') return redirect()->back()->with('error','Nama kelas dan tahun pelajaran wajib diisi.');
  try { db_connect()->table('school_classes')->insert(['name'=>$name,'academic_year'=>$year]); }
  catch(\Throwable $e) { return redirect()->back()->with('error','Kelas gagal ditambahkan; pastikan nama belum digunakan.'); }
  return redirect()->to('/admin/classes')->with('success','Kelas ditambahkan.');
 }
}
