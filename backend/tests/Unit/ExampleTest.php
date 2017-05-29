<?php

namespace Tests\Unit;

use App\Models\Category;
use App\Models\Order;
use App\Models\Tag;
use App\Models\Taxonomy;
use Carbon\Carbon;
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
        $result = Good::with('categories')->find(2);
        $result->categories()->sync([2, 3, 9]);

        echo $result;
    }

}
