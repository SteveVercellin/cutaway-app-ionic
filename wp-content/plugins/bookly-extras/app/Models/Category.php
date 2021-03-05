<?php

namespace App\Models;

use \Illuminate\Database\Eloquent\Model;

class Category extends Model
{
	protected $table = 'bookly_categories';

	protected $fillable = [
		//
	];

	public function services()
	{
	    return $this->hasMany(Service::class);
	}
}
