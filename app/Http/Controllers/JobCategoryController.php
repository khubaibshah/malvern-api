<?php

namespace App\Http\Controllers;

use App\Models\JobCategory;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class JobCategoryController extends Controller
{
    public function index()
    {
        // Retrieve all job categories
        $jobCategories = JobCategory::all();

        return response()->json($jobCategories);
    }

    public function getJobCategoriesWithSubcategories()
    {
        $categories = JobCategory::with('subcategories')->get();

        return response()->json($categories);
    }

    // public function store(Request $request)
    // {
    //     $request->validate([
    //         'job_category' => 'required|string',
    //         'job_category_id' => 'required|numeric',
    //         'active_job' => 'required|boolean',
    //         'job_subcategory_id' => 'required|numeric',
    //     ]);

    //     // Create a new job category
    //     $jobCategory = JobCategory::create($request->all());

    //     return response()->json(['message' => 'Job category created successfully', 'data' => $jobCategory], 201);
    // }
}
