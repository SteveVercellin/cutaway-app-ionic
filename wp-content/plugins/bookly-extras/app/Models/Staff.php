<?php

namespace App\Models;

use \Illuminate\Database\Eloquent\Model;

class Staff extends Model
{
	protected $table = 'bookly_staff';

	protected $fillable = [
		'full_name',
		'email',
	];

	public function user()
	{
	    return $this->belongsTo(User::class, 'wp_user_id');
	}

	public function customers()
	{
	    return $this->belongsToMany(Customer::class, 'bookly_favorites', 'staff_id', 'customer_id');
	}
}
