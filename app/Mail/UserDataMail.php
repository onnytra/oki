<?php

namespace App\Mail;

use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Str;

class UserDataMail extends Mailable
{
    use Queueable, SerializesModels;

    public User $user;

    /**
     * Create a new message instance.
     *
     * @param User $user
     */
    public function __construct(User $user)
    {
        $temporaryPassword = Str::random(20);
        $user->password = $temporaryPassword;
        $user->save();
        $this->user = $user;
        $this->user->password = $temporaryPassword;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        return $this->subject('User Data')
                    ->view('emails.user_data')
                    ->with(['user' => $this->user]);
    }
}
