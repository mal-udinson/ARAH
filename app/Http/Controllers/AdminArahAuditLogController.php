<?php

namespace App\Http\Controllers;

use App\Models\AuditLog;
use Illuminate\Http\Request;

class AdminArahAuditLogController extends Controller
{
    public function index(Request $request)
    {
        $search = trim((string) $request->input('search'));
        $role = $request->input('role');
        $action = $request->input('action');
        $module = $request->input('module');

        $logs = AuditLog::with(['user', 'kampus'])
            ->when($search !== '', function ($query) use ($search) {
                $query->where(function ($query) use ($search) {
                    $query
                        ->where('description', 'like', '%' . $search . '%')
                        ->orWhere('route_path', 'like', '%' . $search . '%')
                        ->orWhereHas('user', function ($query) use ($search) {
                            $query
                                ->where('name', 'like', '%' . $search . '%')
                                ->orWhere('email', 'like', '%' . $search . '%');
                        });
                });
            })
            ->when($role !== null && $role !== '', function ($query) use ($role) {
                $query->where('role', $role);
            })
            ->when($action !== null && $action !== '', function ($query) use ($action) {
                $query->where('action', $action);
            })
            ->when($module !== null && $module !== '', function ($query) use ($module) {
                $query->where('module', $module);
            })
            ->latest()
            ->paginate(20)
            ->withQueryString();

        $modules = AuditLog::query()
            ->select('module')
            ->whereNotNull('module')
            ->distinct()
            ->orderBy('module')
            ->pluck('module');

        return view('admin-arah.audit-log.index', compact(
            'logs',
            'search',
            'role',
            'action',
            'module',
            'modules'
        ));
    }
}