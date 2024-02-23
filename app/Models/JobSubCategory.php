<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class JobSubCategory extends Model
{
    use HasFactory;

    protected $table = 'job_subcategories';

    protected $fillable = [
        'job_subcategory_id',
        'job_subcategory_job',
        'job_subcategory_price'
    ];

    // Define any relationships if needed
    public function category()
{
    return $this->belongsTo(JobCategory::class, 'job_category_id');
}
}
