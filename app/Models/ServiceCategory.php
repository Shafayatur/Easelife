<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\Service;

class ServiceCategory extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name', 
        'slug', 
        'image'
    ];

    /**
     * Get the services for this category
     */
    public function services()
    {
        return $this->hasMany(Service::class, 'category_id')
            ->where('is_active', true);
    }
}
