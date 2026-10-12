<?php
namespace App\Models;
use CodeIgniter\Model;
class AnswerModel extends Model {
 protected $table='answers'; protected $primaryKey='id'; protected $returnType='array';
 protected $allowedFields=['attempt_id','question_id','answer_text','selected_option_key','is_correct','score'];
 protected $useTimestamps=true; protected $createdField='created_at'; protected $updatedField='updated_at';
}