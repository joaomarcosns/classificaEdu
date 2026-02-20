<?php

return [
    'model_label' => 'Nota',
    'plural_label' => 'Notas',
    'navigation_label' => 'Notas',
    'navigation_group' => 'Gestão de Alunos',

    'fields' => [
        'student' => 'Aluno',
        'value' => 'Nota',
        'evaluation_date' => 'Data de Avaliação',
        'evaluation_period' => 'Período de Avaliação',
        'assessment_type' => 'Tipo de Avaliação',
        'notes' => 'Observações',
        'created_at' => 'Criado em',
        'updated_at' => 'Atualizado em',
    ],

    'assessment_types' => [
        'exam' => 'Prova',
        'assignment' => 'Trabalho',
        'participation' => 'Participação',
        'exercise' => 'Exercício',
        'project' => 'Projeto',
    ],

    'ranges' => [
        'low' => 'Abaixo de 6.0',
        'medium' => '6.0 a 7.9',
        'high' => '8.0 ou superior',
    ],

    'sections' => [
        'grade_info' => 'Informações da Nota',
    ],

    'validation' => [
        'value_min' => 'A nota deve ser no mínimo 0',
        'value_max' => 'A nota deve ser no máximo 10',
        'duplicate_period' => 'Já existe uma nota para este aluno neste período',
    ],

    'legacy_assessment_types' => [
        'exam' => 'Prova',
        'assignment' => 'Trabalho',
        'participation' => 'Participação',
        'exercise' => 'Exercício',
        'project' => 'Projeto',
    ],

    'legacy_periods' => [
        'term_1' => 'trimestre_1',
        'term_2' => 'trimestre_2',
        'term_3' => 'trimestre_3',
        'final' => 'final',
    ],
];
