<?php

namespace Tests\Unit;

use App\Models\Category;
use App\Models\Order;
use App\Models\Tag;
use App\Models\Taxonomy;
use Carbon\Carbon;
use Illuminate\Support\Facades\Mail;
use Tests\TestCase;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use App\Models\User;
use App\Models\Good;

class ExampleTest extends TestCase
{
    /**
     * A basic test example.
     *
     * @return void
     */
    public function testBasicTest()
    {
        $name = '学院君';
        $flag = Mail::raw('这是一封测试邮件', function ($message) {
            $to = '1009303339@qq.com';
            $message->to($to)->subject('测试邮件');
        });
        if($flag){
            echo '发送邮件成功，请查收！';
        }else{
            echo '发送邮件失败，请重试！';
        }
        echo $flag;
    }

}
