<?php
session_start();

header("Cache-Control: no-cache, no-store, must-revalidate");
header("Pragma: no-cache");
header("Expires: 0");

if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'customer') {
    header("Location: ../login.php");
    exit;
}

require_once __DIR__ . '/../../../app/Core/Database.php';
require_once __DIR__ . '/../../../app/Models/Appointment.php';
require_once __DIR__ . '/../../../app/Models/Vehicle.php';
require_once __DIR__ . '/../../../app/Models/RepairType.php';

$config = require '../../../config/config.php';
$db = new \App\Core\Database($config['db']);

$appointmentModel = new \App\Models\Appointment($db);
$vehicleModel = new \App\Models\Vehicle($db);
$repairTypeModel = new \App\Models\RepairType($db);

$message = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $datum = $_POST['datum'] ?? '';
    $kenteken = strtoupper(trim($_POST['kenteken'] ?? ''));
    $repairTypeId = $_POST['handeling'] ?? '';
    $opmerkingen = trim($_POST['opmerkingen'] ?? '');

    // Check verplichte velden
    if (empty($datum) || empty($kenteken) || empty($repairTypeId)) {
        $message = "Vul alle verplichte velden in.";
    } else {
        // Haal voertuig op op basis van kenteken
        $vehicle = $vehicleModel->getByLicensePlate($kenteken);

        // Check of voertuig bestaat én bij de ingelogde klant hoort
        if (!$vehicle || $vehicle['customer_id'] != $_SESSION['user_id']) {
            $message = "Kenteken niet gevonden of hoort niet bij jouw account.";
        }
        // Check of er minder dan 4 afspraken zijn op deze datum
        elseif (!$appointmentModel->isDateAvailable($datum)) {
            $message = "Er kunnen maximaal 4 afspraken per dag worden gemaakt. Kies een andere datum.";
        } else {
            // Probeer afspraak aan te maken
            $success = $appointmentModel->create($_SESSION['user_id'], $vehicle['id'], $datum, $opmerkingen, (int)$repairTypeId);

            if ($success) {
                $message = "Afspraak succesvol aangemaakt!";
            } else {
                $message = "Er ging iets mis bij het aanmaken van de afspraak.";
            }
        }
    }
}

$afspraken = $appointmentModel->getByCustomerId($_SESSION['user_id']);
$alleDatums = $appointmentModel->getAllAppointmentDates();
$repairTypes = $repairTypeModel->getAll();
?>
<!DOCTYPE html>
<html lang="nl" class="scroll-smooth">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1" />
  <title>Afspraak maken - Luris Garage</title>
  <script src="https://cdn.tailwindcss.com"></script>
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/flatpickr/dist/flatpickr.min.css">
  <style>
    .fade-in {
      animation: fadeIn 0.5s ease forwards;
      opacity: 0;
    }
    @keyframes fadeIn {
      to { opacity: 1; }
    }
  </style>
</head>
<body class="bg-[#f7f4f0] text-gray-800 min-h-screen flex flex-col">

  <!-- Navigatie -->
  <nav class="bg-[#1f2937] text-white shadow-md py-4 px-6 flex justify-between items-center sticky top-0 z-30">
    <a href="../customer_dashboard.php" class="text-2xl font-extrabold tracking-wide text-yellow-400 hover:underline">Luris Garage</a>
    <a href="../../logout.php" class="hover:text-yellow-400 transition">Uitloggen</a>
  </nav>

  <main class="flex-grow max-w-5xl mx-auto p-6 md:p-12">

    <!-- Afspraakformulier -->
    <section class="bg-white rounded-lg shadow-md border border-yellow-300 p-8 mb-12">
      <h1 class="text-3xl font-bold text-gray-800 mb-6">Plan een afspraak</h1>
      <p class="mb-6 text-yellow-600 font-semibold">
        Nog geen voertuig geregistreerd? <a href="vehicles.php" class="underline hover:text-yellow-400">Registreer je auto hier</a>.
      </p>

      <?php if ($message): ?>
        <div class="mb-6 p-4 rounded-md text-center <?= strpos($message, 'succesvol') !== false ? 'bg-green-100 text-green-800' : 'bg-yellow-200 text-yellow-900' ?> fade-in">
          <?= htmlspecialchars($message) ?>
        </div>
      <?php endif; ?>

      <form method="POST" novalidate class="grid grid-cols-1 md:grid-cols-2 gap-8">
        <!-- Datum -->
        <div>
          <label for="datum" class="block mb-2 font-semibold text-gray-700">Kies een datum <span class="text-red-500">*</span></label>
          <input type="text" id="datum" name="datum" required
                 class="w-full rounded-md border border-gray-300 px-4 py-3 focus:outline-none focus:ring-2 focus:ring-yellow-400"
                 value="<?= isset($_POST['datum']) ? htmlspecialchars($_POST['datum']) : '' ?>">
        </div>

        <!-- Formulier rechterkant -->
        <div class="flex flex-col space-y-5">
          <!-- Kenteken -->
          <label for="kenteken" class="block font-semibold text-gray-700">Kenteken <span class="text-red-500">*</span></label>
          <select id="kenteken" name="kenteken" required
                  class="w-full rounded-md border border-gray-300 px-4 py-3 focus:outline-none focus:ring-2 focus:ring-yellow-400">
            <option value="">-- Selecteer kenteken --</option>
            <?php foreach ($vehicleModel->getVehiclesByCustomerId($_SESSION['user_id']) as $v): ?>
              <option value="<?= htmlspecialchars($v['license_plate']) ?>" <?= ($_POST['kenteken'] ?? '') === $v['license_plate'] ? 'selected' : '' ?>>
                <?= htmlspecialchars(strtoupper($v['license_plate'])) ?>
              </option>
            <?php endforeach; ?>
          </select>

          <!-- Handeling -->
          <label for="handeling" class="block font-semibold text-gray-700">Selecteer werkzaamheden <span class="text-red-500">*</span></label>
          <select id="handeling" name="handeling" required
                  class="w-full rounded-md border border-gray-300 px-4 py-3 focus:outline-none focus:ring-2 focus:ring-yellow-400">
            <option value="">-- Kies een optie --</option>
            <?php foreach ($repairTypes as $type): ?>
              <option value="<?= htmlspecialchars($type['id']) ?>" <?= ($_POST['handeling'] ?? '') == $type['id'] ? 'selected' : '' ?>>
                <?= htmlspecialchars($type['description']) ?>
              </option>
            <?php endforeach; ?>
          </select>

          <!-- Opmerkingen -->
          <label for="opmerkingen" class="block font-semibold text-gray-700">Opmerkingen (optioneel)</label>
          <textarea id="opmerkingen" name="opmerkingen" rows="4"
                    class="w-full rounded-md border border-gray-300 px-4 py-3 resize-none focus:outline-none focus:ring-2 focus:ring-yellow-400"
                    placeholder="Bijvoorbeeld: vreemde geluiden bij het starten..."><?= htmlspecialchars($_POST['opmerkingen'] ?? '') ?></textarea>

          <!-- Verstuur -->
          <button type="submit" class="mt-4 bg-yellow-500 text-black font-bold py-3 rounded-md hover:bg-yellow-400 transition">
            Afspraak aanvragen
          </button>
        </div>
      </form>
    </section>

    <!-- Overzicht van afspraken -->
    <section class="bg-white rounded-lg shadow-md border border-yellow-200 p-8">
      <h2 class="text-2xl font-bold text-gray-800 mb-6">Mijn gemaakte afspraken</h2>

      <div class="overflow-x-auto">
        <table class="min-w-full text-left text-sm border-collapse">
          <thead>
            <tr>
              <th class="px-6 py-3 border-b font-semibold text-gray-600">Datum</th>
              <th class="px-6 py-3 border-b font-semibold text-gray-600">Kenteken</th>
              <th class="px-6 py-3 border-b font-semibold text-gray-600">Handeling</th>
              <th class="px-6 py-3 border-b font-semibold text-gray-600">Status</th>
              <th class="px-6 py-3 border-b font-semibold text-gray-600">Actie</th>
            </tr>
          </thead>
          <tbody>
            <?php if ($afspraken && count($afspraken) > 0): ?>
              <?php foreach ($afspraken as $afspraak): ?>
                <tr class="hover:bg-yellow-50 transition">
                  <td class="px-6 py-4 border-b"><?= htmlspecialchars($afspraak['appointment_date']) ?></td>
                  <td class="px-6 py-4 border-b uppercase"><?= htmlspecialchars($afspraak['license_plate']) ?></td>
                  <td class="px-6 py-4 border-b"><?= htmlspecialchars($afspraak['handeling']) ?></td>
                  <td class="px-6 py-4 border-b"><?= htmlspecialchars($afspraak['status']) ?></td>
                  <td class="px-6 py-4 border-b text-gray-500">-</td>
                </tr>
              <?php endforeach; ?>
            <?php else: ?>
              <tr>
                <td colspan="5" class="text-center py-6 text-gray-400">Je hebt nog geen afspraken gepland.</td>
              </tr>
            <?php endif; ?>
          </tbody>
        </table>
      </div>
    </section>
  </main>

  <script src="https://cdn.jsdelivr.net/npm/flatpickr"></script>
  <script>
    const bezetteDatums = <?= json_encode($alleDatums) ?>;
    flatpickr("#datum", {
      inline: true,
      dateFormat: "Y-m-d",
      minDate: "today",
      disable: bezetteDatums
    });

  </script>

  <footer class="bg-[#1f2937] text-white text-center py-6 border-t text-sm">
    &copy; 2025 Luris Garage. Gedreven door vakmanschap.
  </footer>

</body>
</html>
