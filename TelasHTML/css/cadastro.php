<?php

class UsuarioDTO {
    private $nome;
    private $email;
    private $senha;
    private $telefone;

    public function __construct($nome, $email, $senha, $telefone) {
        $this->nome = $nome;
        $this->email = $email;
        $this->senha = $senha;
        $this->telefone = $telefone;
    }

    public function getNome() {
        return $this->nome;
    }

    public function getEmail() {
        return $this->email;
    }

    public function getSenha() {
        return $this->senha;
    }

    public function getTelefone() {
        return $this->telefone;
    }
}

class UsuarioDAO {
    private $conn;

    public function __construct() {
        // Conexão com o banco de dados utilizando a classe 'conexao'
        $this->conn = (new conexao())->getConnection();
    }

    public function cadastrarUsuario(UsuarioDTO $usuario) {
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
            echo "Usuário cadastrado com sucesso!";
        } catch (PDOException $e) {
            // Imprimir o erro no log de erros
            error_log($e->getMessage());
            echo "Erro ao cadastrar o usuário.";
        }
    }
}

// Exemplo de uso:
$nome = 'João Silva';
$email = 'joao.silva@example.com';
$senha = 'senhaSegura';
$telefone = '11987654321';

$usuarioDTO = new UsuarioDTO($nome, $email, $senha, $telefone);
$usuarioDAO = new UsuarioDAO();

$usuarioDAO->cadastrarUsuario($usuarioDTO);
?>