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
    data_criacao TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

ALTER USER 'root'@'localhost' IDENTIFIED BY '81631240';
FLUSH PRIVILEGES;

