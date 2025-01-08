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

?>

<!DOCTYPE html>
<html lang="it" data-theme="light">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Fatture Elettroniche: Dettagli IVA</title>
    <!-- Import Tailwind CSS e DaisyUI -->
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://cdn.jsdelivr.net/npm/daisyui@4.12.22/dist/full.min.css" rel="stylesheet" type="text/css" />
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
if ($result_iva->num_rows > 0) {
    echo "<h1 class='text-2xl font-bold mb-6'>Dettagli IVA</h1>";
    echo "<table class='table w-full border border-gray-300'>";
    echo "<thead>";
    echo "<tr class='bg-gray-200'>";
    echo "<th class='p-3'>Codice IVA</th>";
    echo "<th class='p-3'>Descrizione</th>";
    echo "</tr>";
    echo "</thead>";
    echo "<tbody>";
    while ($iva = $result_iva->fetch_assoc()) {
        echo "<tr>";
        echo "<td class='p-3'>" . $iva['COD'] . "</td>";
        echo "<td class='p-3'>" . $iva['DESCRIZIONE'] . "</td>";
        echo "</tr>";
    }
    echo "</tbody>";
    echo "</table>";
} else {
    echo "<p class='text-center text-lg font-semibold text-red-500'>Nessuna informazione IVA trovata.</p>";
}

$conn->close();
?>

</body>
</html>
