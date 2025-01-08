<?php
// Connessione al database
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

?>
<!DOCTYPE html>
<html lang="it" data-theme="light">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dettagli Fattura</title>
    <!-- Import Tailwind CSS e DaisyUI -->
    <link href="https://cdn.jsdelivr.net/npm/daisyui@4.12.22/dist/full.min.css" rel="stylesheet" type="text/css" />
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-100 text-gray-800 p-6">

<!-- Bottone "Indietro" -->
<button onclick="window.history.back();" class="btn btn-outline btn-primary flex items-center space-x-2 mb-6">
  <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor" class="w-4 h-4 mr-2">
    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7" />
  </svg>
  <span>Indietro</span>
</button>
<?php
if ($result_dettagli->num_rows > 0) {
    echo "<h1 class='text-2xl font-bold mb-6'>Dettagli Fattura</h1>";
    echo "<div class='overflow-x-auto'>";
    echo "<table class='table w-full border border-gray-300'>";
    echo "<thead>";
    echo "<tr class='bg-gray-200'>";
    echo "<th class='p-3'>Descrizione</th>";
    echo "<th class='p-3'>Quantità</th>";
    echo "<th class='p-3'>Prezzo Unitario</th>";
    echo "<th class='p-3'>IVA</th>";
    echo "<th class='p-3'>Importo Riga</th>";
    echo "</tr>";
    echo "</thead>";
    echo "<tbody>";

    while ($dettaglio = $result_dettagli->fetch_assoc()) {
        echo "<tr>";
        echo "<td class='p-3'>" . htmlspecialchars($dettaglio['DESCRIZIONE']) . "</td>";
        echo "<td class='p-3'>" . $dettaglio['QT'] . "</td>";
        echo "<td class='p-3'>" . number_format($dettaglio['IMPORTOUNITARIO'], 2) . " €</td>";
        echo "<td class='p-3'>" . htmlspecialchars($dettaglio['CodiceIVA']) . " - " . htmlspecialchars($dettaglio['DescrizioneIVA']) . "</td>";
        echo "<td class='p-3'>" . number_format($dettaglio['IMPORTORIGA'], 2) . " €</td>";
        echo "</tr>";
    }
    
    echo "</tbody>";
    echo "</table>";
    echo "</div>";
} else {
    echo "<p class='text-center text-lg font-semibold text-red-500'>Nessun dettaglio trovato per questa fattura.</p>";
}

$conn->close();
?>

</body>
</html>
