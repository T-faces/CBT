<?php
namespace App\Models;
use CodeIgniter\Model;
class QuestionModel extends Model {
 protected $table='questions'; protected $primaryKey='id'; protected $returnType='array';
 protected $allowedFields=['exam_id','question_type','question_text','option_a','option_b','option_c','option_d','correct_option','points'];
 protected $useTimestamps=true; protected $createdField='created_at'; protected $updatedField='updated_at';
}