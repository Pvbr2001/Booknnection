Vamos fazer logo esse projeto da certo.

Script do banco mysql:

CREATE DATABASE meu_banco_de_dados;
USE meu_banco_de_dados; 

CREATE TABLE usuario (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nome VARCHAR(100) NOT NULL,
    email VARCHAR(100) NOT NULL UNIQUE,
    senha VARCHAR(255) NOT NULL,
    account_type ENUM('fisica, juridica') NOT NULL,
    cpf_cnpf VARCHAR(14) not null UNIQUE,
    endereco VARCHAR(100) not null,
    data_criacao TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);
