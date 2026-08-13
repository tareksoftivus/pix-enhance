<?php

namespace App\Modules\Support\Policies;

use App\Models\Admin;
use App\Models\User;
use App\Modules\Support\Models\SupportTicket;
use Illuminate\Contracts\Auth\Authenticatable;

class SupportTicketPolicy
{
    public function viewAny(Authenticatable $user): bool
    {
        return $user->can('support-tickets.view');
    }

    public function view(Authenticatable $user, SupportTicket $ticket): bool
    {
        if ($user instanceof Admin) {
            return $user->can('support-tickets.view');
        }

        return $this->owns($user, $ticket) && $user->can('support-tickets.view');
    }

    public function create(Authenticatable $user): bool
    {
        return $user->can('support-tickets.create');
    }

    public function reply(Authenticatable $user, SupportTicket $ticket): bool
    {
        if ($user instanceof Admin) {
            return $user->can('support-tickets.reply');
        }

        return $this->owns($user, $ticket)
            && ! $ticket->isClosed()
            && $user->can('support-tickets.reply');
    }

    public function update(Authenticatable $user, SupportTicket $ticket): bool
    {
        return $user instanceof Admin && $user->can('support-tickets.edit');
    }

    public function delete(Authenticatable $user, SupportTicket $ticket): bool
    {
        return $user instanceof Admin && $user->can('support-tickets.delete');
    }

    /**
     * Whether the given web user owns the ticket.
     */
    protected function owns(Authenticatable $user, SupportTicket $ticket): bool
    {
        return $user instanceof User && $ticket->user_id === $user->getKey();
    }
}
