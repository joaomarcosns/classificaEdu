<!DOCTYPE html>
<html lang="pt-BR">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Relatório do Estudante - {{ $student->name }}</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        @page {
            margin: 22px;
        }

        body {
            font-family: 'DejaVu Sans', Arial, sans-serif;
            font-size: 12px;
            line-height: 1.5;
            color: #0f172a;
            padding: 0;
            background-color: #ffffff;
        }

        .container {
            width: 100%;
        }

        .mb-4 {
            margin-bottom: 16px;
        }

        .mb-6 {
            margin-bottom: 24px;
        }

        .rounded {
            border-radius: 8px;
        }

        .border {
            border: 1px solid #e2e8f0;
        }

        .p-4 {
            padding: 16px;
        }

        .p-3 {
            padding: 12px;
        }

        .text-sm {
            font-size: 11px;
        }

        .text-lg {
            font-size: 18px;
        }

        .text-xl {
            font-size: 22px;
        }

        .font-semibold {
            font-weight: 600;
        }

        .font-bold {
            font-weight: 700;
        }

        .text-slate-500 {
            color: #64748b;
        }

        .text-slate-700 {
            color: #334155;
        }

        .text-slate-900 {
            color: #0f172a;
        }

        .bg-slate-50 {
            background-color: #f8fafc;
        }

        .bg-slate-100 {
            background-color: #f1f5f9;
        }

        .header {
            border: 1px solid #dbeafe;
            background-color: #f8fbff;
            border-radius: 10px;
            padding: 14px 16px;
        }

        .header-title {
            color: #0f3d5f;
            margin-bottom: 4px;
        }

        .header-meta {
            display: table;
            width: 100%;
        }

        .header-meta-item {
            display: table-cell;
            width: 50%;
        }

        .header-meta-item.right {
            text-align: right;
        }

        .two-col {
            display: table;
            width: 100%;
            table-layout: fixed;
        }

        .two-col-item {
            display: table-cell;
            width: 50%;
            vertical-align: top;
        }

        .two-col-item.left {
            padding-right: 8px;
        }

        .two-col-item.right {
            padding-left: 8px;
        }

        .info-table {
            width: 100%;
            border-collapse: collapse;
        }

        .info-table td {
            padding: 8px 10px;
            border: 1px solid #e2e8f0;
        }

        .label {
            background-color: #f8fafc;
            color: #475569;
            font-weight: 600;
            width: 38%;
        }

        .value {
            color: #0f172a;
        }

        .section-title {
            font-size: 14px;
            font-weight: 700;
            color: #0f3d5f;
            margin-bottom: 10px;
            padding-bottom: 4px;
            border-bottom: 1px solid #dbeafe;
        }

        .metrics {
            display: table;
            width: 100%;
            margin-bottom: 12px;
        }

        .metric {
            display: table-cell;
            border: 1px solid #e2e8f0;
            border-radius: 8px;
            padding: 10px;
            background-color: #f8fafc;
            width: 33.33%;
        }

        .metric+.metric {
            border-left: 8px solid #ffffff;
        }

        .metric-label {
            font-size: 10px;
            color: #64748b;
            margin-bottom: 4px;
        }

        .metric-value {
            font-size: 16px;
            font-weight: 700;
            color: #0f172a;
        }

        .badge {
            display: inline-block;
            padding: 4px 10px;
            border-radius: 999px;
            font-size: 10px;
            font-weight: 700;
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
            background-color: #e0f2fe;
            color: #0c4a6e;
        }

        .table {
            width: 100%;
            border-collapse: collapse;
        }

        .table th,
        .table td {
            border: 1px solid #e2e8f0;
            padding: 10px 12px;
            text-align: left;
        }

        .table th {
            background-color: #eef6ff;
            color: #0f3d5f;
            font-weight: 700;
            font-size: 11px;
        }

        .table tr:nth-child(even) {
            background-color: #f8fafc;
        }

        .text-right {
            text-align: right;
        }

        .observation {
            border: 1px solid #e2e8f0;
            border-left: 4px solid #0ea5e9;
            border-radius: 6px;
            padding: 10px;
            margin-bottom: 10px;
            page-break-inside: avoid;
        }

        .observation-date {
            font-size: 10px;
            color: #64748b;
            margin-bottom: 5px;
        }

        .empty-state {
            border: 1px dashed #cbd5e1;
            border-radius: 8px;
            padding: 12px;
            color: #64748b;
            text-align: center;
            background-color: #f8fafc;
            font-style: italic;
        }

        .footer {
            margin-top: 26px;
            padding-top: 12px;
            border-top: 1px solid #e2e8f0;
            text-align: center;
            color: #64748b;
            font-size: 10px;
        }

        .page-break-inside-avoid {
            page-break-inside: avoid;
        }

        .period-block {
            border: 1px solid #e2e8f0;
            border-radius: 8px;
            padding: 12px 14px 14px;
            background-color: #ffffff;
            margin-bottom: 18px;
            page-break-inside: auto;
        }

        .period-block + .period-block {
            margin-top: 6px;
        }

        .period-title {
            background-color: #eef6ff;
            border: 1px solid #dbeafe;
            border-radius: 6px;
            padding: 8px 10px;
        }
    </style>
</head>

<body>
    @php
        $classification = $student->classification;
        $periods = $student->evaluation_periods;

        $grades = $student->grades->sortByDesc('evaluation_date');
        $observations = $student->observations->sortByDesc('created_at');
        $overallAverage = $grades->avg('value');
        $displayAverage = $overallAverage !== null ? number_format((float) $overallAverage, 2, ',', '.') : 'N/A';

        $classificationColor = match ($classification?->classification_level) {
            'avancado' => 'success',
            'intermediario' => 'warning',
            'basico' => 'danger',
            default => 'info',
        };

        $classificationLabel = $classification?->level_label ?? 'Não classificado';
    @endphp
    <div class="container">
        <div class="header mb-6">
            <h1 class="header-title text-xl font-bold">Relatório Individual do Estudante</h1>
            <div class="header-meta text-sm text-slate-500">
                <div class="header-meta-item">Sistema: ClassificaEdu</div>
                <div class="header-meta-item right">Emitido em {{ now()->format('d/m/Y H:i') }}</div>
            </div>
        </div>

        <div class="metrics page-break-inside-avoid">
            <div class="metric">
                <div class="metric-label">Média geral</div>
                <div class="metric-value">{{ $displayAverage }}</div>
            </div>
            <div class="metric">
                <div class="metric-label">Total de notas</div>
                <div class="metric-value">{{ $grades->count() }}</div>
            </div>
            <div class="metric">
                <div class="metric-label">Total de observações</div>
                <div class="metric-value">{{ $observations->count() }}</div>
            </div>
        </div>

        <div class="two-col mb-6">
            <div class="two-col-item left">
                <div class="section-title">Dados do Aluno</div>
                <table class="info-table rounded border">
                    <tr>
                        <td class="label">Nome</td>
                        <td class="value">{{ $student->name }}</td>
                    </tr>
                    <tr>
                        <td class="label">Matrícula</td>
                        <td class="value">{{ $student->registration_number }}</td>
                    </tr>
                    <tr>
                        <td class="label">Série</td>
                        <td class="value">{{ $student->grade_level_label }}</td>
                    </tr>
                    <tr>
                        <td class="label">Turma</td>
                        <td class="value">{{ $student->class_name ?? 'N/A' }}</td>
                    </tr>
                    <tr>
                        <td class="label">Data de nascimento</td>
                        <td class="value">{{ $student->date_of_birth?->format('d/m/Y') ?? 'N/A' }}</td>
                    </tr>
                    <tr>
                        <td class="label">Status</td>
                        <td class="value">{{ $student->is_active ? 'Ativo' : 'Inativo' }}</td>
                    </tr>
                </table>
            </div>

            <div class="two-col-item right">
                <div class="section-title">Resumo Acadêmico</div>
                <div class="rounded border p-4 bg-slate-50 page-break-inside-avoid">
                    <div class="mb-4">
                        <div class="text-sm text-slate-500">Classificação atual</div>
                        <span class="badge badge-{{ $classificationColor }}">{{ $classificationLabel }}</span>
                    </div>
                    <div class="mb-4">
                        <div class="text-sm text-slate-500">Média geral</div>
                        <div class="text-lg font-semibold text-slate-900">{{ $displayAverage }}</div>
                    </div>
                    <div>
                        <div class="text-sm text-slate-500">Período de avaliação</div>
                        <div class="font-semibold text-slate-700">{{ $classification?->evaluation_period ?? 'N/A' }}
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="mb-6">
            <div class="section-title">Histórico de Notas</div>
            @if ($periods->isNotEmpty())
                <div>
                    @foreach ($periods as $period)
                        <div class="period-block">
                            <div class="period-title sub-title" style="font-size:16px; font-weight:600; color:#0f3d5f; margin-bottom:10px;">
                                @php
                                    $periodKey = $period->name instanceof \App\Enums\EvaluationPeriodName ? $period->name->value : $period->name;
                                    $periodLabel = trans('evaluation_periods.names.' . $periodKey);
                                @endphp
                                {{ $periodLabel !== 'evaluation_periods.names.' . $periodKey ? $periodLabel : ucfirst(str_replace('_', ' ', $periodKey)) }}
                            </div>
                            <table class="table" style="width: 100%;">
                                <thead>
                                    <tr>
                                        <th>Tipo</th>
                                        <th class="text-right">Nota</th>
                                        <th>Observações</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($period->grades as $grade)
                                        <tr>
                                            <td>{{ $grade->assessment_type ? trans('grades.assessment_types.' . $grade->assessment_type) : '-' }}</td>
                                            <td class="text-right">{{ number_format((float) $grade->value, 2, ',', '.') }}</td>
                                            <td>{{ $grade->notes ?: '-' }}</td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    @endforeach
                </div>
            @else
                <div class="empty-state">Nenhuma nota registrada para este estudante.</div>
            @endif
        </div>

        <div class="mb-4">
            <div class="section-title">Observações Pedagógicas</div>
            @if ($observations->isNotEmpty())
                @foreach ($observations as $observation)
                    <div class="observation">
                        <div class="observation-date">
                            {{ $observation->observation_date?->format('d/m/Y') ?? $observation->created_at?->format('d/m/Y') }}
                        </div>
                        <div class="text-slate-900">{{ $observation->description }}</div>
                    </div>
                @endforeach
            @else
                <div class="empty-state">Nenhuma observação registrada para este estudante.</div>
            @endif
        </div>

        <div class="footer">
            ClassificaEdu · Documento gerado automaticamente
        </div>
    </div>
</body>

</html>
