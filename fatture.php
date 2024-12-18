<!DOCTYPE html>
<html lang="it">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Fatture</title>
    <link href="https://cdn.jsdelivr.net/npm/daisyui@4.12.22/dist/full.min.css" rel="stylesheet" type="text/css" />
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-100">

<?php
$conn = new mysqli("localhost", "root", "", "FattureElettroniche");
if ($conn->connect_error) {
    die("Errore di connessione: " . $conn->connect_error);
}

$id_fattura = isset($_GET['id']) ? (int)$_GET['id'] : 1;
$sql_fattura = "
    SELECT f.ID_DOC, f.NDOC, f.DATA, f.TIPODOC, f.TIPOPAGAMENTO, f.IDCLIENTE,
           c.DENOMINAZIONE AS Cliente, c.INDIRIZZO, c.CITTA, c.PIVA
    FROM fatture f
    JOIN tabcliente c ON f.IDCLIENTE = c.IDCLIENTE
    WHERE f.ID_DOC = $id_fattura
";

$result_fattura = $conn->query($sql_fattura);

echo '<div class="container mx-auto mt-10 p-4">';
if ($result_fattura->num_rows > 0) {
    $fattura = $result_fattura->fetch_assoc();

    echo '<div class="card shadow-lg bg-base-100 p-6">';
    echo '<h1 class="text-2xl font-bold mb-4">Fattura: ' . $fattura['NDOC'] . '</h1>';
    echo '<p><strong>Data:</strong> ' . $fattura['DATA'] . '</p>';
    echo '<p><strong>Cliente:</strong> ' . $fattura['Cliente'] . '</p>';
    echo '<p><strong>Indirizzo:</strong> ' . $fattura['INDIRIZZO'] . ', ' . $fattura['CITTA'] . '</p>';
    echo '<p><strong>Partita IVA:</strong> ' . $fattura['PIVA'] . '</p>';
    echo '<p><strong>Tipo Documento:</strong> ' . $fattura['TIPODOC'] . '</p>';
    echo '<p><strong>Tipo Pagamento:</strong> ' . $fattura['TIPOPAGAMENTO'] . '</p>';
    echo '</div>';
} else {
    echo '<div class="alert alert-error mt-4">Fattura non trovata.</div>';
}

$sql_min_max = "SELECT MIN(ID_DOC) AS MinID, MAX(ID_DOC) AS MaxID FROM fatture";
$result_min_max = $conn->query($sql_min_max);
$row_min_max = $result_min_max->fetch_assoc();

$min_id = $row_min_max['MinID'];
$max_id = $row_min_max['MaxID'];

$prev_id = ($id_fattura > $min_id) ? $id_fattura - 1 : $min_id;
$next_id = ($id_fattura < $max_id) ? $id_fattura + 1 : $max_id;

echo '<div class="flex gap-4 mt-6">';
echo '<a href="?id=' . $prev_id . '" class="btn btn-primary">Precedente</a>';
echo '<a href="?id=' . $next_id . '" class="btn btn-success">Successivo</a>';
echo '<a href="dfatture.php?id=' . $id_fattura . '" class="btn btn-warning">Dettagli Fattura</a>';
echo '<a href="tabcliente.php?id=' . $fattura['IDCLIENTE'] . '" class="btn btn-secondary">Cliente</a>';
echo '<a href="tiva.php?id=' . $id_fattura . '" class="btn btn-info">Visualizza IVA</a>';
echo '<a href="index.php" class="btn btn-error">Uscita</a>';
echo '</div>';

echo '</div>';
$conn->close();
?>
</body>
</html>
