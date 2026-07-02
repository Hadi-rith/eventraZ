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
$routes->get('admin/programs/stats/attendance',             'Admin::getProgramAttendanceStats'); // NEW ROUTE

// Registration data
$routes->get('admin/data',                                  'Admin::getAdminData');
$routes->get('admin/data/students/(:num)',                  'Admin::getRegistrationStudents/$1');
$routes->get('admin/data/family/(:num)',                    'Admin::getRegistrationFamilyMembers/$1');

// Accounts
$routes->get('admin/accounts',                              'Admin::getAccounts');
$routes->post('admin/accounts/create/(:segment)',           'Admin::createAccount/$1');
$routes->post('admin/accounts/update/(:segment)/(:num)',    'Admin::updateAccount/$1/$2');
$routes->post('admin/accounts/delete/(:segment)/(:num)',    'Admin::deleteAccount/$1/$2');

// Events tab
$routes->get('admin/events',                                'Admin::getEvents');
$routes->post('admin/events/update/(:num)',                  'Admin::updateEvent/$1');

// ============================================================
// SCHOOL PORTAL
// ============================================================
$routes->get('school',                                      'School::index');
$routes->get('school/portal',                               'School::portal');

// Registration
$routes->post('school/daftar',                              'School::simpanPendaftaran');
$routes->get('school/my-registrations',                     'School::myRegistrations');

// Program lists
$routes->get('school/programs',                             'School::getProgramList');
$routes->get('school/programs/sub/(:num)',                  'School::getSubPrograms/$1');
$routes->get('school/program-details/(:num)',               'School::getProgramDetails/$1');

// Events page
$routes->get('school/events',                               'School::events');
$routes->get('school/events-data',                          'School::getEvents');

// ============================================================
// PUBLIC PORTAL (awam)
// ============================================================
$routes->get('awam',                                        'PublicPortal::index');
$routes->get('awam/portal',                                 'PublicPortal::portal');

// Registration
$routes->post('awam/daftar',                                'PublicPortal::simpanPendaftaran');
$routes->get('awam/my-registrations',                       'PublicPortal::myRegistrations');

// Program lists
$routes->get('awam/programs',                               'PublicPortal::getProgramList');
$routes->get('awam/programs/sub/(:num)',                    'PublicPortal::getSubPrograms/$1');
$routes->get('awam/program-details/(:num)',                 'PublicPortal::getProgramDetails/$1');

// Events page
$routes->get('awam/events',                                 'PublicPortal::events');
$routes->get('awam/events-data',                            'PublicPortal::getEvents');

// ============================================================
// ATTENDANCE MANAGEMENT
// ============================================================

// Admin / Super Admin
$routes->get('admin/attendance',                            'AttendanceAdmin::index');
$routes->post('admin/attendance/create',                    'AttendanceAdmin::create');
$routes->post('admin/attendance/regenerate/(:num)',         'AttendanceAdmin::regenerate/$1');
$routes->post('admin/attendance/toggle/(:num)',              'AttendanceAdmin::toggleStatus/$1');
$routes->get('admin/attendance/records/(:num)',              'AttendanceAdmin::records/$1');

// Participant-facing
$routes->post('attendance/process-scan',                    'Attendance::processScan');
$routes->get('attendance/checkin/(:segment)',                'Attendance::checkin/$1');