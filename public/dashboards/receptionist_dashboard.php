<?php
session_start();

// Zorg dat de browser geen pagina's cached zodat 'back' na logout niet werkt
header("Cache-Control: no-cache, no-store, must-revalidate");
header("Pragma: no-cache");
header("Expires: 0");

// Check of gebruiker is ingelogd én rol klopt
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'receptionist') {
    header("Location: ../login.php");
    exit;
}

require_once __DIR__ . '/../../app/Core/Database.php';
require_once __DIR__ . '/../../app/Models/Appointment.php';
require_once __DIR__ . '/../../app/Models/User.php';
require_once __DIR__ . '/../../app/Models/Invoice.php';

$config = require '../../config/config.php';
$db = new \App\Core\Database($config['db']);

$appointmentModel = new \App\Models\Appointment($db);
$userModel = new \App\Models\User($db);
$invoiceModel = new \App\Models\Invoice($db);

$message = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $appointmentId = $_POST['appointment_id'] ?? null;
    $status = $_POST['status'] ?? null;
    $mechanicId = $_POST['mechanic_id'] ?? null;
    $notes = $_POST['notes'] ?? null;
    $markCollected = isset($_POST['collected']);

    if ($mechanicId === '' || $mechanicId === null || $mechanicId <= 0) {
        $mechanicId = null;
    }

    if ($appointmentId) {
        if ($status !== null) {
    $appointmentModel->updateStatus($appointmentId, $status);

    if ($status === 'ready') {
        // Simuleer bedrag of bereken op basis van reparaties later
        $totaal = $invoiceModel->berekenTotaalBedragVoorAppointment($appointmentId);
        $invoiceModel->createIfNotExists($appointmentId, $totaal);
    }
}

        if ($mechanicId !== null) {
            $appointmentModel->assignMechanic($appointmentId, $mechanicId);
        }
        if ($notes !== null) {
            $appointmentModel->updateNotes($appointmentId, $notes);
        }
        if ($markCollected) {
            $appointmentModel->updateStatus($appointmentId, 'completed');
        }
        $message = "Afspraak bijgewerkt.";
    }
}

$selectedDate = $_GET['date'] ?? date('Y-m-d');
$selectedStatus = $_GET['status'] ?? null;
$appointments = $appointmentModel->getByDateAndStatus($selectedDate, $selectedStatus);
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
  <div class="mb-4 p-3 bg-green-200 text-green-800 rounded"> <?= htmlspecialchars($message) ?> </div>
<?php endif; ?>

<form method="GET" class="mb-6 flex gap-4 items-center">
  <div>
    <label for="date" class="font-semibold">Kies datum:</label>
    <input type="date" id="date" name="date" value="<?= htmlspecialchars($selectedDate) ?>" required>
  </div>
  <div>
    <label for="status" class="font-semibold">Status:</label>
    <select name="status" id="status" class="border rounded px-2 py-1">
      <option value="">-- Alle statussen --</option>
      <?php foreach (["pending", "confirmed", "in_progress", "ready", "completed"] as $statusOption): ?>
        <option value="<?= $statusOption ?>" <?= $selectedStatus === $statusOption ? 'selected' : '' ?>>
          <?= ucfirst(str_replace('_', ' ', $statusOption)) ?>
        </option>
      <?php endforeach; ?>
    </select>
  </div>
  <button type="submit" class="bg-yellow-400 px-4 py-1 rounded font-semibold hover:bg-yellow-500">Filter</button>
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
      <th class="px-4 py-2 border">Factuur</th>
      <th class="px-4 py-2 border">Factuur betaald</th>
      <th class="px-4 py-2 border">Acties</th>
    </tr>
  </thead>
  <tbody>
  <?php if ($appointments): ?>
    <?php foreach ($appointments as $a): 
        $invoice = $invoiceModel->getByAppointmentId($a['id']);
    ?>
    <tr class="border-b hover:bg-yellow-50">
      <td class="px-4 py-2"> <?= htmlspecialchars($a['appointment_date']) ?> </td>
      <td class="px-4 py-2"> <?= htmlspecialchars($a['license_plate']) ?> </td>
      <td class="px-4 py-2"> <?= htmlspecialchars($a['handeling']) ?> </td>
      <td class="px-4 py-2">
        <form method="POST">
          <input type="hidden" name="appointment_id" value="<?= $a['id'] ?>">
          <select name="status" onchange="this.form.submit()" class="border rounded px-2 py-1 w-full">
            <?php foreach (["pending", "confirmed", "in_progress", "ready", "completed"] as $status): ?>
              <option value="<?= $status ?>" <?= ($status === $a['status']) ? 'selected' : '' ?>>
                <?= ucfirst(str_replace('_', ' ', $status)) ?>
              </option>
            <?php endforeach; ?>
          </select>
        </form>
      </td>
      <td class="px-4 py-2">
        <form method="POST">
          <input type="hidden" name="appointment_id" value="<?= $a['id'] ?>">
          <select name="mechanic_id" onchange="this.form.submit()" class="border rounded px-2 py-1 w-full">
            <option value="">-- Selecteer --</option>
            <?php foreach ($mechanics as $m): ?>
              <option value="<?= $m['id'] ?>" <?= ($m['id'] == $a['mechanic_id']) ? 'selected' : '' ?>>
                <?= htmlspecialchars($m['name']) ?>
              </option>
            <?php endforeach; ?>
          </select>
        </form>
      </td>
      <td class="px-4 py-2">
        <form method="POST">
          <input type="hidden" name="appointment_id" value="<?= $a['id'] ?>">
          <input type="text" name="notes" value="<?= htmlspecialchars($a['notes'] ?? '') ?>" class="border rounded px-2 py-1 w-full" onchange="this.form.submit()" placeholder="Voeg notitie toe">
        </form>
      </td>
     

      <td class="px-4 py-2 text-center">
        <?= ($invoice && $invoice['paid']) ? '✅' : '❌' ?>
      </td>
      <td class="px-4 py-2 text-center whitespace-nowrap">
  <?php if ($invoice): ?>
    <a href="/factuur.php?invoice_id=<?= $invoice['id'] ?>" class="text-blue-600 underline">
      Bekijk / Verstuur
    </a>
  <?php else: ?>
    <span class="text-gray-400 italic">Geen factuur</span>
  <?php endif; ?>
</td>

      <td class="px-4 py-2 text-center">
        <?php if (($a['status'] === 'ready') && $invoice && $invoice['paid']): ?>
          <form method="POST">
            <input type="hidden" name="appointment_id" value="<?= $a['id'] ?>">
            <input type="hidden" name="collected" value="1">
            <button type="submit" class="bg-green-600 text-white px-3 py-1 rounded hover:bg-green-700">Auto opgehaald</button>
          </form>
        <?php else: ?>
          -
        <?php endif; ?>
      </td>
    </tr>
    <?php endforeach; ?>
  <?php else: ?>
    <tr><td colspan="8" class="text-center py-6 text-gray-400">Geen afspraken gevonden voor deze datum.</td></tr>
  <?php endif; ?>
  </tbody>
</table>
</main>
</body>
</html>
