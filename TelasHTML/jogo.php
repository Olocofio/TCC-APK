<?php
require_once 'PerguntaService.php';
require_once 'QuestionDTO.php'; 

session_start(); 

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Recuperar os dados da matéria e quantidade de perguntas apenas na primeira requisição
    if (!isset($_SESSION['materia'])) {
        $_SESSION['materia'] = $_POST['materia'];
        $_SESSION['qtdPerguntas'] = $_POST['perguntas'];
        $_SESSION['currentQuestion'] = 0; // Índice da pergunta atual
        $_SESSION['pontuacao'] = 0; // Pontuação do usuário
        $_SESSION['puladas'] = 0; // Contador de perguntas puladas

        // Buscar as perguntas para a matéria selecionada
        $perguntaService = new PerguntaService();
        $_SESSION['questions'] = $perguntaService->findQuestionsByMateria($_SESSION['materia'], $_SESSION['qtdPerguntas']);
    }

    // Obter a pergunta atual
    $questions = $_SESSION['questions'];
    $currentQuestion = $_SESSION['currentQuestion'];

    // Verificar se o usuário já respondeu a alguma pergunta
    if (isset($_POST['resposta']) && isset($_POST['botao_id']) && empty($_POST['pular'])) { // Teste para ver se acertou
        $respostaSelecionada = $_POST['resposta']; // Resposta que o usuário escolheu
        $botaoIdSelecionado = $_POST['botao_id']; // ID do botão selecionado

        if (!empty($questions[$currentQuestion]->getRespostaCorreta())) {
            if (trim($respostaSelecionada) == trim($questions[$currentQuestion]->getRespostaCorreta())) {
                $_SESSION['pontuacao']++; // Incrementar a pontuação se a resposta estiver correta
            }
        }

        // Exibir o ID do botão selecionado (apenas para depuração, pode ser removido)
        echo "<script>console.log('Botão ID selecionado: " . $botaoIdSelecionado . "');</script>";

        // Avançar para a próxima pergunta
        $_SESSION['currentQuestion']++;
    }

    // Lógica para lidar com o pulo da pergunta
    if (!empty($_POST['pular'])) {
        $_SESSION['currentQuestion']++; // Avança para a próxima pergunta
        $_SESSION['puladas']++; // Incrementa o contador de pulos
    }

    // Se ainda houver perguntas, exibe a próxima
    if ($_SESSION['currentQuestion'] < count($questions)) {
        $currentQuestion = $_SESSION['currentQuestion'];
        $question = $questions[$currentQuestion];
    } else {
        // Todas as perguntas respondidas, salvar as informações e redirecionar para resultado.php
        $_SESSION['totalPerguntas'] = count($questions);
        header("Location: resultado.php");
        exit();
    }
} else {
    // Redireciona para a seleção caso o acesso seja direto
    header('Location: login.html');
    exit();
}
?>

<!DOCTYPE html>
<html lang="pt-BR">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>FunLearn Quiz</title>
    <link rel="stylesheet" href="css/jogo.css"> <!-- Arquivo de CSS -->
    <style>
        /* Estilo da barra de notificação no topo */
        .notification {
            visibility: hidden; /* Escondido por padrão */
            min-width: 300px;
            background-color: #333;
            color: #fff;
            text-align: center;
            padding: 16px;
            position: fixed;
            top: 0;
            left: 50%;
            transform: translateX(-50%);
            z-index: 1;
            font-size: 17px;
            border-radius: 4px;
            margin-top: 15px;
        }

        .notification.show {
            visibility: visible; /* Mostrar notificação */
            -webkit-animation: fadeIn 0.5s, fadeOut 2.5s 2s;
            animation: fadeIn 0.5s, fadeOut 2.5s 2s;
        }

        /* Animações para aparecer e desaparecer */
        @keyframes fadeIn {
            from {top: -50px; opacity: 0;}
            to {top: 0; opacity: 1;}
        }

        @keyframes fadeOut {
            from {top: 0; opacity: 1;}
            to {top: -50px; opacity: 0;}
        }

        /* Estilo do timer */
        #timer {
            font-size: 18px;
            font-weight: bold;
            margin-bottom: 10px;
        }
    </style>
</head>

<body>
    <div class="settings-icon">
        <a href="pause.html"><img src="img/settings-icon.png" alt="Configurações"></a>
    </div>

    <div class="logo">
        <img src="img/logo-sf.png" alt="Logo FunLearn">
    </div>

    <?php if (isset($question)): ?>
        <div class="pergunta">
            <p><?= $question->getPergunta(); ?></p>
        </div>

        <!-- Timer para a pergunta -->
        <div id="timer">10</div> <!-- O valor inicial do timer -->

        <form method="POST" action="jogo.php" id="quizForm">
            <div class="btn-group">
                <button type="button" name="resposta" value="<?=htmlspecialchars($question->getResposta1());?>" id="btn1" onclick="enviarResposta(this)"> <?= $question->getResposta1(); ?></button>
                <button type="button" name="resposta" value="<?=htmlspecialchars($question->getResposta2());?>" id="btn2" onclick="enviarResposta(this)"> <?= $question->getResposta2(); ?></button>
                <button type="button" name="resposta" value="<?=htmlspecialchars($question->getResposta3());?>" id="btn3" onclick="enviarResposta(this)"> <?= $question->getResposta3(); ?></button>
                <button type="button" name="resposta" value="<?=htmlspecialchars($question->getResposta4());?>" id="btn4" onclick="enviarResposta(this)"> <?= $question->getResposta4(); ?></button>
            </div>
            
            <!-- Botão para pular a pergunta -->
            <button type="button" onclick="pularPergunta()">Pular</button>

            <!-- Campo oculto para enviar o ID do botão -->
            <input type="hidden" name="botao_id" id="botao_id" value="">
            <input type="hidden" name="resposta" id="resposta" value="">
            <input type="hidden" name="pular" id="pular" value="">
        </form>

        <!-- Notificação de alerta -->
        <div id="notification" class="notification"></div>

        <script>
            let timer;
            let countdown = 10; // Definindo o tempo limite de 10 segundos

            // Função para iniciar o contador regressivo
            function iniciarTimer() {
                timer = setInterval(function() {
                    if (countdown > 0) {
                        countdown--;
                        document.getElementById('timer').textContent = countdown; // Atualiza o valor do timer na tela
                    } else {
                        // Se o tempo esgotar, pular automaticamente a pergunta
                        clearInterval(timer);
                        pularPergunta();
                    }
                }, 1000); // Intervalo de 1 segundo
            }

            function enviarResposta(button) {
                // Para o timer quando o usuário responder
                clearInterval(timer);

                // Captura o valor e o ID do botão clicado
                document.getElementById('resposta').value = button.value;
                document.getElementById('botao_id').value = button.id;

                // Simula uma resposta correta ou incorreta para fins de teste
                const respostaCorreta = "<?= htmlspecialchars($question->getRespostaCorreta()); ?>";
                let mensagem = "";

                if (button.value.trim() === respostaCorreta.trim()) {
                    button.style.backgroundColor = 'green'; 
                    mensagem = "Parabéns, você acertou!";
                } else {
                    button.style.backgroundColor = 'red';
                    mensagem = "Errou, foi quase!";

                    switch (respostaCorreta.trim()) {
                        case "<?= htmlspecialchars($question->getResposta1()); ?>":
                            document.getElementById('btn1').style.backgroundColor = 'green';
                            break;
                        case "<?= htmlspecialchars($question->getResposta2()); ?>":
                            document.getElementById('btn2').style.backgroundColor = 'green';
                            break;
                        case "<?= htmlspecialchars($question->getResposta3()); ?>":
                            document.getElementById('btn3').style.backgroundColor = 'green';
                            break;
                        case "<?= htmlspecialchars($question->getResposta4()); ?>":
                            document.getElementById('btn4').style.backgroundColor = 'green';
                            break;
                    }
                }

                var notification = document.getElementById('notification');
                notification.innerText = mensagem;
                notification.style.backgroundColor = button.value.trim() === respostaCorreta.trim() ? 'green' : 'red';
                notification.className = "notification show";

                setTimeout(function() {
                    document.getElementById('quizForm').submit();
                }, 2000); // 2 segundos de espera antes de enviar
            }

            function pularPergunta() {
                clearInterval(timer); // Para o timer ao pular
                document.getElementById('resposta').value = ''; 
                document.getElementById('botao_id').value = '';
                document.getElementById('pular').value = '1';

                var notification = document.getElementById('notification');
                notification.innerText = "Você pulou a pergunta!";
                notification.style.backgroundColor = 'orange'; 
                notification.className = "notification show";

                setTimeout(function() {
                    document.getElementById('quizForm').submit();
                }, 300); 
            }

            // Iniciar o timer assim que a página carregar
            window.onload = function() {
                iniciarTimer();
            };
        </script>
    <?php else: ?>
        <p>Você não possui mais perguntas disponíveis.</p>
    <?php endif; ?>
</body>

</html>
