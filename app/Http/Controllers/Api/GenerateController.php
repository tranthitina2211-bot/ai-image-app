<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
//insert Jobs
use App\Jobs\GenerateImageJob;
class GenerateController extends Controller
{
    public function generate(Request $request)
	{
	    $request->validate([
	        'prompt' => 'required|string|max:2000',
	    ]);

	    $jobId = (string) Str::uuid();

	    GenerateImageJob::dispatch($jobId, $request->prompt);

	    return response()->json([
            'job_id' => $jobId
        ]);

	}

}







