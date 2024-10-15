<?php
// Inclui o arquivo onde a classe `conexao` está definida
include "conexao.php"; // Certifique-se de que o caminho está correto

class UsuarioDTO
{
    private $nome;
    private $email;
    private $senha;
    private $telefone;

    public function __construct($nome, $email, $senha, $telefone)
    {
        $this->nome = $nome;
        $this->email = $email;
        $this->senha = $senha;
        $this->telefone = $telefone;
    }

    public function getNome()
    {
        return $this->nome;
    }

    public function getEmail()
    {
        return $this->email;
    }

    public function getSenha()
    {
        return $this->senha;
    }

    public function getTelefone()
    {
        return $this->telefone;
    }
}

class UsuarioDAO
{
    private $conn;

    public function __construct()
    {
        // Conexão com o banco de dados utilizando a classe 'conexao'
        $this->conn = (new conexao())->getConnection();
    }

    public function emailJaCadastrado($email)
    {
        $sql = "SELECT COUNT(*) FROM usuario WHERE email = :email";
        try {
            $stmt = $this->conn->prepare($sql);
            $stmt->bindParam(':email', $email);
            $stmt->execute();

            $count = $stmt->fetchColumn();

            return $count > 0;
        } catch (PDOException $e) {
            error_log($e->getMessage());
            return false;
        }
    }

    public function cadastrarUsuario(UsuarioDTO $usuario)
    {
        if ($this->emailJaCadastrado($usuario->getEmail())) {
            header("Location: home.html?mensagem=Usuário já existe", true, 302);
            return;
        }

        // SQL para inserir um novo usuário
        $sql = "INSERT INTO usuario (nome, email, senha, telefone) VALUES (:nome, :email, :senha, :telefone)";

        try {
            // Preparar a consulta
            $stmt = $this->conn->prepare($sql);
            // Definir os parâmetros
            $stmt->bindParam(':nome', $usuario->getNome());
            $stmt->bindParam(':email', $usuario->getEmail());
            $stmt->bindParam(':senha', $usuario->getSenha());
            $stmt->bindParam(':telefone', $usuario->getTelefone());
            // Executar a consulta
            $stmt->execute();

            // Redireciona para a página de login após o cadastro
            header('Location: login.html');
            exit(); // Certifique-se de que o script para aqui após o redirecionamento

        } catch (PDOException $e) {
            // Imprimir o erro no log de erros
            error_log($e->getMessage());
            echo "Erro ao cadastrar o usuário.";
        }
    }
}

// Verifica se o formulário foi enviado via POST
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Verifica se os dados do formulário foram enviados corretamente
    if (isset($_POST['nome']) && isset($_POST['email']) && isset($_POST['senha']) && isset($_POST['telefone'])) {
        // Obtém os dados do formulário via POST
        $nome = $_POST['nome'];
        $email = $_POST['email'];
        $senha = password_hash($_POST['senha'], PASSWORD_DEFAULT); // Cria um hash seguro da senha
        $telefone = $_POST['telefone'];

        // Cria o DTO e DAO e cadastra o usuário
        $usuarioDTO = new UsuarioDTO($nome, $email, $senha, $telefone);
        $usuarioDAO = new UsuarioDAO();

        $usuarioDAO->cadastrarUsuario($usuarioDTO);
    } else {
        echo "Por favor, preencha todos os campos do formulário.";
    }
}
