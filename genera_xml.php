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

// Elemento root con namespace Agenzia delle Entrate
$root = $xml->createElement('FatturaElettronica');
$root->setAttribute('xmlns', 'http://ivaservizi.agenziaentrate.gov.it/docs/xsd/fatture/v1.2');
$root->setAttribute('versione', 'FPR12');
$xml->appendChild($root);

// Dati della prima riga per informazioni principali
$firstRow = $result->fetch_assoc();

if ($firstRow) {
    // FatturaElettronicaHeader
    $header = $xml->createElement('FatturaElettronicaHeader');
    $root->appendChild($header);

    // DatiTrasmissione
    $datiTrasmissione = $xml->createElement('DatiTrasmissione');
    $header->appendChild($datiTrasmissione);
    
    // Creazione corretta del nodo IdentificativoTrasmittente
    $idTrasmittente = $xml->createElement('IdTrasmittente');
    $datiTrasmissione->appendChild($idTrasmittente);
    
    // Aggiunta dei sottoelementi obbligatori
    $idTrasmittente->appendChild($xml->createElement('IdPaese', 'IT'));
    $idTrasmittente->appendChild($xml->createElement('IdCodice', $firstRow['PIVA']));
    
    $datiTrasmissione->appendChild($xml->createElement('ProgressivoInvio', $firstRow['NDOC']));

    // CedentePrestatore
    $cedentePrestatore = $xml->createElement('CedentePrestatore');
    $header->appendChild($cedentePrestatore);
    $cedentePrestatore->appendChild($xml->createElement('Denominazione', $firstRow['DENOMINAZIONE']));
    $cedentePrestatore->appendChild($xml->createElement('Indirizzo', $firstRow['INDIRIZZO']));
    $cedentePrestatore->appendChild($xml->createElement('CAP', $firstRow['CAP']));
    $cedentePrestatore->appendChild($xml->createElement('Comune', $firstRow['CITTA']));
    $cedentePrestatore->appendChild($xml->createElement('Provincia', $firstRow['PROVINCIA']));
    $cedentePrestatore->appendChild($xml->createElement('Nazione', $firstRow['NAZIONE']));

    // CessionarioCommittente
    $cessionarioCommittente = $xml->createElement('CessionarioCommittente');
    $header->appendChild($cessionarioCommittente);
    $cessionarioCommittente->appendChild($xml->createElement('Denominazione', $firstRow['CLIENTE_DENOMINAZIONE']));
    $cessionarioCommittente->appendChild($xml->createElement('Indirizzo', $firstRow['CLIENTE_INDIRIZZO']));
    $cessionarioCommittente->appendChild($xml->createElement('CAP', $firstRow['CLIENTE_CAP']));
    $cessionarioCommittente->appendChild($xml->createElement('Comune', $firstRow['CLIENTE_CITTA']));
    $cessionarioCommittente->appendChild($xml->createElement('Provincia', $firstRow['CLIENTE_PROVINCIA']));
    $cessionarioCommittente->appendChild($xml->createElement('Nazione', $firstRow['CLIENTE_NAZIONE']));

    // FatturaElettronicaBody
    $body = $xml->createElement('FatturaElettronicaBody');
    $root->appendChild($body);

    // DatiGenerali
    $datiGenerali = $xml->createElement('DatiGenerali');
    $body->appendChild($datiGenerali);
    $datiGenerali->appendChild($xml->createElement('TipoDocumento', $firstRow['TIPODOC'] == 1 ? 'TD01' : 'TD04'));
    $datiGenerali->appendChild($xml->createElement('Data', $firstRow['DATA']));
    $datiGenerali->appendChild($xml->createElement('Numero', $firstRow['NDOC']));

    // DatiPagamento
    $datiPagamento = $xml->createElement('DatiPagamento');
    $body->appendChild($datiPagamento);
    $datiPagamento->appendChild($xml->createElement('ModalitaPagamento', $firstRow['TIPOPAGAMENTO']));
    if ($firstRow['BANCA']) {
        $datiPagamento->appendChild($xml->createElement('Banca', $firstRow['BANCA']));
        $datiPagamento->appendChild($xml->createElement('IBAN', $firstRow['IBAN']));
    }

    // DatiBeniServizi
    $datiBeniServizi = $xml->createElement('DatiBeniServizi');
    $body->appendChild($datiBeniServizi);

    // Reset del result set per iterare su tutte le righe
    $result->data_seek(0);
    while ($row = $result->fetch_assoc()) {
        $dettaglioLinea = $xml->createElement('DettaglioLinea');
        $datiBeniServizi->appendChild($dettaglioLinea);
        $dettaglioLinea->appendChild($xml->createElement('Descrizione', $row['DESCRIZIONE']));
        $dettaglioLinea->appendChild($xml->createElement('Quantita', $row['QT']));
        $dettaglioLinea->appendChild($xml->createElement('PrezzoUnitario', $row['IMPORTOUNITARIO']));
        $dettaglioLinea->appendChild($xml->createElement('PrezzoTotale', $row['IMPORTORIGA']));
        $dettaglioLinea->appendChild($xml->createElement('AliquotaIVA', $row['IVA_COD']));
    }
}

$result->free();
$connection->close();

// Output XML
echo $xml->saveXML();
?>