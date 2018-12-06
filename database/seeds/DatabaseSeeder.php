<?php

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     *
     * @return void
     */
    public function run()
    {
        // $this->call(UsersTableSeeder::class);
        $this->call(LanguagesTableSeeder::class);
        $this->call(TagsTableSeeder::class);
        $this->call(CategoriesTableSeeder::class);
        $this->call(FoodsTableSeeder::class);
        $this->call(IngredientsTableSeeder::class);
    }
}
