<!DOCTYPE html>
<html lang="it">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>DB & PHP Test</title>
    <!-- Import Tailwind CSS e DaisyUI -->
    <link href="https://cdn.jsdelivr.net/npm/daisyui@4.12.22/dist/full.min.css" rel="stylesheet" type="text/css" />
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-100 text-gray-800 p-6">

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
        echo "<div class='overflow-x-auto'>";
        echo "<table class='table w-full border border-gray-300'>";
        echo "<thead>";
        echo "<tr class='bg-gray-200'>";
        echo "<th class='p-3'>ID_DOC</th>";
        echo "<th class='p-3'>NDOC</th>";
        echo "<th class='p-3'>DATA</th>";
        echo "<th class='p-3'>IDCLIENTE</th>";
        echo "<th class='p-3'>TIPODOC</th>";
        echo "<th class='p-3'>TIPOPAGAMENTO</th>";
        echo "</tr>";
        echo "</thead>";
        echo "<tbody>";
        while ($row = @ $result->fetch_array()) {
            echo "<tr class='hover:bg-blue-100 hover:text-blue-900 cursor-pointer' onclick=\"window.location.href='fatture.php?id=" . $row[0] . "'\">";
            echo "<td class='p-3'><a href='fatture.php?id=" . $row[0] . "'>$row[0]</a></td>";
            echo "<td class='p-3'><a href='fatture.php?id=" . $row[0] . "'>$row[1]</a></td>";
            echo "<td class='p-3'><a href='fatture.php?id=" . $row[0] . "'>$row[2]</a></td>";
            echo "<td class='p-3'><a href='fatture.php?id=" . $row[0] . "'>$row[3]</a></td>";
            echo "<td class='p-3'><a href='fatture.php?id=" . $row[0] . "'>$row[4]</a></td>";
            echo "<td class='p-3'><a href='fatture.php?id=" . $row[0] . "'>$row[5]</a></td>";
            echo "</tr>";
        }
        echo "</tbody>";
        echo "</table>";
        echo "</div>";
    } else {
        echo "<p class='text-center text-lg font-semibold text-red-500'>Nessuna fattura disponibile.</p>";
    }

    @ $result->free();
    @ $connection->close();
    ?>
    <br>
    <div class="flex justify-center gap-4">
        <a href="http://localhost/fattureelettroniche/add.php" class="btn btn-success">Aggiungi una fattura</a>
        <a href="http://localhost/fattureelettroniche/del.php" class="btn btn-error">Elimina una fattura</a>
    </div>
</body>
</html>
