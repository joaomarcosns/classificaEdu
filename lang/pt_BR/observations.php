<?php

return [
    'model_label' => 'Observação',
    'plural_label' => 'Observações',
    'navigation_label' => 'Observações',
    'navigation_group' => 'Gestão de Alunos',

    'fields' => [
        'student' => 'Aluno',
        'user' => 'Registrado por',
        'observation_date' => 'Data da Observação',
        'category' => 'Categoria',
        'sentiment' => 'Sentimento',
        'description' => 'Descrição',
        'is_private' => 'Privado',
        'created_at' => 'Criado em',
        'updated_at' => 'Atualizado em',
    ],

    'categories' => [
        'behavior' => 'Comportamento',
        'participation' => 'Participação',
        'cooperation' => 'Cooperação',
        'responsibility' => 'Responsabilidade',
        'social_interaction' => 'Interação Social',
        'other' => 'Outro',
    ],

    'sentiments' => [
        'positive' => 'Positivo',
        'neutral' => 'Neutro',
        'concerning' => 'Preocupante',
    ],

    'sections' => [
        'observation_info' => 'Informações da Observação',
    ],

    'help' => [
        'is_private' => 'Observações privadas não aparecem em relatórios públicos',
    ],

    'legacy_category_map' => [
        'behavior' => 'comportamento',
        'participation' => 'participacao',
        'cooperation' => 'cooperacao',
        'responsibility' => 'responsabilidade',
        'social_interaction' => 'interacao_social',
        'other' => 'outro',
    ],

    'legacy_sentiment_map' => [
        'positive' => 'positivo',
        'neutral' => 'neutro',
        'concerning' => 'preocupante',
    ],
];
