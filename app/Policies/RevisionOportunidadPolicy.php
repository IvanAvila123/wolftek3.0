<?php

declare(strict_types=1);

namespace App\Policies;

use Illuminate\Foundation\Auth\User as AuthUser;
use App\Models\RevisionOportunidad;
use Illuminate\Auth\Access\HandlesAuthorization;

class RevisionOportunidadPolicy
{
    use HandlesAuthorization;
    
    public function viewAny(AuthUser $authUser): bool
    {
        return $authUser->can('ViewAny:RevisionOportunidad');
    }

    public function view(AuthUser $authUser, RevisionOportunidad $revisionOportunidad): bool
    {
        return $authUser->can('View:RevisionOportunidad');
    }

    public function create(AuthUser $authUser): bool
    {
        return $authUser->can('Create:RevisionOportunidad');
    }

    public function update(AuthUser $authUser, RevisionOportunidad $revisionOportunidad): bool
    {
        return $authUser->can('Update:RevisionOportunidad');
    }

    public function delete(AuthUser $authUser, RevisionOportunidad $revisionOportunidad): bool
    {
        return $authUser->can('Delete:RevisionOportunidad');
    }

    public function restore(AuthUser $authUser, RevisionOportunidad $revisionOportunidad): bool
    {
        return $authUser->can('Restore:RevisionOportunidad');
    }

    public function forceDelete(AuthUser $authUser, RevisionOportunidad $revisionOportunidad): bool
    {
        return $authUser->can('ForceDelete:RevisionOportunidad');
    }

    public function forceDeleteAny(AuthUser $authUser): bool
    {
        return $authUser->can('ForceDeleteAny:RevisionOportunidad');
    }

    public function restoreAny(AuthUser $authUser): bool
    {
        return $authUser->can('RestoreAny:RevisionOportunidad');
    }

    public function replicate(AuthUser $authUser, RevisionOportunidad $revisionOportunidad): bool
    {
        return $authUser->can('Replicate:RevisionOportunidad');
    }

    public function reorder(AuthUser $authUser): bool
    {
        return $authUser->can('Reorder:RevisionOportunidad');
    }

}