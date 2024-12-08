<html>
  <head>
   <title>DB & PHP Test: DELETE</title>
  </head>
  <body>
    <?php
 
      $connection = new mysqli("localhost", "root", "", "fattureelettroniche");

      if ($connection->connect_error) {
        die("Errore di connessione: " . $connection->connect_error);
      }

      //query per selezionare le fatture
      $query = "SELECT NDOC FROM fatture ORDER BY NDOC";
      $result = $connection->query($query);

      if ($result->num_rows != 0) {
    ?>
        <form action="delete.php" method="GET" ><br>
        Fattura da eliminare<br>
        <select name="ndoc">
    <?php
        while ($row = $result->fetch_array(MYSQLI_ASSOC)) {
          echo "<option value=\"{$row['NDOC']}\">{$row['NDOC']}</option>";
        }
    ?>
        </select><br><br>
        <input type="submit" value="Elimina">
        </form>
    <?php
      } else {
        echo "Nessuna fattura &egrave; presente nel database.";
      }

      $connection->close();
    ?>
  </body>
</html>
