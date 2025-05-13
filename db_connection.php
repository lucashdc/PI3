<?php
// Definir as constantes para os dados de conexão
const DB_SERVER = 'mysql.pratico.app.br';
const DB_USERNAME = 'pratico04';
const DB_PASSWORD = 'PI2Univesp';
const DB_NAME = 'pratico04';

class Database {
    private $conn;

    public function __construct() {
        try {
            $this->conn = new mysqli(DB_SERVER, DB_USERNAME, DB_PASSWORD, DB_NAME);
            $this->conn->set_charset("utf8");
        } catch (Exception $e) {
            die("Erro na conexão com o banco de dados: " . $e->getMessage());
        }
    }

    public function getConnection() {
        return $this->conn;
    }
}

// Criar uma instância da classe Database
$database = new Database();
$conn = $database->getConnection();

// Verificar se houve erro na conexão
if ($conn->connect_error) {
    die("Erro na conexão com o banco de dados: " . $conn->connect_error);
}
?>
