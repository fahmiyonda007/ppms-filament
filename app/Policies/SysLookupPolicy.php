<?php

namespace App\Policies;

use App\Models\User;
use App\Models\SysLookup;
use Illuminate\Auth\Access\HandlesAuthorization;

class SysLookupPolicy
{
    use HandlesAuthorization;

    /**
     * Determine whether the user can view any models.
     *
     * @param  \App\Models\User  $user
     * @return \Illuminate\Auth\Access\Response|bool
     */
    public function viewAny(User $user)
    {
        return $user->can('view_any_sys::lookup');
    }

    /**
     * Determine whether the user can view the model.
     *
     * @param  \App\Models\User  $user
     * @param  \App\Models\SysLookup  $sysLookup
     * @return \Illuminate\Auth\Access\Response|bool
     */
    public function view(User $user, SysLookup $sysLookup)
    {
        return $user->can('view_sys::lookup');
    }

    /**
     * Determine whether the user can create models.
     *
     * @param  \App\Models\User  $user
     * @return \Illuminate\Auth\Access\Response|bool
     */
    public function create(User $user)
    {
        return $user->can('create_sys::lookup');
    }

    /**
     * Determine whether the user can update the model.
     *
     * @param  \App\Models\User  $user
     * @param  \App\Models\SysLookup  $sysLookup
     * @return \Illuminate\Auth\Access\Response|bool
     */
    public function update(User $user, SysLookup $sysLookup)
    {
        return $user->can('update_sys::lookup');
    }

    /**
     * Determine whether the user can delete the model.
     *
     * @param  \App\Models\User  $user
     * @param  \App\Models\SysLookup  $sysLookup
     * @return \Illuminate\Auth\Access\Response|bool
     */
    public function delete(User $user, SysLookup $sysLookup)
    {
        return $user->can('delete_sys::lookup');
    }

    /**
     * Determine whether the user can bulk delete.
     *
     * @param  \App\Models\User  $user
     * @return \Illuminate\Auth\Access\Response|bool
     */
    public function deleteAny(User $user)
    {
        return $user->can('delete_any_sys::lookup');
    }

    /**
     * Determine whether the user can permanently delete.
     *
     * @param  \App\Models\User  $user
     * @param  \App\Models\SysLookup  $sysLookup
     * @return \Illuminate\Auth\Access\Response|bool
     */
    public function forceDelete(User $user, SysLookup $sysLookup)
    {
        return $user->can('{{ ForceDelete }}');
    }

    /**
     * Determine whether the user can permanently bulk delete.
     *
     * @param  \App\Models\User  $user
     * @return \Illuminate\Auth\Access\Response|bool
     */
    public function forceDeleteAny(User $user)
    {
        return $user->can('{{ ForceDeleteAny }}');
    }

    /**
     * Determine whether the user can restore.
     *
     * @param  \App\Models\User  $user
     * @param  \App\Models\SysLookup  $sysLookup
     * @return \Illuminate\Auth\Access\Response|bool
     */
    public function restore(User $user, SysLookup $sysLookup)
    {
        return $user->can('{{ Restore }}');
    }

    /**
     * Determine whether the user can bulk restore.
     *
     * @param  \App\Models\User  $user
     * @return \Illuminate\Auth\Access\Response|bool
     */
    public function restoreAny(User $user)
    {
        return $user->can('{{ RestoreAny }}');
    }

    /**
     * Determine whether the user can replicate.
     *
     * @param  \App\Models\User  $user
     * @param  \App\Models\SysLookup  $sysLookup
     * @return \Illuminate\Auth\Access\Response|bool
     */
    public function replicate(User $user, SysLookup $sysLookup)
    {
        return $user->can('{{ Replicate }}');
    }

    /**
     * Determine whether the user can reorder.
     *
     * @param  \App\Models\User  $user
     * @return \Illuminate\Auth\Access\Response|bool
     */
    public function reorder(User $user)
    {
        return $user->can('{{ Reorder }}');
    }

}
