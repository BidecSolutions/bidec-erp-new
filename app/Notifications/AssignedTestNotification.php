<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;

class AssignedTestNotification extends Notification
{
    use Queueable;

    protected $testDetails;

    // Constructor to pass test details into the notification
    public function __construct($testDetails)
    {
        $this->testDetails = $testDetails;
    }

    // Specify the channels to use
    public function via($notifiable)
    {
        return ['database']; // Only use the database channel
    }

    // Format the notification data for the database
    public function toDatabase($notifiable)
    {
        return [
            'test_title' => $this->testDetails['title'],
            'start_date' => $this->testDetails['start_date'],
            'end_date' => $this->testDetails['end_date'],
            'description' => $this->testDetails['description'],
        ];
    }
}
