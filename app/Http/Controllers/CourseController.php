<?php

namespace App\Http\Controllers;

use App\Models\Course;
use Illuminate\Http\Request;

class CourseController extends Controller
{
    public function index()
    {
        $courses = Course::with(['videos', 'enrollments', 'certificates'])->get();
        return response()->json($courses);
    }

    public function store(Request $request)
    {
        $validatedData = $request->validate([
            'publisher_id' => 'required|exists:users,id',
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'created_at' => 'nullable|date',
            'updated_at' => 'nullable|date',
        ]);

        $course = Course::create($validatedData);

        return response()->json($course, 201);
    }

    public function show($id)
    {
        $course = Course::with(['videos', 'enrollments', 'certificates'])->findOrFail($id);
        return response()->json($course);
    }
}