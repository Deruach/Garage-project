<?php
session_start();
$config = require '../config/config.php';

$conn = new mysqli(
    $config['db']['host'],
    $config['db']['username'],
    $config['db']['password'],
    $config['db']['dbname']
);

if ($conn->connect_error) {
    die("Databasefout: " . $conn->connect_error);
}

// ✅ Invoer ophalen
$appointment_id = $_POST['appointment_id'] ?? null;
$handeling_id = $_POST['handeling_id'] ?? null;
$onderdelen = $_POST['onderdelen'] ?? []; // array van part_id's
$opmerkingen = $_POST['opmerkingen'] ?? '';

if (!$appointment_id || !$handeling_id) {
    die("Ongeldige invoer.");
}

// ✅ 1. Handeling ophalen uit database
$stmt = $conn->prepare("SELECT description, fixed_price FROM repair_types WHERE id = ?");
$stmt->bind_param("i", $handeling_id);
$stmt->execute();
$stmt->bind_result($handeling_naam, $handeling_prijs);
$stmt->fetch();
$stmt->close();



// ✅ 2. Sla reparatie op
$opmerkingVolledig = "Handeling: $handeling_naam\nOpmerkingen: $opmerkingen";
$stmt = $conn->prepare("INSERT INTO repairs (appointment_id, remarks, status) VALUES (?, ?, 'done')");
$stmt->bind_param("is", $appointment_id, $opmerkingVolledig);
$stmt->execute();
$stmt->close();

// ✅ 3. Zoek of er al een factuur is
$invoice_id = null;
$check = $conn->prepare("SELECT id FROM invoices WHERE appointment_id = ?");
$check->bind_param("i", $appointment_id);
$check->execute();
$check->bind_result($invoice_id);
$check->fetch();
$check->close();

if (!$invoice_id) {
    $stmt = $conn->prepare("INSERT INTO invoices (appointment_id, total_amount, status) VALUES (?, 0, 'concept')");
    $stmt->bind_param("i", $appointment_id);
    $stmt->execute();
    $invoice_id = $stmt->insert_id;
    $stmt->close();
}

// ✅ 4. Voeg handeling toe aan factuur
if ($handeling_prijs) {
    $omschrijving = "Werkzaamheden: $handeling_naam";
    $stmt = $conn->prepare("INSERT INTO invoice_lines (invoice_id, description, amount) VALUES (?, ?, ?)");
    $stmt->bind_param("isd", $invoice_id, $omschrijving, $bedrag);
    $stmt->execute();
    $stmt->close();
}


$namen = $_POST['onderdeel_namen'] ?? [];
$prijzen = $_POST['onderdeel_prijzen'] ?? [];

for ($i = 0; $i < count($namen); $i++) {
    $naam = trim($namen[$i]);
    $prijs = floatval($prijzen[$i]);

    if ($naam && $prijs > 0) {
        // Opslaan in parts-tabel (optioneel, of skip als je geen database-opslag wilt)
        $stmt = $conn->prepare("INSERT INTO parts (name, price) VALUES (?, ?)");
        $stmt->bind_param("sd", $naam, $prijs);
        $stmt->execute();
        $stmt->close();

        // Factuurregel toevoegen
        $omschrijving = "Onderdeel: $naam";
        $stmt = $conn->prepare("INSERT INTO invoice_lines (invoice_id, description, amount) VALUES (?, ?, ?)");
        $stmt->bind_param("isd", $invoice_id, $omschrijving, $prijs);
        $stmt->execute();
        $stmt->close();

    }
}


// ✅ 6. Herbereken totaalbedrag
$res = $conn->prepare("SELECT SUM(amount) FROM invoice_lines WHERE invoice_id = ?");
$res->bind_param("i", $invoice_id);
$res->execute();
$res->bind_result($totaal);
$res->fetch();
$res->close();

$update = $conn->prepare("UPDATE invoices SET total_amount = ? WHERE id = ?");
$update->bind_param("di", $totaal, $invoice_id);
$update->execute();
$update->close();

// ✅ 7. Zet afspraakstatus op 'ready'
$conn->query("UPDATE appointments SET status = 'ready' WHERE id = " . (int)$appointment_id);

header("Location: monteur_dashboard.php");
exit;
