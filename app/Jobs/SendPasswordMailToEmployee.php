<?php

namespace App\Jobs;

use App\Mail\SendPassword;
use App\Models\Option;
use App\Models\User;
use Illuminate\Support\Facades\Mail;

class SendPasswordMailToEmployee extends BaseJob
{
    public $userId;

    public $password;

    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct($domain, $userId, $password)
    {
        parent::__construct($domain);

        $this->userId = $userId;
        $this->password = $password;
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        $this->config();

        $storeName = Option::get('name', 'Cute Shop', $this->domain);
        $user = User::find($this->userId);
        if (! $user) {
            return;
        }
        $mail = (new SendPassword($user, $this->password, $storeName))
            ->to($user->email);
        Mail::send($mail);
    }
}
