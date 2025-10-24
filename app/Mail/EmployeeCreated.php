<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class EmployeeCreated extends Mailable
{
    use Queueable, SerializesModels;

    public $employee;
    public $password;

    public function __construct($employee, $password)
    {
        $this->employee = $employee;
        $this->password = $password;
    }

    public function build()
    {
        return $this->subject('Welcome to Our Service')
                    ->view('email.employeeCreated') // Create this view
                    ->with([
                        'employeeName' => $this->employee,
                        'password' => $this->password,
                    ]);
    }
}

