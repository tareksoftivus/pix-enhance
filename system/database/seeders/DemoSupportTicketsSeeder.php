<?php

namespace Database\Seeders;

use App\Models\Admin;
use App\Models\User;
use App\Modules\Support\Models\SupportTicket;
use App\Modules\Support\Models\SupportTicketReply;
use Illuminate\Database\Seeder;

/**
 * Populates the support desk with tickets spanning every status/priority, each
 * carrying a realistic multi-message conversation between a user and staff.
 *
 * Idempotent: tickets are keyed on a fixed reference; replies are only seeded
 * when a freshly created ticket has none yet.
 */
class DemoSupportTicketsSeeder extends Seeder
{
    public function run(): void
    {
        $users = User::query()->orderBy('id')->pluck('id')->all();
        $admin = Admin::query()->orderBy('id')->first();

        if ($users === [] || $admin === null) {
            return;
        }

        foreach ($this->tickets() as $index => $data) {
            $ownerId = $users[$index % count($users)];
            $createdAt = now()->subDays(count($this->tickets()) - $index)->subHours($index);

            $ticket = SupportTicket::firstOrCreate(
                ['reference' => $data['reference']],
                [
                    'user_id' => $ownerId,
                    'subject' => $data['subject'],
                    'body' => $data['body'],
                    'category' => $data['category'],
                    'priority' => $data['priority'],
                    'status' => $data['status'],
                    'last_reply_at' => $createdAt,
                    'created_at' => $createdAt,
                    'updated_at' => $createdAt,
                ]
            );

            // Only seed the thread once (skip on idempotent re-runs).
            if ($ticket->wasRecentlyCreated && $data['replies'] !== []) {
                $this->seedReplies($ticket, $ownerId, $admin, $data['replies']);
            }
        }
    }

    /**
     * Append the conversation, alternating author between the ticket owner and
     * staff, spacing each message a few hours apart from the ticket's creation.
     *
     * @param  array<int, array{staff: bool, message: string}>  $replies
     */
    protected function seedReplies(SupportTicket $ticket, int $ownerId, Admin $admin, array $replies): void
    {
        $timestamp = $ticket->created_at->copy();

        foreach ($replies as $offset => $reply) {
            $timestamp = $timestamp->copy()->addHours(3 + $offset);

            SupportTicketReply::create([
                'support_ticket_id' => $ticket->id,
                'author_type' => $reply['staff'] ? Admin::class : User::class,
                'author_id' => $reply['staff'] ? $admin->id : $ownerId,
                'message' => $reply['message'],
                'created_at' => $timestamp,
                'updated_at' => $timestamp,
            ]);
        }

        $ticket->forceFill(['last_reply_at' => $timestamp])->save();
    }

    /**
     * Deterministic ticket set. Fixed references keep re-runs idempotent.
     *
     * @return array<int, array{reference: string, subject: string, body: string, category: string, priority: string, status: string, replies: array<int, array{staff: bool, message: string}>}>
     */
    protected function tickets(): array
    {
        return [
            [
                'reference' => 'TKT-100001',
                'subject' => 'Unable to reset my password',
                'body' => "I clicked \"Forgot password\" but the reset email never arrives. I've checked my spam folder too.",
                'category' => 'Account',
                'priority' => 'high',
                'status' => 'resolved',
                'replies' => [
                    ['staff' => true, 'message' => 'Thanks for reaching out. Can you confirm the exact email address on your account so I can check our mail logs?'],
                    ['staff' => false, 'message' => "It's the same one I'm writing from. Still nothing after 30 minutes."],
                    ['staff' => true, 'message' => 'Found it — our provider had flagged the domain. I\'ve whitelisted it and re-sent the link. Please check now.'],
                    ['staff' => false, 'message' => 'Got it this time, and I\'m back in. Thank you!'],
                ],
            ],
            [
                'reference' => 'TKT-100002',
                'subject' => 'Payment charged twice',
                'body' => 'I was charged twice for my Pro subscription this month. Can you refund the duplicate?',
                'category' => 'Billing',
                'priority' => 'urgent',
                'status' => 'pending',
                'replies' => [
                    ['staff' => true, 'message' => 'Sorry about that. I can see two charges on the 3rd. I\'ve started a refund for the duplicate — it usually takes 5–7 business days.'],
                    ['staff' => false, 'message' => 'Appreciate the quick response. Will I get an email confirmation?'],
                    ['staff' => true, 'message' => 'Yes, a refund receipt will be emailed once the gateway confirms. I\'ll keep this ticket open until it clears.'],
                ],
            ],
            [
                'reference' => 'TKT-100003',
                'subject' => 'Feature request: dark mode',
                'body' => 'Would love a dark theme for the dashboard. Any plans for this?',
                'category' => 'Feedback',
                'priority' => 'low',
                'status' => 'open',
                'replies' => [
                    ['staff' => true, 'message' => 'Great suggestion! Dark mode is on our roadmap for next quarter. I\'ve added your +1 to the feature request.'],
                ],
            ],
            [
                'reference' => 'TKT-100004',
                'subject' => 'Can\'t upload my avatar',
                'body' => 'When I try to upload a profile photo I get an error that says "file too large". It\'s only 2MB.',
                'category' => 'Technical',
                'priority' => 'medium',
                'status' => 'resolved',
                'replies' => [
                    ['staff' => true, 'message' => 'Could you tell me the file format? We currently accept JPG, PNG and WebP up to 4MB.'],
                    ['staff' => false, 'message' => "It's a HEIC file from my iPhone."],
                    ['staff' => true, 'message' => 'That\'s the cause — HEIC isn\'t supported yet. Convert it to JPG and it\'ll upload fine. I\'ll log HEIC support as a request.'],
                    ['staff' => false, 'message' => 'Converted and it worked. Thanks!'],
                ],
            ],
            [
                'reference' => 'TKT-100005',
                'subject' => 'How do I export my data?',
                'body' => 'Is there a way to export all my account data as a CSV?',
                'category' => 'Account',
                'priority' => 'low',
                'status' => 'closed',
                'replies' => [
                    ['staff' => true, 'message' => 'Yes — go to Settings → Privacy → Export Data. You\'ll receive a download link by email within a few minutes.'],
                    ['staff' => false, 'message' => 'Perfect, found it. Thanks for the help.'],
                ],
            ],
            [
                'reference' => 'TKT-100006',
                'subject' => 'Two-factor authentication not working',
                'body' => 'My authenticator codes are being rejected even though the time is correct on my phone.',
                'category' => 'Security',
                'priority' => 'high',
                'status' => 'pending',
                'replies' => [
                    ['staff' => true, 'message' => 'Let\'s re-sync your device. Please remove the current 2FA entry and re-add it using the QR code in Settings → Security.'],
                ],
            ],
            [
                'reference' => 'TKT-100007',
                'subject' => 'Invoice missing VAT number',
                'body' => 'Our finance team needs the company VAT number on invoices. Where can I add it?',
                'category' => 'Billing',
                'priority' => 'medium',
                'status' => 'resolved',
                'replies' => [
                    ['staff' => true, 'message' => 'You can add your VAT/Tax ID under Settings → Billing → Company Details. New invoices will include it automatically.'],
                    ['staff' => false, 'message' => 'Added. Can you re-issue last month\'s invoice with it?'],
                    ['staff' => true, 'message' => 'Done — the updated invoice is in your billing history now.'],
                ],
            ],
            [
                'reference' => 'TKT-100008',
                'subject' => 'App is slow on mobile',
                'body' => 'The dashboard takes ages to load on my phone over 4G. Desktop is fine.',
                'category' => 'Technical',
                'priority' => 'medium',
                'status' => 'open',
                'replies' => [
                    ['staff' => true, 'message' => 'Thanks for flagging. Which device and browser are you using? We\'re rolling out a lighter mobile bundle this week.'],
                    ['staff' => false, 'message' => 'iPhone 12, Safari.'],
                ],
            ],
            [
                'reference' => 'TKT-100009',
                'subject' => 'Request to delete my account',
                'body' => 'Please delete my account and all associated data.',
                'category' => 'Account',
                'priority' => 'high',
                'status' => 'pending',
                'replies' => [
                    ['staff' => true, 'message' => 'We\'re sorry to see you go. To confirm this is you, please reply from your registered email confirming you want a permanent deletion.'],
                ],
            ],
            [
                'reference' => 'TKT-100010',
                'subject' => 'Team member can\'t access billing',
                'body' => 'I added a colleague but they can\'t see the billing section. How do I grant access?',
                'category' => 'Account',
                'priority' => 'low',
                'status' => 'resolved',
                'replies' => [
                    ['staff' => true, 'message' => 'Billing is restricted to the Owner and Admin roles. Update their role in Team → Members and they\'ll get access.'],
                    ['staff' => false, 'message' => 'Changed their role, works now. Cheers.'],
                ],
            ],
            [
                'reference' => 'TKT-100011',
                'subject' => 'Webhook events are delayed',
                'body' => 'Our webhook endpoint is receiving events 10–15 minutes late. Is there an incident?',
                'category' => 'Technical',
                'priority' => 'urgent',
                'status' => 'open',
                'replies' => [
                    ['staff' => true, 'message' => 'We\'re investigating elevated webhook latency right now. I\'ll update this ticket as soon as we have a fix.'],
                ],
            ],
            [
                'reference' => 'TKT-100012',
                'subject' => 'Discount code not applying',
                'body' => 'The code WELCOME20 says invalid at checkout, but the email says it\'s valid until next week.',
                'category' => 'Billing',
                'priority' => 'medium',
                'status' => 'closed',
                'replies' => [
                    ['staff' => true, 'message' => 'That code is limited to first-time subscribers. Since your account already had a trial, it won\'t apply — but I\'ve issued you a one-time 20% credit instead.'],
                    ['staff' => false, 'message' => 'That\'s more than fair, thank you!'],
                ],
            ],
            [
                'reference' => 'TKT-100013',
                'subject' => 'How to change my email address',
                'body' => 'I want to switch my login email to a new work address.',
                'category' => 'Account',
                'priority' => 'low',
                'status' => 'resolved',
                'replies' => [
                    ['staff' => true, 'message' => 'Head to Settings → Profile → Email. You\'ll need to verify the new address via a confirmation link before it takes effect.'],
                    ['staff' => false, 'message' => 'Done and verified. Thanks.'],
                ],
            ],
            [
                'reference' => 'TKT-100014',
                'subject' => 'Notification emails going to spam',
                'body' => 'All your notification emails land in my spam folder. Anything I can do?',
                'category' => 'Technical',
                'priority' => 'low',
                'status' => 'open',
                'replies' => [
                    ['staff' => true, 'message' => 'Adding no-reply@softivus.com to your contacts usually fixes it. We\'re also improving our sender reputation this month.'],
                ],
            ],
            [
                'reference' => 'TKT-100015',
                'subject' => 'Bug: dashboard chart shows wrong totals',
                'body' => 'The revenue chart on my dashboard doesn\'t match the numbers in the payments list.',
                'category' => 'Technical',
                'priority' => 'high',
                'status' => 'pending',
                'replies' => [
                    ['staff' => true, 'message' => 'Thanks for the detailed report. It looks like the chart isn\'t excluding refunded payments. Our team is patching it now.'],
                    ['staff' => false, 'message' => 'Good catch — that would explain the gap. Looking forward to the fix.'],
                ],
            ],
        ];
    }
}
