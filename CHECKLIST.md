# Implementation checklist

This file tracks Activity 1 (Laravel API) and Activity 2 (React frontend). An item is marked complete only after it exists in the project and has been checked.

## Activity 1 — REST API

### Section 1 — Project setup

- [x] Laravel project is properly configured (Laravel 12.69.2, PHP 8.2, Sanctum)
- [x] SQLite database is configured (`DB_CONNECTION=sqlite`, `database/database.sqlite` present)
- [x] Environment configuration is correct (`.env` and `.env.example`)
- [x] `.env.example` exists
- [x] API structure uses `/api/v1` (42 routes registered)
- [x] Git repository initialized at the workspace root
- [x] Project can start (`php artisan about` succeeds; `php artisan serve` smoke-tested)

### Section 2 — Database design

- [ ] ERD is created (OpenAPI/migrations exist; a dedicated ERD diagram is still missing)
- [x] `users`, `students`, `programs`, `courses`, `academic_terms`, `course_offerings`, `enrollments`, `grades` tables
- [x] Primary keys, foreign keys, indexes
- [x] Unique `student_number` and `course_code`
- [x] Duplicate enrollment in the same offering prevented (`unique(student_id, course_offering_id)`)
- [x] Required Eloquent relationships exist on the models

### Section 3 — Users and roles

- [x] Administrator, registrar/staff, instructor, and student roles (`RoleName` enum)
- [x] User model and role handling (`hasRole`)
- [x] Role authorization in controllers
- [x] Object-level checks for student-owned records and instructor-assigned grades
- [ ] Laravel policies/Form Requests are not used yet (authorization/validation live in controllers)

### Section 4 — Authentication

- [x] `POST /api/v1/auth/login`
- [x] `POST /api/v1/auth/logout`
- [x] `GET /api/v1/auth/me`
- [x] Passwords hashed (`hashed` cast)
- [x] Protected routes use `auth:sanctum`
- [x] Invalid login rejected (422)
- [x] Logout deletes API tokens
- [x] Password hashes excluded from `UserResource`

### Sections 5–12 — Domain APIs

- [x] Students, programs, courses, academic terms, course offerings, enrollments, grades CRUD-style routes
- [x] Nested enrollments/grades and academic record routes
- [ ] Academic record payload should also include student header details (currently grouped enrollments only)
- [ ] Dedicated Form Request classes still missing

### Section 13 — Collection features

- [x] Students: search, filters, sort, pagination, capped `per_page`
- [x] Programs/courses/terms: sort and pagination
- [ ] Search/filter is not consistently implemented on every collection (enrollments/grades/offerings are paginated but have limited query options)

### Section 14 — Validation and errors

- [x] 201 on create
- [x] 401 unauthenticated
- [x] 403 forbidden
- [x] 404 via `findOrFail` / model binding
- [x] 409 duplicate enrollment
- [x] 422 validation JSON
- [ ] DELETE currently returns JSON 200 instead of 204
- [ ] API-wide JSON handlers for 401/403/404/409/500 are incomplete (only validation is customized)
- [ ] Enrollment duplicate pre-check should key on student + offering only

### Section 15 — Security

- [x] Password hashes not returned
- [x] `.env` used for secrets
- [x] Eloquent/query builder used (no unsafe raw SQL found)
- [x] CORS configured for Vite (`localhost:5173`)
- [ ] Confirm `.env` is never committed after Git is in use
- [ ] Production `APP_DEBUG=false` stack-trace behavior should be documented/tested

### Section 16 — Seed data

- [x] Seeder creates 5+ users, 3 programs, 100 students, 20 courses, 2 terms, 20 offerings, 200 enrollments, 100 grades

### Section 17 — Automated testing

- [x] Valid login, invalid login, missing authentication
- [x] Student search/filter/sort/pagination
- [x] Student object-level 403
- [x] Duplicate enrollment 409
- [x] Instructor grade 403
- [ ] Broader CRUD, validation, 404, and collection tests still missing

### Section 18 — Postman

- [x] Collection exists under `postman/` for auth, students, programs, courses, terms, offerings, enrollments, grades, academic record
- [ ] Not every resource has full update/delete example requests

### Section 19 — OpenAPI

- [x] `student-information-api/docs/openapi.yaml` served at `/api/docs`
- [ ] Completeness of request/response/error docs should be reviewed in a later pass

### Section 20 — Final documentation

- [x] API README includes setup, migrate, seed, test, and auth notes
- [ ] Dedicated ERD, AI log, test evidence, and cleaned README (Laravel boilerplate still present) are incomplete

## Activity 2 — Frontend

### Section 1 — Project setup

- [x] Separate React + TypeScript + Vite project
- [x] API base URL via `VITE_API_BASE_URL`
- [x] No second backend / no direct SQLite access
- [ ] Frontend README is still the Vite template
- [ ] `.env.example` for the frontend is missing
- [ ] Live connection to the Laravel API not verified in this session

### Later frontend sections (summary)

- [x] Login, token storage, `/auth/me`, logout, protected routes, 401 interceptor
- [x] Dashboard, students, programs, courses, terms, offerings, grades, profile modules exist
- [ ] Academic record page is missing
- [ ] Enrollments route exists but is not in the main navigation
- [ ] Student edit/delete UI, enrollment create/drop, richer offering forms, and dedicated 403/404/409/500 screens are incomplete
- [ ] Frontend automated tests are missing
- [ ] API integration map and frontend documentation are missing

## Next implementation step

Finish Activity 1 Section 1 by keeping Git history and confirming the API process starts. Do not start a new major backend domain change until that is done.
