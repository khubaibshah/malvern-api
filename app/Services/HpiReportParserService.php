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

        return [
            'registration'   => $this->extractBetween($text, 'VRM', 'VIN'),
            'vin'            => $this->extractBetween($text, 'VIN', 'Engine number'),
            'engine_number'  => $this->extractBetween($text, 'Engine number', 'Colour'),
            'make'           => $this->extractBetween($text, 'Make', 'Model'),
            'model'          => $this->extractBetween($text, 'Model', 'Body type'),
            'mot_expiry'     => $this->extractBetween($text, 'MOT expiry', 'Mileage'),
            'mileage'        => $this->extractBetween($text, 'Mileage recording', 'ADVISORY'),
            'advisories'     => $this->extractBetween($text, 'ADVISORY NOTICES', 'HISTORIC TESTS'),
        ];
    }

    private function extractBetween(string $text, string $start, string $end): string
    {
        $pattern = '/' . preg_quote($start, '/') . '(.*?)' . preg_quote($end, '/') . '/s';
        if (preg_match($pattern, $text, $matches)) {
            return trim($matches[1]);
        }

        return 'Not found';
    }
}
