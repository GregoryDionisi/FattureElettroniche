<?php

$conn = new mysqli("localhost", "root", "", "FattureElettroniche");


if ($conn->connect_error) {
    die("Errore di connessione: " . $conn->connect_error);
}


$id_fattura = isset($_GET['id']) ? (int)$_GET['id'] : 0;

//query per ottenere i dettagli della fattura
$sql_dettagli = "
    SELECT d.DESCRIZIONE, d.QT, d.IMPORTOUNITARIO, d.IMPORTORIGA,
           t.COD AS CodiceIVA, t.DESCRIZIONE AS DescrizioneIVA
    FROM dfatture d
    JOIN tiva t ON d.ID_IVA = t.ID_IVA
    WHERE d.ID_DOC = $id_fattura
";

$result_dettagli = $conn->query($sql_dettagli);

if ($result_dettagli->num_rows > 0) {
    echo "<h1>Dettagli Fattura</h1>";
    echo "<table style='border-collapse: collapse; width: 100%;'>";
    echo "<tr>
            <th style='border: 1px solid #ddd; padding: 8px;'>Descrizione</th>
            <th style='border: 1px solid #ddd; padding: 8px;'>Quantità</th>
            <th style='border: 1px solid #ddd; padding: 8px;'>Prezzo Unitario</th>
            <th style='border: 1px solid #ddd; padding: 8px;'>IVA</th>
            <th style='border: 1px solid #ddd; padding: 8px;'>Importo Riga</th>
          </tr>";

    while ($dettaglio = $result_dettagli->fetch_assoc()) {
        echo "<tr>
                <td style='border: 1px solid #ddd; padding: 8px;'>" . htmlspecialchars($dettaglio['DESCRIZIONE']) . "</td>
                <td style='border: 1px solid #ddd; padding: 8px;'>" . $dettaglio['QT'] . "</td>
                <td style='border: 1px solid #ddd; padding: 8px;'>" . number_format($dettaglio['IMPORTOUNITARIO'], 2) . " €</td>
                <td style='border: 1px solid #ddd; padding: 8px;'>" . htmlspecialchars($dettaglio['CodiceIVA']) . " - " . htmlspecialchars($dettaglio['DescrizioneIVA']) . "</td>
                <td style='border: 1px solid #ddd; padding: 8px;'>" . number_format($dettaglio['IMPORTORIGA'], 2) . " €</td>
              </tr>";
    }
    echo "</table>";
} else {
    echo "<p>Nessun dettaglio trovato per questa fattura.</p>";
}

$conn->close();
?>
