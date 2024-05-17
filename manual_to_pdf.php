<?php
require('fpdf.php');

class PDF extends FPDF
{
    function Header()
    {
        $this->SetFont('Arial', 'B', 15);
        $this->Cell(0, 10, 'Manual', 0, 1, 'C');
        $this->Ln(10);
    }

    function Footer()
    {
        $this->SetY(-15);
        $this->SetFont('Arial', 'I', 8);
        $this->Cell(0, 10, 'Page ' . $this->PageNo(), 0, 0, 'C');
    }

    function ChapterTitle($title)
    {
        $this->SetFont('Arial', 'B', 12);
        $this->SetFillColor(200, 220, 255);
        $this->Cell(0, 10, $title, 0, 1, 'L', true);
        $this->Ln(4);
    }

    function ChapterBody($body)
    {
        $this->SetFont('Arial', '', 12);
        $this->MultiCell(0, 10, $body);
        $this->Ln();
    }

    function PrintChapter($title, $body)
    {
        $this->AddPage();
        $this->ChapterTitle($title);
        $this->ChapterBody($body);
    }
}

$referer = isset($_SERVER['HTTP_REFERER']) ? $_SERVER['HTTP_REFERER'] : null;

if ($referer) {
    echo "Referer: " . $referer . "<br>"; // Debug: zobraz referer URL

    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $referer);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
    curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true); // Dôsledne sleduje presmerovania
    $html = curl_exec($ch);
    $curl_error = curl_error($ch); // Debug: získať cURL chyby
    curl_close($ch);

    if ($html === false) {
        echo "cURL Error: " . $curl_error . "<br>"; // Debug: zobraziť cURL chyby
        exit("Error fetching the page.");
    }

    if (empty($html)) {
        exit("Error: Empty HTML content."); // Debug: skontrolovať prázdny obsah
    }

    libxml_use_internal_errors(true);
    $dom = new DOMDocument();
    if (!$dom->loadHTML($html)) {
        libxml_clear_errors();
        exit("Error loading HTML content.");
    }
    libxml_clear_errors();
    
    $xpath = new DOMXPath($dom);
    $node = $xpath->query('//*[@id="manual"]')->item(0);

    if (!$node) {
        exit("Error: Manual content not found.");
    }

    $manual_content = $dom->saveHTML($node);
    $manual_content_clean = strip_tags($manual_content, '<h2><ul><li>');

    $sections = explode('<h2>', $manual_content_clean);
    array_shift($sections);

    $pdf = new PDF();

    foreach ($sections as $section) {
        $parts = explode('</h2>', $section);
        $title = trim($parts[0]);
        $body = trim($parts[1]);
        $pdf->PrintChapter($title, $body);
    }

    $pdf->Output();
} else {
    exit("No referrer URL found.");
}
?>
