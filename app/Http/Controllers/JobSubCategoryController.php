<?php

namespace App\Http\Controllers;
use App\Models\JobSubCategory;
use Illuminate\Http\Request;

class JobSubCategoryController extends Controller
{
    public function index()
    {
        // Retrieve all job categories
        $jobSubCategories = JobSubCategory::all();

        return response()->json($jobSubCategories);
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
// }   
}
