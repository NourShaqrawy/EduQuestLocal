<?php

namespace App\Http\Controllers;

use App\Models\Course;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;


class CourseController extends Controller
{
    // عرض جميع الكورسات (موجود)
    public function index()
    {
        $courses = Course::with(['videos', 'enrollments', 'certificates'])->get();
        return response()->json($courses);
    }

    // إنشاء كورس جديد (موجود)
    public function store(Request $request)
{
    $validatedData = $request->validate([
        'category_id' => 'required|exists:categories,id',
        'title' => 'required|string|max:255',
        'description' => 'nullable|string'
    ]);

    // تأكد من أن publisher_id هو ID فقط وليس كائن المستخدم
    $validatedData['publisher_id'] = Auth::id(); // هذا سيعيد رقم ID فقط

    $course = Course::create($validatedData);

    return response()->json($course, 201);
}
    // عرض كورس معين (موجود)
    public function show($id)
    {
        $course = Course::with(['videos', 'enrollments', 'certificates'])->findOrFail($id);
        return response()->json($course);
    }

    // تحديث كورس (جديد)
    public function update(Request $request, $id)
    {
        $course = Course::findOrFail($id);
        
        // التحقق من أن المستخدم هو الناشر أو مدير
        if (Auth::user()->role !== 'admin' && Auth::id() !== $course->publisher_id) {
            return response()->json(['message' => 'غير مصرح بالتعديل على هذا الكورس'], 403);
        }

        $validatedData = $request->validate([
            'title' => 'sometimes|string|max:255',
            'description' => 'nullable|string',
         
        ]);

        $course->update($validatedData);
        return response()->json($course);
    }

    // حذف كورس (جديد)
    public function destroy($id)
    {
        $course = Course::findOrFail($id);
        $course->delete();
        
        return response()->json(['message' => 'تم حذف الكورس بنجاح']);
    }
}