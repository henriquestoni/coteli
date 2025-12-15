-- Schema atualizado do sistema COTELI
-- Ajustado para nomes definitivos de tabelas e colunas.

CREATE DATABASE IF NOT EXISTS coteli CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE coteli;

-- Tabelas auxiliares de pregão
CREATE TABLE IF NOT EXISTS tipos_pregao (
    id_tipos_pregao INT AUTO_INCREMENT PRIMARY KEY,
    nome_tipos_pregao VARCHAR(100) NOT NULL,
    sigla_tipos_pregao VARCHAR(3) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE IF NOT EXISTS origens_pedido (
    id_origens_pedido INT AUTO_INCREMENT PRIMARY KEY,
    unidade_origem VARCHAR(150) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE IF NOT EXISTS status_pregao (
    id_status_pregao INT AUTO_INCREMENT PRIMARY KEY,
    nome_status_pregao VARCHAR(100) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE IF NOT EXISTS tipos_licitados (
    id_tipos_licitados INT AUTO_INCREMENT PRIMARY KEY,
    nome_tipos_licitados VARCHAR(100) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE IF NOT EXISTS tipos_parecer (
    id_tipos_parecer INT AUTO_INCREMENT PRIMARY KEY,
    nome_tipos_parecer VARCHAR(100) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE IF NOT EXISTS empresas (
    id_empresas INT AUTO_INCREMENT PRIMARY KEY,
    nome_empresas VARCHAR(150) NOT NULL,
    cnpj_empresas VARCHAR(32) NULL,
    email_empresas VARCHAR(150) NULL,
    telefone_empresas VARCHAR(50) NULL,
    status_empresas TINYINT NOT NULL DEFAULT 1
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Usuarios e perfis (pre-cadastro e acesso)
CREATE TABLE IF NOT EXISTS usuarios (
    id_usuarios INT AUTO_INCREMENT PRIMARY KEY,
    nome_completo VARCHAR(150) NOT NULL,
    login_usuario VARCHAR(60) NULL UNIQUE,
    email_usuario VARCHAR(150) NOT NULL UNIQUE,
    senha_hash VARCHAR(255) NULL,
    nivel_acesso TINYINT NULL CHECK (nivel_acesso BETWEEN 1 AND 5),
    is_pregoeiro TINYINT(1) NOT NULL DEFAULT 0,
    is_responsavel_coteli TINYINT(1) NOT NULL DEFAULT 0,
    ativo_usuario TINYINT(1) NOT NULL DEFAULT 1,
    trocar_senha TINYINT(1) NOT NULL DEFAULT 0,
    criado_em TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    atualizado_em TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Pregões (R-0 e R-X)
CREATE TABLE IF NOT EXISTS base_pregoes (
    id_base_pregoes INT AUTO_INCREMENT PRIMARY KEY,
    id_tipo_pregao INT NOT NULL,
    ano_pregao YEAR NOT NULL,
    id_pregao VARCHAR(20) NOT NULL,
    id_pregao_repeticao TINYINT NOT NULL DEFAULT 0, -- 0 = base (R-0), 1..n = repetições R-X
    data_pregao DATE NULL,
    hora_pregao TIME NULL,
    id_status INT NULL,
    id_origem_pedido INT NULL,
    processo_sei VARCHAR(50) NULL,
    objeto_licitado TEXT NULL,
    data_do DATE NULL,
    id_pregoeiro INT NULL,
    id_responsavel_coteli INT NULL,
    lancado_site_uerj TINYINT(1) NOT NULL DEFAULT 0,
    data_status DATE NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    CONSTRAINT uq_base_pregao UNIQUE (id_tipo_pregao, ano_pregao, id_pregao, id_pregao_repeticao),
    CONSTRAINT fk_base_tipo_pregao FOREIGN KEY (id_tipo_pregao) REFERENCES tipos_pregao (id_tipos_pregao),
    CONSTRAINT fk_base_origem FOREIGN KEY (id_origem_pedido) REFERENCES origens_pedido (id_origens_pedido),
    CONSTRAINT fk_base_pregoeiro FOREIGN KEY (id_pregoeiro) REFERENCES usuarios (id_usuarios),
    CONSTRAINT fk_base_resp_coteli FOREIGN KEY (id_responsavel_coteli) REFERENCES usuarios (id_usuarios),
    CONSTRAINT fk_base_status FOREIGN KEY (id_status) REFERENCES status_pregao (id_status_pregao)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Amostras (referenciam pregoes)
CREATE TABLE IF NOT EXISTS amostras (
    id_amostras INT AUTO_INCREMENT PRIMARY KEY,
    id_base_amostra INT NOT NULL, -- referencia pregão base ou repetição
    id_momento_cadastro INT NOT NULL,
    item_licitado VARCHAR(3) NOT NULL, -- mascara 3 digitos (ex.: 001)
    id_tipos_licitados INT NULL,
    total_unidades DECIMAL(10,2) NULL,
    id_responsavel INT NULL, -- responsavel COTELI (usuario)
    id_empresa INT NULL,
    observacoes TEXT NULL,
    entregue_coteli TINYINT(1) NOT NULL DEFAULT 0,
    chegada_coteli DATETIME NULL,
    saida_coteli DATETIME NULL,
    id_tipos_parecer INT NULL,
    data_parecer DATETIME NULL,
    selecionar_impressao TINYINT(1) NOT NULL DEFAULT 0,
    data_cadastro DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    CONSTRAINT uq_amostra UNIQUE (id_base_amostra, item_licitado, id_tipos_parecer),
    CONSTRAINT fk_amostra_pregao FOREIGN KEY (id_base_amostra) REFERENCES base_pregoes (id_base_pregoes),
    CONSTRAINT fk_amostra_tipo_licitado FOREIGN KEY (id_tipos_licitados) REFERENCES tipos_licitados (id_tipos_licitados),
    CONSTRAINT fk_amostra_responsavel FOREIGN KEY (id_responsavel) REFERENCES usuarios (id_usuarios),
    CONSTRAINT fk_amostra_empresa FOREIGN KEY (id_empresa) REFERENCES empresas (id_empresas),
    CONSTRAINT fk_amostra_parecer FOREIGN KEY (id_tipos_parecer) REFERENCES tipos_parecer (id_tipos_parecer)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Trilhas de auditoria
CREATE TABLE IF NOT EXISTS auditoria (
    id_auditoria BIGINT AUTO_INCREMENT PRIMARY KEY,
    id_usuario INT NULL,
    acao_auditoria VARCHAR(150) NOT NULL,
    entidade_auditoria VARCHAR(100) NOT NULL,
    id_entidade VARCHAR(50) NULL,
    campos_alterados JSON NULL,
    dados_anteriores JSON NULL,
    dados_novos JSON NULL,
    ip_auditoria VARCHAR(45) NULL,
    user_agent VARCHAR(255) NULL,
    criado_em TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    CONSTRAINT fk_auditoria_usuario FOREIGN KEY (id_usuario) REFERENCES usuarios (id_usuarios)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
