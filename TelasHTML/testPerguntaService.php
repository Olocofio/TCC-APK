<?php
require_once 'PerguntaService.php';

// Instanciar o PerguntaService
$perguntaService = new PerguntaService();

// 1. Buscar todas as matérias
$materias = $perguntaService->findAllMaterias();
echo "Matérias disponíveis: " . implode(", ", $materias) . "<br><br>";

// 2. Buscar perguntas por matéria (ex: Matemática) e quantidade de perguntas (ex: 5)
$questions = $perguntaService->findQuestionsByMateria("Matemática", 5);

foreach ($questions as $question) {
    echo "Pergunta: " . $question->getPergunta() . "<br>";
    echo "Respostas: " . implode(", ", $question->getRespostas()) . "<br>";
    echo "Resposta Correta: " . $question->getRespostaCorreta() . "<br><br>";
}

// 3. Inserir pontuação de um usuário
$perguntaService->insertScore(1, 5, 5); // idUsu = 1, pontuação = 80, 5 perguntas
echo "Pontuação inserida com sucesso!";
?>
