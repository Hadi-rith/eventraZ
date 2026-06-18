<?php

use CodeIgniter\Router\RouteCollection;

/**
 * @var RouteCollection $routes
 */

// ─── Public (no auth) ────────────────────────────────────────────────────────
$routes->get('/',             'Login::index');
$routes->post('login/proses', 'Login::proses');
$routes->post('signup/school', 'Login::signupSchool');
$routes->post('signup/awam',   'Login::signupAwam');
$routes->get('logout',        'Login::logout');

$routes->get('public',           'PublicPortal::index');
$routes->post('public/daftar',   'PublicPortal::simpanPendaftaran');
$routes->get('public/programs',  'PublicPortal::getProgramList');

// ─── School (requires school session) ────────────────────────────────────────
$routes->get('school/portal',   'School::portal');
$routes->post('school/daftar',  'School::simpanPendaftaran');
$routes->get('school/programs', 'School::getProgramList');

// ─── Admin (requires admin session) ──────────────────────────────────────────
$routes->get('admin/dashboard', 'Admin::dashboard');
$routes->get('admin/data',      'Admin::getAdminData');
$routes->get('admin/registration-students/(:num)', 'Admin::getRegistrationStudents/$1');
$routes->get('admin/programs',  'Admin::getProgramList');
$routes->post('admin/programs', 'Admin::createProgram');
$routes->post('admin/programs/update/(:segment)', 'Admin::updateProgram/$1');
$routes->post('admin/programs/delete/(:segment)', 'Admin::deleteProgram/$1');
$routes->get('admin/accounts', 'Admin::getAccounts');
$routes->post('admin/accounts/create/(:segment)', 'Admin::createAccount/$1');
$routes->post('admin/accounts/update/(:segment)/(:num)', 'Admin::updateAccount/$1/$2');
$routes->post('admin/accounts/delete/(:segment)/(:num)', 'Admin::deleteAccount/$1/$2');

$routes->get('school/subprograms/(:num)', 'School::getSubPrograms/$1');
$routes->get('public/subprograms/(:num)', 'PublicPortal::getSubPrograms/$1');

$routes->post('admin/programs/sub', 'Admin::createSubProgram');
$routes->get('admin/programs/sub/(:segment)', 'Admin::getSubPrograms/$1');
