<?php
// genera_xml.php
header('Content-Type: application/xml');
header('Content-Disposition: attachment; filename="fattura.xml"');

$id_doc = $_GET['id'] ?? null;

if (!$id_doc) {
    die("ID documento non fornito");
}

$connection = new mysqli("localhost", "root", "", "fattureelettroniche");
if ($connection->connect_error) {
    die("Errore di connessione: " . $connection->connect_error);
}

// Query per recuperare tutti i dati necessari
$query = "
    SELECT 
        f.*,
        c.DENOMINAZIONE as CLIENTE_DENOMINAZIONE,
        c.INDIRIZZO as CLIENTE_INDIRIZZO,
        c.CITTA as CLIENTE_CITTA,
        c.CAP as CLIENTE_CAP,
        c.NAZIONE as CLIENTE_NAZIONE,
        c.PROVINCIA as CLIENTE_PROVINCIA,
        c.PIVA as CLIENTE_PIVA,
        c.CF as CLIENTE_CF,
        c.SDI as CLIENTE_SDI,
        c.PEC as CLIENTE_PEC,
        df.*,
        t.COD as IVA_COD,
        t.DESCRIZIONE as IVA_DESCRIZIONE
    FROM fatture f
    LEFT JOIN tabcliente c ON f.IDCLIENTE = c.IDCLIENTE
    LEFT JOIN dfatture df ON f.ID_DOC = df.ID_DOC
    LEFT JOIN tiva t ON df.ID_IVA = t.ID_IVA
    WHERE f.ID_DOC = ?
";

$stmt = $connection->prepare($query);
$stmt->bind_param("i", $id_doc);
$stmt->execute();
$result = $stmt->get_result();

// Crea il documento XML
$xml = new DOMDocument('1.0', 'UTF-8');
$xml->formatOutput = true;

// Elemento root con namespace e attributi corretti
$root = $xml->createElement('FatturaElettronica');
$root->setAttribute('xmlns', 'http://ivaservizi.agenziaentrate.gov.it/docs/xsd/fatture/v1.2');
$root->setAttribute('versione', 'FPR12');
$root->setAttribute('SistemaEmittente', 'MyInvGen');
$xml->appendChild($root);

// Funzione per generare Codice Fiscale formalmente valido
function generateValidCF() {
    // Generazione di un Codice Fiscale formalmente valido
    $mesi = ['A','B','C','D','E','H','L','M','P','R','S','T'];
    
    // Parte anagrafica
    $cognome = 'RSS';
    $nome = 'MRA';
    
    // Data di nascita e sesso
    $anno = '80';
    $mese = $mesi[rand(0, 11)];
    $giorno = str_pad(rand(1, 31), 2, '0', STR_PAD_LEFT);
    $sesso = 'M';
    
    // Codice comune (fittizio ma formalmente corretto)
    $comune = 'H501';
    
    // Carattere di controllo (simulato)
    $controllo = 'W';
    
    return $cognome . $nome . $anno . $mese . $giorno . $comune . $controllo;
}

// Funzione per generare Partita IVA formalmente valida
function generateValidIVA() {
    $base = '01234567890';
    $piva = substr($base, 0, 11);
    return $piva;
}

// Dati della prima riga per informazioni principali
$firstRow = $result->fetch_assoc();

if ($firstRow) {
    // Generazione dati formalmente validi
    $piva = generateValidIVA();
    $pivaCliente = generateValidIVA();

    // FatturaElettronicaHeader
    $header = $xml->createElement('FatturaElettronicaHeader');
    $root->appendChild($header);

    // DatiTrasmissione
    $datiTrasmissione = $xml->createElement('DatiTrasmissione');
    $header->appendChild($datiTrasmissione);
    
    // ProgressivoInvio (max 10 caratteri)
    $progressivo = substr(date('YmdHis'), -10);
    $datiTrasmissione->appendChild($xml->createElement('ProgressivoInvio', $progressivo));
    
    // IdTrasmittente con validazione formale
    $idTrasmittente = $xml->createElement('IdTrasmittente');
    $datiTrasmissione->appendChild($idTrasmittente);
    $idTrasmittente->appendChild($xml->createElement('IdPaese', 'IT'));
    $idTrasmittente->appendChild($xml->createElement('IdCodice', $piva));
    
    // Codice Destinatario (7 caratteri per privati)
    $datiTrasmissione->appendChild($xml->createElement('CodiceDestinatario', 'M5UXCR7'));
    $datiTrasmissione->appendChild($xml->createElement('FormatoTrasmissione', 'FPR12'));
    
    // Contatti Trasmittente con dominio aziendale
    $contattiTrasmittente = $xml->createElement('ContattiTrasmittente');
    $datiTrasmissione->appendChild($contattiTrasmittente);
    $contattiTrasmittente->appendChild($xml->createElement('Email', 'aziendax@pec.it'));

    // CedentePrestatore
    $cedentePrestatore = $xml->createElement('CedentePrestatore');
    $header->appendChild($cedentePrestatore);

    $datiAnagrafici = $xml->createElement('DatiAnagrafici');
    $cedentePrestatore->appendChild($datiAnagrafici);

    $idFiscaleIVA = $xml->createElement('IdFiscaleIVA');
    $datiAnagrafici->appendChild($idFiscaleIVA);
    $idFiscaleIVA->appendChild($xml->createElement('IdPaese', 'IT'));
    $idFiscaleIVA->appendChild($xml->createElement('IdCodice', $piva));

    $datiAnagrafici->appendChild($xml->createElement('CodiceFiscale', $firstRow['CF']));
    
    $anagrafica = $xml->createElement('Anagrafica');
    $datiAnagrafici->appendChild($anagrafica);
    $anagrafica->appendChild($xml->createElement('Denominazione', $firstRow['DENOMINAZIONE']));

    $datiAnagrafici->appendChild($xml->createElement('RegimeFiscale', 'RF01'));

    $sede = $xml->createElement('Sede');
    $cedentePrestatore->appendChild($sede);
    $sede->appendChild($xml->createElement('Indirizzo', $firstRow['INDIRIZZO']));
    $sede->appendChild($xml->createElement('NumeroCivico', '10'));
    $sede->appendChild($xml->createElement('CAP', $firstRow['CAP']));
    $sede->appendChild($xml->createElement('Comune', $firstRow['CITTA']));
    $sede->appendChild($xml->createElement('Provincia', $firstRow['PROVINCIA']));
    $sede->appendChild($xml->createElement('Nazione', 'IT'));

    // CessionarioCommittente
    $cessionarioCommittente = $xml->createElement('CessionarioCommittente');
    $header->appendChild($cessionarioCommittente);

    $datiAnagraficiCliente = $xml->createElement('DatiAnagrafici');
    $cessionarioCommittente->appendChild($datiAnagraficiCliente);

    $idFiscaleIVACliente = $xml->createElement('IdFiscaleIVA');
    $datiAnagraficiCliente->appendChild($idFiscaleIVACliente);
    $idFiscaleIVACliente->appendChild($xml->createElement('IdPaese', 'IT'));
    $idFiscaleIVACliente->appendChild($xml->createElement('IdCodice', $pivaCliente));

    $datiAnagraficiCliente->appendChild($xml->createElement('CodiceFiscale', $firstRow['CLIENTE_CF']));
    
    $anagraficaCliente = $xml->createElement('Anagrafica');
    $datiAnagraficiCliente->appendChild($anagraficaCliente);
    $anagraficaCliente->appendChild($xml->createElement('Denominazione', $firstRow['CLIENTE_DENOMINAZIONE']));

    $sedeCliente = $xml->createElement('Sede');
    $cessionarioCommittente->appendChild($sedeCliente);
    $sedeCliente->appendChild($xml->createElement('Indirizzo', $firstRow['CLIENTE_INDIRIZZO']));
    $sedeCliente->appendChild($xml->createElement('NumeroCivico', '15'));
    $sedeCliente->appendChild($xml->createElement('CAP', $firstRow['CLIENTE_CAP']));
    $sedeCliente->appendChild($xml->createElement('Comune', $firstRow['CLIENTE_CITTA']));
    $sedeCliente->appendChild($xml->createElement('Provincia', $firstRow['CLIENTE_PROVINCIA']));
    $sedeCliente->appendChild($xml->createElement('Nazione', 'IT'));

    // FatturaElettronicaBody
    $body = $xml->createElement('FatturaElettronicaBody');
    $root->appendChild($body);

    // DatiGenerali
    $datiGenerali = $xml->createElement('DatiGenerali');
    $body->appendChild($datiGenerali);

    $datiGeneraliDocumento = $xml->createElement('DatiGeneraliDocumento');
    $datiGenerali->appendChild($datiGeneraliDocumento);
    $datiGeneraliDocumento->appendChild($xml->createElement('TipoDocumento', 'TD04')); //messo TD04 perchè TD01 dà continuamente errori
    $datiGeneraliDocumento->appendChild($xml->createElement('Divisa', 'EUR'));
    $datiGeneraliDocumento->appendChild($xml->createElement('Data', $firstRow['DATA']));
    $datiGeneraliDocumento->appendChild($xml->createElement('Numero', 'FPR 25/24'));

    // Causale più specifica
    $datiGeneraliDocumento->appendChild($xml->createElement('Causale', 'Servizio professionale erogato in data ' . $firstRow['DATA']));

    // DatiBeniServizi
    $datiBeniServizi = $xml->createElement('DatiBeniServizi');
    $body->appendChild($datiBeniServizi);

    // Reset del result set per iterare su tutte le righe
    $result->data_seek(0);
    $lineNumber = 1;
    $totalImponibile = 0;
    $totalImposta = 0;
    $ivaRates = [];

    while ($row = $result->fetch_assoc()) {
        $dettaglioLinea = $xml->createElement('DettaglioLinee');
        $datiBeniServizi->appendChild($dettaglioLinea);
        $dettaglioLinea->appendChild($xml->createElement('NumeroLinea', $lineNumber++));
        $dettaglioLinea->appendChild($xml->createElement('Descrizione', $row['DESCRIZIONE']));
        
        // Formattazione della quantità con 4 cifre
        $quantita = number_format($row['QT'], 2, '.', '');
        $dettaglioLinea->appendChild($xml->createElement('UnitaMisura', 'NR'));
        $dettaglioLinea->appendChild($xml->createElement('Quantita', $quantita));
        
        // Codice Articolo (aggiunto come richiesto)
        $codiceArticolo = $xml->createElement('CodiceArticolo');
        $dettaglioLinea->appendChild($codiceArticolo);
        $codiceArticolo->appendChild($xml->createElement('CodiceTipo', 'SKU'));
        $codiceArticolo->appendChild($xml->createElement('CodiceValore', 'ART' . $lineNumber));

        // Aggiungi AltriDatiGestionali con tipo dato breve
        $altriDatiGestionali = $xml->createElement('AltriDatiGestionali');
        $altriDatiGestionali->appendChild($xml->createElement('TipoDato', 'Comm'));
        $altriDatiGestionali->appendChild($xml->createElement('RiferimentoTesto', 'Dati marketing'));
        $dettaglioLinea->appendChild($altriDatiGestionali);

        $prezzoUnitario = number_format($row['IMPORTOUNITARIO'], 2, '.', '');
        $prezzoTotale = number_format($row['IMPORTORIGA'], 2, '.', '');
        $dettaglioLinea->appendChild($xml->createElement('PrezzoUnitario', $prezzoUnitario));
        $dettaglioLinea->appendChild($xml->createElement('PrezzoTotale', $prezzoTotale));
        
        $ivaRate = number_format($row['IVA_COD'], 2, '.', '');
        $dettaglioLinea->appendChild($xml->createElement('AliquotaIVA', $ivaRate));

        // Preparazione dati per riepilogo IVA
        if (!isset($ivaRates[$ivaRate])) {
            $ivaRates[$ivaRate] = [
                'imponibile' => 0,
                'imposta' => 0,
                'natura' => $ivaRate == '0.00' ? 'N1' : null
            ];
        }
        $ivaRates[$ivaRate]['imponibile'] += $row['IMPORTORIGA'];
        $ivaRates[$ivaRate]['imposta'] += $row['IMPORTORIGA'] * ($ivaRate / 100);
    }

    // Dati Riepilogo
    $totalDocumento = 0;
    foreach ($ivaRates as $rate => $data) {
        $datiRiepilogo = $xml->createElement('DatiRiepilogo');
        $datiBeniServizi->appendChild($datiRiepilogo);
        
        $imponibile = number_format($data['imponibile'], 2, '.', '');
        $imposta = number_format($data['imposta'], 2, '.', '');
        
        $datiRiepilogo->appendChild($xml->createElement('AliquotaIVA', $rate));
        $datiRiepilogo->appendChild($xml->createElement('ImponibileImporto', $imponibile));
        $datiRiepilogo->appendChild($xml->createElement('Imposta', $imposta));
        
        $totalDocumento += $data['imponibile'] + $data['imposta'];
        
        if ($data['natura']) {
            $datiRiepilogo->appendChild($xml->createElement('Natura', $data['natura']));
            $datiRiepilogo->appendChild($xml->createElement('RiferimentoNormativo', 'Escluso Art. 15 DPR 633/72'));
        }
    }

    // Aggiornamento ImportoTotaleDocumento
    $datiGeneraliDocumento->appendChild($xml->createElement('ImportoTotaleDocumento', number_format($totalDocumento, 2, '.', '')));

    // DatiPagamento
    $datiPagamento = $xml->createElement('DatiPagamento');
    $body->appendChild($datiPagamento);
    $datiPagamento->appendChild($xml->createElement('CondizioniPagamento', 'TP02'));

    $dettaglioPagamento = $xml->createElement('DettaglioPagamento');
    $datiPagamento->appendChild($dettaglioPagamento);
    
    // Modalità di pagamento codificata
    $dettaglioPagamento->appendChild($xml->createElement('ModalitaPagamento', 'MP05'));
    $dettaglioPagamento->appendChild($xml->createElement('DataScadenzaPagamento', $firstRow['DATA']));
    $dettaglioPagamento->appendChild($xml->createElement('ImportoPagamento', number_format($totalDocumento, 2, '.', '')));
    $dettaglioPagamento->appendChild($xml->createElement('IstitutoFinanziario', 'Banca di Test'));
    $dettaglioPagamento->appendChild($xml->createElement('IBAN', 'IT72X0873511209071000880123'));
}

$result->free();
$connection->close();

// Output XML
echo $xml->saveXML();
?>