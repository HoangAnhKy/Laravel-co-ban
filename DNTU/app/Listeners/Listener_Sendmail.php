<?php

namespace App\Listeners;

use App\Events\SendMail_even;
use App\Notifications\Sendmail_Notification;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Support\Facades\Notification;

class Listener_Sendmail
{

    public function handle(SendMail_even $event)
    {
        Notification::send($event->user, new Sendmail_Notification($event->user));
    }
}
