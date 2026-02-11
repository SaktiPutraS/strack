<?php

namespace App\Http\Controllers;

use App\Models\Project;
use Illuminate\Http\Request;
use Illuminate\View\View;

class ProjectInvoiceController extends Controller
{
    public function previewInvoice(Project $project): View
    {
        $invoiceNumber = $this->generateInvoiceNumber($project);
        $isBtools = $project->type === 'BTOOLS';

        return view('projects.invoice-preview', compact('project', 'invoiceNumber', 'isBtools'));
    }

    public function printInvoice(Request $request, Project $project): View
    {
        $invoiceNumber = $this->generateInvoiceNumber($project);
        $terbilang = $this->numberToWords($project->total_value);

        $clientData = [
            'name' => $request->get('client_name', $project->client->name),
            'address' => $request->get('client_address', $project->client->address),
            'phone' => $request->get('client_phone', $project->client->phone),
            'email' => $request->get('client_email', $project->client->email),
        ];

        $qty = max(1, intval($request->get('qty', 1)));
        $unitPrice = $project->total_value / $qty;

        $itemData = [
            'description' => $request->get('item_description', $project->title . "\n" . $project->description),
            'qty' => $qty,
            'unit_price' => $unitPrice,
            'total' => $project->total_value
        ];

        $viewName = $project->type === 'BTOOLS' ? 'projects.invoice' : 'projects.invoice-general';

        return view($viewName, compact('project', 'invoiceNumber', 'terbilang', 'clientData', 'itemData'));
    }

    public function previewQuotation(Project $project): View
    {
        $quotationNumber = $this->generateQuotationNumber($project);
        $isBtools = $project->type === 'BTOOLS';

        return view('projects.quotation-preview', compact('project', 'quotationNumber', 'isBtools'));
    }

    public function printQuotation(Request $request, Project $project): View
    {
        $quotationNumber = $this->generateQuotationNumber($project);
        $terbilang = $this->numberToWords($project->total_value);

        $clientData = [
            'name' => $request->get('client_name', $project->client->name),
            'address' => $request->get('client_address', $project->client->address),
            'phone' => $request->get('client_phone', $project->client->phone),
            'email' => $request->get('client_email', $project->client->email),
        ];

        $qty = max(1, intval($request->get('qty', 1)));
        $unitPrice = $project->total_value / $qty;

        $itemData = [
            'description' => $request->get('item_description', $project->title . "\n" . $project->description),
            'qty' => $qty,
            'unit_price' => $unitPrice,
            'total' => $project->total_value
        ];

        $viewName = $project->type === 'BTOOLS' ? 'projects.quotation' : 'projects.quotation-general';

        return view($viewName, compact('project', 'quotationNumber', 'terbilang', 'clientData', 'itemData'));
    }

    private function generateInvoiceNumber(Project $project): string
    {
        $date = $project->deadline->format('ymd');

        $count = Project::where('type', $project->type)
            ->whereDate('deadline', $project->deadline->format('Y-m-d'))
            ->where('id', '<=', $project->id)
            ->count();

        $sequence = str_pad($count, 3, '0', STR_PAD_LEFT);

        return "INV-{$date}{$sequence}";
    }

    private function generateQuotationNumber(Project $project): string
    {
        $date = $project->created_at->format('ymd');

        $count = Project::where('type', $project->type)
            ->whereDate('created_at', $project->created_at->format('Y-m-d'))
            ->where('id', '<=', $project->id)
            ->count();

        $sequence = str_pad($count, 3, '0', STR_PAD_LEFT);

        return "QUO-{$date}{$sequence}";
    }

    private function numberToWords($number): string
    {
        $number = (int) $number;

        if ($number == 0) return 'nol rupiah';

        $units = ['', 'satu', 'dua', 'tiga', 'empat', 'lima', 'enam', 'tujuh', 'delapan', 'sembilan'];
        $teens = ['sepuluh', 'sebelas', 'dua belas', 'tiga belas', 'empat belas', 'lima belas', 'enam belas', 'tujuh belas', 'delapan belas', 'sembilan belas'];
        $tens = ['', '', 'dua puluh', 'tiga puluh', 'empat puluh', 'lima puluh', 'enam puluh', 'tujuh puluh', 'delapan puluh', 'sembilan puluh'];
        $thousands = ['', 'ribu', 'juta', 'miliar', 'triliun'];

        $convertGroup = function ($num) use ($units, $teens, $tens) {
            $result = '';

            if ($num >= 100) {
                if (intval($num / 100) == 1) {
                    $result .= 'seratus ';
                } else {
                    $result .= $units[intval($num / 100)] . ' ratus ';
                }
                $num %= 100;
            }

            if ($num >= 20) {
                $result .= $tens[intval($num / 10)] . ' ';
                $num %= 10;
            } elseif ($num >= 10) {
                $result .= $teens[$num - 10] . ' ';
                return $result;
            }

            if ($num > 0) {
                if ($num == 1 && strpos($result, 'belas') === false) {
                    $result .= 'satu ';
                } else {
                    $result .= $units[$num] . ' ';
                }
            }

            return $result;
        };

        $result = '';
        $groupIndex = 0;

        while ($number > 0) {
            $group = $number % 1000;

            if ($group > 0) {
                $groupWord = $convertGroup($group);

                if ($groupIndex == 1 && $group == 1) {
                    $groupWord = 'seribu ';
                } else {
                    $groupWord .= $thousands[$groupIndex] . ' ';
                }

                $result = $groupWord . $result;
            }

            $number = intval($number / 1000);
            $groupIndex++;
        }

        return trim($result) . ' rupiah';
    }
}
