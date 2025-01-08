<?php
header('Content-Type: text/html; charset=utf-8');
error_reporting(E_ALL);
ini_set('display_errors', 1);
ob_start();

try {
    require_once(__DIR__ . '/fpdf/fpdf.php');

    class FatturaPDF extends FPDF {
        function Header() {
            $this->SetFont('Arial', 'B', 15);
            $this->Cell(0, 10, 'FATTURA ELETTRONICA', 0, 1, 'C');
            $this->Ln(5);
        }
        
        function Footer() {
            $this->SetY(-15);
            $this->SetFont('Arial', 'I', 8);
            $this->Cell(0, 10, 'Pagina ' . $this->PageNo() . '/1', 0, 0, 'C');
            $this->Ln(5);
        }
    }

    function generaPDF($id_doc) {
        $connection = new mysqli("localhost", "root", "", "fattureelettroniche");
        if ($connection->connect_error) {
            throw new Exception("Errore di connessione con il DBMS: " . $connection->connect_error);
        }

        // Query principale migliorata
        $query = "SELECT f.*, c.* FROM fatture f 
                  LEFT JOIN tabcliente c ON f.IDCLIENTE = c.IDCLIENTE 
                  WHERE f.ID_DOC = ?";
        
        $stmt = $connection->prepare($query);
        $stmt->bind_param("i", $id_doc);
        $stmt->execute();
        $result = $stmt->get_result();
        $fattura = $result->fetch_assoc();

        // Query per i dettagli e IVA
        $query_dettagli = "SELECT d.*, t.* 
                          FROM dfatture d
                          LEFT JOIN tiva t ON d.ID_IVA = t.ID_IVA
                          WHERE d.ID_DOC = ?";
        $stmt = $connection->prepare($query_dettagli);
        $stmt->bind_param("i", $id_doc);
        $stmt->execute();
        $dettagli = $stmt->get_result();

        $pdf = new FatturaPDF();
        $pdf->AddPage();
        
        // Numero Fattura e Data
        $pdf->SetFont('Arial', 'B', 10);
        $pdf->Cell(0, 10, 'nr. ' . $fattura['NDOC'] . ' del ' . date('d/m/Y', strtotime($fattura['DATA'])), 0, 1);
        
        // Sezione Fornitore
        $pdf->SetFont('Arial', 'B', 12);
        $pdf->Cell(0, 10, 'FORNITORE', 0, 1);
        $pdf->SetFont('Arial', '', 10);
        $pdf->Cell(0, 6, $fattura['DENOMINAZIONE'], 0, 1);
        $pdf->Cell(0, 6, 'P.IVA: ' . $fattura['PIVA'], 0, 1);
        $pdf->Cell(0, 6, 'C.F.: ' . $fattura['CF'], 0, 1);
        $pdf->Cell(0, 6, $fattura['INDIRIZZO'], 0, 1);
        $pdf->Cell(0, 6, $fattura['CAP'] . ' - ' . $fattura['CITTA'] . ' (' . $fattura['PROVINCIA'] . ') - ' . $fattura['NAZIONE'], 0, 1);
        if(!empty($fattura['PEC'])) {
            $pdf->Cell(0, 6, 'PEC: ' . $fattura['PEC'], 0, 1);
        }
        
        // Sezione Cliente
        $pdf->Ln(5);
        $pdf->SetFont('Arial', 'B', 12);
        $pdf->Cell(0, 10, 'CLIENTE', 0, 1);
        $pdf->SetFont('Arial', '', 10);
        $pdf->Cell(0, 6, $fattura['CLIENTE_DENOMINAZIONE'], 0, 1);
        $pdf->Cell(0, 6, 'P.IVA: ' . $fattura['PIVA'], 0, 1);
        $pdf->Cell(0, 6, 'C.F.: ' . $fattura['CF'], 0, 1);
        $pdf->Cell(0, 6, $fattura['INDIRIZZO'], 0, 1);
        $pdf->Cell(0, 6, $fattura['CAP'] . ' - ' . $fattura['CITTA'] . ' (' . $fattura['PROVINCIA'] . ') - ' . $fattura['NAZIONE'], 0, 1);
        if(!empty($fattura['SDI'])) {
            $pdf->Cell(0, 6, 'Codice destinatario: ' . $fattura['SDI'], 0, 1);
        }

        // Sezione Prodotti e Servizi
        $pdf->Ln(10);
        $pdf->SetFont('Arial', 'B', 12);
        $pdf->Cell(0, 10, 'PRODOTTI E SERVIZI', 0, 1);
        $pdf->SetFont('Arial', '', 10);
        
        // Intestazione tabella
        $pdf->SetFillColor(200, 200, 200);
        $pdf->Cell(10, 7, 'NR', 1, 0, 'C', true);
        $pdf->Cell(70, 7, 'DESCRIZIONE', 1, 0, 'C', true);
        $pdf->Cell(20, 7, 'QT.', 1, 0, 'C', true);
        $pdf->Cell(30, 7, 'PREZZO', 1, 0, 'C', true);
        $pdf->Cell(30, 7, 'IMPORTO', 1, 0, 'C', true);
        $pdf->Cell(30, 7, 'IVA', 1, 1, 'C', true);

        // Righe dettaglio
        $n = 1;
        $totale_imponibile = 0;
        $totale_iva = 0;
        while ($riga = $dettagli->fetch_assoc()) {
            $pdf->Cell(10, 6, $n, 1, 0, 'C');
            $pdf->Cell(70, 6, $riga['DESCRIZIONE'], 1);
            $pdf->Cell(20, 6, $riga['QT'], 1, 0, 'C');
            $pdf->Cell(30, 6, number_format($riga['IMPORTOUNITARIO'], 2) . ' EUR', 1, 0, 'R');
            $pdf->Cell(30, 6, number_format($riga['IMPORTORIGA'], 2) . ' EUR', 1, 0, 'R');
            $pdf->Cell(30, 6, $riga['COD'] . '%', 1, 1, 'C');
            $totale_imponibile += $riga['IMPORTORIGA'];
            $totale_iva += ($riga['IMPORTORIGA'] * $riga['COD'] / 100);
            $n++;
        }

        // Metodo di Pagamento
        $pdf->Ln(10);
        $pdf->SetFont('Arial', 'B', 12);
        $pdf->Cell(0, 10, 'METODO DI PAGAMENTO', 0, 1);
        $pdf->SetFont('Arial', '', 10);
        $pdf->Cell(0, 6, 'Modalità: ' . $fattura['TIPOPAGAMENTO'], 0, 1);
        if(!empty($fattura['BANCA'])) {
            $pdf->Cell(0, 6, 'Banca: ' . $fattura['BANCA'], 0, 1);
        }
        if(!empty($fattura['IBAN'])) {
            $pdf->Cell(0, 6, 'IBAN: ' . $fattura['IBAN'], 0, 1);
        }

        // Riepilogo Totali
        $pdf->Ln(10);
        $pdf->SetFont('Arial', 'B', 12);
        $pdf->Cell(0, 10, 'RIEPILOGO TOTALI', 0, 1);
        $pdf->SetFont('Arial', '', 10);
        
        $pdf->Cell(120, 6, 'Totale imponibile:', 0, 0, 'R');
        $pdf->Cell(70, 6, number_format($totale_imponibile, 2) . ' EUR', 0, 1, 'R');
        
        $pdf->Cell(120, 6, 'Totale IVA:', 0, 0, 'R');
        $pdf->Cell(70, 6, number_format($totale_iva, 2) . ' EUR', 0, 1, 'R');
        
        $pdf->SetFont('Arial', 'B', 10);
        $pdf->Cell(120, 6, 'Totale documento:', 0, 0, 'R');
        $pdf->Cell(70, 6, number_format($totale_imponibile + $totale_iva, 2) . ' EUR', 0, 1, 'R');

        ob_end_clean();
        $pdf->Output('D', 'Fattura_' . $fattura['NDOC'] . '.pdf');
        
        $connection->close();
    }

    if (isset($_GET['id'])) {
        generaPDF($_GET['id']);
    } else {
        throw new Exception("ID fattura non specificato");
    }

} catch (Exception $e) {
    ob_end_clean();
    die('Errore: ' . $e->getMessage());
}
?>