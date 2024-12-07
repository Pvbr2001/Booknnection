-- Criar o banco de dados
CREATE DATABASE booknnection;

-- Usar o banco de dados
USE booknnection;

-- Criar a tabela usuario
CREATE TABLE usuario (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nome VARCHAR(100) NOT NULL,
    email VARCHAR(100) NOT NULL UNIQUE,
    senha VARBINARY(255) NOT NULL,
    account_type ENUM('fisica', 'juridica') NOT NULL,
    cpf_cnpj VARBINARY(255) NOT NULL UNIQUE,
    endereco VARCHAR(100) NOT NULL,
    telefone VARCHAR(20),
    cidade VARCHAR(100) NOT NULL,
    foto_perfil VARCHAR(255),
    status ENUM('ativo', 'inativo') DEFAULT 'ativo',
    data_criacao TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Criar a tabela livros
CREATE TABLE livros (
    id INT AUTO_INCREMENT PRIMARY KEY,
    titulo VARCHAR(255) NOT NULL,
    autor VARCHAR(255) NOT NULL,
    isbn VARCHAR(20) NOT NULL UNIQUE,
    capa_tipo VARCHAR(50),
    ano_lancamento INT,
    caminho_capa VARCHAR(255)
);

-- Criar a tabela lista_livros
CREATE TABLE lista_livros (
    id INT AUTO_INCREMENT PRIMARY KEY,
    id_usuario INT NOT NULL,
    id_livro INT NOT NULL,
    FOREIGN KEY (id_usuario) REFERENCES usuario(id) ON DELETE CASCADE,
    FOREIGN KEY (id_livro) REFERENCES livros(id) ON DELETE CASCADE
);

-- Criar a tabela posts
CREATE TABLE posts (
    id INT AUTO_INCREMENT PRIMARY KEY,
    id_usuario INT NOT NULL,
    id_livro INT NOT NULL,
    titulo VARCHAR(255) NOT NULL,
    descricao TEXT NOT NULL,
    cidade VARCHAR(100) NOT NULL,
    FOREIGN KEY (id_usuario) REFERENCES usuario(id) ON DELETE CASCADE,
    FOREIGN KEY (id_livro) REFERENCES livros(id) ON DELETE CASCADE
);

-- Criar a tabela comentarios
CREATE TABLE comentarios (
    id INT AUTO_INCREMENT PRIMARY KEY,
    id_post INT NOT NULL,
    id_usuario INT NOT NULL,
    texto TEXT NOT NULL,
    data_comentario TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (id_post) REFERENCES posts(id) ON DELETE CASCADE,
    FOREIGN KEY (id_usuario) REFERENCES usuario(id) ON DELETE CASCADE
);

-- Criar a tabela curtidas
CREATE TABLE curtidas (
    id INT AUTO_INCREMENT PRIMARY KEY,
    id_usuario INT NOT NULL,
    id_post INT NOT NULL,
    data_criacao TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (id_usuario) REFERENCES usuario(id) ON DELETE CASCADE,
    FOREIGN KEY (id_post) REFERENCES posts(id) ON DELETE CASCADE,
    UNIQUE (id_usuario, id_post)
);

-- Criar a tabela posts_salvos
CREATE TABLE posts_salvos (
    id INT AUTO_INCREMENT PRIMARY KEY,
    id_usuario INT NOT NULL,
    id_post INT NOT NULL,
    FOREIGN KEY (id_usuario) REFERENCES usuario(id) ON DELETE CASCADE,
    FOREIGN KEY (id_post) REFERENCES posts(id) ON DELETE CASCADE
);

-- Criar a tabela notificacoes
CREATE TABLE notificacoes (
    id INT AUTO_INCREMENT PRIMARY KEY,
    id_usuario INT NOT NULL, -- Usuário que receberá a notificação
    id_usuario_emissor INT NOT NULL, -- Usuário que enviou a notificação
    tipo ENUM('curtida', 'salvo', 'troca') NOT NULL, -- Tipo de notificação
    id_post INT NOT NULL, -- Post relacionado à notificação
    lida BOOLEAN DEFAULT FALSE, -- Status de leitura
    data_criacao TIMESTAMP DEFAULT CURRENT_TIMESTAMP, -- Data de criação
    FOREIGN KEY (id_usuario) REFERENCES usuario(id) ON DELETE CASCADE,
    FOREIGN KEY (id_usuario_emissor) REFERENCES usuario(id) ON DELETE CASCADE, -- Adicionando a relação com o usuário emissor
    FOREIGN KEY (id_post) REFERENCES posts(id) ON DELETE CASCADE
);

-- Criar a tabela trocas
CREATE TABLE trocas (
    id INT AUTO_INCREMENT PRIMARY KEY,
    id_post INT NOT NULL,
    id_usuario_solicitante INT NOT NULL,
    id_usuario_dono INT NOT NULL,
    status ENUM('pendente', 'confirmada', 'finalizada') DEFAULT 'pendente';
    data_criacao TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (id_post) REFERENCES posts(id) ON DELETE CASCADE,
    FOREIGN KEY (id_usuario_solicitante) REFERENCES usuario(id) ON DELETE CASCADE,
    FOREIGN KEY (id_usuario_dono) REFERENCES usuario(id) ON DELETE CASCADE
);

CREATE TABLE confirmacoes_troca (
    id INT AUTO_INCREMENT PRIMARY KEY,
    id_post INT NOT NULL,
    id_usuario INT NOT NULL,
    confirmado BOOLEAN DEFAULT FALSE,
    FOREIGN KEY (id_post) REFERENCES posts(id) ON DELETE CASCADE,
    FOREIGN KEY (id_usuario) REFERENCES usuario(id) ON DELETE CASCADE
);


-- Criar a view vw_notificacoes
CREATE OR REPLACE VIEW vw_notificacoes AS
SELECT
    u.id AS id_usuario_envio,
    p.id_usuario AS id_usuario_dono_post,
    p.id AS id_post
FROM
    posts p
JOIN
    usuario u ON p.id_usuario = u.id;

-- Criar a view vw_posts
CREATE OR REPLACE VIEW vw_posts AS
SELECT
    p.id AS id,
    p.id_livro AS id_livro,
    p.id_usuario AS id_usuario,
    p.titulo AS post_titulo,
    p.descricao AS descricao,
    l.caminho_capa AS caminho_capa,
    l.titulo AS titulo,
    l.autor AS autor,
    l.isbn AS isbn,
    l.capa_tipo AS capa_tipo,
    l.ano_lancamento AS ano_lancamento,
    u.nome AS nome,
    u.email AS email,
    u.cpf_cnpj AS cpf_cnpj,
    u.endereco AS endereco,
    p.cidade AS cidade
FROM
    posts p
JOIN
    usuario u ON p.id_usuario = u.id
JOIN
    livros l ON p.id_livro = l.id;

-- Alterar o usuário root
ALTER USER 'root'@'localhost' IDENTIFIED BY '81631240';
FLUSH PRIVILEGES;
