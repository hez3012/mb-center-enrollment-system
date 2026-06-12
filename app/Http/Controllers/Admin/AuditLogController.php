<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\AuditLog;
use App\Models\AuthLog;
use App\Models\Enrollment;
use App\Models\Guardian;
use App\Models\Payment;
use App\Models\Student;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AuditLogController extends Controller
{
    public function index(Request $request)
    {
        /** @var \App\Models\User $authUser */
        $authUser = Auth::user();
        $roleName = strtolower($authUser->role?->role_name ?? '');
        $userId   = $authUser->getAuthIdentifier();

        $isDirectress = $roleName === 'directress';
        $isAdmin      = $roleName === 'admin';
        $isOwnOnly    = !$isDirectress && !$isAdmin;

        // ── Trail ──────────────────────────────────────────────────────────────
        $trailQuery = AuditLog::with('user.role')->orderByDesc('timestamp');

        if ($isOwnOnly) {
            $trailQuery->where('user_id', $userId);
        } elseif ($isAdmin) {
            $trailQuery->whereHas('user.role', fn ($q) =>
                $q->where('role_name', '!=', 'directress')
            );
        }

        if ($request->filled('trail_action')) {
            $trailQuery->where('action', $request->trail_action);
        }
        if ($request->filled('trail_table')) {
            $trailQuery->where('table_name', $request->trail_table);
        }
        if ($request->filled('trail_search')) {
            $s = $request->trail_search;
            $trailQuery->where(function ($q) use ($s) {
                $q->where('changes', 'like', "%{$s}%")
                  ->orWhereHas('user', fn ($uq) =>
                      $uq->where('first_name', 'like', "%{$s}%")
                         ->orWhere('last_name',  'like', "%{$s}%")
                  );
            });
        }

        $trails = $trailQuery->paginate(20, ['*'], 'trail_page')->withQueryString();

        $trails->getCollection()->transform(function ($trail) {
            $trail->record_label      = $this->getRecordLabel($trail->table_name, $trail->record_id);
            $trail->formatted_changes = $this->formatChanges($trail->changes, $trail->action, $trail->table_name);
            return $trail;
        });

        // ── Log ────────────────────────────────────────────────────────────────
        $logQuery = AuthLog::with('user.role')->orderByDesc('logged_at');

        if ($isOwnOnly) {
            $logQuery->where('user_id', $userId);
        } elseif ($isAdmin) {
            $logQuery->whereHas('user.role', fn ($q) =>
                $q->where('role_name', '!=', 'directress')
            );
        }

        if ($request->filled('log_action')) {
            $logQuery->where('action', $request->log_action);
        }
        if ($request->filled('log_search')) {
            $s = $request->log_search;
            $logQuery->whereHas('user', fn ($q) =>
                $q->where('first_name', 'like', "%{$s}%")
                  ->orWhere('last_name',  'like', "%{$s}%")
            );
        }

        $logs = $logQuery->paginate(20, ['*'], 'log_page')->withQueryString();

        $activeTab = $request->get('tab', 'log');

        return view('admin.audit-log.index', compact(
            'trails', 'logs', 'activeTab',
            'isDirectress', 'isAdmin', 'isOwnOnly'
        ));
    }

    // ── Helpers ────────────────────────────────────────────────────────────────

    private function getRecordLabel(string $table, ?int $id): string
    {
        if (!$id) return '—';

        try {
            switch (strtolower(trim($table))) {

                case 'student':
                    // Try normal, then soft-deleted
                    $s = Student::find($id);
                    if (!$s) {
                        try { $s = Student::withTrashed()->find($id); } catch (\Throwable $e) {}
                    }
                    return $s && $s->full_name
                        ? 'Student ' . $s->full_name
                        : 'Student (record removed)';

                case 'enrollment':
                    $e = Enrollment::with('student')->find($id);
                    if ($e) {
                        $name = $e->student?->full_name ?? 'Unknown Student';
                        return 'Enrollment for ' . $name;
                    }
                    return 'Enrollment (record removed)';

                case 'users':
                case 'user':
                    // User has SoftDeletes — always use withTrashed
                    $u = User::withTrashed()->find($id);
                    return $u && $u->full_name
                        ? 'User ' . $u->full_name
                        : 'User (account removed)';

                case 'guardian':
                    $g = Guardian::with('user')->find($id);
                    if ($g) {
                        $name = $g->user?->full_name ?? 'Unknown Guardian';
                        return 'Guardian ' . $name;
                    }
                    return 'Guardian (record removed)';

                case 'payment':
                    // Payment has SoftDeletes — always use withTrashed
                    $p = Payment::withTrashed()->with('enrollment.student')->find($id);
                    if ($p) {
                        $name = $p->enrollment?->student?->full_name ?? 'Unknown Student';
                        return 'Payment for ' . $name;
                    }
                    return 'Payment (record removed)';

                default:
                    return ucfirst($table) . ' record';
            }
        } catch (\Throwable $e) {
            return ucfirst(strtolower($table)) . ' record';
        }
    }

    private function formatChanges(?string $changes, string $action, string $table): string
    {
        $action = strtolower($action);

        if (is_null($changes) || trim($changes) === '') {
            return match ($action) {
                'create'  => 'A new ' . strtolower($table) . ' record was successfully added to the system.',
                'update'  => 'The ' . strtolower($table) . ' record was updated with new information.',
                'delete'  => 'A ' . strtolower($table) . ' record was permanently removed from the system.',
                'approve' => 'The enrollment application was reviewed and approved.',
                'reject'  => 'The enrollment application was reviewed and rejected.',
                'login'   => 'The user successfully logged into the system.',
                'logout'  => 'The user logged out of the system.',
                default   => 'An action was performed on the ' . strtolower($table) . ' record.',
            };
        }

        $decoded = json_decode($changes, true);
        if (is_array($decoded)) {
            if (isset($decoded['amount'])) {
                $amount   = '₱' . number_format((float) $decoded['amount'], 2);
                $enrollId = $decoded['enrollment_id'] ?? null;
                return "A payment of {$amount} was recorded" .
                    ($enrollId ? " for enrollment ID #{$enrollId}." : '.');
            }

            $labels = [
                'enrollment_id' => 'Enrollment',
                'student_id'    => 'Student',
                'user_id'       => 'User',
                'amount'        => 'Amount',
                'status'        => 'Status',
                'payment_date'  => 'Payment date',
                'school_year'   => 'School year',
            ];
            $parts = [];
            foreach ($decoded as $key => $val) {
                if ($val !== null && $val !== '') {
                    $label = $labels[$key] ?? ucwords(str_replace('_', ' ', $key));
                    if (stripos($key, 'amount') !== false && is_numeric($val)) {
                        $val = '₱' . number_format((float) $val, 2);
                    }
                    $parts[] = "{$label}: {$val}";
                }
            }
            return empty($parts) ? 'No additional details recorded.' : implode('; ', $parts) . '.';
        }

        return $changes;
    }
}