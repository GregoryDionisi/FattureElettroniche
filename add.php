<html>
<head>
  <title>DB & PHP Test: Inserisci Fattura Elettronica</title>
</head>
<body>
  <h1>Inserisci Fattura Elettronica</h1>
  <form action="insert.php" method="GET">
    <!-- Campi per la fattura -->
    <label for="ndoc">Numero Fattura:</label><br>
    <input type="text" id="ndoc" name="ndoc" required><br><br>

    <label for="data">Data Fattura:</label><br>
    <input type="date" id="data" name="data" required><br><br>

    <!-- Gestione del Cliente: selezione esistente o nuovo -->
    <label for="cliente">Cliente:</label><br>
    <select id="cliente" name="cliente" required>
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
    </select><br><br>

    <label for="new_cliente">Aggiungi nuovo cliente:</label>
    <input type="checkbox" id="new_cliente" name="new_cliente" value="1"><br><br>

    <div id="client_info" style="display: none;">
      <h3>Inserisci Dati Anagrafici del Cliente</h3>
      <label for="denominazione">Denominazione:</label><br>
      <input type="text" id="denominazione" name="denominazione" disabled><br><br>

      <label for="indirizzo">Indirizzo:</label><br>
      <input type="text" id="indirizzo" name="indirizzo" disabled><br><br>

      <label for="citta">Città:</label><br>
      <input type="text" id="citta" name="citta" disabled><br><br>

      <label for="cap">CAP:</label><br>
      <input type="text" id="cap" name="cap" disabled><br><br>

      <label for="nazione">Nazione:</label><br>
      <input type="text" id="nazione" name="nazione" disabled><br><br>

      <label for="provincia">Provincia:</label><br>
      <input type="text" id="provincia" name="provincia" disabled><br><br>

      <label for="piva">Partita IVA:</label><br>
      <input type="text" id="piva" name="piva" disabled><br><br>

      <label for="cf">Codice Fiscale:</label><br>
      <input type="text" id="cf" name="cf" disabled><br><br>

      <label for="sdi">Codice SDI:</label><br>
      <input type="text" id="sdi" name="sdi" disabled><br><br>

      <label for="pec">PEC:</label><br>
      <input type="email" id="pec" name="pec" disabled><br><br>
    </div><br><br>

    <label for="tipodoc">Tipo Documento:</label><br>
    <select id="tipodoc" name="tipodoc" required>
      <option value="1">Fattura</option>
      <option value="2">Nota di Credito</option>
    </select><br><br>

    <label for="tipopagamento">Tipo di Pagamento:</label><br>
    <select id="tipopagamento" name="tipopagamento" required>
      <option value="Contante">Contante</option>
      <option value="Assegno">Assegno</option>
      <option value="Assegno Circolare">Assegno Circolare</option>
      <option value="Bonifico Bancario">Bonifico Bancario</option>
      <option value="Carta di Credito">Carta di Credito</option>
      <option value="Ricevuta Bancaria">Ricevuta Bancaria</option>
      <option value="Altro">Altro</option>
    </select><br><br>

    <!-- Campi per i dettagli della fattura -->
    <h2>Dettagli Fattura</h2>
    <label for="descrizione">Descrizione Prodotto/Servizio:</label><br>
    <input type="text" id="descrizione" name="descrizione" required><br><br>

    <label for="qt">Quantità:</label><br>
    <input type="number" id="qt" name="qt" required min="1"><br><br>

    <label for="importounitario">Importo Unitario (€):</label><br>
    <input type="number" step="0.01" id="importounitario" name="importounitario" required><br><br>

    <label for="idiva">Tipo IVA:</label><br>
    <select id="idiva" name="idiva" required>
    <option value="">-- Seleziona Tipo IVA Esistente --</option>
  <?php
  // Connessione al database
  $conn = new mysqli("localhost", "root", "", "fattureelettroniche");
  if ($conn->connect_error) {
      die("Connessione fallita: " . $conn->connect_error);
  }

  // Recupero IVA esistenti
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
</select><br><br>

<label for="new_iva">Aggiungi nuovo tipo IVA:</label>
<input type="checkbox" id="new_iva" name="new_iva" value="1"><br><br>


    <label for="importoriga">Importo Totale Riga (€):</label><br>
    <input type="text" id="importoriga" name="importoriga" readonly><br><br>

    <div id="iva_info" style="display: none;">
    <h3>Inserisci Nuovo Tipo IVA</h3>
    <label for="descrizione_iva">Descrizione:</label><br>
    <input type="text" id="descrizione_iva" name="descrizione_iva" disabled><br><br>

    <label for="percentuale">Percentuale:</label><br>
    <input type="number" step="0.01" id="percentuale" name="percentuale" disabled><br><br>
    </div>
    <input type="submit" value="Inserisci Fattura">
  </form>

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
        const quantita = parseFloat(qtInput.value) || 0;
        const importoUnitario = parseFloat(importoUnitarioInput.value) || 0;
        const importoTotale = quantita * importoUnitario;
        importoRigaInput.value = importoTotale.toFixed(2);
    }

 
    qtInput.addEventListener('input', aggiornaImportoRiga);
    importoUnitarioInput.addEventListener('input', aggiornaImportoRiga);

    clienteSelect.addEventListener('change', function () {
      if (clienteSelect.value) {
        newClienteCheckbox.checked = false;
        clientInfo.style.display = 'none';
        clientInputs.forEach(input => input.disabled = true);
      }
    });

    newClienteCheckbox.addEventListener('change', function () {
      if (newClienteCheckbox.checked) {
        clienteSelect.value = '';
        clientInfo.style.display = 'block';
        clientInputs.forEach(input => input.disabled = false);
      } else {
        clientInfo.style.display = 'none';
        clientInputs.forEach(input => input.disabled = true);
      }
    });

  // Gestione selezione IVA esistente
idivaSelect.addEventListener('change', function () {
    if (idivaSelect.value) {
        newIvaCheckbox.checked = false; // Deseleziona il checkbox
        ivaInfo.style.display = 'none';
        ivaInputs.forEach(input => input.disabled = true);
    }
});

// Gestione aggiunta nuovo tipo IVA
newIvaCheckbox.addEventListener('change', function () {
    if (newIvaCheckbox.checked) {
        idivaSelect.value = ''; // Deseleziona il menu a tendina
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
