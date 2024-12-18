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
    id_livro_solicitante INT NULL,
    status ENUM('pendente', 'confirmada', 'finalizada') DEFAULT 'pendente',
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

-- seleçao de livros para troca
CREATE TABLE trocas_livros (
    id INT AUTO_INCREMENT PRIMARY KEY,
    id_troca INT NOT NULL,
    id_livro INT NOT NULL,
    FOREIGN KEY (id_troca) REFERENCES trocas(id) ON DELETE CASCADE,
    FOREIGN KEY (id_livro) REFERENCES livros(id) ON DELETE CASCADE
);

-- Procedure para inserir uma nova troca
DELIMITER //
CREATE PROCEDURE InserirTroca(
    IN p_id_post INT,
    IN p_id_usuario_solicitante INT,
    IN p_id_usuario_dono INT,
    IN p_status ENUM('pendente', 'confirmada', 'finalizada')
)
BEGIN
    INSERT INTO trocas (id_post, id_usuario_solicitante, id_usuario_dono, status)
    VALUES (p_id_post, p_id_usuario_solicitante, p_id_usuario_dono, p_status);
END //
DELIMITER ;

-- Procedure para confirmar uma troca
DELIMITER //
CREATE PROCEDURE ConfirmarTroca(
    IN p_id_post INT,
    IN p_id_usuario INT
)
BEGIN
    DECLARE total_confirmacoes INT;

    -- Inserir ou atualizar a confirmação de troca
    INSERT INTO confirmacoes_troca (id_post, id_usuario, confirmado)
    VALUES (p_id_post, p_id_usuario, 1)
    ON DUPLICATE KEY UPDATE confirmado = 1;

    -- Verificar se ambos os usuários confirmaram a troca
    SELECT COUNT(*) INTO total_confirmacoes
    FROM confirmacoes_troca
    WHERE id_post = p_id_post AND confirmado = 1;

    IF total_confirmacoes = 2 THEN
        -- Ambos os usuários confirmaram a troca, finalizar a troca
        UPDATE trocas SET status = 'finalizada' WHERE id_post = p_id_post;

        -- Excluir as notificações relacionadas ao post
        DELETE FROM notificacoes WHERE id_post = p_id_post;

        -- Excluir o post após a troca
        DELETE FROM posts WHERE id = p_id_post;

        -- Trocar os livros entre os usuários
        UPDATE lista_livros
        SET id_usuario = CASE
            WHEN id_usuario = p_id_usuario THEN (SELECT id_usuario_dono FROM trocas WHERE id_post = p_id_post)
            WHEN id_usuario = (SELECT id_usuario_dono FROM trocas WHERE id_post = p_id_post) THEN p_id_usuario
        END
        WHERE id_livro IN (
            SELECT id_livro FROM trocas_livros WHERE id_troca = (SELECT id FROM trocas WHERE id_post = p_id_post)
        );
    END IF;
END //
DELIMITER ;

-- Criar a view vw_notificacoes
CREATE OR REPLACE VIEW vw_notificacoes AS
SELECT
    n.id,
    n.id_usuario,
    n.id_usuario_emissor,
    n.tipo,
    n.id_post,
    n.lida,
    n.data_criacao,
    u.nome AS nome_usuario,
    ue.nome AS nome_usuario_emissor,
    p.titulo AS titulo_post,
    l.caminho_capa AS caminho_capa
FROM
    notificacoes n
JOIN
    usuario u ON n.id_usuario = u.id
JOIN
    usuario ue ON n.id_usuario_emissor = ue.id
JOIN
    posts p ON n.id_post = p.id
JOIN
    livros l ON p.id_livro = l.id;

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
