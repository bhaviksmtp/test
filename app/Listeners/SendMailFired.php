<?php

namespace App\Listeners;

use App\Models\User;
use App\Events\SendMail;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;

class SendMailFired
{
    /**
     * Create the event listener.
     *
     * @return void
     */
    public function __construct()
    {
        //
    }

    /**
     * Handle the event.
     *
     * @param  \App\Events\SendMail  $event
     * @return void
     */
    public function handle(SendMail $event)
    {
        $users = User::find($event->users)->toArray();

        foreach($users as $user)
        {
            Mail::send('eventMail', $users, function($message) use ($user) {
                $message->to($user['email']);
                $message->subject('Event Testing');
            });
        }
    }
}
