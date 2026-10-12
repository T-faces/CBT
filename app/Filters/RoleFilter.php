<?php
namespace App\Filters;
use CodeIgniter\HTTP\RequestInterface;use CodeIgniter\HTTP\ResponseInterface;use CodeIgniter\Filters\FilterInterface;
class RoleFilter implements FilterInterface {
 public function before(RequestInterface $request,$arguments=null){if(!session()->get('user_id'))return redirect()->to('/login');if(!in_array(session()->get('role'),$arguments??[],true))return redirect()->to('/')->with('error','Akses ditolak.');}
 public function after(RequestInterface $request,ResponseInterface $response,$arguments=null){}
}