<?php
namespace App\Controllers;
class MonitoringController extends BaseController {
 public function index() {
  $attempts=db_connect()->table('exam_attempts a')->select('a.*,u.full_name,u.username,e.title')
   ->join('users u','u.id=a.user_id')->join('exams e','e.id=a.exam_id')
   ->where('a.status','in_progress')->orderBy('a.last_seen_at','DESC')->get()->getResultArray();
  return view('teacher/monitoring',['title'=>'Monitoring Peserta','attempts'=>$attempts]);
 }
}
