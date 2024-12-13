<?php
require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../vendor/autoload.php';

use Dotenv\Dotenv;

$dotenv = Dotenv::createImmutable(__DIR__ . '/../');
$dotenv->load();

class User {
    private $conn;
    private $chave;
    private $id;
    private $username;
    private $email;
    private $nome;
    private $cpf_cnpj;
    private $endereco;
    private $cidade;
    private $account_type;
    private $telefone;
    private $foto_perfil;

    public function __construct() {
        $database = Database::getInstance();
        $this->conn = $database->getConnection();
        $this->chave = getenv('CHAVE_CRIPTOGRAFIA');
    }

    // Função para registrar um usuário, criptografando dados pessoais como senha e CPF
    public function register($nome, $email, $senha, $account_type, $cpf_cnpj, $endereco, $cidade, $telefone) {
        if ($this->userExists($email)) {
            return 'UserExists';
        }

        // Gerar um salt para CPF/CNPJ para evitar duplicatas
        $salt = bin2hex(random_bytes(16)); // Salt de 16 bytes
        $cpfCnpjCriptografado = openssl_encrypt($cpf_cnpj . $salt, 'aes-256-cbc', $this->chave, 0, str_repeat('0', 16));

        // Criptografar senha
        $senhaCriptografada = openssl_encrypt($senha, 'aes-256-cbc', $this->chave, 0, str_repeat('0', 16));

        // Definir a foto de perfil padrão
        $fotoPerfil = 'icone-padrao.png';

        // Inserir no banco de dados
        $sql = "INSERT INTO usuario (nome, email, senha, account_type, cpf_cnpj, endereco, cidade, telefone, foto_perfil) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)";
        $stmt = $this->conn->prepare($sql);
        $stmt->bind_param('sssssssss', $nome, $email, $senhaCriptografada, $account_type, $cpfCnpjCriptografado, $endereco, $cidade, $telefone, $fotoPerfil);

        if ($stmt->execute()) {
            return 'Success';
        } else {
            return 'RegistrationFailed';
        }
    }

    // Função para verificar se o usuário já existe no banco de dados
    public function userExists($email) {
        $sql = "SELECT id FROM usuario WHERE email = ?";
        $stmt = $this->conn->prepare($sql);
        $stmt->bind_param('s', $email);
        $stmt->execute();
        $stmt->store_result();

        return $stmt->num_rows > 0;
    }

    // Função para login, descriptografando a senha
    public function login($email, $senha) {
        $sql = "SELECT id, senha, nome, email, account_type, cpf_cnpj, endereco, cidade, telefone FROM usuario WHERE email = ?";
        $stmt = $this->conn->prepare($sql);
        $stmt->bind_param('s', $email);
        $stmt->execute();
        $stmt->store_result();

        if ($stmt->num_rows === 0) {
            return false;
        }

        $stmt->bind_result($id, $senhaCriptografada, $nome, $email, $account_type, $cpf_cnpj, $endereco, $cidade, $telefone);
        $stmt->fetch();

        $senhaDescriptografada = openssl_decrypt($senhaCriptografada, 'aes-256-cbc', $this->chave, 0, str_repeat('0', 16));

        if ($senha === $senhaDescriptografada) {
            $this->id = $id;
            $this->username = $nome;
            $this->email = $email;
            $this->nome = $nome;
            $this->cpf_cnpj = $cpf_cnpj;
            $this->endereco = $endereco;
            $this->cidade = $cidade;
            $this->account_type = $account_type;
            $this->telefone = $telefone;
            return true;
        }

        return false;
    }

    // Método para carregar informações do usuário por ID
    public function loadById($id) {
        $sql = "SELECT id, nome, email, account_type, cpf_cnpj, endereco, cidade, telefone, foto_perfil FROM usuario WHERE id = ?";
        $stmt = $this->conn->prepare($sql);
        $stmt->bind_param('i', $id);
        $stmt->execute();
        $stmt->store_result();

        if ($stmt->num_rows > 0) {
            $stmt->bind_result($id, $nome, $email, $account_type, $cpf_cnpj, $endereco, $cidade, $telefone, $foto_perfil);
            $stmt->fetch();

            $this->id = $id;
            $this->username = $nome;
            $this->email = $email;
            $this->nome = $nome;
            $this->cpf_cnpj = $cpf_cnpj;
            $this->endereco = $endereco;
            $this->cidade = $cidade;
            $this->account_type = $account_type;
            $this->telefone = $telefone;
            $this->foto_perfil = $foto_perfil;
        }
    }

    // Métodos para obter informações do usuário
    public function getId() {
        return $this->id;
    }

    public function getUsername() {
        return $this->username;
    }

    public function getEmail() {
        return $this->email;
    }

    public function getNome() {
        return $this->nome;
    }

    public function getCpfCnpj() {
        return $this->cpf_cnpj;
    }

    public function getEndereco() {
        return $this->endereco;
    }

    public function getCidade() {
        return $this->cidade;
    }

    public function getAccountType() {
        return $this->account_type;
    }

    public function getTelefone() {
        return $this->telefone;
    }

    // Função para verificar se o ISBN já está registrado
    public function verificarISBN($isbn) {
        $sql = "SELECT id FROM livros WHERE isbn = ?";
        $stmt = $this->conn->prepare($sql);
        $stmt->bind_param('s', $isbn);
        $stmt->execute();
        $stmt->store_result();
        return $stmt->num_rows > 0;
    }

    // Função para adicionar um livro
    public function adicionarLivro($titulo, $autor, $isbn, $capa_tipo, $ano_lancamento, $caminhoCapa) {
        $sql = "INSERT INTO livros (titulo, autor, isbn, capa_tipo, ano_lancamento, caminho_capa) VALUES (?, ?, ?, ?, ?, ?)";
        $stmt = $this->conn->prepare($sql);
        $stmt->bind_param('ssssss', $titulo, $autor, $isbn, $capa_tipo, $ano_lancamento, $caminhoCapa);
        $stmt->execute();
        return $this->conn->insert_id;
    }

    // Função para adicionar o livro à lista do usuário
    public function adicionarLivroLista($user_id, $livro_id) {
        $sql = "INSERT INTO lista_livros (id_usuario, id_livro) VALUES (?, ?)";
        $stmt = $this->conn->prepare($sql);
        $stmt->bind_param('ii', $user_id, $livro_id);
        return $stmt->execute();
    }

    // Função para exibir os livros salvos no feed do usuário
    public function exibirLivrosFeed($id_usuario) {
        $sql = "SELECT livros.id, livros.titulo, livros.autor, livros.caminho_capa FROM livros
                INNER JOIN lista_livros ON livros.id = lista_livros.id_livro
                WHERE lista_livros.id_usuario = ?";
        $stmt = $this->conn->prepare($sql);
        $stmt->bind_param('i', $id_usuario);
        $stmt->execute();
        $result = $stmt->get_result();

        $livros = [];
        while ($row = $result->fetch_assoc()) {
            $livros[] = $row;
        }
        return $livros;
    }

    // Função para atualizar o telefone do usuário
    public function atualizarTelefone($id_usuario, $telefone) {
        $sql = "UPDATE usuario SET telefone = ? WHERE id = ?";
        $stmt = $this->conn->prepare($sql);
        $stmt->bind_param('si', $telefone, $id_usuario);
        return $stmt->execute();
    }

    // Método para alterar a foto de perfil
    public function alterarFotoPerfil($id_usuario, $caminhoFoto) {
        $sql = "UPDATE usuario SET foto_perfil = ? WHERE id = ?";
        $stmt = $this->conn->prepare($sql);
        $stmt->bind_param('si', $caminhoFoto, $id_usuario);
        return $stmt->execute();
    }

    // Método para trocar senha
    public function trocarSenha($id_usuario, $senhaAtual, $novaSenha) {
        // Verificar se a senha atual está correta
        $sql = "SELECT senha FROM usuario WHERE id = ?";
        $stmt = $this->conn->prepare($sql);
        $stmt->bind_param('i', $id_usuario);
        $stmt->execute();
        $stmt->store_result();
        $stmt->bind_result($senhaCriptografada);
        $stmt->fetch();

        // Descriptografar a senha atual
        $senhaAtualDescriptografada = openssl_decrypt($senhaCriptografada, 'aes-256-cbc', $this->chave, 0, str_repeat('0', 16));

        if ($senhaAtual !== $senhaAtualDescriptografada) {
            return false;
        }

        // Criptografar a nova senha
        $novaSenhaCriptografada = openssl_encrypt($novaSenha, 'aes-256-cbc', $this->chave, 0, str_repeat('0', 16));

        // Atualizar a senha
        $sql = "UPDATE usuario SET senha = ? WHERE id = ?";
        $stmt = $this->conn->prepare($sql);
        $stmt->bind_param('si', $novaSenhaCriptografada, $id_usuario);
        return $stmt->execute();
    }

    // Método para obter a foto de perfil
    public function getFotoPerfil() {
        return $this->foto_perfil;
    }

    public function desativarConta($id_usuario) {
        // Excluir registros dependentes
        $sql = "DELETE FROM lista_livros WHERE id_usuario = ?";
        $stmt = $this->conn->prepare($sql);
        $stmt->bind_param('i', $id_usuario);
        $stmt->execute();

        $sql = "DELETE FROM posts WHERE id_usuario = ?";
        $stmt = $this->conn->prepare($sql);
        $stmt->bind_param('i', $id_usuario);
        $stmt->execute();

        $sql = "DELETE FROM comentarios WHERE id_usuario = ?";
        $stmt = $this->conn->prepare($sql);
        $stmt->bind_param('i', $id_usuario);
        $stmt->execute();

        $sql = "DELETE FROM curtidas WHERE id_usuario = ?";
        $stmt = $this->conn->prepare($sql);
        $stmt->bind_param('i', $id_usuario);
        $stmt->execute();

        $sql = "DELETE FROM posts_salvos WHERE id_usuario = ?";
        $stmt = $this->conn->prepare($sql);
        $stmt->bind_param('i', $id_usuario);
        $stmt->execute();

        $sql = "DELETE FROM notificacoes WHERE id_usuario = ? OR id_usuario_emissor = ?";
        $stmt = $this->conn->prepare($sql);
        $stmt->bind_param('ii', $id_usuario, $id_usuario);
        $stmt->execute();

        $sql = "DELETE FROM trocas WHERE id_usuario_solicitante = ? OR id_usuario_dono = ?";
        $stmt = $this->conn->prepare($sql);
        $stmt->bind_param('ii', $id_usuario, $id_usuario);
        $stmt->execute();

        // Excluir o usuário
        $sql = "DELETE FROM usuario WHERE id = ?";
        $stmt = $this->conn->prepare($sql);
        $stmt->bind_param('i', $id_usuario);
        return $stmt->execute();
    }
}
