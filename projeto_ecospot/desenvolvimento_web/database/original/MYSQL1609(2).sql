CREATE DATABASE ECOSPORT_ECOALERT;

USE ECOSPORT_ECOALERT;

CREATE TABLE categoria(
    categoria_id INT PRIMARY KEY,
    codigo VARCHAR(30) NOT NULL UNIQUE,
    tipo VARCHAR(20) NOT NULL,
    nome VARCHAR(60) NOT NULL,
    descricao VARCHAR(300),
    situacao VARCHAR(20) NOT NULL,
    cor VARCHAR(10) NOT NULL
);

CREATE TABLE localizacao(
    localizacao_id INT PRIMARY KEY,
    latitude DECIMAL(10,7) NOT NULL,
    longitude DECIMAL(10,7) NOT NULL,
    logradouro VARCHAR(120),
    numero VARCHAR(10),
    bairro VARCHAR(60),
    municipio VARCHAR(60) NOT NULL,
    uf CHAR(2) NOT NULL,
    cep VARCHAR(8)
);

CREATE TABLE usuario(
    usuario_id INT PRIMARY KEY,
    nome VARCHAR(100) NOT NULL,
    email VARCHAR(100) NOT NULL UNIQUE,
    situacao VARCHAR(20) NOT NULL,
    papel VARCHAR(30) NOT NULL,
    data_criacao DATE NOT NULL
);

CREATE TABLE ponto_sustentavel(
    ponto_id INT PRIMARY KEY,
    usuario_id INT NOT NULL,
    categoria_id INT NOT NULL,
    localizacao_id INT NOT NULL UNIQUE,
    nome VARCHAR(100) NOT NULL,
    descricao VARCHAR(500),
    horario_funcionamento VARCHAR(200),
    contato VARCHAR(100),
    estado VARCHAR(30) NOT NULL,
    FOREIGN KEY (usuario_id)REFERENCES usuario(usuario_id),
    FOREIGN KEY (categoria_id)REFERENCES categoria(categoria_id),
    FOREIGN KEY (localizacao_id)REFERENCES localizacao(localizacao_id)
);

CREATE TABLE alerta_ambiental(
    alerta_id INT PRIMARY KEY,
    usuario_id INT NOT NULL,
    categoria_id INT NOT NULL,
    localizacao_id INT NOT NULL UNIQUE,
    descricao VARCHAR(500) NOT NULL,
    data_relato DATE NOT NULL,
    estado VARCHAR(30) NOT NULL,
    FOREIGN KEY (usuario_id)REFERENCES usuario(usuario_id),
    FOREIGN KEY (categoria_id)REFERENCES categoria(categoria_id),
    FOREIGN KEY (localizacao_id)REFERENCES localizacao(localizacao_id)
);

CREATE TABLE midia(
    midia_id INT PRIMARY KEY,
    usuario_id INT NOT NULL,
    entidade_vinculada VARCHAR(30) NOT NULL,
    registro_id INT NOT NULL,
    descricao VARCHAR(150),
    data_envio DATE NOT NULL,
    FOREIGN KEY (usuario_id)REFERENCES usuario(usuario_id)
);

CREATE TABLE confirmacao(
    confirmacao_id INT PRIMARY KEY,
    alerta_id INT NOT NULL,
    usuario_id INT NOT NULL,
    data_confirmacao DATE NOT NULL,
    observacao VARCHAR(200),
    FOREIGN KEY (alerta_id)REFERENCES alerta_ambiental(alerta_id),
    FOREIGN KEY (usuario_id)REFERENCES usuario(usuario_id)
);

CREATE TABLE historico_status (
    historico_id INT PRIMARY KEY,
    entidade_referenciada VARCHAR(30) NOT NULL,
    registro_id INT NOT NULL,
    estado_anterior VARCHAR(30) NOT NULL,
    estado_novo VARCHAR(30) NOT NULL,
    data_alteracao DATE NOT NULL
);

CREATE TABLE solicitacao_rota (
    solicitacao_id INT PRIMARY KEY,
    usuario_id INT,
    origem VARCHAR(255) NOT NULL,
    destino VARCHAR(255) NOT NULL,
    modal VARCHAR(30) NOT NULL,
    data_solicitacao DATE NOT NULL,

    FOREIGN KEY (usuario_id)
        REFERENCES usuario(usuario_id)
);

CREATE TABLE opcao_rota(
    opcao_id INT PRIMARY KEY,
    solicitacao_id INT NOT NULL,
    tipo_rota VARCHAR(30) NOT NULL,
    geometria VARCHAR(500),
    distancia DECIMAL(10,2),
    duracao INT,
    criterio VARCHAR(200),
    FOREIGN KEY (solicitacao_id)REFERENCES solicitacao_rota(solicitacao_id)
);