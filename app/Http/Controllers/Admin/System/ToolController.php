<?php

namespace App\Http\Controllers\Admin\System;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\System\CleanUnusedUploadsRequest;
use Illuminate\Support\Facades\Artisan;

class ToolController extends Controller
{
    public function __construct(
    ) {}

    public function cleanUnusedUploads(CleanUnusedUploadsRequest $request)
    {
        try {
            $minutes = $request->validated('minutes') ?: null;

            $command = 'uploads:clean-unused';
            if ($minutes !== null) {
                $command .= ' --minutes='.(int) $minutes;
            }

            Artisan::call($command);
            $output = Artisan::output();

            return respondSuccess([
                'command' => $command,
                'output' => trim($output),
            ]);
        } catch (\Throwable $e) {
            return respondError('SYSTEM_FAILED', $e);
        }
    }
}
