<html lang="it">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>DB & PHP Test: Inserisci Fattura Elettronica</title>
  <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet">
</head>
<body class="bg-gray-100 font-sans">

  <div class="container mx-auto p-6 bg-white shadow-md rounded-lg my-8">
    <h1 class="text-3xl font-bold text-center text-gray-800 mb-6">Inserisci Fattura Elettronica</h1>

    <form action="insert.php" method="GET" class="space-y-6">

      <!-- Campi per la fattura -->
      <div class="flex flex-col">
        <label for="ndoc" class="font-medium text-gray-700">Numero Fattura:</label>
        <input type="text" id="ndoc" name="ndoc" required class="p-2 border border-gray-300 rounded mt-2" placeholder="Inserisci il numero della fattura">
      </div>

      <div class="flex flex-col">
        <label for="data" class="font-medium text-gray-700">Data Fattura:</label>
        <input type="date" id="data" name="data" required class="p-2 border border-gray-300 rounded mt-2">
      </div>

      <!-- Gestione del Cliente -->
      <div class="flex flex-col">
        <label for="cliente" class="font-medium text-gray-700">Cliente:</label>
        <select id="cliente" name="cliente" class="p-2 border border-gray-300 rounded mt-2">
          <option value="">-- Seleziona Cliente Esistente --</option>
          <?php
          // Connessione al database
          $conn = new mysqli("localhost", "root", "", "fattureelettroniche");
          if ($conn->connect_error) {
              die("Connessione fallita: " . $conn->connect_error);
          }

          // Recupero clienti esistenti
          $sql = "SELECT IDCLIENTE, DENOMINAZIONE FROM tabcliente ORDER BY DENOMINAZIONE";
          $result = $conn->query($sql);

          if ($result->num_rows > 0) {
              while ($row = $result->fetch_assoc()) {
                  echo '<option value="' . $row['IDCLIENTE'] . '">' . htmlspecialchars($row['DENOMINAZIONE']) . '</option>';
              }
          } else {
              echo '<option value="">Nessun cliente trovato</option>';
          }

          $conn->close();
          ?>
        </select>
      </div>

      <div class="flex items-center">
        <input type="checkbox" id="new_cliente" name="new_cliente" value="1" class="mr-2">
        <label for="new_cliente" class="font-medium text-gray-700">Aggiungi nuovo cliente:</label>
      </div>

      <div id="client_info" style="display: none;" class="mt-6 bg-gray-50 p-4 rounded-lg shadow-sm">
        <h3 class="text-lg font-semibold text-gray-800 mb-4">Inserisci Dati Anagrafici del Cliente</h3>

        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
          <div class="flex flex-col">
            <label for="denominazione" class="font-medium text-gray-700">Denominazione:</label>
            <input type="text" id="denominazione" name="denominazione" class="p-2 border border-gray-300 rounded mt-2">
          </div>

          <div class="flex flex-col">
            <label for="indirizzo" class="font-medium text-gray-700">Indirizzo:</label>
            <input type="text" id="indirizzo" name="indirizzo" class="p-2 border border-gray-300 rounded mt-2">
          </div>

          <div class="flex flex-col">
            <label for="citta" class="font-medium text-gray-700">Città:</label>
            <input type="text" id="citta" name="citta" class="p-2 border border-gray-300 rounded mt-2">
          </div>

          <div class="flex flex-col">
            <label for="cap" class="font-medium text-gray-700">CAP:</label>
            <input type="text" id="cap" name="cap" maxlength="5" class="p-2 border border-gray-300 rounded mt-2">
          </div>

          <div class="flex flex-col">
            <label for="nazione" class="font-medium text-gray-700">Nazione:</label>
            <input type="text" id="nazione" name="nazione" class="p-2 border border-gray-300 rounded mt-2">
          </div>

          <div class="flex flex-col">
            <label for="provincia" class="font-medium text-gray-700">Provincia:</label>
            <input type="text" id="provincia" name="provincia" maxlength="2" class="p-2 border border-gray-300 rounded mt-2">
          </div>

          <div class="flex flex-col">
            <label for="piva" class="font-medium text-gray-700">Partita IVA:</label>
            <input type="text" id="piva" name="piva" maxlength="11" class="p-2 border border-gray-300 rounded mt-2">
          </div>

          <div class="flex flex-col">
            <label for="cf" class="font-medium text-gray-700">Codice Fiscale:</label>
            <input type="text" id="cf" name="cf" maxlength="16" class="p-2 border border-gray-300 rounded mt-2">
          </div>

          <div class="flex flex-col">
            <label for="sdi" class="font-medium text-gray-700">Codice SDI:</label>
            <input type="text" id="sdi" name="sdi" maxlength="7" class="p-2 border border-gray-300 rounded mt-2">
          </div>

          <div class="flex flex-col">
            <label for="pec" class="font-medium text-gray-700">PEC:</label>
            <input type="email" id="pec" name="pec" maxlength="50" class="p-2 border border-gray-300 rounded mt-2">
          </div>
        </div>
      </div>
<!-- Tipo di Pagamento -->
<div>
  <label for="tipopagamento" class="block text-lg font-medium text-gray-700">Tipo di Pagamento:</label>
  <select id="tipopagamento" name="tipopagamento" required class="w-full mt-2 p-2 border border-gray-300 rounded-md">
    <option value="Contante">Contante</option>
    <option value="Assegno">Assegno</option>
    <option value="Assegno Circolare">Assegno Circolare</option>
    <option value="Bonifico Bancario">Bonifico Bancario</option>
    <option value="Carta di Credito">Carta di Credito</option>
    <option value="Ricevuta Bancaria">Ricevuta Bancaria</option>
    <option value="Altro">Altro</option>
  </select>
</div>

<!-- Dettagli Fattura -->
<h2 class="text-2xl font-semibold text-gray-800 mt-6">Dettagli Fattura</h2>

<div>
  <label for="descrizione" class="block text-lg font-medium text-gray-700">Descrizione Prodotto/Servizio:</label>
  <input type="text" id="descrizione" name="descrizione" required class="w-full mt-2 p-2 border border-gray-300 rounded-md">
</div>

<div>
  <label for="qt" class="block text-lg font-medium text-gray-700">Quantità:</label>
  <input type="number" id="qt" name="qt" required min="1" class="w-full mt-2 p-2 border border-gray-300 rounded-md">
</div>

<div>
  <label for="importounitario" class="block text-lg font-medium text-gray-700">Importo Unitario (€):</label>
  <input type="number" step="0.01" id="importounitario" name="importounitario" required class="w-full mt-2 p-2 border border-gray-300 rounded-md">
</div>

<!-- Tipo IVA -->
<div>
  <label for="idiva" class="block text-lg font-medium text-gray-700">Tipo IVA:</label>
  <select id="idiva" name="idiva" class="w-full mt-2 p-2 border border-gray-300 rounded-md">
    <option value="">-- Seleziona Tipo IVA Esistente --</option>
    <?php
      $conn = new mysqli("localhost", "root", "", "fattureelettroniche");
      if ($conn->connect_error) {
          die("Connessione fallita: " . $conn->connect_error);
      }

      $sql = "SELECT ID_IVA, DESCRIZIONE FROM tiva ORDER BY DESCRIZIONE";
      $result = $conn->query($sql);

      if ($result->num_rows > 0) {
          while ($row = $result->fetch_assoc()) {
              echo '<option value="' . $row['ID_IVA'] . '">' . htmlspecialchars($row['DESCRIZIONE']) . '</option>';
          }
      } else {
          echo '<option value="">Nessun tipo IVA trovato</option>';
      }

      $conn->close();
    ?>
  </select>
</div>

<div class="flex items-center mt-4">
  <input type="checkbox" id="new_iva" name="new_iva" value="1" class="mr-2">
  <label for="new_iva" class="text-lg font-medium text-gray-700">Aggiungi nuovo tipo IVA:</label>
</div>

<div>
  <label for="importoriga" class="block text-lg font-medium text-gray-700">Importo Totale Riga (€):</label>
  <input type="text" id="importoriga" name="importoriga" readonly class="w-full mt-2 p-2 border border-gray-300 rounded-md bg-gray-100">
</div>

<!-- Nuovo Tipo IVA -->
<div id="iva_info" style="display: none;">
  <h3 class="text-2xl font-semibold text-gray-800 mt-6">Inserisci Nuovo Tipo IVA</h3>

  <div>
    <label for="cod" class="block text-lg font-medium text-gray-700">COD:</label>
    <input type="text" id="cod" name="cod" class="w-full mt-2 p-2 border border-gray-300 rounded-md">
  </div>

  <div>
    <label for="descrizione_iva" class="block text-lg font-medium text-gray-700">Descrizione:</label>
    <input type="text" id="descrizione_iva" name="descrizione_iva" class="w-full mt-2 p-2 border border-gray-300 rounded-md">
  </div>
</div>

<!-- Submit -->
<div class="mt-6">
  <input type="submit" value="Inserisci Fattura" class="w-full bg-blue-600 text-white py-2 rounded-md hover:bg-blue-700 transition">
</div>
    </form>
  </div>


  <script>
    const clienteSelect = document.getElementById('cliente');
    const newClienteCheckbox = document.getElementById('new_cliente');
    const clientInfo = document.getElementById('client_info');
    const clientInputs = document.querySelectorAll('#client_info input');
    const qtInput = document.getElementById('qt');
    const importoUnitarioInput = document.getElementById('importounitario');
    const importoRigaInput = document.getElementById('importoriga');
    const idivaSelect = document.getElementById('idiva');
    const newIvaCheckbox = document.getElementById('new_iva');
    const ivaInfo = document.getElementById('iva_info');
    const ivaInputs = document.querySelectorAll('#iva_info input');


    function aggiornaImportoRiga() {
        const quantita = parseFloat(qtInput.value) || 0; //converte i valori numerici in numeri float e se non sono validi assegna 0
        const importoUnitario = parseFloat(importoUnitarioInput.value) || 0;
        const importoTotale = quantita * importoUnitario;
        importoRigaInput.value = importoTotale.toFixed(2);
    }

 
    qtInput.addEventListener('input', aggiornaImportoRiga);
    importoUnitarioInput.addEventListener('input', aggiornaImportoRiga); //aggiornano il calcolo ogni volta che l'utente modifica la quantità o l'importo unitario
      
    clienteSelect.addEventListener('change', function () {
      if (clienteSelect.value) {
        newClienteCheckbox.checked = false; 
        clientInfo.style.display = 'none'; //nasconde la sezione per i dati del nuovo cliente
        clientInputs.forEach(input => input.disabled = true); //disabilita tutti i campi di input per i dati del nuovo cliente
      }
    });

    newClienteCheckbox.addEventListener('change', function () {
      if (newClienteCheckbox.checked) {
        clienteSelect.value = ''; //resetta il valore del menu a tendina del cliente esistenete
        clientInfo.style.display = 'block'; //mostra i campi per inserire i dati del nuovo cliente
        clientInputs.forEach(input => input.disabled = false); //abilita i campi di input per consentire l'inserimento
      } else {
        clientInfo.style.display = 'none';
        clientInputs.forEach(input => input.disabled = true);
      }
    });

  // Gestione selezione IVA esistente
idivaSelect.addEventListener('change', function () {
    if (idivaSelect.value) {
        newIvaCheckbox.checked = false;
        ivaInfo.style.display = 'none';
        ivaInputs.forEach(input => input.disabled = true);
    }
});

// Gestione aggiunta nuovo tipo IVA
newIvaCheckbox.addEventListener('change', function () {
    if (newIvaCheckbox.checked) {
        idivaSelect.value = '';
        ivaInfo.style.display = 'block';
        ivaInputs.forEach(input => input.disabled = false);
    } else {
        ivaInfo.style.display = 'none';
        ivaInputs.forEach(input => input.disabled = true);
    }
});
  </script>
</body>
</html>
