<?php

namespace App\Http\Controllers;

use App\Models\Student;
use function Spatie\LaravelPdf\Support\pdf;

class StudentPdfDownloadController extends Controller
{
    public function __invoke(Student $student)
    {
        $student->loadMissing(['classification', 'grades', 'observations']);

        return pdf()
            ->view('docs.pdf.student', ['student' => $student])
            ->name('student_' . $student->id . '.pdf')
            ->download();
    }
}
