<?php

require_once 'conexao.php';
require_once 'QuestionDTO.php';

class PerguntaService {
    private $conn;

    public function __construct() {
        $this->conn = (new conexao())->getConnection();
    }

    // Método para buscar todas as matérias
    public function findAllMaterias() {
        $list = [];
        $sql = "SELECT DISTINCT materia FROM pergunta";

        try {
            $stmt = $this->conn->prepare($sql);
            $stmt->execute();
            while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
                $list[] = $row['materia'];
            }
        } catch (PDOException $e) {
            throw new RuntimeException("Erro ao buscar matérias: " . $e->getMessage());
        }
        
        return $list;
    }

    // Método para buscar perguntas por matéria com limite de questões
    public function findQuestionsByMateria($materia, $questoes) {
        $questions = [];
        $sql = "SELECT * FROM pergunta WHERE materia = :materia ORDER BY RANDOM() LIMIT :questoes";
        
        try {
            $stmt = $this->conn->prepare($sql);
            $stmt->bindParam(':materia', $materia);
            $stmt->bindParam(':questoes', $questoes, PDO::PARAM_INT);
            $stmt->execute();

            while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
                // Cria uma instância de QuestionDTO para cada pergunta
                $question = new QuestionDTO();
                $question->setId($row['id_perg']);
                $question->setMateria($row['materia']);
                $question->setPergunta($row['pergunta']);
                $question->setResposta1($row['resposta1']);
                $question->setResposta2($row['resposta2']);
                $question->setResposta3($row['resposta3']);
                $question->setResposta4($row['resposta4']);
                $question->setRespostaCorreta($row['resposta_correta']);
                
                // Adiciona a pergunta na lista de questões
                $questions[] = $question;
            }
        } catch (PDOException $e) {
            throw new RuntimeException("Erro ao buscar perguntas: " . $e->getMessage());
        }

        return $questions;
    }

    // Método para inserir a pontuação
    public function insertScore($idUsu, $pontuacao, $qtdPerguntas) {
        $sql = "INSERT INTO partida (id_usu, dt_game, hr_game, pontuacao, qtd_perguntas) VALUES (:idUsu, :dt_game, :hr_game, :pontuacao, :qtd_perguntas)";
        
        try {
            $stmt = $this->conn->prepare($sql);

            // Obtém a data e a hora atuais
            $dataAtual = date('Y-m-d');
            $horaAtual = date('H:i:s');

            // Define os parâmetros da query
            $stmt->bindParam(':idUsu', $idUsu, PDO::PARAM_INT);
            $stmt->bindParam(':dt_game', $dataAtual);
            $stmt->bindParam(':hr_game', $horaAtual);
            $stmt->bindParam(':pontuacao', $pontuacao, PDO::PARAM_INT);
            $stmt->bindParam(':qtd_perguntas', $qtdPerguntas, PDO::PARAM_INT);

            // Executa o INSERT
            $stmt->execute();
            echo "Pontuação inserida com sucesso!";
        } catch (PDOException $e) {
            throw new RuntimeException("Erro ao inserir pontuação: " . $e->getMessage());
        }
    }
}

?>