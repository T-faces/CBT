<?php
use CodeIgniter\Router\RouteCollection;
/** @var RouteCollection $routes */
$routes->get('/', 'DashboardController::index', ['filter'=>'auth']);
$routes->get('login', 'AuthController::login');
$routes->post('login', 'AuthController::attemptLogin');
$routes->post('logout', 'AuthController::logout', ['filter'=>'auth']);

$routes->group('admin', ['filter'=>'role:admin'], static function($routes) {
 $routes->get('users', 'Admin\UserController::index');
 $routes->get('users/new', 'Admin\UserController::new');
 $routes->post('users', 'Admin\UserController::create');
 $routes->get('users/(:num)/edit', 'Admin\UserController::edit/$1');
 $routes->post('users/(:num)/edit', 'Admin\UserController::update/$1');
 $routes->post('users/(:num)/toggle', 'Admin\UserController::toggle/$1');
 $routes->get('classes', 'Admin\ClassController::index');
 $routes->post('classes', 'Admin\ClassController::create');
 $routes->get('subjects', 'Admin\SubjectController::index');
 $routes->post('subjects', 'Admin\SubjectController::create');
});
$routes->group('guru', ['filter'=>'role:admin,guru'], static function($routes) {
 $routes->get('exams', 'ExamController::index');
 $routes->get('exams/new', 'ExamController::new');
 $routes->post('exams', 'ExamController::create');
 $routes->get('exams/(:num)/questions', 'QuestionController::index/$1');
 $routes->post('exams/(:num)/questions', 'QuestionController::create/$1');
 $routes->get('results/(:num)', 'ResultController::exam/$1');
 $routes->get('results/(:num)/csv', 'ResultController::csv/$1');
 $routes->post('answers/(:num)/grade', 'ResultController::gradeEssay/$1');
 $routes->get('monitoring', 'MonitoringController::index');
});
$routes->group('siswa', ['filter'=>'role:admin,guru,siswa'], static function($routes) {
 $routes->get('exams', 'StudentExamController::index');
 $routes->get('exam/(:num)', 'StudentExamController::start/$1');
 $routes->post('attempt/(:num)/answer', 'StudentExamController::saveAnswer/$1');
 $routes->post('attempt/(:num)/heartbeat', 'StudentExamController::heartbeat/$1');
 $routes->post('attempt/(:num)/submit', 'StudentExamController::submit/$1');
});
