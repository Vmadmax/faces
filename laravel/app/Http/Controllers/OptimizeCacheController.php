<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Artisan;
use Symfony\Component\Console\Output\StreamOutput;

class OptimizeCacheController extends Controller
{
    public function __invoke(Request $request) {
        if($request->get('token') !== 'M890y4lMcQnCTgOqlXSAEm23hNe2QlA1mhCsrjBVeroOtk96XpZ1BBCrHw7K') {
            abort(403, 'Unauthorized action.');
        }

        $stream = fopen("php://output", "w");

        foreach(['view:cache', 'config:cache', 'route:cache'] as $call) {
            Artisan::call($call, [], new StreamOutput($stream));
        }
    }
}
