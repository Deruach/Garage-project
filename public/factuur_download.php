<?php
require_once __DIR__ . '/../vendor/autoload.php';
require_once __DIR__ . '/../app/Core/Database.php';
require_once __DIR__ . '/../app/Models/Invoice.php';

use App\Core\Database;
use App\Models\Invoice;
use Dompdf\Dompdf;


$config = require __DIR__ . '/../config/config.php';
$db = new Database($config['db']);

$invoiceModel = new Invoice($db);

$invoiceId = $_GET['invoice_id'] ?? null;
if (!$invoiceId) {
    die('Geen factuur-ID opgegeven.');
}

$factuur = $invoiceModel->getById((int)$invoiceId);
if (!$factuur) {
    die('Factuur niet gevonden.');
}

$details = $invoiceModel->getDetailsByAppointmentId($factuur['appointment_id']);
$reparatieInfo = $details[0] ?? null;

ob_start();
?>

<h1 style="color:#d97706;">Luris Garage – Factuur</h1>
<p><strong>Factuurnummer:</strong> <?= $factuur['id'] ?></p>
<p><strong>Datum:</strong> <?= $factuur['issue_date'] ?></p>
<p><strong>Status:</strong> <?= $factuur['paid'] ? '✅ Betaald' : '❌ Openstaand' ?></p>
<hr>
<h3>Handeling</h3>
<p><?= $reparatieInfo['repair_description'] ?? '-' ?> – €<?= number_format($reparatieInfo['fixed_price'] ?? 0, 2, ',', '.') ?></p>

<?php if (!empty($details)): ?>
<h3>Gebruikte onderdelen</h3>
<ul>
<?php foreach ($details as $regel): ?>
  <?php if ($regel['part_name']): ?>
    <li><?= $regel['part_name'] ?> (<?= $regel['quantity'] ?> × €<?= number_format($regel['part_price'], 2, ',', '.') ?>)</li>
  <?php endif; ?>
<?php endforeach; ?>
</ul>
<?php endif; ?>

<h3>Totaalbedrag:</h3>
<p>€<?= number_format($factuur['total_amount'], 2, ',', '.') ?></p>

<?php
$html = ob_get_clean();

$dompdf = new Dompdf();
$dompdf->loadHtml($html);
$dompdf->setPaper('A4', 'portrait');
$dompdf->render();

// Download de PDF
$dompdf->stream("factuur-{$factuur['id']}.pdf", ["Attachment" => true]);
exit;
