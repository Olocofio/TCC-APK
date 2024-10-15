<?php

class conexao {
    private $host = 'pgsql.projetoscti.com.br';
    private $db = 'projetoscti28';
    private $user = 'projetoscti28';
    private $password = 'eq13556';
    private $conn;

    public function getConnection() {
        try {
            $this->conn = new PDO("pgsql:host=$this->host;dbname=$this->db", $this->user, $this->password);
            $this->conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
            return $this->conn;
        } catch (PDOException $e) {
            throw new RuntimeException('Connection failed: ' . $e->getMessage());
        }
    }
}

// try {
//     $conexao = (new conexao())->getConnection();
//     echo "Conexão deu certo!";
// } catch (Exception $e) {
//     echo $e->getMessage();
// }
?>
