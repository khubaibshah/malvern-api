<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class JobCategory extends Model
{
    use HasFactory;

    protected $table = 'jobs_categories';

    protected $fillable = [
        'job_category',
        'job_category_id',
        'active_job',
        'job_subcategory_id',
    ];

    // Define any relationships if needed
    public function subcategories()
{
    return $this->hasMany(JobSubCategory::class, 'job_subcategory_id');
}
}
