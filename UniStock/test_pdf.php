<?php
require 'vendor/autoload.php';
$app = require_once __DIR__.'/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

$product = \App\Models\Producto::first();
if (!$product) { echo "No products\n"; exit; }

$entries = \App\Models\Entrada::where('producto_id', $product->id)->get();
$exits   = \App\Models\Salida::where('producto_id', $product->id)->get();

try {
    $pdf = \Barryvdh\DomPDF\Facade\Pdf::loadView('reports.product_history', [
        'product'       => $product,
        'entries'       => $entries,
        'exits'         => $exits,
        'generated_at'  => now()->format('d/m/Y H:i:s'),
        'generated_by'  => 'Test Admin',
    ]);
    $pdf->output();
    echo "PDF generated successfully\n";
} catch (\Exception $e) {
    echo "ERROR: " . $e->getMessage() . "\n";
}
