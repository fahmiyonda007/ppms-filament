<?php

namespace App\Policies;

use App\Models\User;
use App\Models\DepositVendor;
use Illuminate\Auth\Access\HandlesAuthorization;

class DepositVendorPolicy
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
        return $user->can('view_any_deposit::vendor');
    }

    /**
     * Determine whether the user can view the model.
     *
     * @param  \App\Models\User  $user
     * @param  \App\Models\DepositVendor  $depositVendor
     * @return \Illuminate\Auth\Access\Response|bool
     */
    public function view(User $user, DepositVendor $depositVendor)
    {
        return $user->can('view_deposit::vendor');
    }

    /**
     * Determine whether the user can create models.
     *
     * @param  \App\Models\User  $user
     * @return \Illuminate\Auth\Access\Response|bool
     */
    public function create(User $user)
    {
        return $user->can('create_deposit::vendor');
    }

    /**
     * Determine whether the user can update the model.
     *
     * @param  \App\Models\User  $user
     * @param  \App\Models\DepositVendor  $depositVendor
     * @return \Illuminate\Auth\Access\Response|bool
     */
    public function update(User $user, DepositVendor $depositVendor)
    {
        return $user->can('update_deposit::vendor');
    }

    /**
     * Determine whether the user can delete the model.
     *
     * @param  \App\Models\User  $user
     * @param  \App\Models\DepositVendor  $depositVendor
     * @return \Illuminate\Auth\Access\Response|bool
     */
    public function delete(User $user, DepositVendor $depositVendor)
    {
        return $user->can('delete_deposit::vendor');
    }

    /**
     * Determine whether the user can bulk delete.
     *
     * @param  \App\Models\User  $user
     * @return \Illuminate\Auth\Access\Response|bool
     */
    public function deleteAny(User $user)
    {
        return $user->can('delete_any_deposit::vendor');
    }

    /**
     * Determine whether the user can permanently delete.
     *
     * @param  \App\Models\User  $user
     * @param  \App\Models\DepositVendor  $depositVendor
     * @return \Illuminate\Auth\Access\Response|bool
     */
    public function forceDelete(User $user, DepositVendor $depositVendor)
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
     * @param  \App\Models\DepositVendor  $depositVendor
     * @return \Illuminate\Auth\Access\Response|bool
     */
    public function restore(User $user, DepositVendor $depositVendor)
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
     * @param  \App\Models\DepositVendor  $depositVendor
     * @return \Illuminate\Auth\Access\Response|bool
     */
    public function replicate(User $user, DepositVendor $depositVendor)
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
