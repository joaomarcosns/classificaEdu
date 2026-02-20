<?php

return [
    'model_label' => 'Aluno',
    'plural_label' => 'Alunos',
    'navigation_label' => 'Alunos',
    'navigation_group' => 'Gestão de Alunos',

    'fields' => [
        'name' => 'Nome Completo',
        'registration_number' => 'Matrícula',
        'date_of_birth' => 'Data de Nascimento',
        'grade_level' => 'Série',
        'class_name' => 'Turma',
        'is_active' => 'Ativo',
        'age' => 'Idade',
        'created_at' => 'Criado em',
        'updated_at' => 'Atualizado em',
    ],

    'actions' => [
        'recalculate' => 'Recalcular Classificação',
        'generate_report' => 'Gerar Relatório Completo',
        'recalculate_bulk' => 'Recalcular Classificações',
    ],

    'filters' => [
        'active' => 'Ativos',
        'inactive' => 'Inativos',
        'all' => 'Todos',
    ],

    'grade_levels' => [
        'year_1' => '1º ano',
        'year_2' => '2º ano',
        'year_3' => '3º ano',
        'year_4' => '4º ano',
        'year_5' => '5º ano',
        'year_6' => '6º ano',
        'year_7' => '7º ano',
        'year_8' => '8º ano',
        'year_9' => '9º ano',
    ],

    'sections' => [
        'basic_info' => 'Informações Básicas',
        'classification' => 'Classificação',
        'statistics' => 'Estatísticas',
    ],

    'messages' => [
        'classification_recalculated' => 'Classificação recalculada com sucesso!',
        'classifications_recalculated' => ':count classificações recalculadas com sucesso!',
    ],

    'legacy_grade_levels' => [
        'year_1' => '1º ano',
        'year_2' => '2º ano',
        'year_3' => '3º ano',
        'year_4' => '4º ano',
        'year_5' => '5º ano',
        'year_6' => '6º ano',
        'year_7' => '7º ano',
        'year_8' => '8º ano',
        'year_9' => '9º ano',
    ],
];
