<?php

namespace Database\Seeders;

use App\Models\Admin;
use App\Models\User;
use App\Modules\AuditLog\Models\AuditLog;
use App\Modules\LoginActivity\Models\LoginActivity;
use Illuminate\Database\Seeder;

/**
 * Populates login-activity history and the audit trail so those admin screens
 * are lively during a demo.
 *
 * These tables are append-only with no natural unique key, so the seeder is
 * guarded by a presence check: it only writes when there is no demo data yet.
 */
class DemoActivityLogsSeeder extends Seeder
{
    public function run(): void
    {
        $this->seedLoginActivities();
        $this->seedAuditLogs();
    }

    protected function seedLoginActivities(): void
    {
        // Skip if this seeder already ran (avoid duplicating append-only rows).
        if (LoginActivity::query()->where('metadata->source', 'demo-seeder')->exists()) {
            return;
        }

        $users = User::query()->orderBy('id')->get();
        $admin = Admin::query()->orderBy('id')->first();

        if ($users->isEmpty()) {
            return;
        }

        $agents = [
            ['ua' => 'Mozilla/5.0 (Windows NT 10.0; Win64; x64)', 'device' => 'Desktop', 'browser' => 'Chrome', 'platform' => 'Windows'],
            ['ua' => 'Mozilla/5.0 (Macintosh; Intel Mac OS X 10_15_7)', 'device' => 'Desktop', 'browser' => 'Safari', 'platform' => 'macOS'],
            ['ua' => 'Mozilla/5.0 (iPhone; CPU iPhone OS 17_0 like Mac OS X)', 'device' => 'Mobile', 'browser' => 'Safari', 'platform' => 'iOS'],
            ['ua' => 'Mozilla/5.0 (Linux; Android 14; Pixel 8)', 'device' => 'Mobile', 'browser' => 'Chrome', 'platform' => 'Android'],
        ];

        $rows = [];
        $i = 0;

        foreach ($users as $user) {
            // A few successful logins per user plus the occasional logout / failure.
            $sessions = 2 + ($user->id % 3);

            for ($s = 0; $s < $sessions; $s++) {
                $agent = $agents[($user->id + $s) % count($agents)];
                $at = now()->subDays(($i % 25) + 1)->subHours(($i * 3) % 24);

                $rows[] = $this->activityRow(User::class, $user->id, 'login', $agent, $at);

                if ($s % 2 === 1) {
                    $rows[] = $this->activityRow(User::class, $user->id, 'logout', $agent, $at->copy()->addHours(1));
                }

                if (($user->id + $s) % 7 === 0) {
                    $rows[] = $this->activityRow(User::class, $user->id, 'failed', $agent, $at->copy()->subMinutes(5));
                }

                $i++;
            }
        }

        // Admin sign-ins.
        if ($admin !== null) {
            for ($s = 0; $s < 6; $s++) {
                $agent = $agents[$s % count($agents)];
                $rows[] = $this->activityRow(Admin::class, $admin->id, 'login', $agent, now()->subDays($s + 1)->subHours($s));
            }
        }

        foreach (array_chunk($rows, 200) as $chunk) {
            LoginActivity::insert($chunk);
        }
    }

    /**
     * @param  array{ua: string, device: string, browser: string, platform: string}  $agent
     * @return array<string, mixed>
     */
    protected function activityRow(string $userType, int $userId, string $event, array $agent, \DateTimeInterface $at): array
    {
        return [
            'user_type' => $userType,
            'user_id' => $userId,
            'event' => $event,
            'ip_address' => '203.0.113.'.(($userId * 7) % 254 + 1),
            'user_agent' => $agent['ua'],
            'device' => $agent['device'],
            'browser' => $agent['browser'],
            'platform' => $agent['platform'],
            'metadata' => json_encode(['source' => 'demo-seeder']),
            'created_at' => $at,
        ];
    }

    protected function seedAuditLogs(): void
    {
        // Audit logs FK to users; skip if demo audit data already exists.
        if (AuditLog::query()->where('user_agent', 'demo-seeder')->exists()) {
            return;
        }

        $users = User::query()->orderBy('id')->get();

        if ($users->isEmpty()) {
            return;
        }

        $events = [
            ['action' => 'updated', 'type' => User::class, 'url' => '/dashboard/profile'],
            ['action' => 'created', 'type' => 'App\\Modules\\Support\\Models\\SupportTicket', 'url' => '/dashboard/support-tickets'],
            ['action' => 'login', 'type' => User::class, 'url' => '/login'],
            ['action' => 'updated', 'type' => User::class, 'url' => '/dashboard/settings'],
        ];

        $i = 0;

        foreach ($users as $user) {
            $event = $events[$user->id % count($events)];
            $at = now()->subDays(($i % 20) + 1)->subHours($i % 12);

            AuditLog::create([
                'user_id' => $user->id,
                'action' => $event['action'],
                'auditable_type' => $event['type'],
                'auditable_id' => $user->id,
                'old_values' => $event['action'] === 'updated' ? ['name' => $user->name.' (old)'] : null,
                'new_values' => $event['action'] === 'updated' ? ['name' => $user->name] : null,
                'ip_address' => '203.0.113.'.(($user->id * 5) % 254 + 1),
                'user_agent' => 'demo-seeder',
                'url' => $event['url'],
                'created_at' => $at,
                'updated_at' => $at,
            ]);

            $i++;
        }
    }
}
