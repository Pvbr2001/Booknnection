<?php
class Database {
    // informaçoes do banco de dados
    private $host = '127.0.0.1';        
    private $username = 'root';         
    private $password = '';      
    private $database = 'booknnection';   
    private $port = '3306';                
    public $conn;

    //metodo construtor é chamado automaticamente quando um objeto da classe Database é criado
    public function __construct() {
        //cria uma nova conexão com o banco de dados usando a classe mysqli.
        $this->connect();
    }

    private function connect() {
        //Cria uma nova instância da classe mysqli com as informações fornecidas.
        $this->conn = new mysqli($this->host, $this->username, $this->password, $this->database, $this->port);

        if ($this->conn->connect_error) {
            die('Erro na conexão: ' . $this->conn->connect_error);
        }
    }
}


?>
