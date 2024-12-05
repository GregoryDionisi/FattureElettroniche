<html>
  <head>
    <title>DB & PHP Test: INSERT</title>
  </head>
  <body>
   <?php
     // Recupero i dati inviati dal form tramite GET
     $ndoc = $_GET["ndoc"];
     $data = $_GET["data"];
     $tipodoc = $_GET["tipodoc"];
     $tipopagamento = $_GET["tipopagamento"];
     $descrizione = $_GET["descrizione"];
     $qt = $_GET["qt"];
     $importounitario = $_GET["importounitario"];
     $idiva = $_GET["idiva"];
     $importoriga = $_GET["importoriga"];
     $new_cliente = isset($_GET["new_cliente"]) ? $_GET["new_cliente"] : null;

     // Connessione al database
     $connection = new mysqli("localhost", "root", "", "FattureElettroniche");

     // Controllo la connessione
     if ($connection->connect_error) {
       die("Errore di connessione: " . $connection->connect_error);
     }

     // Gestione cliente (nuovo o esistente)
     if ($new_cliente) {
       // Recupero i dati del nuovo cliente
       $denominazione = $_GET["denominazione"];
       $indirizzo = $_GET["indirizzo"];
       $citta = $_GET["citta"];
       $cap = $_GET["cap"];
       $nazione = $_GET["nazione"];
       $provincia = $_GET["provincia"];
       $piva = $_GET["piva"];
       $cf = $_GET["cf"];
       $sdi = $_GET["sdi"];
       $pec = $_GET["pec"];

       // Inserimento del nuovo cliente nella tabella 'tabcliente'
       $stmt_cliente = $connection->prepare("INSERT INTO tabcliente (DENOMINAZIONE, INDIRIZZO, CITTA, CAP, NAZIONE, PROVINCIA, PIVA, CF, SDI, PEC) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");
       $stmt_cliente->bind_param("ssssssssss", $denominazione, $indirizzo, $citta, $cap, $nazione, $provincia, $piva, $cf, $sdi, $pec);

       if ($stmt_cliente->execute()) {
         echo "Nuovo cliente aggiunto con successo!<br>";
         $cliente = $stmt_cliente->insert_id; // ID del nuovo cliente
       } else {
         die("Errore nell'aggiunta del nuovo cliente: " . $stmt_cliente->error);
       }

       $stmt_cliente->close();
     } else {
       // Recupero l'ID del cliente esistente
       $cliente = $_GET["cliente"];
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
         
         // Inserimento dei dettagli della fattura nella tabella 'dfatture'
         // Se è stato selezionato un nuovo tipo di IVA, bisogna aggiungerlo prima
         if (isset($_GET["new_iva"]) && $_GET["new_iva"] == "1") {
           $descrizione_iva = $_GET["descrizione_iva"];
           $percentuale = $_GET["percentuale"];

           // Inserimento del nuovo tipo di IVA
           $stmt_iva = $connection->prepare("INSERT INTO tiva (DESCRIZIONE, PERCENTUALE) VALUES (?, ?)");
           $stmt_iva->bind_param("sd", $descrizione_iva, $percentuale);

           if ($stmt_iva->execute()) {
             $idiva = $stmt_iva->insert_id; // ID del nuovo tipo di IVA
             echo "<br>Nuovo tipo di IVA aggiunto con successo!";
           } else {
             echo "<br>Errore nell'aggiunta del nuovo tipo di IVA: " . $stmt_iva->error;
           }

           $stmt_iva->close();
         }

         // Inserimento dei dettagli della fattura con l'ID IVA
         $stmt2 = $connection->prepare("INSERT INTO dfatture (ID_DOC, DESCRIZIONE, QT, IMPORTOUNITARIO, ID_IVA, IMPORTORIGA) VALUES (LAST_INSERT_ID(), ?, ?, ?, ?, ?)");
         $stmt2->bind_param("siidi", $descrizione, $qt, $importounitario, $idiva, $importoriga);
         
         if ($stmt2->execute()) {
           echo "<br>I dettagli della fattura sono stati aggiunti con successo!";
         } else {
           echo "<br>Errore nell'aggiunta dei dettagli della fattura: " . $stmt2->error;
         }
       } else {
         echo "Errore nell'aggiunta della fattura: " . $stmt->error;
       }
     }

     // Chiudo le dichiarazioni e la connessione
     $stmt->close();
     $stmt2->close();
     $connection->close();
   ?><br><br>
   <a href="http://localhost/fattureelettroniche/index.php">
    Visualizza elenco fatture.
   </a>
  </body>
</html>
