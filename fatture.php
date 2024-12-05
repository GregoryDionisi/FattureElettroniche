<?php
// Connessione al database
$conn = new mysqli("localhost", "root", "", "FattureElettroniche");

// Verifica connessione
if ($conn->connect_error) {
    die("Errore di connessione: " . $conn->connect_error);
}

// Recupera l'ID della fattura dalla query string o imposta un valore predefinito
$id_fattura = isset($_GET['id']) ? (int)$_GET['id'] : 1;

// Query per ottenere i dati della fattura principale
$sql_fattura = "
    SELECT f.ID_DOC, f.NDOC, f.DATA, f.TIPODOC, f.TIPOPAGAMENTO, f.IDCLIENTE,
           c.DENOMINAZIONE AS Cliente, c.INDIRIZZO, c.CITTA, c.PIVA
    FROM fatture f
    JOIN tabcliente c ON f.IDCLIENTE = c.IDCLIENTE
    WHERE f.ID_DOC = $id_fattura
";

$result_fattura = $conn->query($sql_fattura);

if ($result_fattura->num_rows > 0) {
    $fattura = $result_fattura->fetch_assoc();

    // Mostra i dati della fattura
    echo "<h1>Fattura: " . $fattura['NDOC'] . "</h1>";
    echo "<p><strong>Data:</strong> " . $fattura['DATA'] . "</p>";
    echo "<p><strong>Cliente:</strong> " . $fattura['Cliente'] . "</p>";
    echo "<p><strong>Indirizzo:</strong> " . $fattura['INDIRIZZO'] . ", " . $fattura['CITTA'] . "</p>";
    echo "<p><strong>Partita IVA:</strong> " . $fattura['PIVA'] . "</p>";
    echo "<p><strong>Tipo Documento:</strong> " . $fattura['TIPODOC'] . "</p>";
    echo "<p><strong>Tipo Pagamento:</strong> " . $fattura['TIPOPAGAMENTO'] . "</p>";
} else {
    echo "<p>Fattura non trovata.</p>";
}

// Recupera l'ID massimo e minimo delle fatture per i limiti
$sql_min_max = "SELECT MIN(ID_DOC) AS MinID, MAX(ID_DOC) AS MaxID FROM fatture";
$result_min_max = $conn->query($sql_min_max);
$row_min_max = $result_min_max->fetch_assoc();

$min_id = $row_min_max['MinID'];
$max_id = $row_min_max['MaxID'];

// Calcola gli ID per i pulsanti
$prev_id = ($id_fattura > $min_id) ? $id_fattura - 1 : $min_id;
$next_id = ($id_fattura < $max_id) ? $id_fattura + 1 : $max_id;

// Mostra i pulsanti di navigazione
echo "<div style='margin-top: 20px;'>";
echo "<a href='?id=$prev_id' style='padding: 10px; background: #007BFF; color: white; text-decoration: none; margin-right: 10px;'>Precedente</a>";
echo "<a href='?id=$next_id' style='padding: 10px; background: #28A745; color: white; text-decoration: none; margin-right: 10px;'>Successivo</a>";

// Pulsanti per navigare alle tabelle esterne
echo "<a href='dfatture.php?id=$id_fattura' style='padding: 10px; background: #FFC107; color: black; text-decoration: none; margin-right: 10px;'>Visualizza Dettagli Fattura</a>";
echo "<a href='tabcliente.php?id=" . $fattura['IDCLIENTE'] . "' style='padding: 10px; background: #6C757D; color: white; text-decoration: none; margin-right: 10px;'>Visualizza Cliente</a>";
echo "<a href='tiva.php?id=$id_fattura' style='padding: 10px; background: #17A2B8; color: white; text-decoration: none; margin-right: 10px;'>Visualizza IVA</a>";
echo "<a href='index.php' style='padding: 10px; background: #f44336; color: white; text-decoration: none;'>Uscita</a>";
echo "</div>";

$conn->close();
?>
