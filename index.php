<!DOCTYPE html>
<html lang="it" data-theme="light">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Fatture Elettroniche</title>
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
        echo "<h1 class='text-2xl font-bold mb-6'>Fatture</h1>";
        echo "<table class='table w-full border border-gray-300'>";
        echo "<thead>";
        echo "<tr class='bg-gray-200'>";
        echo "<th class='p-3'>ID_DOC</th>";
        echo "<th class='p-3'>NDOC</th>";
        echo "<th class='p-3'>DATA</th>";
        echo "<th class='p-3'>IDFORNITORE</th>";
        echo "<th class='p-3'>DENOMINAZIONE</th>";
        echo "<th class='p-3'>INDIRIZZO</th>";
        echo "<th class='p-3'>CITTA</th>";
        echo "<th class='p-3'>CAP</th>";
        echo "<th class='p-3'>NAZIONE</th>";
        echo "<th class='p-3'>PROVINCIA</th>";
        echo "<th class='p-3'>PIVA</th>";
        echo "<th class='p-3'>CF</th>";
        echo "<th class='p-3'>SDI</th>";
        echo "<th class='p-3'>PEC</th>";
        echo "<th class='p-3'>IDCLIENTE</th>";
        echo "<th class='p-3'>TIPODOC</th>";
        echo "<th class='p-3'>TIPOPAGAMENTO</th>";
        echo "<th class='p-3'>BANCA</th>";
        echo "<th class='p-3'>IBAN</th>";
        echo "<th class='p-3'>AZIONI</th>";
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
            echo "<td class='p-3'><a href='fatture.php?id=" . $row[0] . "'>$row[6]</a></td>";
            echo "<td class='p-3'><a href='fatture.php?id=" . $row[0] . "'>$row[7]</a></td>";
            echo "<td class='p-3'><a href='fatture.php?id=" . $row[0] . "'>$row[8]</a></td>";
            echo "<td class='p-3'><a href='fatture.php?id=" . $row[0] . "'>$row[9]</a></td>";
            echo "<td class='p-3'><a href='fatture.php?id=" . $row[0] . "'>$row[10]</a></td>";
            echo "<td class='p-3'><a href='fatture.php?id=" . $row[0] . "'>$row[11]</a></td>";
            echo "<td class='p-3'><a href='fatture.php?id=" . $row[0] . "'>$row[12]</a></td>";
            echo "<td class='p-3'><a href='fatture.php?id=" . $row[0] . "'>$row[13]</a></td>";
            echo "<td class='p-3'><a href='fatture.php?id=" . $row[0] . "'>$row[14]</a></td>";
            echo "<td class='p-3'><a href='fatture.php?id=" . $row[0] . "'>$row[15]</a></td>";
            echo "<td class='p-3'><a href='fatture.php?id=" . $row[0] . "'>$row[16]</a></td>";
            echo "<td class='p-3'><a href='fatture.php?id=" . $row[0] . "'>$row[17]</a></td>";
            echo "<td class='p-3'><a href='fatture.php?id=" . $row[0] . "'>$row[18]</a></td>";
            echo "<td class='p-3 flex gap-2'>";  // Aggiunto flex e gap-2 per spaziare i pulsanti
echo "<a href='genera_xml.php?id=" . $row[0] . "' class='btn btn-sm btn-primary flex items-center gap-1 min-w-[5rem]'>
    <svg xmlns='http://www.w3.org/2000/svg' class='h-4 w-4' viewBox='0 0 20 20' fill='currentColor'>
        <path fill-rule='evenodd' d='M3 17a1 1 0 011-1h12a1 1 0 110 2H4a1 1 0 01-1-1zm3.293-7.707a1 1 0 011.414 0L9 10.586V3a1 1 0 112 0v7.586l1.293-1.293a1 1 0 111.414 1.414l-3 3a1 1 0 01-1.414 0l-3-3a1 1 0 010-1.414z' clip-rule='evenodd'/>
    </svg>
    <span>XML</span>
</a>";
echo "<a href='genera_pdf.php?id=" . $row[0] . "' class='btn btn-sm btn-error flex items-center gap-1 min-w-[5rem]'>  <!-- Cambiato in btn-error per il rosso -->
    <svg xmlns='http://www.w3.org/2000/svg' class='h-4 w-4' viewBox='0 0 20 20' fill='currentColor'>
        <path fill-rule='evenodd' d='M4 4a2 2 0 012-2h4.586A2 2 0 0112 2.586L15.414 6A2 2 0 0116 7.414V16a2 2 0 01-2 2H6a2 2 0 01-2-2V4zm2 6a1 1 0 011-1h6a1 1 0 110 2H7a1 1 0 01-1-1zm1 3a1 1 0 100 2h6a1 1 0 100-2H7z' clip-rule='evenodd'/>
    </svg>
    <span>PDF</span>
</a>";
echo "</td>";
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
