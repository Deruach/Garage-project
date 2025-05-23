<?php
session_start();

// Zorg dat de browser geen pagina's cached zodat 'back' na logout niet werkt
header("Cache-Control: no-cache, no-store, must-revalidate"); // HTTP 1.1
header("Pragma: no-cache"); // HTTP 1.0
header("Expires: 0"); // Proxies

// Check of gebruiker is ingelogd én rol klopt
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'receptionist') {
    header("Location: ../login.php");
    exit;
}

require_once __DIR__ . '/../../app/Core/Database.php';
require_once __DIR__ . '/../../app/Models/Appointment.php';
require_once __DIR__ . '/../../app/Models/User.php';

$config = require '../../config/config.php';
$db = new \App\Core\Database($config['db']);

$appointmentModel = new \App\Models\Appointment($db);
$userModel = new \App\Models\User($db);

$message = '';

// Afspraak status bevestigen of monteur toewijzen
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $appointmentId = $_POST['appointment_id'] ?? null;
    $status = $_POST['status'] ?? null;
    $mechanicId = $_POST['mechanic_id'] ?? null;

        // Als de waarde een lege string is, zet dan op null
        if ($mechanicId === '') {
            $mechanicId = null;
        } else {
            $mechanicId = (int)$mechanicId;  // cast naar int
        }

        if ($mechanicId !== null) {
            $appointmentModel->assignMechanic($appointmentId, $mechanicId);
        }
    $notes = $_POST['notes'] ?? null;

    if ($appointmentId) {
        if ($status !== null) {
            $appointmentModel->updateStatus($appointmentId, $status);
        }
        if ($mechanicId !== null) {
            $appointmentModel->assignMechanic($appointmentId, $mechanicId);
        }
        if ($notes !== null) {
            $appointmentModel->updateNotes($appointmentId, $notes);
        }
        $message = "Afspraak bijgewerkt.";
    }
}

// Ophalen van afspraken voor vandaag (of voor een specifieke dag)
$selectedDate = $_GET['date'] ?? date('Y-m-d');
$appointments = $appointmentModel->getByDate($selectedDate);

// Alle monteurs ophalen om toe te wijzen
$mechanics = $userModel->getUsersByRole('mechanic');
?>

<!DOCTYPE html>
<html lang="nl">
<head>
  <meta charset="UTF-8" />
  <title>Afspraken beheren - Receptionist</title>
  <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-100 ">
  <nav class="bg-gray-900 text-white shadow-md py-4 px-6 flex justify-between items-center">
    <div class="text-2xl font-extrabold tracking-wide text-yellow-400">Luris Garage</div>
    <div class="flex items-center gap-6">
      <a href="../logout.php" class="text-sm text-yellow-400 hover:underline font-medium">Uitloggen</a>
    </div>
  </nav>
<main class="p-6">
<h1 class="text-3xl font-bold mb-6">Afspraken beheren voor <?= htmlspecialchars($selectedDate) ?></h1>

<?php if ($message): ?>
  <div class="mb-4 p-3 bg-green-200 text-green-800 rounded"><?= htmlspecialchars($message) ?></div>
<?php endif; ?>

<form method="GET" class="mb-6">
  <label for="date" class="mr-2 font-semibold">Kies datum:</label>
  <input type="date" id="date" name="date" value="<?= htmlspecialchars($selectedDate) ?>" required>
  <button type="submit" class="ml-2 bg-yellow-400 px-4 py-1 rounded font-semibold hover:bg-yellow-500">Filter</button>
</form>

<table class="min-w-full bg-white rounded shadow">
  <thead>
    <tr class="bg-yellow-300">
      <th class="px-4 py-2 border">Datum</th>
      <th class="px-4 py-2 border">Kenteken</th>
      <th class="px-4 py-2 border">Handeling</th>
      <th class="px-4 py-2 border">Status</th>
      <th class="px-4 py-2 border">Monteur</th>
      <th class="px-4 py-2 border">Notities</th>
      <th class="px-4 py-2 border">Acties</th>
    </tr>
  </thead>
    <tbody>
    <?php if ($appointments): ?>
        <?php foreach ($appointments as $a): ?>
        <tr class="border-b hover:bg-yellow-50">
            <td class="px-4 py-2 whitespace-nowrap"><?= htmlspecialchars($a['appointment_date'] ?? '') ?></td>
            <td class="px-4 py-2 uppercase whitespace-nowrap"><?= htmlspecialchars($a['license_plate'] ?? '') ?></td>
            <td class="px-4 py-2"><?= htmlspecialchars($a['handeling'] ?? '') ?></td>

            <!-- Status dropdown -->
            <td class="px-4 py-2 whitespace-nowrap">
            <form method="POST" class="inline-block">
                <input type="hidden" name="appointment_id" value="<?= htmlspecialchars($a['id']) ?>">
                <select name="status" onchange="this.form.submit()" class="border rounded px-2 py-1 w-full">
                <?php
                    $statuses = ['pending', 'confirmed', 'in_progress', 'ready', 'completed'];
                    foreach ($statuses as $status):
                    $selected = ($status === ($a['status'] ?? '')) ? 'selected' : '';
                ?>
                    <option value="<?= htmlspecialchars($status) ?>" <?= $selected ?>><?= ucfirst(str_replace('_', ' ', $status)) ?></option>
                <?php endforeach; ?>
                </select>
            </form>
            </td>

            <!-- Monteur dropdown -->
            <td class="px-4 py-2 whitespace-nowrap">
            <form method="POST" class="inline-block w-full">
                <input type="hidden" name="appointment_id" value="<?= htmlspecialchars($a['id']) ?>">
                <select name="mechanic_id" onchange="this.form.submit()" class="border rounded px-2 py-1 w-full">
                <option value="">-- Selecteer monteur --</option>
                <?php foreach ($mechanics as $m):
                    $sel = ($m['id'] == ($a['mechanic_id'] ?? '')) ? 'selected' : '';
                ?>
                    <option value="<?= htmlspecialchars($m['id']) ?>" <?= $sel ?>><?= htmlspecialchars($m['name']) ?></option>
                <?php endforeach; ?>
                </select>
            </form>
            </td>

            <!-- Notities -->
            <td class="px-4 py-2">
            <form method="POST" class="">
                <input type="hidden" name="appointment_id" value="<?= htmlspecialchars($a['id']) ?>">
                <input 
                type="text" 
                name="notes" 
                value="<?= htmlspecialchars($a['notes'] ?? '') ?>" 
                class="border rounded px-2 py-1 w-full" 
                onchange="this.form.submit()" 
                placeholder="Voeg notitie toe"
                >
            </form>
            </td>

            <td class="px-4 py-2 text-center text-gray-500 whitespace-nowrap">-</td>
        </tr>
        <?php endforeach; ?>
    <?php else: ?>
        <tr>
        <td colspan="7" class="text-center py-6 text-gray-400">Geen afspraken gevonden voor deze datum.</td>
        </tr>
    <?php endif; ?>
    </tbody>
</table>
</main>
</body>
</html>