<html>
 <head>
  <title>DB & PHP Test: DELETE</title>
 </head>
 <body>
  <?php
    $ndoc = $_GET["ndoc"];


    $connection = new mysqli("localhost", "root", "", "FattureElettroniche");

    if ($connection->connect_error) {
      die("Errore di connessione: " . $connection->connect_error);
    }

    //inizio della transazione per garantire che entrambe le eliminazioni avvengano insieme in modo da garantire coerenza dei dati
    $connection->begin_transaction();

    try {
      //eliminazione dalla tabella dfatture, basata su ID_DOC che corrisponde a NDOC
      $stmt2 = $connection->prepare("DELETE FROM dfatture WHERE ID_DOC = (SELECT ID_DOC FROM fatture WHERE NDOC = ?)");
      $stmt2->bind_param("s", $ndoc);

      if ($stmt2->execute()) {
        echo "I dettagli della fattura sono stati eliminati correttamente.<br>";
      } else {
        throw new Exception("Errore nell'eliminazione dei dettagli della fattura: " . $stmt2->error);
      }

      //eliminazione dalla tabella fatture
      $stmt = $connection->prepare("DELETE FROM fatture WHERE NDOC = ?");
      $stmt->bind_param("s", $ndoc);

      if ($stmt->execute()) {
        echo "La fattura $ndoc &egrave; stata eliminata dal database.";
      } else {
        throw new Exception("Errore nell'eliminazione della fattura: " . $stmt->error);
      }

      //se entrambe le eliminazioni hanno successo, la transazione viene confermata, rendendo permanenti le modifiche nel database
      $connection->commit(); //in particolare il commit è uno statement che chiude in maniera definitiva una Transazione-SQL o una semplice operazione di UPDATE all'interno di un db
      
    } catch (Exception $e) {
      //in caso di errore viene annullata la transazione
      $connection->rollback();
      echo "Errore durante l'eliminazione: " . $e->getMessage();
    }

    //chiusura delle dichiarazioni e connessione
    $stmt->close();
    $stmt2->close();
    $connection->close();
  ?><br><br>
  <a href="http://localhost/fattureelettroniche/index.php">
   Visualizza elenco fatture.
  </a>
 </body>
</html>
