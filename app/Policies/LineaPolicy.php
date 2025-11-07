<?php

declare(strict_types=1);

namespace App\Policies;

use Illuminate\Foundation\Auth\User as AuthUser;
use App\Models\Linea;
use Illuminate\Auth\Access\HandlesAuthorization;

class LineaPolicy
{
    use HandlesAuthorization;
    
    public function viewAny(AuthUser $authUser): bool
    {
        return $authUser->can('ViewAny:Linea');
    }

    public function view(AuthUser $authUser, Linea $linea): bool
    {
        return $authUser->can('View:Linea');
    }

    public function create(AuthUser $authUser): bool
    {
        return $authUser->can('Create:Linea');
    }

    public function update(AuthUser $authUser, Linea $linea): bool
    {
        return $authUser->can('Update:Linea');
    }

    public function delete(AuthUser $authUser, Linea $linea): bool
    {
        return $authUser->can('Delete:Linea');
    }

    public function restore(AuthUser $authUser, Linea $linea): bool
    {
        return $authUser->can('Restore:Linea');
    }

    public function forceDelete(AuthUser $authUser, Linea $linea): bool
    {
        return $authUser->can('ForceDelete:Linea');
    }

    public function forceDeleteAny(AuthUser $authUser): bool
    {
        return $authUser->can('ForceDeleteAny:Linea');
    }

    public function restoreAny(AuthUser $authUser): bool
    {
        return $authUser->can('RestoreAny:Linea');
    }

    public function replicate(AuthUser $authUser, Linea $linea): bool
    {
        return $authUser->can('Replicate:Linea');
    }

    public function reorder(AuthUser $authUser): bool
    {
        return $authUser->can('Reorder:Linea');
    }

}