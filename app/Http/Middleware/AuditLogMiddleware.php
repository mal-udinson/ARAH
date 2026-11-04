<?php

namespace App\Http\Middleware;

use App\Models\AuditLog;
use Closure;
use Illuminate\Http\Request;
use Throwable;
use Symfony\Component\HttpFoundation\Response;

class AuditLogMiddleware
{
    public function handle(
        Request $request,
        Closure $next
    ): Response {
        $response = $next($request);

        $user = $request->user();

        if (!$user) {
            return $response;
        }

        if (!in_array(
            strtolower((string) $user->role),
            ['admin_kampus', 'admin_arah'],
            true
        )) {
            return $response;
        }

        if (!in_array(
            $request->method(),
            ['POST', 'PUT', 'PATCH', 'DELETE'],
            true
        )) {
            return $response;
        }

        if (!$response->isSuccessful() && !$response->isRedirection()) {
            return $response;
        }

        try {
            $route = $request->route();
            $routeName = $route?->getName() ?? $request->path();
            $parameters = $route?->parameters() ?? [];

            $targetId = null;

            foreach ($parameters as $value) {
                if (is_scalar($value)) {
                    $targetId = (string) $value;
                    break;
                }
            }

            $parts = explode('.', $routeName);

            $moduleKey = $parts[1] ?? 'lainnya';
            $actionKey = end($parts);

            $moduleLabels = [
                'mahasiswa' => 'Mahasiswa',
                'dosen' => 'Dosen',
                'kampus' => 'Kampus',
                'admin-kampus' => 'Admin Kampus',
                'audit-log' => 'Audit Log',
                'monitoring' => 'Monitoring',
            ];

            $actionLabels = [
                'store' => 'Menambahkan data',
                'update' => 'Memperbarui data',
                'destroy' => 'Menghapus data',
                'toggle-status' => 'Mengubah status',
            ];

            $module = $moduleLabels[$moduleKey] ?? ucfirst(str_replace('-', ' ', $moduleKey));

            $action = $actionLabels[$actionKey]
                ?? match ($request->method()) {
                    'POST' => 'Menambahkan data',
                    'PUT', 'PATCH' => 'Memperbarui data',
                    'DELETE' => 'Menghapus data',
                    default => 'Melakukan aksi',
                };

            $description = $action . ' pada modul ' . $module;

            if ($targetId !== null) {
                $description .= ' (ID: ' . $targetId . ')';
            }

            AuditLog::create([
                'user_id' => $user->id,
                'kampus_id' => $user->kampus_id,
                'role' => $user->role,
                'action' => $this->normalizeAction($actionKey, $request->method()),
                'module' => $module,
                'description' => $description,
                'method' => $request->method(),
                'route_name' => $routeName,
                'route_path' => $request->path(),
                'target_type' => $module,
                'target_id' => $targetId,
                'ip_address' => $request->ip(),
                'user_agent' => $request->userAgent(),
            ]);
        } catch (Throwable $e) {
            report($e);
        }

        return $response;
    }

    private function normalizeAction(
        string $action,
        string $method
    ): string {
        return match ($action) {
            'store' => 'create',
            'update' => 'update',
            'destroy' => 'delete',
            'toggle-status' => 'status_change',
            default => match ($method) {
                'POST' => 'create',
                'PUT', 'PATCH' => 'update',
                'DELETE' => 'delete',
                default => 'other',
            },
        };
    }
}