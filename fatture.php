<!DOCTYPE html>
<html lang="it" data-theme="light">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Fatture Elettroniche</title>
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

// Query per ottenere la fattura precedente
$sql_prev = "
    SELECT MAX(ID_DOC) as prev_id 
    FROM fatture 
    WHERE ID_DOC < $id_fattura";

// Query per ottenere la fattura successiva
$sql_next = "
    SELECT MIN(ID_DOC) as next_id 
    FROM fatture 
    WHERE ID_DOC > $id_fattura";

// Ottieni l'ID della fattura precedente
$result_prev = $conn->query($sql_prev);
$prev_id = $result_prev->fetch_assoc()['prev_id'];

// Ottieni l'ID della fattura successiva
$result_next = $conn->query($sql_next);
$next_id = $result_next->fetch_assoc()['next_id'];

// Query per ottenere i dettagli della fattura corrente
$sql_fattura = "
    SELECT f.ID_DOC, f.NDOC, f.DATA, f.TIPODOC, f.TIPOPAGAMENTO, f.IDCLIENTE,
           c.DENOMINAZIONE AS Cliente, c.INDIRIZZO AS IndirizzoCliente, c.CITTA AS CittaCliente, c.PIVA AS PivaCliente,
           f.DENOMINAZIONE AS Fornitore, f.INDIRIZZO AS IndirizzoFornitore, f.CITTA AS CittaFornitore, f.PIVA AS PivaFornitore
    FROM fatture f
    LEFT JOIN tabcliente c ON f.IDCLIENTE = c.IDCLIENTE
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
    echo '<p><strong>Indirizzo Cliente:</strong> ' . $fattura['IndirizzoCliente'] . ', ' . $fattura['CittaCliente'] . '</p>';
    echo '<p><strong>Partita IVA Cliente:</strong> ' . $fattura['PivaCliente'] . '</p>';
    echo '<p><strong>Fornitore:</strong> ' . $fattura['Fornitore'] . '</p>';
    echo '<p><strong>Indirizzo Fornitore:</strong> ' . $fattura['IndirizzoFornitore'] . ', ' . $fattura['CittaFornitore'] . '</p>';
    echo '<p><strong>Partita IVA Fornitore:</strong> ' . $fattura['PivaFornitore'] . '</p>';
    echo '<p><strong>Tipo Documento:</strong> ' . $fattura['TIPODOC'] . '</p>';
    echo '<p><strong>Tipo Pagamento:</strong> ' . $fattura['TIPOPAGAMENTO'] . '</p>';
    echo '</div>';

    echo '<div class="flex gap-4 mt-6">';

    // Bottone Precedente
    if ($prev_id !== null) {
        echo '<a href="?id=' . $prev_id . '" class="btn btn-primary">';
    } else {
        echo '<a class="btn btn-primary btn-disabled">';
    }
    echo '<svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor" class="w-5 h-5">';
    echo '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7" />';
    echo '</svg><span>Precedente</span></a>';

    // Bottone Successivo
    if ($next_id !== null) {
        echo '<a href="?id=' . $next_id . '" class="btn btn-success">';
    } else {
        echo '<a class="btn btn-success btn-disabled">';
    }
    echo '<span>Successivo</span><svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor" class="w-5 h-5">';
    echo '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />';
    echo '</svg></a>';

    echo '<a href="dfatture.php?id=' . $id_fattura . '" class="btn btn-warning">Dettagli Fattura</a>';
    echo '<a href="tabcliente.php?id=' . $fattura['IDCLIENTE'] . '" class="btn btn-secondary">Cliente</a>';
    echo '<a href="tiva.php?id=' . $id_fattura . '" class="btn btn-info">Visualizza IVA</a>';
    echo '<a href="index.php" class="btn btn-outline btn-primary flex items-center space-x-2">';
    echo '<svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor" class="w-5 h-5">';
    echo '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7" />';
    echo '</svg><span>Indietro</span></a>';
    echo '</div>';
} else {
    echo '<div class="alert alert-error mt-4">Fattura non trovata.</div>';
    echo '<div class="mt-4">';
    echo '<a href="index.php" class="btn btn-primary">Torna all\'elenco fatture</a>';
    echo '</div>';
}

echo '</div>';
$conn->close();
?>
</body>
</html>
