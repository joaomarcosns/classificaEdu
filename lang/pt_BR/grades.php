<?php

return [
    'model_label' => 'Nota',
    'plural_label' => 'Notas',
    'navigation_label' => 'Notas',

    'fields' => [
        'student' => 'Aluno',
        'value' => 'Nota',
        'evaluation_date' => 'Data de Avaliação',
        'evaluation_period' => 'Período de Avaliação',
        'notes' => 'Observações',
        'created_at' => 'Criado em',
        'updated_at' => 'Atualizado em',
    ],

    'periods' => [
        'trimestre_1' => '1º Trimestre',
        'trimestre_2' => '2º Trimestre',
        'trimestre_3' => '3º Trimestre',
        'final' => 'Final',
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
];
