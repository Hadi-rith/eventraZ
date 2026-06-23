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
$routes->get('public/my-registrations', 'PublicPortal::myRegistrations');
$routes->get('public/program-details/(:num)', 'PublicPortal::getProgramDetails/$1');
$routes->get('public/events',    'PublicPortal::events');
$routes->get('public/events-data', 'PublicPortal::getEvents');

// ─── School (requires school session) ────────────────────────────────────────
$routes->get('school/portal',   'School::portal');
$routes->post('school/daftar',  'School::simpanPendaftaran');
$routes->get('school/programs', 'School::getProgramList');
$routes->get('school/my-registrations', 'School::myRegistrations');
$routes->get('school/program-details/(:num)', 'School::getProgramDetails/$1');
$routes->get('school/events',   'School::events');
$routes->get('school/events-data', 'School::getEvents');

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
$routes->get('admin/events', 'Admin::getEvents');
$routes->post('admin/events/update/(:num)', 'Admin::updateEvent/$1');

$routes->get('school/subprograms/(:num)', 'School::getSubPrograms/$1');
$routes->get('public/subprograms/(:num)', 'PublicPortal::getSubPrograms/$1');

$routes->post('admin/programs/sub', 'Admin::createSubProgram');
$routes->get('admin/programs/sub/(:segment)', 'Admin::getSubPrograms/$1');