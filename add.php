<html lang="it" data-theme="light">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Fatture Elettroniche: ADD</title>
  <link href="https://cdn.jsdelivr.net/npm/daisyui@4.12.22/dist/full.min.css" rel="stylesheet" type="text/css" />
  <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-100 font-sans">

  <div class="container mx-auto p-6 bg-white shadow-md rounded-lg my-8">

  <button onclick="window.history.back();" class="btn btn-outline btn-primary flex items-center space-x-2 mb-6">
  <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor" class="w-4 h-4 mr-2">
    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7" />
  </svg>
  <span>Indietro</span>
</button>


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

      <div class="flex flex-col">
  <label for="tipodoc" class="font-medium text-gray-700">Tipo Documento:</label>
  <select id="tipodoc" name="tipodoc" required class="p-2 border border-gray-300 rounded mt-2">
    <option value="">-- Seleziona Tipo Documento --</option>
    <option value="1">Fattura</option>
    <option value="2">Nota di Credito</option>
  </select>
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

<div id="dettagli-container">
  <div class="dettaglio-fattura border p-4 rounded-lg mb-4">
    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
      <div>
        <label class="block text-lg font-medium text-gray-700">Descrizione Prodotto/Servizio:</label>
        <input type="text" name="descrizione[]" required class="w-full mt-2 p-2 border border-gray-300 rounded-md">
      </div>

      <div>
        <label class="block text-lg font-medium text-gray-700">Quantità:</label>
        <input type="number" name="qt[]" required min="1" class="w-full mt-2 p-2 border border-gray-300 rounded-md quantita">
      </div>

      <div>
        <label class="block text-lg font-medium text-gray-700">Importo Unitario (€):</label>
        <input type="number" step="0.01" name="importounitario[]" required class="w-full mt-2 p-2 border border-gray-300 rounded-md importo-unitario">
      </div>

      <div>
    <label class="block text-lg font-medium text-gray-700">Tipo IVA:</label>
    <select name="idiva[]" class="w-full mt-2 p-2 border border-gray-300 rounded-md">
        <option value="">-- Seleziona Tipo IVA --</option>
        <option value="1">IVA 22%</option>
        <option value="2">IVA 10%</option>
        <option value="3">Esente IVA</option>
    </select>
</div>

      <div>
        <label class="block text-lg font-medium text-gray-700">Importo Totale Riga (€):</label>
        <input type="text" name="importoriga[]" readonly class="w-full mt-2 p-2 border border-gray-300 rounded-md bg-gray-100 importo-riga">
      </div>

      <div class="flex items-center mt-4">
        <button type="button" class="btn btn-error btn-sm remove-dettaglio" style="display: none;">
          Rimuovi riga
        </button>
      </div>
    </div>
  </div>
</div>

<button type="button" id="aggiungi-dettaglio" class="btn btn-secondary mt-4">
  Aggiungi altra riga
</button>

<!-- Submit -->
<div class="flex justify-end mt-6">
  <button type="submit" class="btn btn-primary">
    Invia Fattura
  </button>
</div>
    </form>
  </div>


  <script>
 document.addEventListener('DOMContentLoaded', function() {
  // --- Elementi DOM Cliente ---
  const clienteSelect = document.getElementById('cliente');
  const newClienteCheckbox = document.getElementById('new_cliente');
  const clientInfo = document.getElementById('client_info');
  const clientInputs = document.querySelectorAll('#client_info input');

  // --- Elementi DOM Dettagli Fattura ---
  const dettagliContainer = document.getElementById('dettagli-container');
  const aggiungiDettaglioBtn = document.getElementById('aggiungi-dettaglio');

  // --- Gestione Cliente ---
  function handleClienteSelection() {
    if (clienteSelect.value) {
      newClienteCheckbox.checked = false;
      clientInfo.style.display = 'none';
      clientInputs.forEach(input => {
        input.disabled = true;
        input.required = false;
      });
    }
  }

  function handleNewCliente() {
    if (newClienteCheckbox.checked) {
      clienteSelect.value = '';
      clientInfo.style.display = 'block';
      clientInputs.forEach(input => {
        input.disabled = false;
        if (['denominazione', 'indirizzo', 'citta', 'piva'].includes(input.id)) {
          input.required = true;
        }
      });
    } else {
      clientInfo.style.display = 'none';
      clientInputs.forEach(input => {
        input.disabled = true;
        input.required = false;
      });
    }
  }

  // --- Gestione Dettagli Fattura ---
  function calcolaImportoRiga(row) {
    const quantita = parseFloat(row.querySelector('input[name="qt[]"]').value) || 0;
    const importoUnitario = parseFloat(row.querySelector('input[name="importounitario[]"]').value) || 0;
    const importoTotale = quantita * importoUnitario;
    row.querySelector('input[name="importoriga[]"]').value = importoTotale.toFixed(2);
  }

  function addRowEventListeners(row) {
    const quantitaInput = row.querySelector('input[name="qt[]"]');
    const importoUnitarioInput = row.querySelector('input[name="importounitario[]"]');
    const removeBtn = row.querySelector('.remove-dettaglio');

    quantitaInput.addEventListener('input', () => calcolaImportoRiga(row));
    importoUnitarioInput.addEventListener('input', () => calcolaImportoRiga(row));
    
    if (removeBtn) {
      removeBtn.style.display = 'block'; // Mostra il pulsante rimuovi
      removeBtn.addEventListener('click', () => {
        const allRows = document.querySelectorAll('.dettaglio-fattura');
        if (allRows.length > 1) {
          row.remove();
        }
      });
    }
  }

  function resetRow(row) {
    row.querySelectorAll('input[type="text"], input[type="number"]').forEach(input => {
      if (!input.readOnly) {
        input.value = '';
      } else {
        input.value = '0.00';
      }
    });

    row.querySelectorAll('select').forEach(select => {
      select.selectedIndex = 0;
    });
  }

  // Inizializza la prima riga
  const primaRiga = document.querySelector('.dettaglio-fattura');
  if (primaRiga) {
    addRowEventListeners(primaRiga);
  }

  // Gestione aggiunta nuova riga
  if (aggiungiDettaglioBtn) {
    aggiungiDettaglioBtn.addEventListener('click', () => {
      const primaRiga = document.querySelector('.dettaglio-fattura');
      if (primaRiga) {
        const nuovaRiga = primaRiga.cloneNode(true);
        resetRow(nuovaRiga);
        dettagliContainer.appendChild(nuovaRiga);
        addRowEventListeners(nuovaRiga);
      }
    });
  }

  // Event listeners iniziali
  clienteSelect.addEventListener('change', handleClienteSelection);
  newClienteCheckbox.addEventListener('change', handleNewCliente);

  // --- Validazione Form ---
  const form = document.querySelector('form');
  if (form) {
    form.addEventListener('submit', function(e) {
      let isValid = true;

      // Validazione cliente
      if (!clienteSelect.value && !newClienteCheckbox.checked) {
        isValid = false;
        alert('Seleziona un cliente esistente o aggiungi un nuovo cliente');
      }

      // Validazione campi obbligatori per nuovo cliente
      if (newClienteCheckbox.checked) {
        const requiredFields = ['denominazione', 'indirizzo', 'citta', 'piva'];
        requiredFields.forEach(fieldId => {
          const input = document.getElementById(fieldId);
          if (input && !input.value.trim()) {
            isValid = false;
            alert(`Il campo ${fieldId} è obbligatorio per il nuovo cliente`);
          }
        });
      }

      // Validazione dettagli fattura
      const dettagli = document.querySelectorAll('.dettaglio-fattura');
      dettagli.forEach((dettaglio, index) => {
        const descrizione = dettaglio.querySelector('input[name="descrizione[]"]');
        const quantita = dettaglio.querySelector('input[name="qt[]"]');
        const importoUnitario = dettaglio.querySelector('input[name="importounitario[]"]');
        const iva = dettaglio.querySelector('select[name="idiva[]"]');

        if (!descrizione.value.trim() || !quantita.value || !importoUnitario.value || !iva.value) {
          isValid = false;
          alert(`Completa tutti i campi obbligatori nella riga ${index + 1} dei dettagli fattura`);
        }
      });

      if (!isValid) {
        e.preventDefault();
      }
    });
  }
});
  </script>
</body>
</html>
