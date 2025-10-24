<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;

class AssignedTaskNotification extends Notification
{
    use Queueable;

    protected $taskDetails;

    // Constructor to pass task details into the notification
    public function __construct($taskDetails)
    {
        $this->taskDetails = $taskDetails;
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
            'task_title' => $this->taskDetails['title'],
            'start_date' => $this->taskDetails['start_date'],
            'end_date' => $this->taskDetails['end_date'],
            'description' => $this->taskDetails['description'],
        ];
    }
}
