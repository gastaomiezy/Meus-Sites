-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Tempo de geração: 11-Jul-2025 às 10:00
-- Versão do servidor: 10.4.28-MariaDB
-- Versão do PHP: 8.2.4

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Banco de dados: `gestaoadulto_aea`
--

-- --------------------------------------------------------

--
-- Estrutura da tabela `utilizadores`
--

DROP TABLE IF EXISTS `utilizadores`;
CREATE TABLE `utilizadores` (
  `id` INT(11) NOT NULL AUTO_INCREMENT,
  `nome` VARCHAR(255) NOT NULL,
  `papel` ENUM('Administrador', 'Utilizador') NOT NULL, -- Ex: 'Administrador', 'Utilizador'
  `senha_hash` VARCHAR(255) NOT NULL,
  `email` VARCHAR(255) NOT NULL,
  `telefone` VARCHAR(50) DEFAULT NULL,
  `status` ENUM('Pendente', 'Aprovado', 'Rejeitado') NOT NULL DEFAULT 'Pendente',
  PRIMARY KEY (`id`),
  UNIQUE KEY `email` (`email`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Estrutura da tabela `cursos_escotistas`
--

DROP TABLE IF EXISTS `cursos_escotistas`;
CREATE TABLE `cursos_escotistas` (
  `id` INT(11) NOT NULL AUTO_INCREMENT,
  `nome_curso` VARCHAR(255) NOT NULL,
  `descricao` TEXT DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `nome_curso` (`nome_curso`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Estrutura da tabela `regioes`
--

DROP TABLE IF EXISTS `regioes`;
CREATE TABLE `regioes` (
  `id` INT(11) NOT NULL AUTO_INCREMENT,
  `nome_regiao` VARCHAR(255) NOT NULL UNIQUE,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Estrutura da tabela `adultos`
--

DROP TABLE IF EXISTS `adultos`;
CREATE TABLE `adultos` (
  `id` INT(11) NOT NULL AUTO_INCREMENT,
  `nome` VARCHAR(255) NOT NULL,
  `numero_identificacao` VARCHAR(50) UNIQUE,
  `cargo_funcao` VARCHAR(255) DEFAULT NULL,
  `data_nascimento` DATE NOT NULL,
  `formacao_academica` VARCHAR(255) DEFAULT NULL,
  `profissao` VARCHAR(255) DEFAULT NULL,
  `regiao` VARCHAR(255) NOT NULL,
  `nucleo` VARCHAR(255) DEFAULT NULL,
  `agrupamento` VARCHAR(255) DEFAULT NULL,
  `estrutura_pertence` VARCHAR(255) DEFAULT NULL,
  `credo` ENUM('CATÓLICO', 'KIMBANGUISTA', 'IECA', 'UEBA', 'METODISTA') NOT NULL,
  `grupo_sanguineo` VARCHAR(5) DEFAULT NULL,
  `telefone` VARCHAR(50) NOT NULL,
  `email` VARCHAR(255) NOT NULL,
  `morada_actual` TEXT DEFAULT NULL,
  `criado_por_id` INT(11) DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `email` (`email`),
  FOREIGN KEY (`criado_por_id`) REFERENCES `utilizadores`(`id`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Estrutura da tabela `servicos_centrais`
--

DROP TABLE IF EXISTS `servicos_centrais`;
CREATE TABLE `servicos_centrais` (
  `id` INT(11) NOT NULL AUTO_INCREMENT,
  `nome` VARCHAR(255) NOT NULL,
  `orgao` VARCHAR(255) DEFAULT NULL,
  `cargo_funcao` VARCHAR(255) DEFAULT NULL,
  `curso_id` INT(11) DEFAULT NULL,
  `regiao_id` INT(11) DEFAULT NULL,
  `numero_identificacao` VARCHAR(50) UNIQUE,
  PRIMARY KEY (`id`),
  FOREIGN KEY (`curso_id`) REFERENCES `cursos_escotistas`(`id`) ON DELETE SET NULL,
  FOREIGN KEY (`regiao_id`) REFERENCES `regioes`(`id`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Estrutura da tabela `solicitacoes_curso`
--

DROP TABLE IF EXISTS `solicitacoes_curso`;
CREATE TABLE `solicitacoes_curso` (
  `id` INT(11) NOT NULL AUTO_INCREMENT,
  `adulto_id` INT(11) NOT NULL,
  `curso_id` INT(11) NOT NULL,
  `numero_inscritos` INT(11) DEFAULT 0,
  `homens_inscritos` INT(11) DEFAULT 0,
  `mulheres_inscritas` INT(11) DEFAULT 0,
  `data_solicitacao` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `remetente_id` INT(11) NOT NULL,
  `regiao` VARCHAR(255) NOT NULL, -- Nova coluna para a região da solicitação
  `documentos_paths` TEXT DEFAULT NULL, -- JSON ou lista de caminhos separados por vírgula
  PRIMARY KEY (`id`),
  FOREIGN KEY (`adulto_id`) REFERENCES `adultos`(`id`) ON DELETE CASCADE,
  FOREIGN KEY (`curso_id`) REFERENCES `cursos_escotistas`(`id`) ON DELETE CASCADE,
  FOREIGN KEY (`remetente_id`) REFERENCES `utilizadores`(`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Estrutura da tabela `respostas_curso`
--

DROP TABLE IF EXISTS `respostas_curso`;
CREATE TABLE `respostas_curso` (
  `id` INT(11) NOT NULL AUTO_INCREMENT,
  `solicitacao_id` INT(11) NOT NULL,
  `ordem_servico_path` VARCHAR(255) DEFAULT NULL,
  `data_curso` DATE NOT NULL,
  `data_resposta` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `remetente_id` INT(11) NOT NULL,
  PRIMARY KEY (`id`),
  FOREIGN KEY (`solicitacao_id`) REFERENCES `solicitacoes_curso`(`id`) ON DELETE CASCADE,
  FOREIGN KEY (`remetente_id`) REFERENCES `utilizadores`(`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Estrutura da tabela `chat_mensagens`
--

DROP TABLE IF EXISTS `chat_mensagens`;
CREATE TABLE `chat_mensagens` (
  `id` INT(11) NOT NULL AUTO_INCREMENT,
  `remetente_id` INT(11) NOT NULL,
  `destinatario_id` INT(11) NOT NULL,
  `mensagem` TEXT NOT NULL,
  `data_envio` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  FOREIGN KEY (`remetente_id`) REFERENCES `utilizadores`(`id`) ON DELETE CASCADE,
  FOREIGN KEY (`destinatario_id`) REFERENCES `utilizadores`(`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;