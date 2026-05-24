<?php

namespace App\Http\Controllers;

use App\Models\AuditTrail;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AuditTrailController extends Controller
{
    public function show(AuditTrail $auditTrail)
    {
        $this->authorizeCompany($auditTrail);
        return view('audit-trails.show', compact('auditTrail'));
    }

    public function index(Request $request)
    {
        $companyId = Auth::user()->current_company_id;

        $query = AuditTrail::with('user')
            ->where('company_id', $companyId);

        if ($request->filled('event')) {
            $query->where('event', $request->event);
        }

        if ($request->filled('auditable_type')) {
            $query->where('auditable_type', $request->auditable_type);
        }

        if ($request->filled('start_date')) {
            $query->whereDate('created_at', '>=', $request->start_date);
        }

        if ($request->filled('end_date')) {
            $query->whereDate('created_at', '<=', $request->end_date);
        }

        if ($request->filled('search')) {
            $query->where(function ($q) use ($request) {
                $q->whereHas('user', fn($u) => $u->where('name', 'like', '%' . $request->search . '%'));
            });
        }

        $trails = $query->orderBy('created_at', 'desc')
            ->paginate(config('moneyku.pagination', 15));

        $events = AuditTrail::where('company_id', $companyId)
            ->select('event')
            ->distinct()
            ->orderBy('event')
            ->pluck('event');

        return view('audit-trails.index', compact('trails', 'events'));
    }

    public function exportCsv(Request $request)
    {
        $companyId = $this->getCompanyId();

        $trails = AuditTrail::where('company_id', $companyId)
            ->with('user')
            ->when($request->event, fn($q) => $q->where('event', $request->event))
            ->when($request->auditable_type, fn($q) => $q->where('auditable_type', $request->auditable_type))
            ->when($request->date_from, fn($q) => $q->whereDate('created_at', '>=', $request->date_from))
            ->when($request->date_to, fn($q) => $q->whereDate('created_at', '<=', $request->date_to))
            ->orderBy('created_at', 'desc')
            ->get();

        $filename = 'audit-trail-' . now()->format('Y-m-d-His') . '.csv';

        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => "attachment; filename=\"$filename\"",
        ];

        $callback = function() use ($trails) {
            $handle = fopen('php://output', 'w');
            fputcsv($handle, ['Waktu', 'User', 'Event', 'Tipe', 'ID', 'Perubahan']);

            foreach ($trails as $t) {
                $changes = '';
                if ($t->old_values || $t->new_values) {
                    $old = $t->old_values ? json_encode($t->old_values) : '';
                    $new = $t->new_values ? json_encode($t->new_values) : '';
                    $changes = ($old ? "Lama: $old" : '') . ($old && $new ? ' | ' : '') . ($new ? "Baru: $new" : '');
                }
                fputcsv($handle, [
                    $t->created_at->format('d/m/Y H:i'),
                    $t->user?->name ?? 'System',
                    $t->event,
                    class_basename($t->auditable_type),
                    $t->auditable_id,
                    $changes,
                ]);
            }

            fclose($handle);
        };

        return response()->stream($callback, 200, $headers);
    }

    private function authorizeCompany($model)
    {
        if ($model->company_id !== $this->getCompanyId()) {
            abort(403);
        }
    }

    private function getCompanyId()
    {
        return Auth::user()->current_company_id;
    }
}
