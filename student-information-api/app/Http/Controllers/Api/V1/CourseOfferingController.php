<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Models\CourseOffering;
use Illuminate\Http\Request;

class CourseOfferingController extends Controller
{
    public function index(Request $request)
    {
        $query = CourseOffering::with(['course', 'academicTerm', 'instructor']);
        if ($request->user()->hasRole('INSTRUCTOR')) $query->where('instructor_id', $request->user()->id);
        return response()->json(['success' => true, 'message' => 'Course offerings retrieved successfully.', 'data' => $query->paginate(min((int) $request->query('per_page', 20), 100))]);
    }

    public function store(Request $request)
    {
        $this->manage($request);
        $offering = CourseOffering::create($request->validate($this->rules()));
        return response()->json(['success' => true, 'message' => 'Course offering created successfully.', 'data' => $offering->load(['course', 'academicTerm', 'instructor'])], 201);
    }

    public function show(Request $request, CourseOffering $courseOffering)
    {
        abort_unless(! $request->user()->hasRole('INSTRUCTOR') || $courseOffering->instructor_id === $request->user()->id, 403);
        return response()->json(['success' => true, 'message' => 'Course offering retrieved successfully.', 'data' => $courseOffering->load(['course', 'academicTerm', 'instructor'])]);
    }

    public function update(Request $request, CourseOffering $courseOffering)
    {
        $this->manage($request);
        $courseOffering->update($request->validate($this->rules()));
        return response()->json(['success' => true, 'message' => 'Course offering updated successfully.', 'data' => $courseOffering]);
    }

    public function destroy(Request $request, CourseOffering $courseOffering)
    {
        $this->manage($request);
        $courseOffering->update(['status' => 'INACTIVE']);
        return response()->json(['success' => true, 'message' => 'Course offering deactivated successfully.', 'data' => null]);
    }

    public function students(Request $request, CourseOffering $courseOffering)
    {
        $canView = $request->user()->hasRole('ADMINISTRATOR')
            || $request->user()->hasRole('STAFF')
            || ($request->user()->hasRole('INSTRUCTOR') && $courseOffering->instructor_id === $request->user()->id);
        abort_unless($canView, 403);
        return response()->json(['success' => true, 'message' => 'Enrolled students retrieved successfully.', 'data' => $courseOffering->enrollments()->with(['student', 'grade'])->paginate(20)]);
    }

    private function rules(): array
    {
        return ['course_id' => ['required', 'exists:courses,id'], 'academic_term_id' => ['required', 'exists:academic_terms,id'], 'instructor_id' => ['required', 'exists:users,id'], 'section' => ['required', 'string'], 'schedule' => ['nullable', 'string'], 'room' => ['nullable', 'string'], 'capacity' => ['required', 'integer', 'min:1', 'max:500'], 'status' => ['sometimes', 'in:ACTIVE,INACTIVE']];
    }

    private function manage(Request $request): void
    {
        abort_unless($request->user()->hasRole('ADMINISTRATOR') || $request->user()->hasRole('STAFF'), 403);
    }
}