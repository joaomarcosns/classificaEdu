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
        'basico' => 'Básico',
        'intermediario' => 'Intermediário',
        'avancado' => 'Avançado',
    ],

    'impact' => [
        'positive_prefix' => ':count observações positivas em :category indicam',
        'concerning_prefix' => ':count observações preocupantes em :category requerem',
        'neutral_prefix' => ':count observações neutras em :category foram registradas',

        'skills' => [
            'comportamento' => 'conduta geral em sala de aula',
            'participacao' => 'engajamento nas atividades',
            'cooperacao' => 'trabalho em equipe e colaboração',
            'responsabilidade' => 'cumprimento de tarefas e compromissos',
            'interacao_social' => 'relacionamento interpessoal',
            'outro' => 'aspectos diversos do desenvolvimento',
        ],

        'positive_impact' => 'impacto positivo',
        'needs_attention' => 'atenção e acompanhamento',
    ],

    'no_data' => [
        'grades' => 'Nenhuma nota registrada para este aluno.',
        'observations' => 'Nenhuma observação registrada para este aluno.',
        'classification' => 'Classificação não disponível.',
    ],
];
