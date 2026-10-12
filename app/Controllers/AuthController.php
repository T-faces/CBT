<?php
namespace App\Controllers;
use App\Models\UserModel;
class AuthController extends BaseController {
 public function login(){if(session()->get('user_id'))return redirect()->to('/');return view('auth/login',['title'=>'Login CBT']);}
 public function attemptLogin(){
  if(!$this->validate(['username'=>'required|min_length[3]|max_length[80]','password'=>'required|max_length[200]']))return redirect()->back()->withInput()->with('error','Masukkan username dan password.');
  $u=(new UserModel())->where('username',trim((string)$this->request->getPost('username')))->where('is_active',1)->first();
  if(!$u||!password_verify((string)$this->request->getPost('password'),$u['password_hash']))return redirect()->back()->withInput()->with('error','Login gagal. Periksa username dan password.');
  session()->regenerate(true);session()->set(['user_id'=>(int)$u['id'],'username'=>$u['username'],'full_name'=>$u['full_name'],'role'=>$u['role']]);
  (new UserModel())->update($u['id'],['last_login_at'=>date('Y-m-d H:i:s')]);return redirect()->to('/');
 }
 public function logout(){session()->destroy();return redirect()->to('/login')->with('success','Anda berhasil keluar.');}
}