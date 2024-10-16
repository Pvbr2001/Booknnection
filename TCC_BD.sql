CREATE DATABASE booknnection;

USE booknnection;

CREATE TABLE usuario (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nome VARCHAR(100) NOT NULL,
    email VARCHAR(100) NOT NULL UNIQUE,
    senha VARBINARY(255) NOT NULL,
    account_type ENUM('fisica', 'juridica') NOT NULL,
    cpf_cnpf VARBINARY(255) NOT NULL UNIQUE,
    endereco VARCHAR(100) NOT NULL,
    cidade VARCHAR(100) NOT NULL,
    data_criacao TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

CREATE TABLE livros (
    id INT AUTO_INCREMENT PRIMARY KEY,
    titulo VARCHAR(255) NOT NULL,
    autor VARCHAR(255) NOT NULL,
    isbn VARCHAR(20) NOT NULL UNIQUE,
    capa_tipo VARCHAR(50),
    ano_lancamento INT,
    caminho_capa VARCHAR(255)
);

CREATE TABLE lista_livros (
    id INT AUTO_INCREMENT PRIMARY KEY,
    id_usuario INT NOT NULL,
    id_livro INT NOT NULL,
    FOREIGN KEY (id_usuario) REFERENCES usuario(id),
    FOREIGN KEY (id_livro) REFERENCES livros(id)
);

CREATE TABLE posts (
    id INT AUTO_INCREMENT PRIMARY KEY,
    id_usuario INT NOT NULL,
    id_livro INT NOT NULL,
    titulo VARCHAR(255) NOT NULL,
    descricao TEXT NOT NULL,
    cidade VARCHAR(100) NOT NULL,
    FOREIGN KEY (id_usuario) REFERENCES usuario(id),
    FOREIGN KEY (id_livro) REFERENCES livros(id)
);

CREATE TABLE curtidas (
    id INT AUTO_INCREMENT PRIMARY KEY,
    id_usuario INT NOT NULL,
    id_post INT NOT NULL,
    data_criacao TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (id_usuario) REFERENCES usuario(id),
    FOREIGN KEY (id_post) REFERENCES posts(id),
    UNIQUE (id_usuario, id_post) 
);

CREATE TABLE posts_salvos (
    id INT AUTO_INCREMENT PRIMARY KEY,
    id_usuario INT NOT NULL,
    id_post INT NOT NULL,
    FOREIGN KEY (id_usuario) REFERENCES usuario(id),
    FOREIGN KEY (id_post) REFERENCES posts(id)
);

create table pedding(
    id INT AUTO_INCREMENT PRIMARY KEY,
    id_usuario2 int not null,
    id_usuario INT NOT NULL,
    id_post INT NOT NULL,
    id_livro int not null,
    id_livro2 int not null,
    data_criacao TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (id_usuario) REFERENCES usuario(id),
    FOREIGN KEY (id_usuario2) REFERENCES usuario(id),
    FOREIGN KEY (id_post) REFERENCES posts(id),
    FOREIGN KEY (id_livro) REFERENCES livros(id_livro),
    FOREIGN KEY (id_livro2) REFERENCES livros(id_livro)
);

ALTER USER 'root'@'localhost' IDENTIFIED BY '81631240';
FLUSH PRIVILEGES;

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
    u.cpf_cnpf AS cpf_cnpf,
    u.endereco AS endereco,
    p.cidade AS cidade
FROM
    posts p
JOIN 
    usuario u ON p.id_usuario = u.id
JOIN 
    livros l ON p.id_livro = l.id;
