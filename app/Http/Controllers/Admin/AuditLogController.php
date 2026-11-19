<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\AuditLog;
use Illuminate\Http\Request;
use Illuminate\View\View;

class AuditLogController extends Controller
{
    public function index(Request $request): View
    {
        $user = $request->user();

        $query = AuditLog::with('user')->latest('created_at');

        if ($user->isAdminKota()) {
            // City admin sees logs related to their city or themselves
            $query->where('user_id', $user->id)
                  ->orWhere('description', 'like', "%{$user->city?->name}%");
        }

        $logs = $query->paginate(20);

        return view('admin.audit_logs.index', compact('logs', 'user'));
    }
}
