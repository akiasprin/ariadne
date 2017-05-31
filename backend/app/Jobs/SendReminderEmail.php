<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Support\Facades\Mail;

class SendReminderEmail implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected $data;

    public function __construct($id, $name, $code, $email)
    {
        $this->data = [
            'id'    => $id,
            'name'  => $name,
            'code'  => $code,
            'email' => $email,
        ];
    }

    public function handle()
    {
        $data = $this->data;
        Mail::send('activemail', $data, function ($message) use ($data) {
            $message->to($data['email'], $data['name'])->subject('Ariadne交易网欢迎您，这是您的注册码~');
        });
    }
}
