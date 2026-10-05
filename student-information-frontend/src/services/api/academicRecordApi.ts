import api from './client'

export interface AcademicRecordEnrollment {
  id: number
  enrollment_date: string
  status: string
  course_offering: {
    section?: string | null
    schedule?: string | null
    room?: string | null
    course?: {
      course_code?: string
      course_title?: string
      units?: number
    }
    academic_term?: {
      academic_year?: string
      term?: string
    }
  }
  grade?: {
    midterm_grade?: number | null
    final_grade?: number | null
    remarks?: string | null
  } | null
}

export type AcademicRecord = Record<string, AcademicRecordEnrollment[]>

export async function getAcademicRecord(studentId: number) {
  const response = await api.get<{ data: AcademicRecord }>(`/students/${studentId}/academic-record`)
  return response.data.data
}