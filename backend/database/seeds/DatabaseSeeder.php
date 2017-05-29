<?php

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $this->call(AdminsTableSeeder::class);
        $this->call(UsersTableSeeder::class);
        $this->call(AddressesTableSeeder::class);
        //$this->call(CategoriesTableSeeder::class);
        //$this->call(TagsTableSeeder::class);
        $this->call(GoodsTableSeeder::class);
    }
}
