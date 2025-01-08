<?php
$conn = new mysqli("localhost", "root", "", "FattureElettroniche");

if ($conn->connect_error) {
    die("Errore di connessione: " . $conn->connect_error);
}

$id_cliente = isset($_GET['id']) ? (int)$_GET['id'] : 0;

$sql_cliente = "
    SELECT IDCLIENTE, DENOMINAZIONE, INDIRIZZO, CITTA, CAP, NAZIONE, PROVINCIA, PIVA, CF, SDI, PEC
    FROM tabcliente
    WHERE IDCLIENTE = $id_cliente
";

$result_cliente = $conn->query($sql_cliente);

?>
<!DOCTYPE html>
<html lang="it" data-theme="light">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dettagli Cliente</title>
    
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
if ($result_cliente->num_rows > 0) {
    $cliente = $result_cliente->fetch_assoc();
    echo "<h1 class='text-2xl font-bold mb-6'>Dettagli Cliente</h1>";
    echo "<div class='overflow-x-auto'>";
    echo "<table class='table w-full border border-gray-300'>";
    echo "<thead>";
    echo "<tr class='bg-gray-200'>";
    echo "<th class='p-3'>Campo</th>";
    echo "<th class='p-3'>Valore</th>";
    echo "</tr>";
    echo "</thead>";
    echo "<tbody>";
    echo "<tr><td class='p-3'>ID Cliente</td><td class='p-3'>" . $cliente['IDCLIENTE'] . "</td></tr>";
    echo "<tr><td class='p-3'>Denominazione</td><td class='p-3'>" . $cliente['DENOMINAZIONE'] . "</td></tr>";
    echo "<tr><td class='p-3'>Indirizzo</td><td class='p-3'>" . $cliente['INDIRIZZO'] . "</td></tr>";
    echo "<tr><td class='p-3'>Città</td><td class='p-3'>" . $cliente['CITTA'] . "</td></tr>";
    echo "<tr><td class='p-3'>CAP</td><td class='p-3'>" . $cliente['CAP'] . "</td></tr>";
    echo "<tr><td class='p-3'>Nazione</td><td class='p-3'>" . $cliente['NAZIONE'] . "</td></tr>";
    echo "<tr><td class='p-3'>Provincia</td><td class='p-3'>" . $cliente['PROVINCIA'] . "</td></tr>";
    echo "<tr><td class='p-3'>Partita IVA</td><td class='p-3'>" . $cliente['PIVA'] . "</td></tr>";
    echo "<tr><td class='p-3'>Codice fiscale</td><td class='p-3'>" . $cliente['CF'] . "</td></tr>";
    echo "<tr><td class='p-3'>SDI</td><td class='p-3'>" . $cliente['SDI'] . "</td></tr>";
    echo "<tr><td class='p-3'>PEC</td><td class='p-3'>" . $cliente['PEC'] . "</td></tr>";
    echo "</tbody>";
    echo "</table>";
    echo "</div>";
} else {
    echo "<p>Cliente non trovato.</p>";
}

$conn->close();
?>
</body>
</html>
