<?php

namespace App\Services;

use Smalot\PdfParser\Parser;

class HpiReportParserService
{
    public function parseHpiReport(string $filePath): array
    {
        $parser = new Parser();
        $pdf = $parser->parseFile($filePath);
        $text = $pdf->getText();

        //get the raw file and save to logs
        // file_put_contents(storage_path('logs/raw_pdf.txt'), $text);


        return [
        'registration'   => $this->extractBetween($text, 'VRM', 'VIN'),
        'vin'            => $this->extractBetween($text, 'VIN', 'Engine number'),
        'engine_number'  => $this->extractBetween($text, 'Engine number', 'Colour'),
        'make'           => $this->extractBetween($text, 'Make', 'Model'),
        'model'          => $this->extractBetween($text, 'Model', 'Body type'),
        'body_type'      => $this->extractBetween($text, 'Body type', 'Fuel type'),
        'transmission'   => $this->extractBetween($text, 'Transmission', 'Engine capacity'),
        'current_v5c_issue_date' => $this->extractBetween($text, 'Current V5C issue date', 'CO2 emissions'),
        'mileage'        => $this->extractBetween($text, 'Mileage recording', 'ADVISORY'),
        'advisories'     => $this->extractBetween($text, 'ADVISORY NOTICES', 'HISTORIC TESTS'),
        'previous_owners' => $this->extractBetween($text, 'Number of previous owners', 'Current V5C issue date'),
    ];
    }

    private function extractBetween(string $text, string $start, string $end): string
    {
        $pattern = '/' . preg_quote($start, '/') . '\s*(.*?)\s*' . preg_quote($end, '/') . '/si';
        if (preg_match($pattern, $text, $matches)) {
            return trim($matches[1]);
        }

        return 'Not found';
    }

}
