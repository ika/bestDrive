<?php

// spares|print

if (!defined('MULTIDB_MODULE') || !USER_AUTHENTICATED) {
    die("UNAUTHORIZED ACCESS");
} else {

    require('lib/fpdf181/fpdf.php');

    class PDF extends FPDF {

        private $nr = 0;

        public function Header() {
            $title = 'Gerry\'s Spares Stock';
            $this->SetFont('Arial', 'B', 15);
            $this->Cell(100, 10, $title, 0, 0, 'L');
            $this->Cell(80);
            $date = date("j F Y, g:i a");
            $this->SetFont('Arial', 'B', 10);
            $this->Cell(50, 10, $date, 0, 0, 'L');
            $this->Ln(15);
        }

        public function Footer() {
            // Position at 1.5 cm from bottom
            $this->SetY(-15);
            // Arial italic 8
            $this->SetFont('Arial', 'I', 8);
            // Text color in gray
            $this->SetTextColor(128);
            // Page number
            $this->Cell(0, 10, 'Page ' . $this->PageNo() . '/{nb}', 0, 0, 'C');
        }

        public function makeTable($header, $data) {
            // Colors, line width and bold font
            $this->SetFillColor(255, 0, 0);
            $this->SetTextColor(255);
            $this->SetDrawColor(128, 0, 0);
            $this->SetLineWidth(.3);
            $this->SetFont('', 'B');
            // Header
            $w = array(10, 25, 80, 35, 25, 10, 10, 10, 10, 10, 10, 10, 10, 10);
            for ($i = 0; $i < count($header); $i++)
                if ($i == 0) {
                    $this->Cell($w[$i], 7, $header[$i], 1, 0, 'C', true);
                } else {
                    $this->Cell($w[$i], 7, $header[$i], 1, 0, 'L', true);
                }
            $this->Ln();
            // Color and font restoration
            $this->SetFillColor(224, 235, 255);
            $this->SetTextColor(0);
            $this->SetFont('');
            // Data
            $fill = false;

            foreach ($data as $row) {
                $this->nr++;
                $this->Cell($w[0], 6, $this->nr, 'LR', 0, 'C', $fill);
                $this->Cell($w[1], 6, $row['sp_partid'], 'LR', 0, 'L', $fill);
                $this->Cell($w[2], 6, $row['sp_descr'], 'LR', 0, 'L', $fill);
                $this->Cell($w[3], 6, $row['sp_partno'], 'LR', 0, 'L', $fill);
                $this->Cell($w[4], 6, $row['sp_cost'], 'LR', 0, 'L', $fill);
                $this->Cell($w[5], 6, $row['sp_onhand'], 'LR', 0, 'C', $fill);
                $this->Cell($w[6], 6, "", 'LR', 0, 'C', $fill);
                $this->Cell($w[7], 6, "", 'LR', 0, 'C', $fill);
                $this->Cell($w[8], 6, "", 'LR', 0, 'C', $fill);
                $this->Cell($w[9], 6, "", 'LR', 0, 'C', $fill);
                $this->Cell($w[10], 6, "", 'LR', 0, 'C', $fill);
                $this->Cell($w[11], 6, "", 'LR', 0, 'C', $fill);
                $this->Cell($w[12], 6, "", 'LR', 0, 'C', $fill);
                $this->Cell($w[13], 6, "", 'LR', 0, 'C', $fill);
                $this->Ln();
                $fill = !$fill;
            }
            // Closing line
            $this->Cell(array_sum($w), 0, '', 'T');
        }

    }

    $name = 'spares_stock-' . time() . '.pdf';

    $pdf = new PDF();

    $class = new Printing();
    
    $result = $class->printAllSpares();

    if (count($result) > 0) {

        $chunk = array_chunk($result, 25);

        $header = array('Nr', 'ID', 'Description', 'Part Nr.', 'Cost', 'O/H', '', '', '', '', '', '', '', '');

        $pdf->SetFont('Arial', '', 10);
        $pdf->AliasNbPages();

        foreach ($chunk as $data) {
            $pdf->AddPage('L', 'A4');
            $pdf->makeTable($header, $data);
        }
    }

    $pdf->Output('I', $name, true);
}
exit();
?>
