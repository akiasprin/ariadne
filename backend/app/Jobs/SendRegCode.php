<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;

class SendRegCode implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    private $phone;
    private $name;
    private $code;

    public function __construct($phone, $name, $code)
    {
        $this->phone = $phone;
        $this->name = $name;
        $this->code  = $code;
    }

    public function handle()
    {
        $smsParams = [
            'code'    => $this->code,
            'name'    => $this->name,
        ];

    }
}
