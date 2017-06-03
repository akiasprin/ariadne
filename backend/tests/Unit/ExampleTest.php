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
        $user = User::find(1)->first();
        $good = $user->cart()->with('user')->get()->groupBy('user_id')->values();
        echo(json_encode($good->toArray()));
    }

}
