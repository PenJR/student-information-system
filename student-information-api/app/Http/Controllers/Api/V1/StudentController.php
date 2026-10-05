<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Models\Student;
use Illuminate\Http\Request;

class StudentController extends Controller
{
    public function index(Request $request)
    {
        $query = Student::with('program');
        if ($request->user()->hasRole('STUDENT')) $query->where('user_id', $request->user()->id);
        $query->when($request->search, fn ($q, $value) => $q->where(fn ($q) => $q->where('first_name', 'like', "%$value%")->orWhere('last_name', 'like', "%$value%")->orWhere('student_number', 'like', "%$value%")));
        $query->when($request->program_id, fn ($q, $value) => $q->where('program_id', $value));
        $query->when($request->year_level, fn ($q, $value) => $q->where('year_level', $value));
        $query->when($request->status, fn ($q, $value) => $q->where('status', $value));
        $sort = in_array($request->sort, ['student_number', 'first_name', 'last_name', 'year_level', 'created_at'], true) ? $request->sort : 'id';
        return response()->json(['success' => true, 'message' => 'Students retrieved successfully.', 'data' => $query->orderBy($sort)->paginate(min((int) $request->query('per_page', 20), 100))]);
    }

    public function store(Request $request)
    {
        $this->manage($request);
        $student = Student::create($request->validate($this->rules()));
        return response()->json(['success' => true, 'message' => 'Student created successfully.', 'data' => $student], 201);
    }

    public function show(Request $request, Student $student)
    {
        $this->view($request, $student);
        return response()->json(['success' => true, 'message' => 'Student retrieved successfully.', 'data' => $student->load('program')]);
    }

    public function update(Request $request, Student $student)
    {
        $this->manage($request);
        $student->update($request->validate($this->rules($student->id)));
        return response()->json(['success' => true, 'message' => 'Student updated successfully.', 'data' => $student]);
    }

    public function destroy(Request $request, Student $student)
    {
        $this->manage($request);
        $student->update(['status' => 'INACTIVE']);
        return response()->json(['success' => true, 'message' => 'Student deactivated successfully.', 'data' => null]);
    }

    private function rules(?int $id = null): array
    {
        $unique = $id ? ','.$id : '';
        return ['student_number' => ['required', 'string', 'max:50', 'unique:students,student_number'.$unique], 'first_name' => ['required', 'string', 'max:255'], 'middle_name' => ['nullable', 'string'], 'last_name' => ['required', 'string', 'max:255'], 'suffix' => ['nullable', 'string'], 'birth_date' => ['nullable', 'date'], 'email' => ['nullable', 'email'], 'contact_number' => ['nullable', 'string'], 'address' => ['nullable', 'string'], 'program_id' => ['required', 'exists:programs,id'], 'year_level' => ['required', 'integer', 'between:1,4'], 'status' => ['sometimes', 'in:ACTIVE,INACTIVE,GRADUATED,SUSPENDED']];
    }

    private function manage(Request $request): void
    {
        abort_unless($request->user()->hasRole('ADMINISTRATOR') || $request->user()->hasRole('STAFF'), 403);
    }

    private function view(Request $request, Student $student): void
    {
        abort_unless(! $request->user()->hasRole('STUDENT') || $student->user_id === $request->user()->id, 403);
    }
}