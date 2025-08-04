<?php

namespace App\Http\Controllers;

use App\Models\UrfavShopeeProduct;
use Illuminate\Http\Request;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;
use Illuminate\Http\JsonResponse;
use Maatwebsite\Excel\Facades\Excel;
use PhpOffice\PhpSpreadsheet\IOFactory;

class UrfavController extends Controller
{
    public function getValidationStatus(): array
    {
        $messages = [];

        // Import Status
        $totalProducts = UrfavShopeeProduct::count();
        $messages[] = ['type' => 'info', 'text' => "Total: {$totalProducts} produk"];

        $invalidPrices = UrfavShopeeProduct::where('jakmall_harga', '<=', 0)->count();
        if ($invalidPrices > 0) {
            $messages[] = ['type' => 'error', 'text' => "Import: {$invalidPrices} harga tidak valid"];
        }

        $emptyStock = UrfavShopeeProduct::whereNotIn('jakmall_stock', ['tersedia', 'tidak'])->count();
        if ($emptyStock > 0) {
            $messages[] = ['type' => 'error', 'text' => "Import: {$emptyStock} status stock tidak valid"];
        }

        // Sync Status
        $synced = UrfavShopeeProduct::where('shopee_harga', '>', 0)->count();
        $messages[] = ['type' => 'success', 'text' => "Sync: {$synced} produk tersinkronisasi"];

        $noMargin = UrfavShopeeProduct::whereNull('shopee_margin')->orWhere('shopee_margin', 0)->count();
        if ($noMargin > 0) {
            $messages[] = ['type' => 'warning', 'text' => "Sync: {$noMargin} produk tanpa margin"];
        }

        $emptySku = UrfavShopeeProduct::whereNull('shopee_sku')->orWhere('shopee_sku', '')->count();
        if ($emptySku > 0) {
            $messages[] = ['type' => 'error', 'text' => "Sync: {$emptySku} produk tanpa SKU"];
        }

        // Urutan Status
        $hasUrutan = UrfavShopeeProduct::whereNotNull('shopee_urut')->count();
        $messages[] = ['type' => 'success', 'text' => "Urutan: {$hasUrutan} produk berurutan"];

        $emptyShopeeId = UrfavShopeeProduct::whereNull('shopee_id')->orWhere('shopee_id', '')->count();
        if ($emptyShopeeId > 0) {
            $messages[] = ['type' => 'warning', 'text' => "Urutan: {$emptyShopeeId} produk tanpa Shopee ID"];
        }

        $duplicateUrutan = UrfavShopeeProduct::select('shopee_urut')
            ->whereNotNull('shopee_urut')
            ->groupBy('shopee_urut')
            ->havingRaw('COUNT(*) > 1')
            ->count();
        if ($duplicateUrutan > 0) {
            $messages[] = ['type' => 'error', 'text' => "Urutan: {$duplicateUrutan} urutan duplikat"];
        }

        // Ready for export
        $readyExport = UrfavShopeeProduct::whereNotNull('shopee_sku')
            ->whereNotNull('shopee_id')
            ->whereNotNull('shopee_urut')
            ->where('shopee_harga', '>', 0)
            ->count();

        return [
            'messages' => $messages,
            'ready_export' => $readyExport,
            'total_products' => $totalProducts
        ];
    }

    public function index(Request $request): View
    {
        $query = UrfavShopeeProduct::query();

        // Search functionality
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('jakmall_sku', 'LIKE', "%{$search}%")
                    ->orWhere('shopee_id', 'LIKE', "%{$search}%")
                    ->orWhere('shopee_sku', 'LIKE', "%{$search}%");
            });
        }

        $products = $query->orderBy('shopee_urut')
            ->orderBy('created_at', 'desc')
            ->paginate(20)
            ->withQueryString();

        // Get validation status
        $validation = $this->getValidationStatus();

        return view('urfav.index', compact('products', 'validation'));
    }

    private function parseJakmallStock(string $stockText): string
    {
        $stockText = strtolower(trim($stockText));

        // Handle "Stok tersedia"
        if (strpos($stockText, 'tersedia') !== false) {
            return 'tersedia';
        }

        // Handle "Stok habis"
        if (strpos($stockText, 'habis') !== false) {
            return 'tidak';
        }

        // Handle "Stok sisa X" - extract number
        if (preg_match('/sisa\s+(\d+)/', $stockText, $matches)) {
            $sisaStock = intval($matches[1]);
            return $sisaStock > 0 ? 'tersedia' : 'tidak';
        }

        // Fallback untuk format lain
        return in_array($stockText, ['tersedia', 'available', '1', 'true', 'ada']) ? 'tersedia' : 'tidak';
    }

    public function store(Request $request): RedirectResponse
    {
        $request->validate([
            'jakmall_sku' => 'required|string|max:255|unique:urfav_shopee_products,jakmall_sku',
            'jakmall_harga' => 'required|numeric|min:0',
            'jakmall_stock' => 'required|in:tersedia,tidak',
            'shopee_id' => 'nullable|string|max:255',
            'shopee_margin' => 'nullable|numeric|min:0|max:100'
        ]);

        $product = new UrfavShopeeProduct($request->all());

        // Set default values
        $product->shopee_sku = $product->jakmall_sku;
        $product->shopee_stock = $product->calculateShopeeStock();

        if ($product->shopee_margin) {
            $product->shopee_harga = $product->calculateShopeePrice();
        }

        $product->save();

        return back()->with('success', 'Produk berhasil ditambahkan');
    }

    public function importJakmall(Request $request): RedirectResponse
    {
        $request->validate([
            'jakmall_file' => 'required|file|mimes:xlsx,xls,csv'
        ]);

        try {
            $file = $request->file('jakmall_file');
            $extension = $file->getClientOriginalExtension();

            if (in_array($extension, ['xlsx', 'xls'])) {
                // Handle Excel files
                $spreadsheet = IOFactory::load($file->getPathname());
                $data = $spreadsheet->getActiveSheet()->toArray();
                array_shift($data); // Skip header
            } else {
                // Handle CSV files
                $handle = fopen($file->getPathname(), 'r');
                fgetcsv($handle); // Skip header
                $data = [];
                while (($row = fgetcsv($handle)) !== FALSE) {
                    $data[] = $row;
                }
                fclose($handle);
            }

            $imported = 0;
            $errors = [];

            foreach ($data as $row) {
                if (empty($row[0])) continue; // Skip empty SKU

                $sku = trim($row[0]);
                $harga = floatval($row[1] ?? 0);
                $stockRaw = trim($row[2] ?? '');

                // Parse stock dari berbagai format Jakmall
                $stock = $this->parseJakmallStock($stockRaw);

                try {
                    UrfavShopeeProduct::updateOrCreate(
                        ['jakmall_sku' => $sku],
                        [
                            'jakmall_harga' => $harga,
                            'jakmall_stock' => $stock
                        ]
                    );
                    $imported++;
                } catch (\Exception $e) {
                    $errors[] = "SKU {$sku}: " . $e->getMessage();
                }
            }

            if (!empty($errors)) {
                return back()->with('error', 'Import selesai dengan error: ' . implode(', ', $errors));
            }

            return back()->with('success', "Berhasil import {$imported} produk dari Jakmall");
        } catch (\Exception $e) {
            return back()->with('error', 'Error saat import: ' . $e->getMessage());
        }
    }

    public function syncToShopee(Request $request): RedirectResponse
    {
        $products = UrfavShopeeProduct::whereNotNull('jakmall_sku')->get();

        if ($products->isEmpty()) {
            return back()->with('error', 'Tidak ada data Jakmall untuk disinkronkan');
        }

        foreach ($products as $product) {
            // Set default shopee_sku if not set
            if (!$product->shopee_sku) {
                $product->shopee_sku = $product->jakmall_sku;
            }

            // Calculate shopee_harga only if margin is set
            if ($product->shopee_margin) {
                $product->shopee_harga = $product->calculateShopeePrice();
            }

            // Calculate shopee_stock
            $product->shopee_stock = $product->calculateShopeeStock();

            $product->save();
        }

        return back()->with('success', 'Berhasil sinkronisasi ' . $products->count() . ' produk ke Shopee');
    }

    public function updateUrutanFromFile(Request $request): RedirectResponse
    {
        $request->validate([
            'urutan_file' => 'required|file|mimes:xlsx,xls'
        ]);

        try {
            $file = $request->file('urutan_file');
            $spreadsheet = IOFactory::load($file->getPathname());
            $data = $spreadsheet->getActiveSheet()->toArray();

            // Skip header row
            array_shift($data);

            // Check for duplicate SKUs in Excel
            $skuCount = [];
            $duplicateSkus = [];
            foreach ($data as $index => $row) {
                $shopeeSku = trim($row[1] ?? '');
                if (!empty($shopeeSku)) {
                    if (isset($skuCount[$shopeeSku])) {
                        if (!in_array($shopeeSku, $duplicateSkus)) {
                            $duplicateSkus[] = $shopeeSku;
                        }
                    }
                    $skuCount[$shopeeSku] = ($skuCount[$shopeeSku] ?? 0) + 1;
                }
            }

            // REPLACE MODE: Reset semua urutan terlebih dahulu
            UrfavShopeeProduct::query()->update(['shopee_urut' => null]);

            $updated = 0;
            $errors = [];
            $notFound = [];

            foreach ($data as $index => $row) {
                if (empty($row[0]) && empty($row[1])) continue; // Skip empty rows

                $shopeeId = trim($row[0] ?? '');
                $shopeeSku = trim($row[1] ?? '');
                $urut = $index + 1;

                // Try to find product by Shopee ID first, then by Shopee SKU
                $product = null;

                if (!empty($shopeeId)) {
                    $product = UrfavShopeeProduct::where('shopee_id', $shopeeId)->first();
                }

                if (!$product && !empty($shopeeSku)) {
                    $product = UrfavShopeeProduct::where('shopee_sku', $shopeeSku)->first();
                }

                if ($product) {
                    // Update urutan
                    $product->shopee_urut = $urut;

                    // Update Shopee ID jika kosong dan ada di file
                    if (empty($product->shopee_id) && !empty($shopeeId)) {
                        $product->shopee_id = $shopeeId;
                    }

                    $product->save();
                    $updated++;
                } else {
                    $notFound[] = "Row " . ($index + 2) . ": ID '{$shopeeId}' / SKU '{$shopeeSku}' tidak ditemukan";
                }
            }

            // Hitung data yang tidak ada di Excel (shopee_urut masih null)
            $notInExcel = UrfavShopeeProduct::whereNull('shopee_urut')->count();

            $message = "Berhasil update {$updated} produk";
            if ($notInExcel > 0) {
                $message .= ". {$notInExcel} produk tidak ada di Excel (urutan direset)";
            }
            if (!empty($duplicateSkus)) {
                $message .= ". SKU duplikat di Excel: " . implode(', ', array_slice($duplicateSkus, 0, 3));
                if (count($duplicateSkus) > 3) {
                    $message .= " dan " . (count($duplicateSkus) - 3) . " lainnya";
                }
            }
            if (!empty($notFound)) {
                $message .= ". Tidak ditemukan: " . implode(', ', array_slice($notFound, 0, 3));
                if (count($notFound) > 3) {
                    $message .= " dan " . (count($notFound) - 3) . " lainnya";
                }
            }

            return back()->with('success', $message);
        } catch (\Exception $e) {
            return back()->with('error', 'Error saat update urutan: ' . $e->getMessage());
        }
    }

    public function updateUrutan(Request $request): JsonResponse
    {
        $request->validate([
            'shopee_ids' => 'required|string'
        ]);

        try {
            $shopeeIds = array_filter(array_map('trim', explode("\n", $request->shopee_ids)));

            // Get all products that have shopee_id
            $existingProducts = UrfavShopeeProduct::whereNotNull('shopee_id')->pluck('shopee_id')->toArray();

            // Check for missing IDs
            $missingInInput = array_diff($existingProducts, $shopeeIds);
            $missingInDb = array_diff($shopeeIds, $existingProducts);

            if (!empty($missingInInput) || !empty($missingInDb)) {
                $errorMsg = [];
                if (!empty($missingInInput)) {
                    $errorMsg[] = 'ID tidak ditemukan dalam input: ' . implode(', ', $missingInInput);
                }
                if (!empty($missingInDb)) {
                    $errorMsg[] = 'ID tidak ditemukan dalam database: ' . implode(', ', $missingInDb);
                }

                return response()->json([
                    'success' => false,
                    'message' => implode('; ', $errorMsg)
                ]);
            }

            // Update urutan
            foreach ($shopeeIds as $index => $shopeeId) {
                UrfavShopeeProduct::where('shopee_id', $shopeeId)
                    ->update(['shopee_urut' => $index + 1]);
            }

            return response()->json([
                'success' => true,
                'message' => 'Berhasil update urutan ' . count($shopeeIds) . ' produk'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error: ' . $e->getMessage()
            ]);
        }
    }

    public function exportAll(): \Symfony\Component\HttpFoundation\BinaryFileResponse
    {
        $products = UrfavShopeeProduct::orderBy('shopee_urut')
            ->orderBy('created_at', 'desc')
            ->get();

        // Create Excel file
        $filename = 'urfav_all_products_' . date('Y-m-d_H-i-s') . '.xlsx';
        $filepath = storage_path('app/temp/' . $filename);

        // Ensure directory exists
        if (!file_exists(dirname($filepath))) {
            mkdir(dirname($filepath), 0755, true);
        }

        // Create new spreadsheet
        $spreadsheet = new \PhpOffice\PhpSpreadsheet\Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();

        // Set headers
        $headers = [
            'Jakmall_SKU',
            'Jakmall_Harga',
            'Jakmall_Stock',
            'Shopee_Urut',
            'Shopee_ID',
            'Shopee_SKU',
            'Shopee_Harga',
            'Shopee_Margin',
            'Shopee_Stock'
        ];

        $sheet->fromArray($headers, null, 'A1');

        // Add data
        $row = 2;
        foreach ($products as $product) {
            $sheet->fromArray([
                $product->jakmall_sku,
                $product->jakmall_harga,
                $product->jakmall_stock,
                $product->shopee_urut,
                $product->shopee_id,
                $product->shopee_sku,
                $product->shopee_harga,
                $product->shopee_margin,
                $product->shopee_stock
            ], null, 'A' . $row);
            $row++;
        }

        // Auto-size columns
        foreach (range('A', 'I') as $col) {
            $sheet->getColumnDimension($col)->setAutoSize(true);
        }

        // Save file
        $writer = new \PhpOffice\PhpSpreadsheet\Writer\Xlsx($spreadsheet);
        $writer->save($filepath);

        return response()->download($filepath)->deleteFileAfterSend();
    }

    public function exportShopee(): \Symfony\Component\HttpFoundation\BinaryFileResponse
    {
        $products = UrfavShopeeProduct::orderBy('shopee_urut')
            ->whereNotNull('shopee_sku')
            ->select(['shopee_sku', 'shopee_harga', 'shopee_stock'])
            ->get();

        // Create Excel file
        $filename = 'shopee_export_' . date('Y-m-d_H-i-s') . '.xlsx';
        $filepath = storage_path('app/temp/' . $filename);

        // Ensure directory exists
        if (!file_exists(dirname($filepath))) {
            mkdir(dirname($filepath), 0755, true);
        }

        // Create new spreadsheet
        $spreadsheet = new \PhpOffice\PhpSpreadsheet\Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();

        // Set headers in separate cells
        $sheet->setCellValue('A1', 'Shopee_SKU');
        $sheet->setCellValue('B1', 'Shopee_Harga');
        $sheet->setCellValue('C1', 'Shopee_Stock');

        // Add data in separate cells
        $row = 2;
        foreach ($products as $product) {
            $sheet->setCellValue('A' . $row, $product->shopee_sku);
            $sheet->setCellValue('B' . $row, $product->shopee_harga);
            $sheet->setCellValue('C' . $row, $product->shopee_stock);
            $row++;
        }

        // Auto-size columns
        $sheet->getColumnDimension('A')->setAutoSize(true);
        $sheet->getColumnDimension('B')->setAutoSize(true);
        $sheet->getColumnDimension('C')->setAutoSize(true);

        // Save file
        $writer = new \PhpOffice\PhpSpreadsheet\Writer\Xlsx($spreadsheet);
        $writer->save($filepath);

        return response()->download($filepath)->deleteFileAfterSend();
    }

    public function updateProduct(Request $request, UrfavShopeeProduct $product): JsonResponse
    {
        $request->validate([
            'shopee_id' => 'nullable|string|max:255',
            'shopee_margin' => 'nullable|numeric|min:0|max:100'
        ]);

        try {
            if ($request->has('shopee_id')) {
                $product->shopee_id = $request->shopee_id;
            }

            if ($request->has('shopee_margin')) {
                $product->shopee_margin = $request->shopee_margin;
                $product->shopee_harga = $product->calculateShopeePrice();
            }

            $product->save();

            return response()->json([
                'success' => true,
                'message' => 'Produk berhasil diupdate',
                'data' => [
                    'shopee_harga' => number_format($product->shopee_harga, 0, ',', '.')
                ]
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error: ' . $e->getMessage()
            ]);
        }
    }

    public function destroy(UrfavShopeeProduct $product): RedirectResponse
    {
        $product->delete();
        return back()->with('success', 'Produk berhasil dihapus');
    }
}
