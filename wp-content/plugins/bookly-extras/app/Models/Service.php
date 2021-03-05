<?php

namespace App\Models;

use \Illuminate\Database\Eloquent\Model;

class Service extends Model
{
	protected $table = 'bookly_services';

	protected $fillable = [
		//
	];

	public function category()
	{
	    return $this->belongsTo(Category::class);
	}
}
