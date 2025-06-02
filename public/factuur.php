<?php
session_start();

use App\Core\Database;
use App\Models\Invoice;

require_once __DIR__ . '/../app/Core/Database.php';
require_once __DIR__ . '/../app/Models/Invoice.php';

$config = require __DIR__ . '/../config/config.php';
$db = new Database($config['db']);

$invoiceModel = new Invoice($db);
$invoiceId = $_GET['invoice_id'] ?? null;



if (!$invoiceId) {
    die('Geen factuur-ID meegegeven.');
}

$factuur = $invoiceModel->getById((int)$invoiceId);
if (!$factuur) {
    die('Factuur niet gevonden.');
}
$details = $invoiceModel->getDetailsByAppointmentId($factuur['appointment_id']);
$reparatieInfo = $details[0] ?? null;

?>

<!DOCTYPE html>
<html lang="nl">
<head>
  <meta charset="UTF-8">
  <title>Factuur bekijken</title>
  <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-100 p-10 text-gray-800">
  <div class="max-w-xl mx-auto bg-white border border-yellow-300 shadow p-6 rounded-lg">
    <h1 class="text-2xl font-bold text-yellow-600 mb-4">Luris Garage – Factuur</h1>
    <p><strong>Factuurnummer:</strong> <?= $factuur['id'] ?></p>
    <p><strong>Datum:</strong> <?= $factuur['issue_date'] ?></p>
    <p><strong>Status:</strong> <?= $factuur['paid'] ? '✅ Betaald' : '❌ Openstaand' ?></p>

    <hr class="my-4">
<h2 class="text-xl font-semibold mt-6 mb-2">Gewerkte handeling</h2>
<p><strong><?= htmlspecialchars($reparatieInfo['repair_description'] ?? '-') ?></strong> — €<?= number_format($reparatieInfo['fixed_price'] ?? 0, 2, ',', '.') ?></p>

<?php if (!empty($details)): ?>
  <h2 class="text-xl font-semibold mt-6 mb-2">Gebruikte onderdelen</h2>
  <ul class="list-disc list-inside text-sm text-gray-700">
    <?php foreach ($details as $regel): ?>
      <?php if ($regel['part_name']): ?>
        <li>
          <?= htmlspecialchars($regel['part_name']) ?> (<?= $regel['quantity'] ?> × €<?= number_format($regel['part_price'], 2, ',', '.') ?>)
        </li>
      <?php endif; ?>
    <?php endforeach; ?>
  </ul>
<?php endif; ?>

    <h2 class="text-xl font-semibold mb-2">Totaal te betalen:</h2>
    <p class="text-lg font-bold text-green-700">€<?= number_format($factuur['total_amount'], 2, ',', '.') ?></p>

    <?php if (!$factuur['paid']): ?>
      <form method="POST" action="simuleer_betaling.php" class="mt-6">
        <input type="hidden" name="invoice_id" value="<?= $factuur['id'] ?>">
        <button class="bg-green-600 text-white px-4 py-2 rounded hover:bg-green-700">Simuleer betaling</button>
      </form>
    <?php endif; ?>

    <div class="mt-4">
      <a href="factuur_download.php?invoice_id=<?= $factuur['id'] ?>" class="text-blue-600 underline">Download factuur als PDF</a>
    </div>
  </div>
</body>
</html>
