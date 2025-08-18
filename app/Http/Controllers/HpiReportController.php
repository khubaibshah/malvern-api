<?php

namespace App\Http\Controllers;

use App\Services\HpiReportParserService;
use Illuminate\Http\Request;

class HpiReportController extends Controller
{
    public function __construct(protected HpiReportParserService $hpiReportParserService) {}

    public function hpiReport(Request $request)
    {
        $request->validate([
            'pdf' => 'required|file|mimes:pdf',
        ]);

        // Save to disk explicitly and get full path
        $file = $request->file('pdf');
        $path = $file->store('hpi_reports', 'local'); // default is 'local' but explicit here

        $fullPath = storage_path("app/{$path}");

        if (!file_exists($fullPath)) {
            return response()->json(['error' => 'File not saved or path invalid', 'path' => $fullPath], 500);
        }

        $data = $this->hpiReportParserService->parseHpiReport($fullPath);

        return response()->json($data);
    }
}
