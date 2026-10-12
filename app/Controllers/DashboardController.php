<?php
namespace App\Controllers;
use App\Models\ExamModel;
class DashboardController extends BaseController {
 public function index(){
  $role=session()->get('role');
  return view('dashboard/index',['title'=>'Dashboard CBT','examCount'=>(new ExamModel())->where('status','published')->countAllResults(),'role'=>$role]);
 }
}