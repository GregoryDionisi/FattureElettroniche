<?php
// Recupero i dati inviati dal form tramite GET
$ndoc = $_GET["ndoc"] ?? null;
$data = $_GET["data"] ?? null;
$tipodoc = $_GET["tipodoc"] ?? null;
$tipopagamento = $_GET["tipopagamento"] ?? null;
$new_cliente = $_GET["new_cliente"] ?? null;

// Array per i dettagli multipli della fattura
$descrizioni = $_GET["descrizione"] ?? [];
$quantita = $_GET["qt"] ?? [];
$importi_unitari = $_GET["importounitario"] ?? [];
$id_iva = $_GET["idiva"] ?? [];
$importi_riga = $_GET["importoriga"] ?? [];

// Connessione al database
$connection = new mysqli("localhost", "root", "", "fattureelettroniche");

if ($connection->connect_error) {
    die("Errore di connessione: " . $connection->connect_error);
}

// Inizia la transazione
$connection->begin_transaction();

try {
    // Array associativo per i dati IVA
    $iva_data = [
        1 => ['cod' => 22, 'descrizione' => 'IVA al 22%'],
        2 => ['cod' => 10, 'descrizione' => 'IVA al 10%'],
        3 => ['cod' => 0, 'descrizione' => 'Esente IVA']
    ];

    // Verifica e inserimento dei dati IVA
    foreach ($id_iva as $iva_id) {
        if (isset($iva_data[$iva_id])) {
            // Verifica se l'ID_IVA esiste già
            $stmt_check = $connection->prepare("SELECT ID_IVA FROM tiva WHERE ID_IVA = ?");
            $stmt_check->bind_param("i", $iva_id);
            $stmt_check->execute();
            $result = $stmt_check->get_result();
            
            // Se non esiste, inserisci i nuovi dati IVA
            if ($result->num_rows == 0) {
                $stmt_iva = $connection->prepare("INSERT INTO tiva (ID_IVA, COD, DESCRIZIONE) VALUES (?, ?, ?)");
                $stmt_iva->bind_param("iis", 
                    $iva_id,
                    $iva_data[$iva_id]['cod'],
                    $iva_data[$iva_id]['descrizione']
                );
                
                if (!$stmt_iva->execute()) {
                    throw new Exception("Errore nell'inserimento dei dati IVA: " . $stmt_iva->error);
                }
                $stmt_iva->close();
            }
            $stmt_check->close();
        }
    }

    // Gestione cliente
    if ($new_cliente == "1") {
        // Recupero i dati del nuovo cliente
        $denominazione = $_GET["denominazione"] ?? null;
        $indirizzo = $_GET["indirizzo"] ?? null;
        $citta = $_GET["citta"] ?? null;
        $cap = $_GET["cap"] ?? null;
        $nazione = $_GET["nazione"] ?? null;
        $provincia = $_GET["provincia"] ?? null;
        $piva = $_GET["piva"] ?? null;
        $cf = $_GET["cf"] ?? null;
        $sdi = $_GET["sdi"] ?? null;
        $pec = $_GET["pec"] ?? null;

        // Controllo dei campi obbligatori
        if (!$denominazione || !$indirizzo || !$citta || !$piva) {
            throw new Exception("Errore: Tutti i campi obbligatori del nuovo cliente devono essere compilati.");
        }

        // Inserimento del nuovo cliente
        $stmt_cliente = $connection->prepare("INSERT INTO tabcliente (DENOMINAZIONE, INDIRIZZO, CITTA, CAP, NAZIONE, PROVINCIA, PIVA, CF, SDI, PEC) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");
        $stmt_cliente->bind_param("ssssssssss", $denominazione, $indirizzo, $citta, $cap, $nazione, $provincia, $piva, $cf, $sdi, $pec);
        
        if (!$stmt_cliente->execute()) {
            throw new Exception("Errore nell'aggiunta del nuovo cliente: " . $stmt_cliente->error);
        }
        
        $cliente = $stmt_cliente->insert_id;
        $stmt_cliente->close();
        echo "Nuovo cliente aggiunto con successo!<br>";
    } else {
        $cliente = $_GET["cliente"] ?? null;
        if (empty($cliente)) {
            throw new Exception("Errore: Nessun cliente selezionato o aggiunto.");
        }
    }

    // Verifica se la fattura esiste già
    $stmt = $connection->prepare("SELECT * FROM fatture WHERE NDOC = ?");
    $stmt->bind_param("s", $ndoc);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows != 0) {
        throw new Exception("La fattura $ndoc è già presente nel database!");
    }

    // Inserimento della fattura con i dati predefiniti
    $id_fornitore = 1;
    $denominazione_default = "Azienda X";
    $indirizzo_default = "Piazza Europa 19";
    $citta_default = "Mairano";
    $cap_default = "25030";
    $nazione_default = "Italia";
    $provincia_default = "BS";
    $piva_default = "38475638564";
    $cf_default = "QMKCLS28C52XRDIU";
    $sdi_default = "3857463";
    $pec_default = "aziendax@pec.it";
    $banca_default = "BTL Banca del Territorio Lombardo";
    $iban_default = "IT60X0542811101000001234567";

    $stmt = $connection->prepare(
        "INSERT INTO fatture (NDOC, DATA, IDCLIENTE, TIPODOC, TIPOPAGAMENTO, IDFORNITORE, DENOMINAZIONE, INDIRIZZO, CITTA, CAP, NAZIONE, PROVINCIA, PIVA, CF, SDI, PEC, BANCA, IBAN) 
        VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)"
    );
    $stmt->bind_param(
        "ssississssssssssss", 
        $ndoc, $data, $cliente, $tipodoc, $tipopagamento, $id_fornitore, $denominazione_default, 
        $indirizzo_default, $citta_default, $cap_default, $nazione_default, $provincia_default, 
        $piva_default, $cf_default, $sdi_default, $pec_default, $banca_default, $iban_default
    );

    if (!$stmt->execute()) {
        throw new Exception("Errore nell'aggiunta della fattura: " . $stmt->error);
    }

    $id_doc = $stmt->insert_id;
    $stmt->close();
    echo "La fattura $ndoc è stata aggiunta al database con i dati predefiniti!<br>";

    // Inserimento dei dettagli della fattura
    $stmt2 = $connection->prepare("INSERT INTO dfatture (ID_DOC, DESCRIZIONE, QT, IMPORTOUNITARIO, ID_IVA, IMPORTORIGA) VALUES (?, ?, ?, ?, ?, ?)");

    // Itera su tutte le righe dei dettagli
    for ($i = 0; $i < count($descrizioni); $i++) {
        $descrizione = $descrizioni[$i];
        $qt = $quantita[$i];
        $importounitario = $importi_unitari[$i];
        $idiva = $id_iva[$i];
        $importoriga = $importi_riga[$i];

        // Verifica che i campi necessari siano presenti
        if (empty($descrizione) || empty($qt) || empty($importounitario) || empty($idiva)) {
            throw new Exception("Errore: Tutti i campi dei dettagli della fattura devono essere compilati.");
        }

        $stmt2->bind_param("isiids", $id_doc, $descrizione, $qt, $importounitario, $idiva, $importoriga);
        
        if (!$stmt2->execute()) {
            throw new Exception("Errore nell'aggiunta dei dettagli della fattura: " . $stmt2->error);
        }
    }

    $stmt2->close();
    echo "I dettagli della fattura sono stati aggiunti con successo!<br>";

    // Se tutto è andato bene, commit della transazione
    $connection->commit();
    
} catch (Exception $e) {
    // In caso di errore, rollback della transazione
    $connection->rollback();
    echo "Errore: " . $e->getMessage();
} finally {
    $connection->close();
}
?>
<br><br>
<a href="http://localhost/fattureelettroniche/index.php">Visualizza elenco fatture.</a>