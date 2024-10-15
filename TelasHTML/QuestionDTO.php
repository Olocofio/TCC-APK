<?php

class QuestionDTO {
    private $id;
    private $materia;
    private $pergunta;
    private $resposta1;
    private $resposta2;
    private $resposta3;
    private $resposta4;
    private $respostaCorreta;

    // Getters e Setters para cada atributo
    public function getId() {
        return $this->id;
    }

    public function setId($id) {
        $this->id = $id;
    }

    public function getMateria() {
        return $this->materia;
    }

    public function setMateria($materia) {
        $this->materia = $materia;
    }

    public function getPergunta() {
        return $this->pergunta;
    }

    public function setPergunta($pergunta) {
        $this->pergunta = $pergunta;
    }

    public function getResposta1() {
        return $this->resposta1;
    }

    public function setResposta1($resposta1) {
        $this->resposta1 = $resposta1;
    }

    public function getResposta2() {
        return $this->resposta2;
    }

    public function setResposta2($resposta2) {
        $this->resposta2 = $resposta2;
    }

    public function getResposta3() {
        return $this->resposta3;
    }

    public function setResposta3($resposta3) {
        $this->resposta3 = $resposta3;
    }

    public function getResposta4() {
        return $this->resposta4;
    }

    public function setResposta4($resposta4) {
        $this->resposta4 = $resposta4;
    }

    public function getRespostaCorreta() {
        return $this->respostaCorreta;
    }

    public function setRespostaCorreta($respostaCorreta) {
        $this->respostaCorreta = $respostaCorreta;
    }

    // Método para retornar todas as respostas em um array
    public function getRespostas() {
        return array($this->resposta1, $this->resposta2, $this->resposta3, $this->resposta4);
    }
}

?>
