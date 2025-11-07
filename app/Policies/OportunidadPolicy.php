<?php

declare(strict_types=1);

namespace App\Policies;

use Illuminate\Foundation\Auth\User as AuthUser;
use App\Models\Oportunidad;
use Illuminate\Auth\Access\HandlesAuthorization;

class OportunidadPolicy
{
    use HandlesAuthorization;
    
    public function viewAny(AuthUser $authUser): bool
    {
        return $authUser->can('ViewAny:Oportunidad');
    }

    public function view(AuthUser $authUser, Oportunidad $oportunidad): bool
    {
        return $authUser->can('View:Oportunidad');
    }

    public function create(AuthUser $authUser): bool
    {
        return $authUser->can('Create:Oportunidad');
    }

    public function update(AuthUser $authUser, Oportunidad $oportunidad): bool
    {
        return $authUser->can('Update:Oportunidad');
    }

    public function delete(AuthUser $authUser, Oportunidad $oportunidad): bool
    {
        return $authUser->can('Delete:Oportunidad');
    }

    public function restore(AuthUser $authUser, Oportunidad $oportunidad): bool
    {
        return $authUser->can('Restore:Oportunidad');
    }

    public function forceDelete(AuthUser $authUser, Oportunidad $oportunidad): bool
    {
        return $authUser->can('ForceDelete:Oportunidad');
    }

    public function forceDeleteAny(AuthUser $authUser): bool
    {
        return $authUser->can('ForceDeleteAny:Oportunidad');
    }

    public function restoreAny(AuthUser $authUser): bool
    {
        return $authUser->can('RestoreAny:Oportunidad');
    }

    public function replicate(AuthUser $authUser, Oportunidad $oportunidad): bool
    {
        return $authUser->can('Replicate:Oportunidad');
    }

    public function reorder(AuthUser $authUser): bool
    {
        return $authUser->can('Reorder:Oportunidad');
    }

}