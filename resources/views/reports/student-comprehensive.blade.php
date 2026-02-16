<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ trans('reports.title') }} - {{ $student->name }}</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'DejaVu Sans', Arial, sans-serif;
            font-size: 12px;
            line-height: 1.6;
            color: #333;
            padding: 20px;
        }

        .header {
            text-align: center;
            margin-bottom: 30px;
            padding-bottom: 20px;
            border-bottom: 3px solid #2563eb;
        }

        .header h1 {
            font-size: 24px;
            color: #1e40af;
            margin-bottom: 10px;
        }

        .header .meta {
            color: #666;
            font-size: 11px;
        }

        .section {
            margin-bottom: 25px;
            page-break-inside: avoid;
        }

        .section-title {
            font-size: 16px;
            font-weight: bold;
            color: #1e40af;
            margin-bottom: 12px;
            padding-bottom: 5px;
            border-bottom: 2px solid #dbeafe;
        }

        .info-grid {
            display: table;
            width: 100%;
            margin-bottom: 15px;
        }

        .info-row {
            display: table-row;
        }

        .info-label {
            display: table-cell;
            font-weight: bold;
            padding: 6px 10px;
            background-color: #f0f9ff;
            border: 1px solid #dbeafe;
            width: 30%;
        }

        .info-value {
            display: table-cell;
            padding: 6px 10px;
            border: 1px solid #dbeafe;
        }

        .badge {
            display: inline-block;
            padding: 4px 12px;
            border-radius: 12px;
            font-size: 11px;
            font-weight: bold;
            text-transform: uppercase;
        }

        .badge-success {
            background-color: #dcfce7;
            color: #166534;
        }

        .badge-warning {
            background-color: #fef3c7;
            color: #92400e;
        }

        .badge-danger {
            background-color: #fee2e2;
            color: #991b1b;
        }

        .badge-info {
            background-color: #dbeafe;
            color: #1e40af;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 15px;
        }

        table th {
            background-color: #f0f9ff;
            color: #1e40af;
            font-weight: bold;
            padding: 10px;
            text-align: left;
            border: 1px solid #dbeafe;
        }

        table td {
            padding: 8px 10px;
            border: 1px solid #dbeafe;
        }

        table tr:nth-child(even) {
            background-color: #f9fafb;
        }

        .observation-item {
            margin-bottom: 15px;
            padding: 12px;
            background-color: #f9fafb;
            border-left: 4px solid #3b82f6;
            border-radius: 4px;
        }

        .observation-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 8px;
            font-size: 11px;
        }

        .observation-date {
            font-weight: bold;
            color: #1e40af;
        }

        .observation-description {
            color: #374151;
            line-height: 1.5;
        }

        .impact-item {
            padding: 10px;
            margin-bottom: 10px;
            background-color: #f0f9ff;
            border-radius: 4px;
            border-left: 3px solid #3b82f6;
        }

        .impact-category {
            font-weight: bold;
            color: #1e40af;
            margin-bottom: 5px;
        }

        .insight {
            margin-left: 15px;
            margin-bottom: 5px;
            color: #4b5563;
        }

        .no-data {
            padding: 20px;
            text-align: center;
            color: #9ca3af;
            font-style: italic;
        }

        .footer {
            margin-top: 40px;
            padding-top: 20px;
            border-top: 1px solid #e5e7eb;
            text-align: center;
            font-size: 10px;
            color: #9ca3af;
        }
    </style>
</head>
<body>
    <!-- Header -->
    <div class="header">
        <h1>{{ trans('reports.title') }}</h1>
        <div class="meta">
            {{ trans('reports.generated_at') }}: {{ $generated_at->format('d/m/Y H:i') }}
        </div>
    </div>

    <!-- Student Information -->
    <div class="section">
        <h2 class="section-title">{{ trans('reports.sections.student_info') }}</h2>
        <div class="info-grid">
            <div class="info-row">
                <div class="info-label">{{ trans('students.fields.name') }}</div>
                <div class="info-value">{{ $student->name }}</div>
            </div>
            <div class="info-row">
                <div class="info-label">{{ trans('students.fields.registration_number') }}</div>
                <div class="info-value">{{ $student->registration_number }}</div>
            </div>
            <div class="info-row">
                <div class="info-label">{{ trans('students.fields.grade_level') }}</div>
                <div class="info-value">{{ $student->grade_level }}</div>
            </div>
            <div class="info-row">
                <div class="info-label">{{ trans('students.fields.class_name') }}</div>
                <div class="info-value">{{ $student->class_name ?? 'N/A' }}</div>
            </div>
        </div>
    </div>

    <!-- Classification Summary -->
    <div class="section">
        <h2 class="section-title">{{ trans('reports.sections.classification_summary') }}</h2>
        @if($classification)
            <div class="info-grid">
                <div class="info-row">
                    <div class="info-label">{{ trans('reports.labels.current_classification') }}</div>
                    <div class="info-value">
                        <span class="badge badge-{{ $classification->level_color }}">
                            {{ $classification->level_label }}
                        </span>
                    </div>
                </div>
                <div class="info-row">
                    <div class="info-label">{{ trans('reports.labels.overall_average') }}</div>
                    <div class="info-value">
                        <strong>{{ number_format($classification->overall_average, 2) }}</strong> / 10.0
                    </div>
                </div>
            </div>
        @else
            <div class="no-data">{{ trans('reports.no_data.classification') }}</div>
        @endif
    </div>

    <!-- Academic Performance -->
    <div class="section">
        <h2 class="section-title">{{ trans('reports.sections.academic_performance') }}</h2>
        @if($grades['all']->isNotEmpty())
            <table>
                <thead>
                    <tr>
                        <th>{{ trans('reports.labels.period') }}</th>
                        <th>{{ trans('reports.labels.grade') }}</th>
                        <th>{{ trans('reports.labels.classification') }}</th>
                        <th>{{ trans('grades.fields.evaluation_date') }}</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($grades['by_period'] as $period => $periodData)
                        @foreach($periodData['grades'] as $grade)
                            <tr>
                                <td>{{ trans("grades.periods.{$period}") }}</td>
                                <td>
                                    <span class="badge badge-{{ $grade->value < 6.0 ? 'danger' : ($grade->value < 8.0 ? 'warning' : 'success') }}">
                                        {{ number_format($grade->value, 2) }}
                                    </span>
                                </td>
                                <td>{{ $periodData['classification'] }}</td>
                                <td>{{ $grade->evaluation_date->format('d/m/Y') }}</td>
                            </tr>
                        @endforeach
                    @endforeach
                </tbody>
            </table>

            @if($grades['by_period']->isNotEmpty())
                <div class="info-grid">
                    @foreach($grades['by_period'] as $period => $periodData)
                        <div class="info-row">
                            <div class="info-label">{{ trans("grades.periods.{$period}") }} - Média</div>
                            <div class="info-value">
                                <strong>{{ number_format($periodData['average'], 2) }}</strong> / 10.0
                            </div>
                        </div>
                    @endforeach
                </div>
            @endif
        @else
            <div class="no-data">{{ trans('reports.no_data.grades') }}</div>
        @endif
    </div>

    <!-- Behavioral Observations -->
    <div class="section">
        <h2 class="section-title">{{ trans('reports.sections.behavioral_observations') }}</h2>
        @if($observations['all']->isNotEmpty())
            <p style="margin-bottom: 15px;">
                <strong>{{ trans('reports.labels.total_observations') }}:</strong> {{ $observations['total_count'] }}
            </p>

            @foreach($observations['by_category'] as $category => $categoryObservations)
                <h3 style="font-size: 14px; color: #1e40af; margin-top: 20px; margin-bottom: 10px;">
                    {{ trans("observations.categories.{$category}") }}
                    ({{ $categoryObservations->count() }})
                </h3>

                @foreach($categoryObservations as $observation)
                    <div class="observation-item">
                        <div class="observation-header">
                            <span class="observation-date">{{ $observation->observation_date->format('d/m/Y') }}</span>
                            <span class="badge badge-{{ $observation->sentiment === 'positivo' ? 'success' : ($observation->sentiment === 'preocupante' ? 'danger' : 'info') }}">
                                {{ trans("observations.sentiments.{$observation->sentiment}") }}
                            </span>
                        </div>
                        <div class="observation-description">
                            {!! $observation->description !!}
                        </div>
                        <div style="margin-top: 5px; font-size: 10px; color: #9ca3af;">
                            Registrado por: {{ $observation->user->name }}
                        </div>
                    </div>
                @endforeach
            @endforeach
        @else
            <div class="no-data">{{ trans('reports.no_data.observations') }}</div>
        @endif
    </div>

    <!-- Impact Analysis -->
    @if(!empty($impact_analysis))
        <div class="section">
            <h2 class="section-title">{{ trans('reports.sections.impact_analysis') }}</h2>

            @foreach($impact_analysis as $category => $analysis)
                <div class="impact-item">
                    <div class="impact-category">
                        {{ $analysis['category_label'] }} ({{ $analysis['total_count'] }} observações)
                    </div>
                    @foreach($analysis['insights'] as $insight)
                        <div class="insight">• {{ $insight }}</div>
                    @endforeach
                </div>
            @endforeach
        </div>
    @endif

    <!-- Footer -->
    <div class="footer">
        Relatório gerado automaticamente pelo sistema ClassificaEdu
    </div>
</body>
</html>
