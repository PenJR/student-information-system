<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Models\AcademicTerm;
use App\Models\Course;
use App\Models\Program;
use Illuminate\Http\Request;

class AcademicController extends Controller
{
    private array $models = ['programs' => Program::class, 'courses' => Course::class, 'academic-terms' => AcademicTerm::class];

    public function index(Request $request, string $resource)
    {
        $query = $this->query($resource);
        $allowed = ['id', 'name', 'code', 'course_code', 'academic_year', 'created_at'];
        $sort = in_array($request->query('sort'), $allowed, true) ? $request->query('sort') : 'id';
        return response()->json(['success' => true, 'message' => 'Records retrieved successfully.', 'data' => $query->orderBy($sort)->paginate(min((int) $request->query('per_page', 20), 100))]);
    }

    public function store(Request $request, string $resource)
    {
        $this->manage($request);
        $data = $request->validate($this->rules($resource));
        $record = $this->query($resource)->create($data);
        return response()->json(['success' => true, 'message' => 'Record created successfully.', 'data' => $record], 201);
    }

    public function show(Request $request, string $resource, int $id)
    {
        return response()->json(['success' => true, 'message' => 'Record retrieved successfully.', 'data' => $this->query($resource)->findOrFail($id)]);
    }

    public function update(Request $request, string $resource, int $id)
    {
        $this->manage($request);
        $record = $this->query($resource)->findOrFail($id);
        $record->update($request->validate($this->rules($resource, $id)));
        return response()->json(['success' => true, 'message' => 'Record updated successfully.', 'data' => $record]);
    }

    public function destroy(Request $request, string $resource, int $id)
    {
        $this->manage($request);
        $record = $this->query($resource)->findOrFail($id);
        $record->update(['status' => 'INACTIVE']);
        return response()->json(['success' => true, 'message' => 'Record deactivated successfully.', 'data' => null]);
    }

    private function query(string $resource)
    {
        abort_unless(isset($this->models[$resource]), 404);
        return $this->models[$resource]::query();
    }

    private function rules(string $resource, ?int $id = null): array
    {
        $unique = $id ? ','.$id : '';
        return match ($resource) {
            'programs' => ['code' => ['required', 'string', 'max:50', 'unique:programs,code'.$unique], 'name' => ['required', 'string', 'max:255'], 'description' => ['nullable', 'string'], 'status' => ['sometimes', 'in:ACTIVE,INACTIVE']],
            'courses' => ['course_code' => ['required', 'string', 'max:50', 'unique:courses,course_code'.$unique], 'course_title' => ['required', 'string', 'max:255'], 'description' => ['nullable', 'string'], 'units' => ['required', 'integer', 'between:1,6'], 'status' => ['sometimes', 'in:ACTIVE,INACTIVE']],
            default => ['academic_year' => ['required', 'regex:/^\d{4}-\d{4}$/'], 'term' => ['required', 'in:FIRST,SECOND,SUMMER'], 'start_date' => ['required', 'date'], 'end_date' => ['required', 'date', 'after:start_date'], 'status' => ['sometimes', 'in:ACTIVE,INACTIVE']],
        };
    }

    private function manage(Request $request): void
    {
        abort_unless($request->user()->hasRole('ADMINISTRATOR') || $request->user()->hasRole('STAFF'), 403);
    }
}