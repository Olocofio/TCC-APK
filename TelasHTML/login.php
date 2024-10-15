<?php

include "conexao.php";

class UsuarioDAO
{
    private $conn;

    public function __construct()
    {
        $this->conn = (new conexao())->getConnection();
    }

    public function loginUsuario($email, $senha)
    {
        $sql = "SELECT * FROM usuario WHERE email = :email";

        try {
            $stmt = $this->conn->prepare($sql);
            $stmt->bindParam(':email', $email);
            $stmt->execute();

            $usuario = $stmt->fetch(PDO::FETCH_ASSOC);

            if ($usuario && password_verify($senha, $usuario['senha'])) {
                header("Location: QuizData.php", true, 302);
                exit; // Certifique-se de chamar exit após o redirecionamento
            } else {
                header("Location: home.html?mensagem=Senha inválida", true, 302);
                exit; // Também aqui
            }
        } catch (PDOException $e) {
            error_log($e->getMessage());
            return false;
        }
    }
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = $_POST['email'] ?? '';
    $senha = $_POST['senha'] ?? '';

    $usuarioDAO = new UsuarioDAO();
    $usuarioDAO->loginUsuario($email, $senha);
}
