<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Models\Student;
use Illuminate\Http\Request;

class AcademicRecordController extends Controller
{
    public function show(Request $request, Student $student)
    {
        abort_unless(! $request->user()->hasRole('STUDENT') || $student->user_id === $request->user()->id, 403);
        $record = $student->enrollments()->with(['courseOffering.course', 'courseOffering.academicTerm', 'grade'])->get()->groupBy(fn ($enrollment) => $enrollment->courseOffering->academicTerm->academic_year.' '.$enrollment->courseOffering->academicTerm->term);
        return response()->json(['success' => true, 'message' => 'Academic record retrieved successfully.', 'data' => $record]);
    }
}