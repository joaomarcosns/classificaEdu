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
        '1º ano' => '1º ano',
        '2º ano' => '2º ano',
        '3º ano' => '3º ano',
        '4º ano' => '4º ano',
        '5º ano' => '5º ano',
        '6º ano' => '6º ano',
        '7º ano' => '7º ano',
        '8º ano' => '8º ano',
        '9º ano' => '9º ano',
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
];
