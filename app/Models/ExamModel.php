<?php
namespace App\Models;
use CodeIgniter\Model;
class ExamModel extends Model {
 protected $table='exams'; protected $primaryKey='id'; protected $returnType='array';
 protected $allowedFields=['title','description','subject_id','class_id','duration_minutes','start_at','end_at','access_pin','shuffle_questions','shuffle_options','max_attempts','status','created_by'];
 protected $useTimestamps=true; protected $createdField='created_at'; protected $updatedField='updated_at';
}