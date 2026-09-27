-- --------------------------------------------------------
-- Servidor:                     127.0.0.1
-- Versão do servidor:           8.0.30 - MySQL Community Server - GPL
-- OS do Servidor:               Win64
-- HeidiSQL Versão:              12.1.0.6537
-- --------------------------------------------------------

/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET NAMES utf8 */;
/*!50503 SET NAMES utf8mb4 */;
/*!40103 SET @OLD_TIME_ZONE=@@TIME_ZONE */;
/*!40103 SET TIME_ZONE='+00:00' */;
/*!40014 SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0 */;
/*!40101 SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='NO_AUTO_VALUE_ON_ZERO' */;
/*!40111 SET @OLD_SQL_NOTES=@@SQL_NOTES, SQL_NOTES=0 */;

-- Criando banco de dados se não existir
CREATE DATABASE IF NOT EXISTS `clinica_medica` /*!40100 DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci */ /*!80016 DEFAULT ENCRYPTION='N' */;
USE `clinica_medica`;


CREATE TABLE IF NOT EXISTS `convenio` (
    `id` INT UNSIGNED NOT NULL AUTO_INCREMENT,
    `nome` VARCHAR(100) NOT NULL,
    `cnpj` CHAR(18) DEFAULT NULL,
    `telefone` VARCHAR(20) DEFAULT NULL,
    `ativo` TINYINT(1) NOT NULL DEFAULT 1,
    `data_registro` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    PRIMARY KEY (`id`),
    UNIQUE KEY `uk_convenio_nome` (`nome`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;


CREATE TABLE IF NOT EXISTS `paciente` (
    `id` INT UNSIGNED NOT NULL AUTO_INCREMENT,
    `id_convenio` INT UNSIGNED DEFAULT NULL,
    `nome` VARCHAR(50) NOT NULL,
    `cpf` CHAR(14) NOT NULL,
    `rg` VARCHAR(20) NOT NULL,
    `data_nascimento` DATE NOT NULL,
    `telefone` VARCHAR(20) NOT NULL,
    `cep` CHAR(9) NOT NULL,
    `estado` VARCHAR(50) NOT NULL,
    `cidade` VARCHAR(50) NOT NULL,
    `bairro` VARCHAR(50) NOT NULL,
    `rua` VARCHAR(200) NOT NULL,
    `numero` VARCHAR(15) NOT NULL,
    `numero_convenio` VARCHAR(30) DEFAULT NULL,
    `condicoes_especiais` TEXT,
    `status` ENUM('ATIVO', 'INATIVO') NOT NULL DEFAULT 'ATIVO',
    `data_registro` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    PRIMARY KEY (`id`),
    UNIQUE KEY `cpf` (`cpf`),
    UNIQUE KEY `rg` (`rg`),
    KEY `fk_paciente_convenio` (`id_convenio`),
    CONSTRAINT `fk_paciente_convenio` FOREIGN KEY (`id_convenio`) REFERENCES `convenio` (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;


CREATE TABLE IF NOT EXISTS `funcionario` (
    `id` INT UNSIGNED NOT NULL AUTO_INCREMENT,
    `nome` VARCHAR(50) NOT NULL,
    `cpf` CHAR(14) NOT NULL,
    `rg` VARCHAR(20) NOT NULL,
    `data_nascimento` DATE NOT NULL,
    `telefone` VARCHAR(20) NOT NULL,
    `cep` CHAR(9) NOT NULL,
    `estado` VARCHAR(50) NOT NULL,
    `cidade` VARCHAR(50) NOT NULL,
    `bairro` VARCHAR(50) NOT NULL,
    `rua` VARCHAR(200) NOT NULL,
    `numero` VARCHAR(15) NOT NULL,
    `salario` DECIMAL(10,2) NOT NULL,
    `cargo` VARCHAR(50) NOT NULL,
    `status` ENUM('ATIVO', 'INATIVO') NOT NULL DEFAULT 'ATIVO',
    `data_registro` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    PRIMARY KEY (`id`),
    UNIQUE KEY `cpf` (`cpf`),
    UNIQUE KEY `rg` (`rg`)
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

CREATE TABLE IF NOT EXISTS `medico` (
    `id` INT UNSIGNED NOT NULL AUTO_INCREMENT,
    `nome` VARCHAR(50) NOT NULL,
    `crm` VARCHAR(20) NOT NULL,
    `especialidade` VARCHAR(50) DEFAULT NULL COMMENT 'Null: Médico Generalista',
    `cpf` CHAR(14) NOT NULL,
    `rg` VARCHAR(20) NOT NULL,
    `data_nascimento` DATE NOT NULL,
    `telefone` VARCHAR(20) NOT NULL,
    `cep` CHAR(9) NOT NULL,
    `estado` VARCHAR(50) NOT NULL,
    `cidade` VARCHAR(50) NOT NULL,
    `bairro` VARCHAR(50) NOT NULL,
    `rua` VARCHAR(200) NOT NULL,
    `numero` VARCHAR(15) NOT NULL,
    `salario` DECIMAL(10,2) NOT NULL,
    `status` ENUM('ATIVO', 'INATIVO') NOT NULL DEFAULT 'ATIVO',
    `data_registro` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    PRIMARY KEY (`id`),
    UNIQUE KEY `CRMS` (`crm`),
    UNIQUE KEY `cpf` (`cpf`),
    UNIQUE KEY `rg` (`rg`)
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

CREATE TABLE IF NOT EXISTS `usuario` (
    `id` INT UNSIGNED NOT NULL AUTO_INCREMENT,
    `id_funcionario` INT UNSIGNED DEFAULT NULL,
    `id_medico` INT UNSIGNED DEFAULT NULL,
    `id_paciente` INT UNSIGNED DEFAULT NULL,
    `login` VARCHAR(50) NOT NULL,
    `senha_hash` VARCHAR(255) NOT NULL,
    `perfil` ENUM('Paciente', 'Recepcionista', 'Administrador', 'Financeiro', 'Medico') NOT NULL,
    `ativo` TINYINT(1) NOT NULL DEFAULT 1,
    `data_registro` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    PRIMARY KEY (`id`),
    UNIQUE KEY `uk_usuario_login` (`login`),
    KEY `fk_usuario_funcionario` (`id_funcionario`),
    KEY `fk_usuario_medico` (`id_medico`),
    KEY `fk_usuario_paciente` (`id_paciente`),
    CONSTRAINT `fk_usuario_funcionario` FOREIGN KEY (`id_funcionario`) REFERENCES `funcionario` (`id`),
    CONSTRAINT `fk_usuario_medico` FOREIGN KEY (`id_medico`) REFERENCES `medico` (`id`),
    CONSTRAINT `fk_usuario_paciente` FOREIGN KEY (`id_paciente`) REFERENCES `paciente` (`id`),
   CONSTRAINT `chk_usuario_vinculo_unico` CHECK (
        (`id_funcionario` IS NOT NULL AND `id_medico` IS NULL AND `id_paciente` IS NULL) OR
        (`id_funcionario` IS NULL AND `id_medico` IS NOT NULL AND `id_paciente` IS NULL) OR
        (`id_funcionario` IS NULL AND `id_medico` IS NULL AND `id_paciente` IS NOT NULL) OR
        (`id_funcionario` IS NULL AND `id_medico` IS NULL AND `id_paciente` IS NULL)
    )
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;


CREATE TABLE IF NOT EXISTS `medicamento` (
    `id` INT UNSIGNED NOT NULL AUTO_INCREMENT,
    `nome` VARCHAR(100) NOT NULL,
    `forma` VARCHAR(50) NOT NULL,
    `ativo` TINYINT(1) NOT NULL DEFAULT 1,
    `data_registro` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;


CREATE TABLE IF NOT EXISTS `tipo_exame` (
    `id` INT UNSIGNED NOT NULL AUTO_INCREMENT,
    `nome` VARCHAR(100) NOT NULL,
    `ativo` TINYINT(1) NOT NULL DEFAULT 1,
    `data_registro` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    PRIMARY KEY (`id`),
    UNIQUE KEY `nome` (`nome`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;


CREATE TABLE IF NOT EXISTS `procedimento` (
    `id` INT UNSIGNED NOT NULL AUTO_INCREMENT,
    `nome` VARCHAR(255) NOT NULL,
    `descricao` TEXT NOT NULL,
    `valor` DECIMAL(10,2) NOT NULL,
    `duracao` TIME DEFAULT NULL COMMENT 'Duração estimada do procedimento',
    `ativo` TINYINT(1) NOT NULL DEFAULT 1,
    `data_registro` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;


CREATE TABLE IF NOT EXISTS `consulta` (
    `id` INT UNSIGNED NOT NULL AUTO_INCREMENT,
    `id_medico` INT UNSIGNED NOT NULL,
    `id_paciente` INT UNSIGNED NOT NULL,
    `valor` DECIMAL(10,2) DEFAULT NULL,
    `pagamento_status` ENUM('PENDENTE', 'PAGO', 'PARCIAL', 'CANCELADO') NOT NULL DEFAULT 'PENDENTE',
    `pagamento_forma` ENUM('DINHEIRO', 'PIX', 'CARTAO', 'CONVENIO') DEFAULT NULL,
    `data_consulta` DATE NOT NULL,
    `horario` TIME NOT NULL,
    `prioridade` ENUM('Urgente', 'Agendada', 'Preferencial', 'Espontânea') NOT NULL,
    `diagnostico` TEXT,
    `status` ENUM('Agendada', 'Realizada', 'Cancelada') NOT NULL,
    `observacoes` TEXT,
    `data_registro` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    PRIMARY KEY (`id`),
    UNIQUE KEY `Consulta_Medico` (`id_medico`,`data_consulta`,`horario`),
    KEY `FK_consulta_paciente` (`id_paciente`),
    CONSTRAINT `FK_consulta_medico` FOREIGN KEY (`id_medico`) REFERENCES `medico` (`id`),
    CONSTRAINT `FK_consulta_paciente` FOREIGN KEY (`id_paciente`) REFERENCES `paciente` (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;


CREATE TABLE IF NOT EXISTS `agendamento_procedimento` (
    `id` INT UNSIGNED NOT NULL AUTO_INCREMENT,
    `id_paciente` INT UNSIGNED NOT NULL,
    `id_procedimento` INT UNSIGNED NOT NULL,
    `id_medico` INT UNSIGNED DEFAULT NULL COMMENT 'Profissional responsável opcional',
    `data_procedimento` DATE NOT NULL,
    `horario` TIME NOT NULL,
    `valor` DECIMAL(10,2) NOT NULL,
    `status` ENUM('AGENDADO', 'REALIZADO', 'CANCELADO') NOT NULL,
    `observacoes` TEXT,
    `data_registro` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    PRIMARY KEY (`id`),
    KEY `fk_ap_paciente` (`id_paciente`),
    KEY `fk_ap_procedimento` (`id_procedimento`),
    KEY `fk_ap_medico` (`id_medico`),
    CONSTRAINT `fk_ap_paciente` FOREIGN KEY (`id_paciente`) REFERENCES `paciente` (`id`),
    CONSTRAINT `fk_ap_procedimento` FOREIGN KEY (`id_procedimento`) REFERENCES `procedimento` (`id`),
    CONSTRAINT `fk_ap_medico` FOREIGN KEY (`id_medico`) REFERENCES `medico` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;


CREATE TABLE IF NOT EXISTS `exame` (
    `id` INT UNSIGNED NOT NULL AUTO_INCREMENT,
    `id_consulta` INT UNSIGNED NOT NULL,
    `id_tipo` INT UNSIGNED NOT NULL,
    `data_realizacao` DATE NOT NULL,
    `hora_realizacao` TIME NOT NULL,
    `resultado` TEXT,
    `status` ENUM('Agendada', 'Realizada', 'Cancelada') NOT NULL,
    `observacoes` TEXT,
    `data_registro` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    PRIMARY KEY (`id`),
    KEY `FK_exame_consulta` (`id_consulta`),
    KEY `FK_exame_tipoexame` (`id_tipo`),
    CONSTRAINT `FK_exame_consulta` FOREIGN KEY (`id_consulta`) REFERENCES `consulta` (`id`),
    CONSTRAINT `FK_exame_tipoexame` FOREIGN KEY (`id_tipo`) REFERENCES `tipo_exame` (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;


CREATE TABLE IF NOT EXISTS `receita` (
    `id` INT UNSIGNED NOT NULL AUTO_INCREMENT,
    `id_consulta` INT UNSIGNED NOT NULL,
    `data_receita` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    PRIMARY KEY (`id`),
    KEY `FK_receita_consulta` (`id_consulta`),
    CONSTRAINT `FK_receita_consulta` FOREIGN KEY (`id_consulta`) REFERENCES `consulta` (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;


CREATE TABLE IF NOT EXISTS `receita_medicamento` (
    `id` INT UNSIGNED NOT NULL AUTO_INCREMENT,
    `id_receita` INT UNSIGNED NOT NULL,
    `id_medicamento` INT UNSIGNED NOT NULL,
    `dosagem` VARCHAR(50) NOT NULL,
    `posologia` VARCHAR(150) NOT NULL,
    `quantidade` INT NOT NULL COMMENT 'Ex.: 20',
    `unidade` VARCHAR(50) NOT NULL COMMENT 'Ex.: comprimidos, mL',
    `data_registro` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    PRIMARY KEY (`id`),
    KEY `FK_rm_receita` (`id_receita`),
    KEY `FK_rm_medicamento` (`id_medicamento`),
    CONSTRAINT `FK_rm_medicamento` FOREIGN KEY (`id_medicamento`) REFERENCES `medicamento` (`id`),
    CONSTRAINT `FK_rm_receita` FOREIGN KEY (`id_receita`) REFERENCES `receita` (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

/*!40103 SET TIME_ZONE=IFNULL(@OLD_TIME_ZONE, 'system') */;
/*!40101 SET SQL_MODE=IFNULL(@OLD_SQL_MODE, '') */;
/*!40014 SET FOREIGN_KEY_CHECKS=IFNULL(@OLD_FOREIGN_KEY_CHECKS, 1) */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40111 SET SQL_NOTES=IFNULL(@OLD_SQL_NOTES, 1) */;
