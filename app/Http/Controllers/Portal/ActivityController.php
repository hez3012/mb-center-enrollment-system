<?php

namespace App\Http\Controllers\Portal;

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

class ActivityController extends Controller
{
    public function index(Request $request)
    {
        /** @var \App\Models\User|null $authUser */
        $authUser = Auth::user();
        $userId   = $authUser?->getAuthIdentifier();

        $trails = AuditLog::where('user_id', $userId)
            ->orderByDesc('timestamp')
            ->paginate(15, ['*'], 'trail_page')
            ->withQueryString();

        $trails->getCollection()->transform(function ($trail) {
            $trail->record_label      = $this->getRecordLabel($trail->table_name, $trail->record_id);
            $trail->formatted_changes = $this->formatChanges(
                $trail->changes !== null ? (string) $trail->changes : null,
                $trail->action,
                $trail->table_name
            );
            return $trail;
        });

        $logs = AuthLog::where('user_id', $userId)
            ->orderByDesc('logged_at')
            ->paginate(15, ['*'], 'log_page')
            ->withQueryString();

        $activeTab = $request->get('tab', 'log');

        return view('portal.activity.index', compact('trails', 'logs', 'activeTab'));
    }

    // ── Helpers ────────────────────────────────────────────────────────────────

    private function getRecordLabel(string $table, ?int $id): string
    {
        if (!$id) return '—';

        try {
            switch (strtolower(trim($table))) {

                case 'student':
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