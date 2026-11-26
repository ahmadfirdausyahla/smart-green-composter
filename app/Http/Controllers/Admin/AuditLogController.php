<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\AuditLog;
use App\Services\Audit\AuditService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class AuditLogController extends Controller
{
    public function index(Request $request): View
    {
        $user = $request->user();

        $query = AuditLog::with('user')->latest('id');

        if ($user->isAdminKota()) {
            // City admin sees logs related to their city or themselves
            $query->where('user_id', $user->id)
                  ->orWhere('description', 'like', "%{$user->city?->name}%");
        }

        $logs = $query->paginate(20);

        // Run integrity check
        $integrity = AuditService::verifyIntegrity();

        return view('admin.audit_logs.index', compact('logs', 'user', 'integrity'));
    }

    /**
     * Run full cryptographic hash chain integrity verification.
     */
    public function verify(Request $request): RedirectResponse
    {
        $integrity = AuditService::verifyIntegrity();

        if ($integrity['valid']) {
            return back()->with('success', $integrity['message']);
        }

        return back()->with('error', $integrity['message']);
    }
}
