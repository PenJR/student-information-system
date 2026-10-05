import { useEffect, useState } from 'react'
import { isAxiosError } from 'axios'
import { useParams } from 'react-router-dom'
import { ShieldAlert } from 'lucide-react'
import { useAuth } from '../contexts/AuthContext'
import { getAcademicRecord, type AcademicRecord } from '../services/api/academicRecordApi'
import { getResource } from '../services/api/resourcesApi'
import { listStudents } from '../services/api/studentsApi'
import type { Student } from '../types/api'

function errorMessage(error: unknown) {
  if (!isAxiosError(error)) return error instanceof Error ? error.message : 'Unable to load the academic record.'
  if (!error.response) return 'The Laravel API could not be reached. Check that the backend is running.'
  if (error.response.status === 401) return 'Your session has expired. Sign in again to continue.'
  if (error.response.status === 403) return 'You do not have permission to view this academic record.'
  if (error.response.status === 404) return 'The requested student record could not be found.'
  if (error.response.status === 409) return 'The API could not return this record because of a data conflict.'
  if (error.response.status === 422) return 'The API rejected the academic-record request.'
  if (error.response.status >= 500) return 'The Laravel API encountered an error. Try again later.'
  return error.response.data?.message || 'Unable to load the academic record.'
}

function grade(value: number | null | undefined) {
  return value === null || value === undefined ? 'Not recorded' : `${value}%`
}

export function AcademicRecordPage() {
  const { id } = useParams()
  const { user } = useAuth()
  const [student, setStudent] = useState<Student | null>(null)
  const [record, setRecord] = useState<AcademicRecord>({})
  const [loading, setLoading] = useState(true)
  const [error, setError] = useState('')

  useEffect(() => {
    let active = true

    async function load() {
      setLoading(true)
      setError('')
      try {
        let studentId: number | undefined
        if (user?.role === 'STUDENT') {
          const ownStudents = await listStudents({ page: 1, per_page: 1 })
          studentId = ownStudents.data[0]?.id
        } else if (id && Number.isInteger(Number(id)) && Number(id) > 0) {
          studentId = Number(id)
        }

        if (!studentId) throw new Error('Choose a student from the student directory to view an academic record.')

        const [studentData, academicRecord] = await Promise.all([
          getResource<Student>('/students', studentId),
          getAcademicRecord(studentId),
        ])
        if (active) {
          setStudent(studentData)
          setRecord(academicRecord)
        }
      } catch (requestError) {
        if (active) setError(errorMessage(requestError))
      } finally {
        if (active) setLoading(false)
      }
    }

    void load()
    return () => { active = false }
  }, [id, user?.role])

  const terms = Object.entries(record)
  const hasEnrollments = terms.some(([, enrollments]) => enrollments.length > 0)

  return (
    <section className="page">
      <div className="page-heading">
        <div>
          <span className="eyebrow">Academic records</span>
          <h2>Academic record</h2>
          <p className="muted">Course history and grades retrieved from the Laravel API.</p>
        </div>
      </div>

      {loading ? (
        <div className="panel state"><div className="spinner" /><p>Loading academic record...</p></div>
      ) : error ? (
        <div className="panel state" role="alert"><ShieldAlert size={28} /><p>{error}</p></div>
      ) : student ? (
        <>
          <div className="metric-grid">
            <div className="metric"><span>Student</span><strong>{student.first_name} {student.last_name}</strong><small>{student.student_number}</small></div>
            <div className="metric gold"><span>Program</span><strong>{student.program?.code || 'Not assigned'}</strong><small>{student.program?.name || 'No program recorded'}</small></div>
            <div className="metric coral"><span>Academic standing</span><strong>Year {student.year_level}</strong><small>Status: {student.status}</small></div>
          </div>

          {!hasEnrollments ? (
            <div className="panel state"><p>No enrollment or grade records are available for this student.</p></div>
          ) : terms.filter(([, enrollments]) => enrollments.length > 0).map(([term, enrollments]) => (
            <section className="panel" key={term}>
              <div className="panel-heading"><h3>{term}</h3></div>
              <div className="table-wrap">
                <table>
                  <thead><tr><th>Course</th><th>Section</th><th>Schedule / Room</th><th>Units</th><th>Enrollment</th><th>Midterm</th><th>Final</th><th>Remarks</th></tr></thead>
                  <tbody>{enrollments.map(enrollment => {
                    const offering = enrollment.course_offering
                    const course = offering.course
                    return (
                      <tr key={enrollment.id}>
                        <td><strong>{course?.course_code || 'Course'}</strong><br />{course?.course_title || 'Title unavailable'}</td>
                        <td>{offering.section || 'Not assigned'}</td>
                        <td>{[offering.schedule, offering.room].filter(Boolean).join(' · ') || 'Not scheduled'}</td>
                        <td>{course?.units ?? 'Not recorded'}</td>
                        <td>{enrollment.status}</td>
                        <td>{grade(enrollment.grade?.midterm_grade)}</td>
                        <td>{grade(enrollment.grade?.final_grade)}</td>
                        <td>{enrollment.grade?.remarks || 'None'}</td>
                      </tr>
                    )
                  })}</tbody>
                </table>
              </div>
            </section>
          ))}
        </>
      ) : null}
    </section>
  )
}