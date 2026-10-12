<?php
namespace App\Controllers\Admin;
use App\Controllers\BaseController;
use App\Models\UserModel;
class UserController extends BaseController {
 public function index() {
  return view('admin/users',['title'=>'Manajemen Pengguna','users'=>(new UserModel())->orderBy('created_at','DESC')->findAll()]);
 }
 public function new() { return view('admin/user_form',['title'=>'Tambah Pengguna','user'=>null]); }
 public function create() {
  $rules=['username'=>'required|min_length[3]|max_length[80]|is_unique[users.username]','full_name'=>'required|max_length[160]','role'=>'required|in_list[admin,guru,siswa]','password'=>'required|min_length[8]'];
  if(!$this->validate($rules)) return redirect()->back()->withInput()->with('error',implode(' ',$this->validator->getErrors()));
  (new UserModel())->insert(['username'=>trim($this->request->getPost('username')),'full_name'=>trim($this->request->getPost('full_name')),'role'=>$this->request->getPost('role'),'password_hash'=>password_hash((string)$this->request->getPost('password'),PASSWORD_DEFAULT),'is_active'=>1]);
  return redirect()->to('/admin/users')->with('success','Pengguna dibuat.');
 }
 public function edit($id) {
  $user=(new UserModel())->find((int)$id); if(!$user) throw \CodeIgniter\Exceptions\PageNotFoundException::forPageNotFound();
  return view('admin/user_form',['title'=>'Edit Pengguna','user'=>$user]);
 }
 public function update($id) {
  $m=new UserModel(); $user=$m->find((int)$id); if(!$user) throw \CodeIgniter\Exceptions\PageNotFoundException::forPageNotFound();
  $rules=['full_name'=>'required|max_length[160]','role'=>'required|in_list[admin,guru,siswa]','password'=>'permit_empty|min_length[8]'];
  if(!$this->validate($rules)) return redirect()->back()->withInput()->with('error','Data tidak valid; password baru minimal 8 karakter.');
  $data=['full_name'=>trim($this->request->getPost('full_name')),'role'=>$this->request->getPost('role')];
  $pw=(string)$this->request->getPost('password'); if($pw!=='') $data['password_hash']=password_hash($pw,PASSWORD_DEFAULT);
  $m->update((int)$id,$data);
  return redirect()->to('/admin/users')->with('success','Pengguna diperbarui.');
 }
 public function toggle($id) {
  $m=new UserModel(); $user=$m->find((int)$id); if(!$user) throw \CodeIgniter\Exceptions\PageNotFoundException::forPageNotFound();
  if((int)$user['id']===(int)session()->get('user_id')) return redirect()->back()->with('error','Akun yang sedang dipakai tidak dapat dinonaktifkan.');
  $m->update((int)$id,['is_active'=>$user['is_active']?0:1]);
  return redirect()->to('/admin/users')->with('success','Status pengguna diperbarui.');
 }
}
