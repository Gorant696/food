<?php

use Illuminate\Database\Seeder;
use App\Tags;
use App\Meta\Metadata;

class TagsTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run(Tags $tags, Metadata $meta)
    {
        foreach ($meta->listOfTags() as $key => $one_tag) {
            $tag_main = $tags->create(['slug' => $key]);
            foreach ($one_tag as $one) {
                $tag_main->tagsTrans()->create([
                    'language_id' => $one['language_id'],
                    'title' => $one['title']
                ]);
            }
        }
    }
}
