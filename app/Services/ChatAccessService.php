<?php

namespace App\Services;

use App\Models\ChatConversation;
use App\Models\Office;
use App\Models\User;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Collection;

class ChatAccessService
{
    private ?Collection $directory = null;
    private ?Collection $officeOwners = null;

    // Supports: super_admin, Super Admin, super-admin, Team Leader, etc.
    public function role(User $user): ?string
    {
        $names = $user->getRoleNames()->map(fn ($name) =>
            trim(preg_replace('/[^a-z0-9]+/', '_', strtolower(trim($name))), '_')
        )->all();

        foreach (['super_admin', 'owner', 'admin', 'team_leader', 'employee'] as $role) {
            if (in_array($role, $names, true)) {
                return $role;
            }
        }

        return null; // Unknown/unassigned roles never receive hierarchy access.
    }

    public function level(User $user): int
    {
        return ['super_admin' => 5, 'owner' => 4, 'admin' => 3,
            'team_leader' => 2, 'employee' => 1][$this->role($user)] ?? 0;
    }

    public function users(): Collection
    {
        return $this->directory ??= User::query()
            ->select(['id', 'name', 'office_id', 'team_leader_id'])->with('roles')->get()->keyBy('id');
    }

    public function ownedOfficeIds(User $owner): array
    {
        $this->officeOwners ??= Office::query()->pluck('owner_id', 'id');

        return $this->officeOwners
            ->filter(fn ($ownerId) => (int) $ownerId === (int) $owner->id)
            ->keys()->map(fn ($id) => (int) $id)->all();
    }

    private function ownsUserOffice(User $owner, User $other): bool
    {
        return $other->office_id !== null
            && in_array((int) $other->office_id, $this->ownedOfficeIds($owner), true);
    }

    private function sameOffice(User $a, User $b): bool
    {
        return !empty($a->office_id) && !empty($b->office_id)
            && (int) $a->office_id === (int) $b->office_id;
    }

    // Follows the actual reporting chain; cycles and missing/deleted users stop it.
    public function isAncestor(User $ancestor, User $child): bool
    {
        $seen = [(int) $child->id => true];
        $id = (int) $child->team_leader_id;
        $found = false;

        while ($id > 0) {
            if (isset($seen[$id])) {
                return false; // A corrupt cyclic chain grants no reporting access.
            }
            $seen[$id] = true;
            $leader = $this->users()->get($id);
            if (!$leader) {
                return false;
            }
            if ($id === (int) $ancestor->id) {
                $found = true;
            }
            $id = (int) $leader->team_leader_id;
        }

        return $found;
    }

    // Symmetric: A->B and B->A always use the same rule.
    // office_id/owner_id are persisted scope; session office is never applied to B.
    public function canChat(User $a, User $b): bool
    {
        if ((int) $a->id === (int) $b->id || !$this->level($a) || !$this->level($b)) {
            return false;
        }

        $roleA = $this->role($a);
        $roleB = $this->role($b);
        if ($roleA === 'team_leader' && $roleB === 'team_leader') {
            return $this->isAncestor($a, $b) || $this->isAncestor($b, $a);
        }
        if ($this->level($a) === $this->level($b)) {
            return false;
        }

        [$higher, $lower] = $this->level($a) > $this->level($b) ? [$a, $b] : [$b, $a];
        return match ($this->role($higher)) {
            'super_admin' => true,
            'owner' => $this->ownsUserOffice($higher, $lower),
            'admin' => $this->sameOffice($higher, $lower),
            'team_leader' => $this->role($lower) === 'employee'
                && $this->isAncestor($higher, $lower),
            default => false,
        };
    }

    public function canMonitorUser(User $viewer, User $target): bool
    {
        if ((int) $viewer->id === (int) $target->id || !$this->level($target)) {
            return false;
        }

        $role = $this->role($viewer);
        if ($role === 'team_leader') {
            return in_array($this->role($target), ['team_leader', 'employee'], true)
                && $this->isAncestor($viewer, $target);
        }
        if ($this->level($viewer) <= $this->level($target)) {
            return false;
        }

        return match ($role) {
            'super_admin' => true,
            'owner' => $this->ownsUserOffice($viewer, $target),
            'admin' => $this->sameOffice($viewer, $target),
            default => false,
        };
    }

    public function allowedUsers(User $viewer): Collection
    {
        return $this->users()->filter(fn (User $target) => $this->canChat($viewer, $target))
            ->sort(function (User $a, User $b) {
                return ($this->level($b) <=> $this->level($a))
                    ?: strcasecmp((string) $a->name, (string) $b->name);
            })->values();
    }

    public function monitoredIds(User $viewer): array
    {
        return $this->users()->filter(fn (User $target) =>
            $this->canMonitorUser($viewer, $target)
        )->keys()->map(fn ($id) => (int) $id)->all();
    }

    // Used for BOTH sidebar and every read/download endpoint.
    // One lower participant grants visibility to the WHOLE conversation.
    public function visibleQuery(User $viewer): Builder
    {
        $ids = array_values(array_unique(array_merge([(int) $viewer->id], $this->monitoredIds($viewer))));

        return ChatConversation::query()->whereHas('participants', fn (Builder $q) =>
            $q->whereIn('user_id', $ids)
        );
    }

    public function canView(User $viewer, ChatConversation $conversation): bool
    {
        return $this->visibleQuery($viewer)->whereKey($conversation->id)->exists();
    }

    public function canSend(User $viewer, ChatConversation $conversation): bool
    {
        if ($conversation->type !== 'private') {
            return false; // Legacy group histories are read-only.
        }
        $ids = $conversation->participants()->pluck('user_id')->map(fn ($id) => (int) $id);
        if ($ids->count() !== 2 || $ids->unique()->count() !== 2 || !$ids->contains((int) $viewer->id)) {
            return false;
        }
        $target = $this->users()->get($ids->first(fn ($id) => $id !== (int) $viewer->id));

        return $target && $this->canChat($viewer, $target);
    }
}
