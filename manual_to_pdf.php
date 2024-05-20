<?php
require('fpdf/fpdf.php');

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

function RemoveDiacritics($text)
{
    $diacritics = array(
        'ľ' => 'l', 'š' => 's', 'č' => 'c', 'ť' => 't', 'ž' => 'z', 'ý' => 'y', 'á' => 'a', 'í' => 'i', 'é' => 'e',
        'ä' => 'a', 'ô' => 'o', 'ň' => 'n', 'ď' => 'd', 'Ľ' => 'L', 'Š' => 'S', 'Č' => 'C', 'Ť' => 'T', 'Ž' => 'Z',
        'Ý' => 'Y', 'Á' => 'A', 'Í' => 'I', 'É' => 'E', 'Ä' => 'A', 'Ô' => 'O', 'Ň' => 'N', 'Ď' => 'D'
    );

    return strtr($text, $diacritics);
}

function FormatListItems($text)
{
    // Prvotná úprava textu na odstránenie prebytočných bielych znakov
    $text = preg_replace('/\s+/', ' ', $text);
    $text = preg_replace('/\s?<li>\s?/', "\n- ", $text);
    $text = preg_replace('/\s?<\/li>\s?/', "\n", $text);
    $text = preg_replace('/\s?<ul>\s?/', "\n", $text);
    $text = preg_replace('/\s?<\/ul>\s?/', "\n", $text);

    // Odstránenie všetkých ostatných HTML tagov
    $text = strip_tags($text);

    // Odstránenie diakritiky
    $text = RemoveDiacritics($text);

    return $text;
}

$referer = isset($_SERVER['HTTP_REFERER']) ? $_SERVER['HTTP_REFERER'] : null;

if ($referer) {
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $referer);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
    curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
    curl_setopt($ch, CURLOPT_ENCODING, 'UTF-8');
    $html = curl_exec($ch);
    curl_close($ch);

    if ($html === false) {
        exit("Error fetching the page.");
    }

    libxml_use_internal_errors(true);
    $dom = new DOMDocument();
    if (!$dom->loadHTML('<?xml encoding="UTF-8">' . $html)) {
        $errors = libxml_get_errors();
        foreach ($errors as $error) {
            echo "Libxml error: " . $error->message . "<br>";
        }
        libxml_clear_errors();
        exit("Error loading HTML content.");
    }
    libxml_clear_errors();
    
    $manual = $dom->getElementById('manual');

    if (!$manual) {
        exit("Error: Manual content not found.");
    }

    $sections = $manual->getElementsByTagName('h2');

    if (empty($sections)) {
        exit("Error: No sections found in the manual content.");
    }

    $pdf = new PDF();

    foreach ($sections as $section) {
        $title = $section->nodeValue;
        $body = '';
        $next = $section->nextSibling;
        while ($next && $next->nodeName !== 'h2') {
            if ($next->nodeName == 'ul') {
                $body .= FormatListItems($dom->saveHTML($next));
            } else {
                $body .= $dom->saveHTML($next);
            }
            $next = $next->nextSibling;
        }

        // Priame použitie UTF-8 bez konverzie a odstránenie diakritiky
        $title = utf8_decode(RemoveDiacritics($title));
        $body = utf8_decode(RemoveDiacritics($body));

        $pdf->PrintChapter($title, $body);
    }

    $pdf->Output('manual.pdf', 'I');
} else {
    exit("No referrer URL found.");
}
?>
