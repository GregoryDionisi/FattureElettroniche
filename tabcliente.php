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

if ($result_cliente->num_rows > 0) {
    $cliente = $result_cliente->fetch_assoc();
    echo "<h1>Dettagli Cliente</h1>";
    echo "<table style='border-collapse: collapse; width: 100%;'>
            <tr><th style='border: 1px solid #ddd; padding: 8px;'>Campo</th><th style='border: 1px solid #ddd; padding: 8px;'>Valore</th></tr>";
    echo "<tr><td style='border: 1px solid #ddd; padding: 8px;'>ID Cliente</td><td style='border: 1px solid #ddd; padding: 8px;'>" . $cliente['IDCLIENTE'] . "</td></tr>";
    echo "<tr><td style='border: 1px solid #ddd; padding: 8px;'>Denominazione</td><td style='border: 1px solid #ddd; padding: 8px;'>" . $cliente['DENOMINAZIONE'] . "</td></tr>";
    echo "<tr><td style='border: 1px solid #ddd; padding: 8px;'>Indirizzo</td><td style='border: 1px solid #ddd; padding: 8px;'>" . $cliente['INDIRIZZO'] . "</td></tr>";
    echo "<tr><td style='border: 1px solid #ddd; padding: 8px;'>Città</td><td style='border: 1px solid #ddd; padding: 8px;'>" . $cliente['CITTA'] . "</td></tr>";
    echo "<tr><td style='border: 1px solid #ddd; padding: 8px;'>CAP</td><td style='border: 1px solid #ddd; padding: 8px;'>" . $cliente['CAP'] . "</td></tr>";
    echo "<tr><td style='border: 1px solid #ddd; padding: 8px;'>Nazione</td><td style='border: 1px solid #ddd; padding: 8px;'>" . $cliente['NAZIONE'] . "</td></tr>";
    echo "<tr><td style='border: 1px solid #ddd; padding: 8px;'>Provincia</td><td style='border: 1px solid #ddd; padding: 8px;'>" . $cliente['PROVINCIA'] . "</td></tr>";
    echo "<tr><td style='border: 1px solid #ddd; padding: 8px;'>Partita IVA</td><td style='border: 1px solid #ddd; padding: 8px;'>" . $cliente['PIVA'] . "</td></tr>";
    echo "<tr><td style='border: 1px solid #ddd; padding: 8px;'>Codice fiscale</td><td style='border: 1px solid #ddd; padding: 8px;'>" . $cliente['CF'] . "</td></tr>";
    echo "<tr><td style='border: 1px solid #ddd; padding: 8px;'>SDI</td><td style='border: 1px solid #ddd; padding: 8px;'>" . $cliente['SDI'] . "</td></tr>";
    echo "<tr><td style='border: 1px solid #ddd; padding: 8px;'>PEC</td><td style='border: 1px solid #ddd; padding: 8px;'>" . $cliente['PEC'] . "</td></tr>";
    echo "</table>";
} else {
    echo "<p>Cliente non trovato.</p>";
}

$conn->close();
?>
