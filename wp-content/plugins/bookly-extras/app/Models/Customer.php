<?php

namespace App\Models;

use \Illuminate\Database\Eloquent\Model;

class Customer extends Model
{
	protected $table = 'bookly_customers';

	protected $fillable = [
		'full_name',
		'email',
	];

	public function user()
	{
	    return $this->belongsTo(User::class, 'wp_user_id');
	}

	public function favorites()
	{
		return $this->belongsToMany(Staff::class, 'bookly_favorites', 'customer_id', 'staff_id');
	}
}
