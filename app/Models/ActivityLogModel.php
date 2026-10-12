<?php
namespace App\Models;
use CodeIgniter\Model;
class ActivityLogModel extends Model {
 protected $table='activity_logs'; protected $primaryKey='id'; protected $returnType='array';
 protected $allowedFields=['user_id','event_type','details','ip_address']; protected $useTimestamps=false;
}