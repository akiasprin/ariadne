<?php

namespace App\Http\Utilities;


use App\Exceptions\SmsPusherException;

class AlidayuHelper
{
    static function code($sms, $phone, $name, $code)
    {
        $sign = 'ariadne项目实验';
        $templateCode = 'SMS_69070534';
        $content = json_encode([
            'code' => "$code",
            'name' => "$name"
        ]);
        $data = $sms->send("$phone", "$sign", "$content", "$templateCode");
        if (!property_exists($data, 'result')) {
            throw new SmsPusherException();
        }
    }
}