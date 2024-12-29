<!DOCTYPE html>
<html lang="it" data-theme="light">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Fatture Elettroniche: DELETE</title>
    <!-- Import Tailwind CSS e DaisyUI -->
    <link href="https://cdn.jsdelivr.net/npm/daisyui@4.12.22/dist/full.min.css" rel="stylesheet" type="text/css" />
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-100 text-gray-800 p-6 flex justify-center items-center min-h-screen">
    <div class="bg-white shadow-md rounded-lg p-8 max-w-md w-full">
        <!-- Pulsante per tornare indietro -->
        <button onclick="window.history.back();" class="btn btn-outline btn-primary flex items-center space-x-2 mb-6">
  <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor" class="w-4 h-4 mr-2">
    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7" />
  </svg>
  <span>Indietro</span>
</button>

        <?php
        $connection = new mysqli("localhost", "root", "", "fattureelettroniche");

        if ($connection->connect_error) {
            die("<p class='text-red-500 font-semibold'>Errore di connessione: " . $connection->connect_error . "</p>");
        }

        // Query per selezionare le fatture
        $query = "SELECT NDOC FROM fatture ORDER BY NDOC";
        $result = $connection->query($query);

        if ($result->num_rows != 0) {
        ?>
            <form action="delete.php" method="GET" class="space-y-4">
                <label for="ndoc" class="block text-lg font-medium">Fattura da eliminare:</label>
                <select name="ndoc" id="ndoc" class="select select-bordered w-full">
                    <?php
                    while ($row = $result->fetch_array(MYSQLI_ASSOC)) {
                        echo "<option value=\"{$row['NDOC']}\">{$row['NDOC']}</option>";
                    }
                    ?>
                </select>
                <div class="text-center">
                    <input type="submit" value="Elimina" class="btn btn-error w-full">
                </div>
            </form>
        <?php
        } else {
            echo "<p class='text-center text-lg font-semibold text-gray-600'>Nessuna fattura è presente nel database.</p>";
        }

        $connection->close();
        ?>
    </div>
</body>
</html>
