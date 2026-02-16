<?php

return [
    'model_label' => 'Observação',
    'plural_label' => 'Observações',
    'navigation_label' => 'Observações',

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
        'comportamento' => 'Comportamento',
        'participacao' => 'Participação',
        'cooperacao' => 'Cooperação',
        'responsabilidade' => 'Responsabilidade',
        'interacao_social' => 'Interação Social',
        'outro' => 'Outro',
    ],

    'sentiments' => [
        'positivo' => 'Positivo',
        'neutro' => 'Neutro',
        'preocupante' => 'Preocupante',
    ],

    'sections' => [
        'observation_info' => 'Informações da Observação',
    ],

    'help' => [
        'is_private' => 'Observações privadas não aparecem em relatórios públicos',
    ],
];
