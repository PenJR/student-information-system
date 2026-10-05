<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Models\Grade;
use Illuminate\Http\Request;

class GradeController extends Controller
{
    public function index(Request $request)
    {
        $query = Grade::with('enrollment.student', 'enrollment.courseOffering.course');
        if ($request->user()->hasRole('STUDENT')) $query->whereHas('enrollment.student', fn ($q) => $q->where('user_id', $request->user()->id));
        if ($request->user()->hasRole('INSTRUCTOR')) $query->whereHas('enrollment.courseOffering', fn ($q) => $q->where('instructor_id', $request->user()->id));
        return response()->json(['success' => true, 'message' => 'Grades retrieved successfully.', 'data' => $query->paginate(20)]);
    }

    public function store(Request $request)
    {
        $data = $request->validate($this->rules());
        $grade = new Grade($data);
        $this->authorizeGrade($request, $grade->enrollment_id);
        $grade->save();
        return response()->json(['success' => true, 'message' => 'Grade created successfully.', 'data' => $grade], 201);
    }

    public function show(Request $request, Grade $grade) { $this->authorizeGrade($request, $grade->enrollment_id, true); return response()->json(['success' => true, 'message' => 'Grade retrieved successfully.', 'data' => $grade->load('enrollment')]); }
    public function update(Request $request, Grade $grade) { $this->authorizeGrade($request, $grade->enrollment_id); $grade->update($request->validate($this->rules(false))); return response()->json(['success' => true, 'message' => 'Grade updated successfully.', 'data' => $grade]); }
    public function studentGrades(Request $request, int $student) { abort_unless(! $request->user()->hasRole('STUDENT') || $request->user()->student?->id === $student, 403); return response()->json(['success' => true, 'message' => 'Student grades retrieved successfully.', 'data' => Grade::whereHas('enrollment', fn ($q) => $q->where('student_id', $student))->with('enrollment.courseOffering')->paginate(20)]); }
    private function rules(bool $enrollment = true): array { return ['enrollment_id' => [$enrollment ? 'required' : 'sometimes', 'exists:enrollments,id'], 'midterm_grade' => ['nullable', 'numeric', 'between:0,100'], 'final_grade' => ['nullable', 'numeric', 'between:0,100'], 'remarks' => ['nullable', 'string']]; }
    private function authorizeGrade(Request $request, int $enrollmentId, bool $read = false): void { $enrollment = \App\Models\Enrollment::with('courseOffering')->findOrFail($enrollmentId); $allowed = $request->user()->hasRole('ADMINISTRATOR') || $request->user()->hasRole('STAFF') || ($request->user()->hasRole('INSTRUCTOR') && $enrollment->courseOffering->instructor_id === $request->user()->id) || ($read && $request->user()->hasRole('STUDENT') && $enrollment->student->user_id === $request->user()->id); abort_unless($allowed, 403); }
}