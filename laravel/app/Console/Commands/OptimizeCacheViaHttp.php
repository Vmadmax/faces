<?php

namespace App\Console\Commands;

use Http;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\URL;

class OptimizeCacheViaHttp extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'optimize:cache:http';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Optimize the cache via http because CLI does not have permissions to get the correct __DIR__ folder by bootstraping the app (jailkit feature).';

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
     * @return int
     */
    public function handle()
    {
        if(App::isLocal()) {
            URL::forceRootUrl('http://webserver');
        }

        $response = Http::post(URL::temporarySignedRoute('optimize.cache', now()->addMinute()), [
            'token' => 'M890y4lMcQnCTgOqlXSAEm23hNe2QlA1mhCsrjBVeroOtk96XpZ1BBCrHw7K'
        ]);

        if($response->ok()) {
            $this->comment($response->body());

            $this->info('Successfully optimized the configuration via HTTP call.');
        }
    }
}
