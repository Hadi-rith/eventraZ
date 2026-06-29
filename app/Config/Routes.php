<?php

use CodeIgniter\Router\RouteCollection;

/**
 * @var RouteCollection $routes
 */

// ============================================================
// HOME / LANDING
// ============================================================
$routes->get('/', 'Home::index');

// ============================================================
// AUTH
// ============================================================
$routes->get('login',           'Login::index');
$routes->post('login/proses',   'Login::proses');
$routes->post('login/sekolah',  'Login::signupSchool');
$routes->post('login/awam',     'Login::signupAwam');
$routes->get('logout',          'Login::logout');

// ============================================================
// ADMIN — Super Admin & Regular Admin
// ============================================================
$routes->get('admin/dashboard',         'Admin::dashboard');
$routes->get('admin/dashboard-stats',   'Admin::getDashboardStats');

// Programs
$routes->get('admin/programs',                              'Admin::getProgramList');
$routes->post('admin/programs',                             'Admin::createProgram');
$routes->post('admin/programs/sub',                         'Admin::createSubProgram');
$routes->post('admin/programs/update/(:segment)',           'Admin::updateProgram/$1');
$routes->post('admin/programs/delete/(:segment)',           'Admin::deleteProgram/$1');
$routes->get('admin/programs/subs/(:segment)',              'Admin::getSubPrograms/$1');
$routes->get('admin/programs/capacity/(:num)',               'Admin::getProgramCapacity/$1');
$routes->get('admin/programs/stats',                        'Admin::getProgramStats');
$routes->get('admin/programs/stats/export',                 'Admin::exportProgramStats');

// Registration data
$routes->get('admin/data',                                  'Admin::getAdminData');
$routes->get('admin/data/students/(:num)',                  'Admin::getRegistrationStudents/$1');
$routes->get('admin/data/family/(:num)',                    'Admin::getRegistrationFamilyMembers/$1');

// Accounts
$routes->get('admin/accounts',                              'Admin::getAccounts');
$routes->post('admin/accounts/create/(:segment)',           'Admin::createAccount/$1');
$routes->post('admin/accounts/update/(:segment)/(:num)',    'Admin::updateAccount/$1/$2');
$routes->post('admin/accounts/delete/(:segment)/(:num)',    'Admin::deleteAccount/$1/$2');

// Events tab (alias — same data as programs, used by event management view)
$routes->get('admin/events',                                'Admin::getEvents');
$routes->post('admin/events/update/(:num)',                  'Admin::updateEvent/$1');

// ============================================================
// SCHOOL PORTAL
// ============================================================
$routes->get('school',                                      'School::index');       // redirects → school/events after login
$routes->get('school/portal',                               'School::portal');

// Registration
$routes->post('school/daftar',                              'School::simpanPendaftaran');
$routes->get('school/my-registrations',                     'School::myRegistrations');

// Program lists (used by registration form dropdowns)
$routes->get('school/programs',                             'School::getProgramList');
$routes->get('school/programs/sub/(:num)',                  'School::getSubPrograms/$1');
$routes->get('school/program-details/(:num)',               'School::getProgramDetails/$1');

// Events page
$routes->get('school/events',                               'School::events');
$routes->get('school/events-data',                          'School::getEvents');

// ============================================================
// PUBLIC PORTAL
// ============================================================
$routes->get('public',                                      'PublicPortal::index');  // redirects → public/events after login
$routes->get('public/portal',                               'PublicPortal::portal'); // registration form page

// Registration
$routes->post('public/daftar',                              'PublicPortal::simpanPendaftaran');
$routes->get('public/my-registrations',                     'PublicPortal::myRegistrations');

// Program lists (used by registration form dropdowns)
$routes->get('public/programs',                             'PublicPortal::getProgramList');
$routes->get('public/programs/sub/(:num)',                  'PublicPortal::getSubPrograms/$1');
$routes->get('public/program-details/(:num)',               'PublicPortal::getProgramDetails/$1');

// Events page
$routes->get('public/events',                               'PublicPortal::events');
$routes->get('public/events-data',                          'PublicPortal::getEvents');