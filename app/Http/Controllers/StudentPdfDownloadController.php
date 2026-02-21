<?php

namespace App\Http\Controllers;

use App\Models\Student;
use Illuminate\Support\Str;

use function Spatie\LaravelPdf\Support\pdf;

class StudentPdfDownloadController extends Controller
{
    public function __invoke(Student $student)
    {
        $student->loadMissing([
            'classification',
            'observations',
            'evaluation_periods.grades',
        ]);

        return pdf()
            ->view('docs.pdf.student', ['student' => $student])
            ->name('student_' . Str::slug($student->name) . '.pdf')
            ->download();
    }
}
