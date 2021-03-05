<?php

namespace App\Models;

use \Illuminate\Database\Eloquent\Model;

class User extends Model
{
	protected $table = 'users';
    protected $primaryKey = 'ID';

	protected $fillable = [
		'user_login',
		'user_email',
	];

	public function staff()
	{
	    return $this->hasOne(Staff::class, 'wp_user_id');
	}

    public function customer()
    {
        return $this->hasOne(Customer::class, 'wp_user_id');
    }
}
