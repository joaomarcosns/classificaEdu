<?php

return [
    'title' => 'Relatório Completo do Aluno',
    'generated_at' => 'Gerado em',

    'sections' => [
        'student_info' => 'Informações do Aluno',
        'classification_summary' => 'Resumo da Classificação',
        'academic_performance' => 'Desempenho Acadêmico',
        'behavioral_observations' => 'Observações Comportamentais',
        'impact_analysis' => 'Análise de Impacto',
        'conclusion' => 'Conclusão',
    ],

    'labels' => [
        'current_classification' => 'Classificação Atual',
        'overall_average' => 'Média Geral',
        'period' => 'Período',
        'grade' => 'Nota',
        'classification' => 'Classificação',
        'date' => 'Data',
        'category' => 'Categoria',
        'observation' => 'Observação',
        'total_observations' => 'Total de Observações',
    ],

    'classifications' => [
        'basic' => 'Básico',
        'intermediate' => 'Intermediário',
        'advanced' => 'Avançado',
    ],

    'impact' => [
        'positive_prefix' => ':count observações positivas em :category indicam',
        'concerning_prefix' => ':count observações preocupantes em :category requerem',
        'neutral_prefix' => ':count observações neutras em :category foram registradas',
        'skill_suffix' => 'em :skill',

        'skills' => [
            'behavior' => 'conduta geral em sala de aula',
            'participation' => 'engajamento nas atividades',
            'cooperation' => 'trabalho em equipe e colaboração',
            'responsibility' => 'cumprimento de tarefas e compromissos',
            'social_interaction' => 'relacionamento interpessoal',
            'other' => 'aspectos diversos do desenvolvimento',
            'general_development' => 'desenvolvimento geral',
        ],

        'positive_impact' => 'impacto positivo',
        'needs_attention' => 'atenção e acompanhamento',
    ],

    'no_data' => [
        'grades' => 'Nenhuma nota registrada para este aluno.',
        'observations' => 'Nenhuma observação registrada para este aluno.',
        'classification' => 'Classificação não disponível.',
    ],

    'legacy_classification_map' => [
        'basic' => 'basico',
        'intermediate' => 'intermediario',
        'advanced' => 'avancado',
    ],
];
