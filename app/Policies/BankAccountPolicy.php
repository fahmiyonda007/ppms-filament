<?php

namespace App\Policies;

use App\Models\BankAccount;
use App\Models\User;
use Illuminate\Auth\Access\Response;

class BankAccountPolicy
{
    public static function getPermissions(): array
    {
        return [
            "view" => "bankaccount:view",
            "create" => "bankaccount:create",
            "update" => "bankaccount:update",
            "delete" => "bankaccount:delete",
            "restore" => "bankaccount:restore",
            "forceDelete" => "bankaccount:force_delete",
        ];
    }
    /**
     * Determine whether the user can view any models.
     */
    public function viewAny(User $user): bool
    {
        return $user->hasPermissionTo($this->getPermissions()['view']);
    }

    /**
     * Determine whether the user can view the model.
     */
    public function view(User $user, BankAccount $bankAccount): bool
    {
        return $user->hasPermissionTo($this->getPermissions()['view']);
    }

    /**
     * Determine whether the user can create models.
     */
    public function create(User $user): bool
    {
        return $user->hasPermissionTo($this->getPermissions()['create']);
    }

    /**
     * Determine whether the user can update the model.
     */
    public function update(User $user, BankAccount $bankAccount): bool
    {
        return $user->hasPermissionTo($this->getPermissions()['update']);
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, BankAccount $bankAccount): bool
    {
        return $user->hasPermissionTo($this->getPermissions()['delete']);
    }

    /**
     * Determine whether the user can restore the model.
     */
    public function restore(User $user, BankAccount $bankAccount): bool
    {
        return $user->hasPermissionTo($this->getPermissions()['restore']);
    }

    /**
     * Determine whether the user can permanently delete the model.
     */
    public function forceDelete(User $user, BankAccount $bankAccount): bool
    {
        return $user->hasPermissionTo($this->getPermissions()['forceDelete']);
    }
}
