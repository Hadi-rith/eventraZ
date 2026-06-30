<?php
namespace App\Validation;
class ValidationRules
{
    const LOGIN = [
        'username' => ['rules' => 'required|min_length[3]'],
        'password' => ['rules' => 'required|min_length[3]'],
    ];
    const SIGNUP_SCHOOL = [
        'school_code' => ['rules' => 'required|min_length[3]|max_length[20]'],
        'school_name' => ['rules' => 'required|min_length[3]|max_length[100]'],
        'email'       => ['rules' => 'required|valid_email|max_length[100]'],
        'password'    => ['rules' => 'required|min_length[6]'],
    ];
    const SIGNUP_AWAM = [
        'name'     => ['rules' => 'required|min_length[2]|max_length[100]'],
        'email'    => ['rules' => 'required|valid_email|max_length[100]'],
        'password' => ['rules' => 'required|min_length[6]'],
    ];
    const PUBLIC_REGISTRATION = [
        'programId'  => ['rules' => 'required|is_natural_no_zero'],
        'namaPenuh'  => ['rules' => 'required|max_length[100]'],
        'noIC'       => ['rules' => 'required|max_length[20]'],
        'telAwam'    => ['rules' => 'required|max_length[20]'],
        'email'      => ['rules' => 'required|valid_email|max_length[100]'],
        'bilAhli'    => ['rules' => 'required|is_natural'],
    ];
}