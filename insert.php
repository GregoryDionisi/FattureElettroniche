<html>
  <head>
    <title>DB & PHP Test: INSERT</title>
  </head>
  <body>
   <?php
     // Recupero i dati inviati dal form tramite GET
     $ndoc = $_GET["ndoc"] ?? null;
     $data = $_GET["data"] ?? null;
     $tipodoc = $_GET["tipodoc"] ?? null;
     $tipopagamento = $_GET["tipopagamento"] ?? null;
     $descrizione = $_GET["descrizione"] ?? null;
     $qt = $_GET["qt"] ?? null;
     $importounitario = $_GET["importounitario"] ?? null;
     $idiva = $_GET["idiva"] ?? null;
     $importoriga = $_GET["importoriga"] ?? null;
     $new_cliente = $_GET["new_cliente"] ?? null;

     // Connessione al database
     $connection = new mysqli("localhost", "root", "", "fattureelettroniche");

     // Controllo la connessione
     if ($connection->connect_error) {
       die("Errore di connessione: " . $connection->connect_error);
     }

     // Gestione cliente (nuovo o esistente)
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
         die("Errore: Tutti i campi obbligatori del nuovo cliente devono essere compilati.");
       }

       // Inserimento del nuovo cliente nella tabella 'tabcliente'
       $stmt_cliente = $connection->prepare("INSERT INTO tabcliente (DENOMINAZIONE, INDIRIZZO, CITTA, CAP, NAZIONE, PROVINCIA, PIVA, CF, SDI, PEC) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");
       $stmt_cliente->bind_param("ssssssssss", $denominazione, $indirizzo, $citta, $cap, $nazione, $provincia, $piva, $cf, $sdi, $pec);

       if ($stmt_cliente->execute()) {
         echo "Nuovo cliente aggiunto con successo!<br>";
         $cliente = $stmt_cliente->insert_id; // ID del nuovo cliente
       } else {
         echo "Errore nell'aggiunta del nuovo cliente: " . $stmt_cliente->error;
         exit();
       }

       $stmt_cliente->close();
     } else {
       // Recupero l'ID del cliente esistente
       $cliente = $_GET["cliente"] ?? null;
       if (empty($cliente)) {
         die("Errore: Nessun cliente selezionato o aggiunto.");
       }
     }

     // Verifica se la fattura esiste già nel database
     $stmt = $connection->prepare("SELECT * FROM fatture WHERE NDOC = ?");
     $stmt->bind_param("s", $ndoc);
     $stmt->execute();
     $result = $stmt->get_result();

     if ($result->num_rows != 0) {
       echo "La fattura $ndoc è già presente nel database!";
     } else {
       // Inserimento dei dati nella tabella 'fatture'
       $stmt = $connection->prepare("INSERT INTO fatture (NDOC, DATA, IDCLIENTE, TIPODOC, TIPOPAGAMENTO) VALUES (?, ?, ?, ?, ?)");
       $stmt->bind_param("ssiss", $ndoc, $data, $cliente, $tipodoc, $tipopagamento);

       if ($stmt->execute()) {
         echo "La fattura $ndoc è stata aggiunta al database!";
         $id_doc = $stmt->insert_id;  // Ottieni l'ID della fattura appena inserita

         // Inserimento dei dettagli della fattura nella tabella dfatture
         if (isset($_GET["new_iva"]) && $_GET["new_iva"] == "1") {
            $cod = $_GET["cod"] ?? null;
            $descrizione_iva = $_GET["descrizione_iva"] ?? null;

           // Inserimento del nuovo tipo di IVA
           if ($cod && $descrizione_iva) {
             $stmt_iva = $connection->prepare("INSERT INTO tiva (COD, DESCRIZIONE) VALUES (?, ?)");
             $stmt_iva->bind_param("ss", $cod, $descrizione_iva);

             if ($stmt_iva->execute()) {
               $idiva = $stmt_iva->insert_id; // ID del nuovo tipo di IVA
               echo "<br>Nuovo tipo di IVA aggiunto con successo!";
             } else {
               echo "<br>Errore nell'aggiunta del nuovo tipo di IVA: " . $stmt_iva->error;
             }
             $stmt_iva->close();
           } else {
             echo "<br>Errore: Campi IVA mancanti.";
           }
         }

         // Inserimento dei dettagli della fattura
         $stmt2 = $connection->prepare("INSERT INTO dfatture (ID_DOC, DESCRIZIONE, QT, IMPORTOUNITARIO, ID_IVA, IMPORTORIGA) VALUES (?, ?, ?, ?, ?, ?)");
         $stmt2->bind_param("iisiid", $id_doc, $descrizione, $qt, $importounitario, $idiva, $importoriga);

         if ($stmt2->execute()) {
           echo "<br>I dettagli della fattura sono stati aggiunti con successo!";
         } else {
           echo "<br>Errore nell'aggiunta dei dettagli della fattura: " . $stmt2->error;
         }
         $stmt2->close();
       } else {
         echo "Errore nell'aggiunta della fattura: " . $stmt->error;
       }
     }

     // Chiudo le dichiarazioni e la connessione
     $stmt->close();
     $connection->close();
   ?><br><br>
   <a href="http://localhost/fattureelettroniche/index.php">Visualizza elenco fatture.</a>
  </body>
</html>
