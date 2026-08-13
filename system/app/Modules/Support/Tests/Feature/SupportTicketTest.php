<?php

use App\Models\Admin;
use App\Models\User;
use App\Modules\Support\Models\SupportTicket;
use App\Modules\Support\Models\SupportTicketReply;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Blade;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;
use Tests\TestCase;

uses(TestCase::class, RefreshDatabase::class);

/**
 * Create an admin with the super-admin role (all permissions via Gate::before).
 */
function supportAdmin(): Admin
{
    $role = Role::findOrCreate('super-admin', 'admin');

    $admin = Admin::query()->create([
        'name' => 'Support Admin',
        'email' => 'support-admin@example.com',
        'password' => 'password',
        'is_active' => true,
        'email_verified_at' => now(),
    ]);

    $admin->assignRole($role);

    return $admin;
}

/**
 * Create a verified, active user holding the web-guard support permissions.
 */
function supportUser(): User
{
    foreach (['support-tickets.view', 'support-tickets.create', 'support-tickets.reply'] as $name) {
        Permission::findOrCreate($name, 'web');
    }

    $user = User::factory()->create([
        'is_active' => true,
        'email_verified_at' => now(),
    ]);

    $user->givePermissionTo('support-tickets.view', 'support-tickets.create', 'support-tickets.reply');

    return $user;
}

it('lets a user open a ticket which seeds the thread and sets a reference', function () {
    $user = supportUser();

    $this->actingAs($user)
        ->post(route('user.support-tickets.store'), [
            'subject' => 'Cannot log in',
            'body' => 'I forgot my password.',
            'priority' => 'high',
        ])
        ->assertRedirect();

    $ticket = SupportTicket::first();

    expect($ticket)->not->toBeNull()
        ->and($ticket->user_id)->toBe($user->id)
        ->and($ticket->status)->toBe('open')
        ->and($ticket->priority)->toBe('high')
        ->and($ticket->reference)->toStartWith('TKT-');
});

it('validates the ticket priority against the allowed set', function () {
    $user = supportUser();

    $this->actingAs($user)
        ->post(route('user.support-tickets.store'), [
            'subject' => 'Bad priority',
            'body' => 'x',
            'priority' => 'whenever',
        ])
        ->assertSessionHasErrors('priority');
});

it('scopes the user index to their own tickets only', function () {
    $owner = supportUser();
    $ticket = SupportTicket::create([
        'reference' => SupportTicket::generateReference(),
        'user_id' => $owner->id,
        'subject' => 'Mine',
        'body' => 'body',
        'priority' => 'medium',
        'status' => 'open',
        'last_reply_at' => now(),
    ]);

    $other = User::factory()->create(['is_active' => true, 'email_verified_at' => now()]);
    SupportTicket::create([
        'reference' => SupportTicket::generateReference(),
        'user_id' => $other->id,
        'subject' => 'Theirs',
        'body' => 'body',
        'priority' => 'medium',
        'status' => 'open',
        'last_reply_at' => now(),
    ]);

    $this->actingAs($owner)
        ->get(route('user.support-tickets.index'))
        ->assertSuccessful()
        ->assertSee('Mine')
        ->assertDontSee('Theirs');
});

it('404s when a user views a ticket they do not own', function () {
    $user = supportUser();
    $other = User::factory()->create(['is_active' => true, 'email_verified_at' => now()]);

    $ticket = SupportTicket::create([
        'reference' => SupportTicket::generateReference(),
        'user_id' => $other->id,
        'subject' => 'Not yours',
        'body' => 'body',
        'priority' => 'medium',
        'status' => 'open',
        'last_reply_at' => now(),
    ]);

    $this->actingAs($user)
        ->get(route('user.support-tickets.show', $ticket))
        ->assertNotFound();
});

it('records a staff reply and moves the ticket to pending', function () {
    $admin = supportAdmin();
    $user = supportUser();

    $ticket = SupportTicket::create([
        'reference' => SupportTicket::generateReference(),
        'user_id' => $user->id,
        'subject' => 'Help',
        'body' => 'body',
        'priority' => 'medium',
        'status' => 'open',
        'last_reply_at' => now(),
    ]);

    $this->actingAs($admin, 'admin')
        ->post(route('admin.support-tickets.reply', $ticket), ['message' => 'We are on it.'])
        ->assertRedirect(route('admin.support-tickets.show', $ticket));

    $reply = SupportTicketReply::first();

    expect($reply->author_type)->toBe(Admin::class)
        ->and($reply->isFromStaff())->toBeTrue()
        ->and($ticket->fresh()->status)->toBe('pending');
});

it('re-opens a pending ticket when the owner replies', function () {
    $user = supportUser();

    $ticket = SupportTicket::create([
        'reference' => SupportTicket::generateReference(),
        'user_id' => $user->id,
        'subject' => 'Reply flow',
        'body' => 'body',
        'priority' => 'medium',
        'status' => 'pending',
        'last_reply_at' => now(),
    ]);

    $this->actingAs($user)
        ->post(route('user.support-tickets.reply', $ticket), ['message' => 'Still broken.'])
        ->assertRedirect(route('user.support-tickets.show', $ticket));

    expect($ticket->fresh()->status)->toBe('open');
});

it('forbids replying to a closed ticket from the user side', function () {
    $user = supportUser();

    $ticket = SupportTicket::create([
        'reference' => SupportTicket::generateReference(),
        'user_id' => $user->id,
        'subject' => 'Closed',
        'body' => 'body',
        'priority' => 'medium',
        'status' => 'closed',
        'last_reply_at' => now(),
    ]);

    $this->actingAs($user)
        ->post(route('user.support-tickets.reply', $ticket), ['message' => 'Hello?'])
        ->assertForbidden();

    expect($ticket->fresh()->replies)->toHaveCount(0);
});

it('lets an admin change a ticket status', function () {
    $admin = supportAdmin();
    $user = supportUser();

    $ticket = SupportTicket::create([
        'reference' => SupportTicket::generateReference(),
        'user_id' => $user->id,
        'subject' => 'Resolve me',
        'body' => 'body',
        'priority' => 'medium',
        'status' => 'open',
        'last_reply_at' => now(),
    ]);

    $this->actingAs($admin, 'admin')
        ->patch(route('admin.support-tickets.status', $ticket), ['status' => 'resolved'])
        ->assertRedirect(route('admin.support-tickets.show', $ticket));

    expect($ticket->fresh()->status)->toBe('resolved');
});

it('shows every ticket in the admin queue regardless of owner', function () {
    $admin = supportAdmin();
    $userA = User::factory()->create(['is_active' => true, 'email_verified_at' => now()]);
    $userB = User::factory()->create(['is_active' => true, 'email_verified_at' => now()]);

    SupportTicket::create([
        'reference' => 'TKT-000101',
        'user_id' => $userA->id,
        'subject' => 'Ticket A',
        'body' => 'body',
        'priority' => 'low',
        'status' => 'open',
        'last_reply_at' => now(),
    ]);
    SupportTicket::create([
        'reference' => 'TKT-000102',
        'user_id' => $userB->id,
        'subject' => 'Ticket B',
        'body' => 'body',
        'priority' => 'urgent',
        'status' => 'open',
        'last_reply_at' => now(),
    ]);

    $this->actingAs($admin, 'admin')
        ->get(route('admin.support-tickets.index'))
        ->assertSuccessful()
        ->assertSee('Ticket A')
        ->assertSee('Ticket B');
});

/**
 * Create a ticket owned by $user with $replyCount user replies whose messages
 * are "Reply 1", "Reply 2", … in chronological order.
 */
function ticketWithReplies(User $user, int $replyCount): SupportTicket
{
    $ticket = SupportTicket::create([
        'reference' => SupportTicket::generateReference(),
        'user_id' => $user->id,
        'subject' => 'Paginated',
        'body' => 'Opening message',
        'priority' => 'medium',
        'status' => 'open',
        'last_reply_at' => now(),
    ]);

    for ($i = 1; $i <= $replyCount; $i++) {
        SupportTicketReply::create([
            'support_ticket_id' => $ticket->id,
            'author_type' => User::class,
            'author_id' => $user->id,
            'message' => "Reply {$i}",
            'created_at' => now()->addSeconds($i),
            'updated_at' => now()->addSeconds($i),
        ]);
    }

    return $ticket;
}

it('returns the newest five messages and flags more on the first page', function () {
    $user = supportUser();
    $ticket = ticketWithReplies($user, 8);

    $response = $this->actingAs($user)
        ->getJson(route('user.support-tickets.messages', $ticket).'?page=1');

    $response->assertSuccessful()
        ->assertJson(['has_more' => true, 'next_page' => 2]);

    // Newest first: page 1 holds replies 8..4, not the opening message yet.
    $response->assertSee('Reply 8')->assertSee('Reply 4')
        ->assertDontSee('Reply 3')
        ->assertDontSee('Opening message');
});

it('appends the opening message on the final page and ends pagination', function () {
    $user = supportUser();
    $ticket = ticketWithReplies($user, 8);

    $response = $this->actingAs($user)
        ->getJson(route('user.support-tickets.messages', $ticket).'?page=2');

    $response->assertSuccessful()
        ->assertJson(['has_more' => false]);

    // Page 2 holds replies 3..1 plus the opening message at the end.
    $response->assertSee('Reply 3')
        ->assertSee('Reply 1')
        ->assertSee('Opening message')
        ->assertDontSee('Reply 4');
});

it('includes the opening message on page one when there are few replies', function () {
    $user = supportUser();
    $ticket = ticketWithReplies($user, 2);

    $this->actingAs($user)
        ->getJson(route('user.support-tickets.messages', $ticket).'?page=1')
        ->assertSuccessful()
        ->assertJson(['has_more' => false])
        ->assertSee('Reply 2')
        ->assertSee('Reply 1')
        ->assertSee('Opening message');
});

it('404s the messages endpoint for a ticket the user does not own', function () {
    $user = supportUser();
    $other = User::factory()->create(['is_active' => true, 'email_verified_at' => now()]);
    $ticket = ticketWithReplies($other, 3);

    $this->actingAs($user)
        ->getJson(route('user.support-tickets.messages', $ticket))
        ->assertNotFound();
});

it('serves the messages endpoint to admins for any ticket', function () {
    $admin = supportAdmin();
    $user = supportUser();
    $ticket = ticketWithReplies($user, 3);

    $this->actingAs($admin, 'admin')
        ->getJson(route('admin.support-tickets.messages', $ticket).'?page=1')
        ->assertSuccessful()
        ->assertJson(['has_more' => false])
        ->assertSee('Opening message');
});

it('renders message bodies flush without a phantom leading blank line', function () {
    $html = Blade::render(
        '<x-support::message name="Sophia" body="Line one" :at="now()" :staff="false" />'
    );

    // whitespace-pre-line preserves newlines, so any template whitespace around
    // the body would render as a visible blank line above the message text.
    expect($html)->toMatch('/whitespace-pre-line[^>]*>Line one<\/div>/');
});
