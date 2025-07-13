<?php

namespace App\Policies;

use App\Models\User;
use Illuminate\Auth\Access\Response;

class AdminPolicy
{
// app/Policies/AdminPolicy.php
public function manageUsers(User $user): bool {
    return $user->isAdmin();
}
}
