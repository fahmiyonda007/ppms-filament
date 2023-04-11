<?php

namespace App\Policies;

use App\Models\Vendor;
use App\Models\User;
use Illuminate\Auth\Access\Response;

class VendorPolicy
{
    public static function getPermissions(): array
    {
        return [
            "view" => "vendor:view",
            "create" => "vendor:create",
            "update" => "vendor:update",
            "delete" => "vendor:delete",
            "restore" => "vendor:restore",
            "forceDelete" => "vendor:force_delete",
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
    public function view(User $user, Vendor $vendor): bool
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
    public function update(User $user, Vendor $vendor): bool
    {
        return $user->hasPermissionTo($this->getPermissions()['update']);
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, Vendor $vendor): bool
    {
        return $user->hasPermissionTo($this->getPermissions()['delete']);
    }

    /**
     * Determine whether the user can restore the model.
     */
    public function restore(User $user, Vendor $vendor): bool
    {
        return $user->hasPermissionTo($this->getPermissions()['restore']);
    }

    /**
     * Determine whether the user can permanently delete the model.
     */
    public function forceDelete(User $user, Vendor $vendor): bool
    {
        return $user->hasPermissionTo($this->getPermissions()['forceDelete']);
    }
}
