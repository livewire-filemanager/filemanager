<?php

namespace LivewireFilemanager\Filemanager\Tests\Models;

use Illuminate\Foundation\Auth\User;

class TestUserModel extends User
{
    protected $table = 'users';

    protected $fillable = [
        'name',
        'email',
    ];
}
