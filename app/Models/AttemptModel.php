<?php
namespace App\Models;
use CodeIgniter\Model;
class AttemptModel extends Model {
 protected $table='exam_attempts'; protected $primaryKey='id'; protected $returnType='array';
 protected $allowedFields=['exam_id','user_id','started_at','submitted_at','last_seen_at','status','objective_score','essay_pending'];
 protected $useTimestamps=true; protected $createdField='created_at'; protected $updatedField='updated_at';
}