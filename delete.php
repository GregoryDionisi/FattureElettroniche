<html>
 <head>
  <title>DB & PHP test: DELETE</title>
 </head>
 <body>
  <?php
	$libro = $_GET["libro"];

	$connection = new mysqli("localhost", "root", "", "biblioteca");
	if ($connection->connect_error) {
		die("Errore di connessione: " . $connection->connect_error);
	}

	$stmt = $connection->prepare("DELETE FROM libri WHERE TITOLO = ?");
	$stmt->bind_param("s", $libro);

	if ($stmt->execute()) {
		echo "Il libro $libro &egrave; stato eliminato dal database.";
	} else {
		echo "Errore nell'eliminazione del prodotto: " . $stmt->error;
	}

	$stmt->close();
	$connection->close();
  ?><br><br>
  <a href="http://localhost/biblioteca/index.php">
   Visualizza elenco libri.
  </a>
 </body>
</html>
