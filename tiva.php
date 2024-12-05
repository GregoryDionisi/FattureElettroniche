<?php
$conn = new mysqli("localhost", "root", "", "FattureElettroniche");

if ($conn->connect_error) {
    die("Errore di connessione: " . $conn->connect_error);
}

$id_fattura = isset($_GET['id']) ? (int)$_GET['id'] : 0;

$sql_iva = "
    SELECT t.COD, t.DESCRIZIONE
    FROM dfatture d
    JOIN tiva t ON d.ID_IVA = t.ID_IVA
    WHERE d.ID_DOC = $id_fattura
";

$result_iva = $conn->query($sql_iva);

if ($result_iva->num_rows > 0) {
    echo "<h1>Dettagli IVA</h1>";
    echo "<table style='border-collapse: collapse; width: 100%;'>
            <tr>
                <th style='border: 1px solid #ddd; padding: 8px;'>Codice IVA</th>
                <th style='border: 1px solid #ddd; padding: 8px;'>Descrizione</th>
            </tr>";
    while ($iva = $result_iva->fetch_assoc()) {
        echo "<tr>
                <td style='border: 1px solid #ddd; padding: 8px;'>" . $iva['COD'] . "</td>
                <td style='border: 1px solid #ddd; padding: 8px;'>" . $iva['DESCRIZIONE'] . "</td>
              </tr>";
    }
    echo "</table>";
} else {
    echo "<p>Nessuna informazione IVA trovata.</p>";
}

$conn->close();
?>
