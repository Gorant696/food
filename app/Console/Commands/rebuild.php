<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Artisan;



class rebuild extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'install:app';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Install Food Menu application';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        
        //Set variable for database "menu"
        $db = env('DB_DATABASE');

        //Make connection to DB
        $conn = mysqli_connect(env('DB_HOST'), env('DB_USERNAME'), env('DB_PASSWORD'));

        if (!$conn) {
            dd("Can't connect to MySQL");
        }

        //Queries to drop and create fresh database "menu"
         mysqli_query($conn,"DROP DATABASE ".$db);
         mysqli_query($conn,"CREATE DATABASE ".$db." character set UTF8 collate utf8_general_ci");
         Artisan::call('migrate:refresh');
         Artisan::call('db:seed');

        dd('Application installed successfully');

    }
}
