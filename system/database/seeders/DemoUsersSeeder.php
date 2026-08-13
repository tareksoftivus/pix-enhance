<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Spatie\Permission\Models\Role;

/**
 * Populates a realistic roster of website users for demos and screenshots.
 *
 * Idempotent: keyed on email via firstOrCreate, so re-running never duplicates.
 * Every user shares the password "password" so any account can be logged into.
 */
class DemoUsersSeeder extends Seeder
{
    public function run(): void
    {
        Role::findOrCreate('user', 'web');

        $password = Hash::make('password');

        foreach ($this->users() as $index => $data) {
            $user = User::firstOrCreate(
                ['email' => $data['email']],
                [
                    'name' => $data['name'],
                    'password' => $password,
                    'phone' => $data['phone'],
                    'is_active' => $data['is_active'],
                    // Stagger sign-up and last-login dates so lists look organic.
                    'last_login_at' => $data['is_active'] ? now()->subDays($index)->subHours($index) : null,
                    'email_verified_at' => $data['verified'] ? now()->subDays($index + 3) : null,
                    'created_at' => now()->subDays(60 - $index),
                    'updated_at' => now()->subDays($index),
                ]
            );

            $user->syncRoles(['user']);
        }
    }

    /**
     * Deterministic demo roster. Fixed data keeps re-runs idempotent.
     *
     * @return array<int, array{name: string, email: string, phone: string, is_active: bool, verified: bool}>
     */
    protected function users(): array
    {
        return [
            ['name' => 'Olivia Bennett', 'email' => 'olivia.bennett@example.com', 'phone' => '+1 202 555 0114', 'is_active' => true, 'verified' => true],
            ['name' => 'Liam Carter', 'email' => 'liam.carter@example.com', 'phone' => '+1 202 555 0132', 'is_active' => true, 'verified' => true],
            ['name' => 'Sophia Nguyen', 'email' => 'sophia.nguyen@example.com', 'phone' => '+1 202 555 0178', 'is_active' => true, 'verified' => true],
            ['name' => 'Noah Patel', 'email' => 'noah.patel@example.com', 'phone' => '+44 20 7946 0958', 'is_active' => true, 'verified' => true],
            ['name' => 'Emma Rodriguez', 'email' => 'emma.rodriguez@example.com', 'phone' => '+34 91 123 4567', 'is_active' => true, 'verified' => true],
            ['name' => 'Ethan Kim', 'email' => 'ethan.kim@example.com', 'phone' => '+82 2 1234 5678', 'is_active' => true, 'verified' => true],
            ['name' => 'Ava Johansson', 'email' => 'ava.johansson@example.com', 'phone' => '+46 8 123 456', 'is_active' => true, 'verified' => true],
            ['name' => 'Mason Silva', 'email' => 'mason.silva@example.com', 'phone' => '+55 11 91234 5678', 'is_active' => true, 'verified' => true],
            ['name' => 'Isabella Rossi', 'email' => 'isabella.rossi@example.com', 'phone' => '+39 06 1234 5678', 'is_active' => true, 'verified' => true],
            ['name' => 'James O\'Connor', 'email' => 'james.oconnor@example.com', 'phone' => '+353 1 234 5678', 'is_active' => true, 'verified' => true],
            ['name' => 'Mia Andersen', 'email' => 'mia.andersen@example.com', 'phone' => '+45 32 12 34 56', 'is_active' => true, 'verified' => true],
            ['name' => 'Benjamin Cohen', 'email' => 'benjamin.cohen@example.com', 'phone' => '+972 2 123 4567', 'is_active' => true, 'verified' => true],
            ['name' => 'Charlotte Dubois', 'email' => 'charlotte.dubois@example.com', 'phone' => '+33 1 23 45 67 89', 'is_active' => true, 'verified' => true],
            ['name' => 'Lucas Meyer', 'email' => 'lucas.meyer@example.com', 'phone' => '+49 30 123456', 'is_active' => true, 'verified' => true],
            ['name' => 'Amelia Costa', 'email' => 'amelia.costa@example.com', 'phone' => '+351 21 123 4567', 'is_active' => true, 'verified' => true],
            ['name' => 'Henry Walsh', 'email' => 'henry.walsh@example.com', 'phone' => '+61 2 1234 5678', 'is_active' => true, 'verified' => true],
            ['name' => 'Zoe Fischer', 'email' => 'zoe.fischer@example.com', 'phone' => '+43 1 234 5678', 'is_active' => true, 'verified' => false],
            ['name' => 'Daniel Okafor', 'email' => 'daniel.okafor@example.com', 'phone' => '+234 1 234 5678', 'is_active' => true, 'verified' => true],
            ['name' => 'Grace Sullivan', 'email' => 'grace.sullivan@example.com', 'phone' => '+1 415 555 0199', 'is_active' => false, 'verified' => true],
            ['name' => 'Oscar Lindqvist', 'email' => 'oscar.lindqvist@example.com', 'phone' => '+358 9 123 4567', 'is_active' => true, 'verified' => true],
            ['name' => 'Chloe Martin', 'email' => 'chloe.martin@example.com', 'phone' => '+1 312 555 0143', 'is_active' => true, 'verified' => true],
            ['name' => 'Ryan Thompson', 'email' => 'ryan.thompson@example.com', 'phone' => '+1 646 555 0165', 'is_active' => false, 'verified' => false],
            ['name' => 'Layla Hassan', 'email' => 'layla.hassan@example.com', 'phone' => '+20 2 1234 5678', 'is_active' => true, 'verified' => true],
            ['name' => 'Victor Nowak', 'email' => 'victor.nowak@example.com', 'phone' => '+48 22 123 45 67', 'is_active' => true, 'verified' => true],
            ['name' => 'Hannah Berg', 'email' => 'hannah.berg@example.com', 'phone' => '+47 21 23 45 67', 'is_active' => true, 'verified' => true],
        ];
    }
}
