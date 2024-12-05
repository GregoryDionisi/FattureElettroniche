<html>
<head>
    <title>DB & PHP Test</title>
    <style>
        table {
            border-collapse: collapse;
            width: 100%;
        }
        th, td {
            border: 1px solid #ddd;
            padding: 8px;
            text-align: left;
        }
        tr:hover {
            background-color: #f1f1f1;
            cursor: pointer;
        }
        a {
            display: block;
            color: inherit;
            text-decoration: none;
        }
        .button {
            padding: 10px;
            color: white;
            text-decoration: none;
            margin-right: 10px;
            display: inline-block;
        }
        .button.add {
            background: #28A745;
        }
        .button.delete {
            background: #f44336;
        }
    </style>
</head>
<body>
    <?php
    $connection = @ new mysqli("localhost", "root", "", "fattureelettroniche");
    if ($connection->connect_error) {
        die("Errore di connessione con il DBMS.");
    }

    $query = "SELECT * FROM fatture";
    $result = @ $connection->query($query);
    if ($connection->errno) {
        @ $connection->close();
        die("Errore nell'esecuzione della query");
    }

    if (@ $result->num_rows != 0) {
        echo "<table>";
        echo "<tr>";
        echo "<th>ID_DOC</th>";
        echo "<th>NDOC</th>";
        echo "<th>DATA</th>";
        echo "<th>IDCLIENTE</th>";
        echo "<th>TIPODOC</th>";
        echo "<th>TIPOPAGAMENTO</th>";
        echo "</tr>";
        while ($row = @ $result->fetch_array()) {
            echo "<tr onclick=\"window.location.href='fatture.php?id=" . $row[0] . "'\">";
            echo "<td><a href='fatture.php?id=" . $row[0] . "'>$row[0]</a></td>";
            echo "<td><a href='fatture.php?id=" . $row[0] . "'>$row[1]</a></td>";
            echo "<td><a href='fatture.php?id=" . $row[0] . "'>$row[2]</a></td>";
            echo "<td><a href='fatture.php?id=" . $row[0] . "'>$row[3]</a></td>";
            echo "<td><a href='fatture.php?id=" . $row[0] . "'>$row[4]</a></td>";
            echo "<td><a href='fatture.php?id=" . $row[0] . "'>$row[5]</a></td>";
            echo "</tr>";
        }
        echo "</table>";
    } else {
        echo "<p>Nessuna fattura disponibile.</p>";
    }

    @ $result->free();
    @ $connection->close();
    ?>
    <br>
    <div>
        <a href="http://localhost/fattureelettroniche/add.php" class="button add">Aggiungi una fattura</a>
        <a href="http://localhost/fattureelettroniche/del.php" class="button delete">Elimina una fattura</a>
    </div>
</body>
</html>
