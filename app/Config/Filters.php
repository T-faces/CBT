<?php
namespace Config;
use CodeIgniter\Config\Filters as BaseFilters;use CodeIgniter\Filters\CSRF;use CodeIgniter\Filters\DebugToolbar;use CodeIgniter\Filters\Honeypot;use CodeIgniter\Filters\InvalidChars;use CodeIgniter\Filters\SecureHeaders;use App\Filters\AuthFilter;use App\Filters\RoleFilter;
class Filters extends BaseFilters {
 public array $aliases=['csrf'=>CSRF::class,'toolbar'=>DebugToolbar::class,'honeypot'=>Honeypot::class,'invalidchars'=>InvalidChars::class,'secureheaders'=>SecureHeaders::class,'auth'=>AuthFilter::class,'role'=>RoleFilter::class];
 public array $globals=['before'=>['csrf'],'after'=>['toolbar']]; public array $methods=[]; public array $filters=[];
}