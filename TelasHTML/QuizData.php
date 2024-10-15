<?php
// Definir as matérias manualmente com base na lista fornecida
$materias = [
    "História",
    "Geografia",
    "Ciência e Tecnologia",
    "Língua Portuguesa",
    "Matemática"
];
?>

<!DOCTYPE html>
<html lang="pt-BR">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Selecionar Matérias</title>
    <link rel="stylesheet" href="css/QuizData.css"> <!-- Arquivo de CSS -->
</head>

<body>
    <div class="icons">
        <a href="home.html"> <img src="img/logout.png" class="icon" alt="Voltar"> </a>
        <a href="home.html"> <img src="img/casaBranca.png" class="icon" alt="Início"> </a>
    </div>

    <div class="logo">
        <img src="img/logo-sf.png" alt="Logo FunLearn">
    </div>

    <h1>SEJA BEM-VINDO</h1>

    <form action="jogo.php" method="POST" id="meuFormulario">
        <label for="materia">Escolha a Matéria</label>
        <select id="materia" name="materia" required>
            <option value="">Selecione...</option>
            <!-- Preencher o select com as matérias carregadas -->
            <?php foreach ($materias as $materia): ?>
                <option value="<?= htmlspecialchars($materia) ?>"><?= htmlspecialchars($materia) ?></option>
            <?php endforeach; ?>
        </select>

        <label for="perguntas">Quantidade de Perguntas</label>
        <input type="number" id="perguntas" name="perguntas" min="5" max="15" required>

        <div id="alerta" class="alert">
            <span>Por favor, insira um valor entre 5 e 15.</span>
            <button class="close-btn" type="button" onclick="fecharAlerta()">X</button>
        </div>

        <button class="comecar-btn" type="button" onclick="validarValor()">Começar</button>
    </form>

    <script>
        function validarValor() {
            const input = document.getElementById('perguntas').value;
            const alerta = document.getElementById('alerta');

            if (input < 5 || input > 15 || input === "") {
                alerta.classList.add('show');
            } else {
                alerta.classList.remove('show');
                document.getElementById('meuFormulario').submit(); 
            }
        }

        function fecharAlerta() {
            const alerta = document.getElementById('alerta');
            alerta.classList.remove('show');
        }
    </script>

</body>

</html>
