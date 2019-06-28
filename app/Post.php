<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Post extends Model
{
	protected $table = "posts";
    protected $fillable = [
        'title', 'content'
    ];
	
	use SoftDeletes;
    protected $dates =['deleted_at'];
}
