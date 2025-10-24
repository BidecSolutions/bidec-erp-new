<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class StudentCreated extends Mailable
{
    use Queueable, SerializesModels;

    public $student;
    public $password;

    public function __construct($student, $password)
    {
        $this->student = $student;
        $this->password = $password;
    }

    public function build()
    {
        return $this->subject('Welcome to Our Service')
                    ->view('emails.studentCreated') // Create this view
                    ->with([
                        'studentName' => $this->student->name,
                        'password' => $this->password,
                    ]);
    }
}

