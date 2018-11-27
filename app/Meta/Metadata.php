<?php

namespace App\Meta;

use App\Languages;
use App\Categories;

/*
	Class responsible holding predefined data for seeding in Database
*/
class Metadata
{
	public function __construct(Languages $languages, Categories $categories)
	{
		if ($hr = $languages->where('code_key', 'hr')->first()->id) {
			$this->hr = $hr;
		}

		if ($en = $languages->where('code_key', 'en')->first()->id) {
			$this->en = $en;
		}

	}

	static function list_of_languages()
	{
		 return [
		        'hr' => 'Croatian',
		        'en' => 'English'
       	];
	}


	static function ingredients_for_food()
	{
		return [
				'sir' => ['pizza', 'tjestenina_skampi'],
				'maslina_ulje' => ['tjestenina_skampi', 'rizoto_piletina'],
				'celer' => ['tjestenina_skampi', 'rizoto_piletina', 'mix_salata', 'krumpir_salata'],
				'cesnjak' =>  ['tjestenina_skampi', 'rizoto_piletina', 'krumpir_salata'],
				'tajni_sastojak' => ['ketchup', 'vocna_torta', 'sladoled_cokolada', 'pizza']
			   ];
	}

	public function list_of_ingredients()
	{
		return 
        [
        	'sir' 			=>		[
			        					[
			        					'language_id' => $this->en,
			        					'title' => 'Cheese'
			        					],
			        					[
			        					'language_id' => $this->hr,
			        					'title' => 'Sir'
			        					]
		        					],
            'maslina_ulje'  =>		[
		    	        				[
		    	        				'language_id' => $this->en,
		    	        				'title' => 'Olive oil'
		    	        				],
		    	        				[
		    	        				'language_id' => $this->hr,
		    	        				'title' => 'Maslinovo Ulje'
		    	        				]
		            				],
		    'celer'    		 => 	[
		    	        				[
		    	        				'language_id' => $this->en,
		    	        				'title' => 'Celery'
		    	        				],
		    	        				[
		    	        				'language_id' => $this->hr,
		    	        				'title' => 'Celer'
		    	        				]
		            				],
	        'cesnjak'   	=>		[
		    	        				[
		    	        				'language_id' => $this->en,
		    	        				'title' => 'Garlic'
		    	        				],
		    	        				[
		    	        				'language_id' => $this->hr,
		    	        				'title' => 'Češnjak'
		    	        				]
		            				],
		    'tajni_sastojak'=>		[
		    	        				[
		    	        				'language_id' => $this->en,
		    	        				'title' => 'Secret Ingredient'
		    	        				],
		    	        				[
		    	        				'language_id' => $this->hr,
		    	        				'title' => 'Tajni sastojak'
		    	        				]
		            				]
    	];
	}

	public function list_of_food(Categories $categories)
	{
		$main_meal = $categories->where('slug', 'glavno-jelo')->first()->id;

		$salad = $categories->where('slug', 'salata')->first()->id;

		$dessert = $categories->where('slug', 'desert')->first()->id;

		return 
        [
            'pizza'                 => [
                                            [
                                            'language_id' => $this->en,
                                            'title' => 'Pizza',
                                            'description' => 'Popular italian food',
                                            'category_id' => $main_meal
                                            ],
                                            [
                                            'language_id' => $this->hr,
                                            'title' => 'Pizza',
                                            'description' => 'Popularno talijansko jelo',
                                            'category_id' => $main_meal
                                            ]
                                        ],
            'tjestenina_skampi'     => [
                                            [
                                            'language_id' => $this->en,
                                            'title' => 'Green Pasta with shrimps',
                                            'description' => 'Green Pasta with shrimps in white sauce with garlic and mozzarella',
                                            'category_id' => $main_meal,
                                            ],
                                            [
                                            'language_id' => $this->hr,
                                            'title' => 'Zelena tjestenina sa škampima',
                                            'description' => 'Zelena tjestenina sa škampima u bijelom umaku sa češnjakom i mozzarellom',
                                            'category_id' => $main_meal
                                            ]
                                        ],
            'rizoto_piletina'       =>  [
                                            [
                                            'language_id' => $this->en,
                                            'title' => 'Rice with chicken',
                                            'description' => 'Integrated rice with fried chicken meat',
                                            'category_id' => $main_meal,
                                            ],
                                            [
                                            'language_id' => $this->hr,
                                            'title' => 'Rižoto sa piletinom',
                                            'description' => 'Integrirana riža sa pečenim pilećim komadima mesa',
                                            'category_id' => $main_meal,
                                            ]
                                        ],
            'krumpir_salata'        =>  [
                                            [
                                            'language_id' => $this->en,
                                            'title' => 'Potato salad',
                                            'description' => 'Salad with small potato pieces and onion',
                                            'category_id' => $salad,
                                            ],
                                            [
                                            'language_id' => $this->hr,
                                            'title' => 'Krumpir salata',
                                            'description' => 'Salata od malih komadića krumpira sa lukom',
                                            'category_id' => $salad,
                                            ]
                                        ],
            'mix_salata'            =>  [
                                            [
                                            'language_id' => $this->en,
                                            'title' => 'Mixed salad',
                                            'description' => 'Mixed salad with tomato, cabbage and olives',
                                            'category_id' => $salad,
                                            ],
                                            [
                                            'language_id' => $this->hr,
                                            'title' => 'Mješana salata',
                                            'description' => 'Mješana salata sa rajčicom, kupusom i maslinama',
                                            'category_id' => $salad,
                                            ]
                                        ],
            'sladoled_cokolada'     =>  [
                                            [
                                            'language_id' => $this->en,
                                            'title' => 'Choco icecream',
                                            'description' => 'Icecream with chocolade',
                                            'category_id' => $dessert,
                                            ],
                                            [
                                            'language_id' => $this->hr,
                                            'title' => 'Sladoled sa čokoloadom',
                                            'description' => 'Sladoled od čokolade',
                                            'category_id' => $dessert,
                                            ]
                                        ],
            'vocna_torta'           =>  [
                                            [
                                            'language_id' => $this->en,
                                            'title' => 'Fruit cake',
                                            'description' => 'Cake with small pieces of season fruits',
                                            'category_id' => $dessert,
                                            ],
                                            [
                                            'language_id' => $this->hr,
                                            'title' => 'Voćni kolač',
                                            'description' => 'Voćni kolač sa komadićima sezonskog voća',
                                            'category_id' => $dessert,
                                            ]
                                        ],
            'ketchup'               =>  [
                                            [
                                            'language_id' => $this->en,
                                            'title' => 'Ketchup',
                                            'description' => 'Tomato sauce',
                                            'category_id' => null,
                                            ],
                                            [
                                            'language_id' => $this->hr,
                                            'title' => 'Ketchup',
                                            'description' => 'umak od rajčice',
                                            'category_id' => null,
                                            ]
                                        ]
        ];
	}

	public function list_of_categories()
	{

		return 
        [
        	'glavno-jelo' => [
	        					[
	        					'language_id' => $this->en,
	        					'title' => 'Main meal'
	        					],
	        					[
	        					'language_id' => $this->hr,
	        					'title' => 'Glavno jelo'
	        					]
        					],
            'desert'     => [
    	        				[
    	        				'language_id' => $this->en,
    	        				'title' => 'Dessert'
    	        				],
    	        				[
    	        				'language_id' => $this->hr,
    	        				'title' => 'Desert'
    	        				]
            				],
            'salata'     => [
    	        				[
    	        				'language_id' => $this->en,
    	        				'title' => 'Salad'
    	        				],
    	        				[
    	        				'language_id' => $this->hr,
    	        				'title' => 'Salata'
    	        				]
            				]
    	];
	}

	public function list_of_tags()
	{
		return 
        [
        	'ukusno'	=>			[
			        					[
			        					'language_id' => $this->en,
			        					'title' => 'Tasty'
			        					],
			        					[
			        					'language_id' => $this->hr,
			        					'title' => 'Ukusno'
			        					]
		        					],
            'super'		=>		[
		    	        				[
		    	        				'language_id' => $this->en,
		    	        				'title' => 'Great'
		    	        				],
		    	        				[
		    	        				'language_id' => $this->hr,
		    	        				'title' => 'Super'
		    	        				]
		            				],
		    'mljac'    		 => 	[
		    	        				[
		    	        				'language_id' => $this->en,
		    	        				'title' => 'Yumi'
		    	        				],
		    	        				[
		    	        				'language_id' => $this->hr,
		    	        				'title' => 'Mljac'
		    	        				]
		            				]
    	];
	}

}
