<?php
session_start();

// Verifica se a pontuação e a quantidade de perguntas estão definidas e válidas
if (!isset($_SESSION['pontuacao']) || !isset($_SESSION['totalPerguntas']) || $_SESSION['totalPerguntas'] <= 0) {
    header('Location: QuizData.php');
    exit();
}

$pontuacao = $_SESSION['pontuacao'];
$totalPerguntas = $_SESSION['totalPerguntas'];

// Limpa as sessões para um novo jogo
session_unset();
session_destroy();
?>

<!DOCTYPE html>
<html lang="pt-BR">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Resultado do Quiz</title>
    <link rel="stylesheet" href="css/resultado.css">
</head>

<body>
    <div>
        <div class="logo">
            <img src="img/logoSF.png" alt="Logo FunLearn">
        </div>

        <h1>Resultado</h1>
        <p class="resultado">Você acertou <span class="acertos"><?= htmlspecialchars($pontuacao) ?></span> de <span class="total"><?= htmlspecialchars($totalPerguntas) ?></span> perguntas.</p>

        <a href="QuizData.php" class="btn">Tentar Novamente</a>
    </div>
</body>

</html>
