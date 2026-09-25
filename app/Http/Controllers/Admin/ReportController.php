<?php

namespace App\Http\Controllers\Admin;

use App\Exports\OrderExport;
use App\Http\Controllers\Controller;
use App\Services\ReportService;
use Barryvdh\DomPDF\Facade\Pdf;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Maatwebsite\Excel\Facades\Excel;

class ReportController extends Controller
{
    public function __construct(private readonly ReportService $reports) {}

    public function index(Request $request)
    {
        [$from, $to] = $this->dates($request);
        $summary = $this->reports->summary($from, $to);
        $orders = $this->reports->ordersQuery($from, $to)->limit(100)->get();

        return view('admin.reports.index', compact('summary', 'orders', 'from', 'to'));
    }

    public function pdf(Request $request)
    {
        [$from, $to] = $this->dates($request);
        $summary = $this->reports->summary($from, $to);
        $orders = $this->reports->ordersQuery($from, $to)->limit(500)->get();
        $pdf = Pdf::loadView('admin.reports.pdf', compact('summary', 'orders', 'from', 'to'));

        return $pdf->download('laporan-laundryku-'.$from->format('Ymd').'-'.$to->format('Ymd').'.pdf');
    }

    public function excel(Request $request)
    {
        [$from, $to] = $this->dates($request);

        return Excel::download(new OrderExport($from, $to), 'laporan-pesanan-'.$from->format('Ymd').'-'.$to->format('Ymd').'.xlsx');
    }

    /**
     * @return array{0: Carbon, 1: Carbon}
     */
    private function dates(Request $request): array
    {
        $from = Carbon::parse($request->input('from', now()->startOfMonth()->toDateString()))->startOfDay();
        $to = Carbon::parse($request->input('to', now()->toDateString()))->endOfDay();

        if ($from->isAfter($to)) {
            abort(422, 'Tanggal awal tidak boleh lebih besar dari tanggal akhir.');
        }

        return [$from, $to];
    }
}
