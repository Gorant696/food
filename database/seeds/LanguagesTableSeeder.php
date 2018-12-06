<?php

use Illuminate\Database\Seeder;
use App\Languages;
use App\Meta\Metadata;

class LanguagesTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run(Languages $languages)
    {
        foreach (Metadata::listOfLanguages() as $key => $language) {
            $languages->create([
                'code_key' => $key,
                'lang_name' => $language
            ]);
        }
    }
}
