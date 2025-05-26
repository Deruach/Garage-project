<?php
session_start();

// Verhinderen caching
header("Cache-Control: no-cache, no-store, must-revalidate");
header("Pragma: no-cache");
header("Expires: 0");

// Check login en rol
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'customer') {
    header("Location: ../login.php");
    exit;
}

require_once __DIR__ . '/../../../app/Core/Database.php';
require_once __DIR__ . '/../../../app/Models/Vehicle.php';

$config = require '../../../config/config.php';
$db = new \App\Core\Database($config['db']);
$vehicleModel = new \App\Models\Vehicle($db);

$message = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $kenteken = strtoupper(trim($_POST['kenteken'] ?? ''));
    $brand = trim($_POST['brand'] ?? '');
    $modelName = trim($_POST['model'] ?? '');
    $year = trim($_POST['year'] ?? '');

    if (!$kenteken) {
        $message = "Vul een kenteken in.";
    } else {
        // Check of kenteken al bestaat voor deze klant
        $existing = $vehicleModel->getByLicensePlate($kenteken);
        if ($existing && $existing['customer_id'] == $_SESSION['user_id']) {
            $message = "Dit kenteken staat al bij jouw voertuigen geregistreerd.";
        } else {
            $success = $vehicleModel->addVehicle($_SESSION['user_id'], $kenteken, $brand, $modelName, $year ?: null);
            if ($success) {
                $message = "Voertuig succesvol toegevoegd!";
            } else {
                $message = "Er ging iets mis bij het toevoegen.";
            }
        }
    }
}

// Haal voertuigen van deze klant
$vehicles = $vehicleModel->getVehiclesByCustomerId($_SESSION['user_id']);
?>

<!DOCTYPE html>
<html lang="nl" class="scroll-smooth">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <title>Mijn Voertuigen - Luris Garage</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-[#f7f4f0] min-h-screen flex flex-col">

<nav class="bg-[#1f2937] text-white shadow-md py-4 px-6 flex justify-between items-center sticky top-0 z-30">
    <a href="../customer_dashboard.php" class="text-2xl font-extrabold tracking-wide text-yellow-400 hover:underline">Luris Garage</a>
    <a href="../logout.php" class="hover:text-yellow-400 transition">Uitloggen</a>
</nav>

<main class="flex-grow max-w-4xl mx-auto p-6 md:p-12">

    <section class="bg-white rounded-lg shadow-md border border-yellow-300 p-8 mb-12">
        <h1 class="text-3xl font-bold text-gray-800 mb-6">Mijn voertuigen</h1>

        <?php if ($message): ?>
            <div class="mb-6 p-4 rounded-md text-center
            <?= strpos($message, 'succesvol') !== false ? 'bg-green-100 text-green-800' : 'bg-yellow-200 text-yellow-900' ?>">
                <?= htmlspecialchars($message) ?>
            </div>
        <?php endif; ?>

        <form method="POST" class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-8">

            <div>
                <label for="kenteken" class="block font-semibold mb-2 text-gray-700">Kenteken <span class="text-red-500">*</span></label>
                <input id="kenteken" name="kenteken" type="text" required
                       class="w-full rounded-md border border-gray-300 px-4 py-3 focus:outline-none focus:ring-2 focus:ring-yellow-400 uppercase"
                       placeholder="Bijv. XX-123-YY" value="<?= isset($_POST['kenteken']) ? htmlspecialchars($_POST['kenteken']) : '' ?>">
            </div>

            <div>
                <label for="brand" class="block font-semibold mb-2 text-gray-700">Merk</label>
                <input id="brand" name="brand" type="text"
                       class="w-full rounded-md border border-gray-300 px-4 py-3 focus:outline-none focus:ring-2 focus:ring-yellow-400"
                       placeholder="Bijv. Toyota" value="<?= isset($_POST['brand']) ? htmlspecialchars($_POST['brand']) : '' ?>">
            </div>

            <div>
                <label for="model" class="block font-semibold mb-2 text-gray-700">Model</label>
                <input id="model" name="model" type="text"
                       class="w-full rounded-md border border-gray-300 px-4 py-3 focus:outline-none focus:ring-2 focus:ring-yellow-400"
                       placeholder="Bijv. Corolla" value="<?= isset($_POST['model']) ? htmlspecialchars($_POST['model']) : '' ?>">
            </div>

            <div>
                <label for="year" class="block font-semibold mb-2 text-gray-700">Bouwjaar</label>
                <input id="year" name="year" type="number" min="1900" max="<?= date('Y') ?>"
                       class="w-full rounded-md border border-gray-300 px-4 py-3 focus:outline-none focus:ring-2 focus:ring-yellow-400"
                       placeholder="Bijv. 2018" value="<?= isset($_POST['year']) ? htmlspecialchars($_POST['year']) : '' ?>">
            </div>

            <div class="md:col-span-2">
                <button type="submit" class="w-full bg-yellow-500 text-black font-bold py-3 rounded-md hover:bg-yellow-400 transition">
                    Voertuig toevoegen
                </button>
            </div>
        </form>

        <h2 class="text-2xl font-semibold text-gray-800 mb-4">Geregistreerde voertuigen</h2>

        <?php if ($vehicles && count($vehicles) > 0): ?>
            <table class="min-w-full bg-white rounded-md border border-gray-300 shadow-sm overflow-hidden">
                <thead class="bg-yellow-100">
                    <tr>
                        <th class="text-left px-6 py-3 font-semibold text-gray-700">Kenteken</th>
                        <th class="text-left px-6 py-3 font-semibold text-gray-700">Merk</th>
                        <th class="text-left px-6 py-3 font-semibold text-gray-700">Model</th>
                        <th class="text-left px-6 py-3 font-semibold text-gray-700">Bouwjaar</th>
                    </tr>
                </thead>
                <tbody>
                <?php foreach ($vehicles as $v): ?>
                    <tr class="border-t hover:bg-yellow-50 transition">
                        <td class="px-6 py-4 uppercase font-mono tracking-wider"><?= htmlspecialchars($v['license_plate']) ?></td>
                        <td class="px-6 py-4"><?= htmlspecialchars($v['brand'] ?? '-') ?></td>
                        <td class="px-6 py-4"><?= htmlspecialchars($v['model'] ?? '-') ?></td>
                        <td class="px-6 py-4"><?= htmlspecialchars($v['year'] ?? '-') ?></td>
                    </tr>
                <?php endforeach; ?>
                </tbody>
            </table>
        <?php else: ?>
            <p class="text-gray-500 italic">Je hebt nog geen voertuigen toegevoegd.</p>
        <?php endif; ?>

    </section>

</main>

<footer class="bg-[#1f2937] text-white text-center py-6 border-t text-sm">
    &copy; 2025 Luris Garage. Gedreven door vakmanschap.
</footer>

</body>
</html>
