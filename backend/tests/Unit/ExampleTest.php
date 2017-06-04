<?php

namespace Tests\Unit;

use App\Models\Cart;
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
        $goods = [[1, 2], [2, 1]];
        $orders = [];
        foreach ($goods as $good) {
            $user = Good::find($good[0])->user_id;
            $orders[$user][] = $good;
        }
        foreach ($orders as $order) {
            dd($order);
        }
    }

}
