<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Models\Enrollment;
use Illuminate\Database\QueryException;
use Illuminate\Http\Request;

class EnrollmentController extends Controller
{
    public function index(Request $request)
    {
        $query = Enrollment::with(['student', 'courseOffering.course', 'courseOffering.academicTerm']);
        if ($request->user()->hasRole('STUDENT')) $query->whereHas('student', fn ($q) => $q->where('user_id', $request->user()->id));
        return response()->json(['success' => true, 'message' => 'Enrollments retrieved successfully.', 'data' => $query->paginate(20)]);
    }

    public function store(Request $request)
    {
        $this->manage($request);
        $data = $request->validate(['student_id' => ['required', 'exists:students,id'], 'course_offering_id' => ['required', 'exists:course_offerings,id'], 'enrollment_date' => ['nullable', 'date'], 'status' => ['sometimes', 'in:ENROLLED,DROPPED,COMPLETED']]);
        if (Enrollment::where($data)->exists()) return response()->json(['success' => false, 'message' => 'Enrollment already exists.'], 409);
        try { $enrollment = Enrollment::create($data + ['enrollment_date' => $data['enrollment_date'] ?? now()->toDateString()]); }
        catch (QueryException) { return response()->json(['success' => false, 'message' => 'Enrollment already exists.'], 409); }
        return response()->json(['success' => true, 'message' => 'Enrollment created successfully.', 'data' => $enrollment], 201);
    }

    public function show(Request $request, Enrollment $enrollment)
    {
        $this->view($request, $enrollment);
        return response()->json(['success' => true, 'message' => 'Enrollment retrieved successfully.', 'data' => $enrollment->load(['student', 'courseOffering.course', 'courseOffering.academicTerm'])]);
    }

    public function update(Request $request, Enrollment $enrollment)
    {
        $this->manage($request);
        $enrollment->update($request->validate(['status' => ['required', 'in:ENROLLED,DROPPED,COMPLETED']]));
        return response()->json(['success' => true, 'message' => 'Enrollment updated successfully.', 'data' => $enrollment]);
    }

    public function destroy(Request $request, Enrollment $enrollment)
    {
        $this->manage($request);
        $enrollment->update(['status' => 'DROPPED']);
        return response()->json(['success' => true, 'message' => 'Enrollment deactivated successfully.', 'data' => null]);
    }

    public function studentEnrollments(Request $request, int $student)
    {
        abort_unless(! $request->user()->hasRole('STUDENT') || $request->user()->student?->id === $student, 403);
        return response()->json(['success' => true, 'message' => 'Student enrollments retrieved successfully.', 'data' => Enrollment::where('student_id', $student)->with('courseOffering.course')->paginate(20)]);
    }

    private function manage(Request $request): void { abort_unless($request->user()->hasRole('ADMINISTRATOR') || $request->user()->hasRole('STAFF'), 403); }
    private function view(Request $request, Enrollment $enrollment): void { abort_unless(! $request->user()->hasRole('STUDENT') || $enrollment->student->user_id === $request->user()->id, 403); }
}