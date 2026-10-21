-- =====================================================================
-- BANCO DE DADOS - CLÍNICA NAKAMURA
-- 
-- rode este arquivo no HeidiSQL (Arquivo -> Carregar SQL -> F9)
-- Login padrão: 
--
-- User: admin
-- Senha: 123456
-- =====================================================================
DROP DATABASE IF EXISTS clinica_medica;
CREATE DATABASE clinica_medica CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci;
USE clinica_medica;

-- Tabelas
CREATE TABLE convenio (
    id INT UNSIGNED NOT NULL AUTO_INCREMENT,
    nome VARCHAR(100) NOT NULL,
    cnpj CHAR(18) DEFAULT NULL,
    telefone VARCHAR(20) DEFAULT NULL,
    ativo TINYINT(1) NOT NULL DEFAULT 1,
    data_registro TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    PRIMARY KEY (id),
    UNIQUE KEY uk_convenio_nome (nome)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

CREATE TABLE paciente (
    id INT UNSIGNED NOT NULL AUTO_INCREMENT,
    id_convenio INT UNSIGNED DEFAULT NULL,
    nome VARCHAR(50) NOT NULL,
    email VARCHAR(100) NOT NULL,
    cpf CHAR(14) NOT NULL,
    rg VARCHAR(20) NOT NULL,
    data_nascimento DATE NOT NULL,
    telefone VARCHAR(20) NOT NULL,
    cep CHAR(9) NOT NULL,
    estado VARCHAR(50) NOT NULL,
    cidade VARCHAR(50) NOT NULL,
    bairro VARCHAR(50) NOT NULL,
    rua VARCHAR(200) NOT NULL,
    numero VARCHAR(15) NOT NULL,
    numero_convenio VARCHAR(30) DEFAULT NULL,
    condicoes_especiais TEXT,
    status ENUM('ATIVO', 'INATIVO') NOT NULL DEFAULT 'ATIVO',
    data_registro TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    PRIMARY KEY (id),
    UNIQUE KEY cpf (cpf),
    UNIQUE KEY rg (rg),
    KEY fk_paciente_convenio (id_convenio),
    CONSTRAINT fk_paciente_convenio FOREIGN KEY (id_convenio) REFERENCES convenio (id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

CREATE TABLE funcionario (
    id INT UNSIGNED NOT NULL AUTO_INCREMENT,
    nome VARCHAR(50) NOT NULL,
    email VARCHAR(100) NOT NULL,
    cpf CHAR(14) NOT NULL,
    rg VARCHAR(20) NOT NULL,
    data_nascimento DATE NOT NULL,
    telefone VARCHAR(20) NOT NULL,
    cep CHAR(9) NOT NULL,
    estado VARCHAR(50) NOT NULL,
    cidade VARCHAR(50) NOT NULL,
    bairro VARCHAR(50) NOT NULL,
    rua VARCHAR(200) NOT NULL,
    numero VARCHAR(15) NOT NULL,
    salario DECIMAL(10,2) NOT NULL,
    cargo VARCHAR(50) NOT NULL,
    status ENUM('ATIVO', 'INATIVO') NOT NULL DEFAULT 'ATIVO',
    data_registro TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    PRIMARY KEY (id),
    UNIQUE KEY cpf (cpf),
    UNIQUE KEY rg (rg)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

CREATE TABLE medico (
    id INT UNSIGNED NOT NULL AUTO_INCREMENT,
    nome VARCHAR(50) NOT NULL,
    email VARCHAR(100) NOT NULL,
    crm VARCHAR(20) NOT NULL,
    especialidade VARCHAR(50) DEFAULT NULL,
    cpf CHAR(14) NOT NULL,
    rg VARCHAR(20) NOT NULL,
    data_nascimento DATE NOT NULL,
    telefone VARCHAR(20) NOT NULL,
    cep CHAR(9) NOT NULL,
    estado VARCHAR(50) NOT NULL,
    cidade VARCHAR(50) NOT NULL,
    bairro VARCHAR(50) NOT NULL,
    rua VARCHAR(200) NOT NULL,
    numero VARCHAR(15) NOT NULL,
    salario DECIMAL(10,2) NOT NULL,
    status ENUM('ATIVO', 'INATIVO') NOT NULL DEFAULT 'ATIVO',
    data_registro TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    PRIMARY KEY (id),
    UNIQUE KEY CRMS (crm),
    UNIQUE KEY cpf (cpf),
    UNIQUE KEY rg (rg)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

CREATE TABLE usuario (
    id INT UNSIGNED NOT NULL AUTO_INCREMENT,
    id_funcionario INT UNSIGNED DEFAULT NULL,
    id_medico INT UNSIGNED DEFAULT NULL,
    id_paciente INT UNSIGNED DEFAULT NULL,
    login VARCHAR(50) NOT NULL,
    senha_hash VARCHAR(255) NOT NULL,
    perfil ENUM('Paciente', 'Recepcionista', 'Administrador', 'Financeiro', 'Medico') NOT NULL,
    ativo TINYINT(1) NOT NULL DEFAULT 1,
    data_registro TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    PRIMARY KEY (id),
    UNIQUE KEY uk_usuario_login (login),
    KEY fk_usuario_funcionario (id_funcionario),
    KEY fk_usuario_medico (id_medico),
    KEY fk_usuario_paciente (id_paciente),
    CONSTRAINT fk_usuario_funcionario FOREIGN KEY (id_funcionario) REFERENCES funcionario (id),
    CONSTRAINT fk_usuario_medico FOREIGN KEY (id_medico) REFERENCES medico (id),
    CONSTRAINT fk_usuario_paciente FOREIGN KEY (id_paciente) REFERENCES paciente (id),
    CONSTRAINT chk_usuario_vinculo_unico CHECK (
        (id_funcionario IS NOT NULL AND id_medico IS NULL AND id_paciente IS NULL) OR
        (id_funcionario IS NULL AND id_medico IS NOT NULL AND id_paciente IS NULL) OR
        (id_funcionario IS NULL AND id_medico IS NULL AND id_paciente IS NOT NULL) OR
        (id_funcionario IS NULL AND id_medico IS NULL AND id_paciente IS NULL)
    )
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

CREATE TABLE medicamento (
    id INT UNSIGNED NOT NULL AUTO_INCREMENT,
    nome VARCHAR(100) NOT NULL,
    forma VARCHAR(50) NOT NULL,
    ativo TINYINT(1) NOT NULL DEFAULT 1,
    data_registro TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    PRIMARY KEY (id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

CREATE TABLE tipo_exame (
    id INT UNSIGNED NOT NULL AUTO_INCREMENT,
    nome VARCHAR(100) NOT NULL,
    ativo TINYINT(1) NOT NULL DEFAULT 1,
    data_registro TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    PRIMARY KEY (id),
    UNIQUE KEY nome (nome)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

CREATE TABLE procedimento (
    id INT UNSIGNED NOT NULL AUTO_INCREMENT,
    nome VARCHAR(255) NOT NULL,
    descricao TEXT NOT NULL,
    valor DECIMAL(10,2) NOT NULL,
    duracao TIME DEFAULT NULL,
    ativo TINYINT(1) NOT NULL DEFAULT 1,
    data_registro TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    PRIMARY KEY (id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

CREATE TABLE consulta (
    id INT UNSIGNED NOT NULL AUTO_INCREMENT,
    id_medico INT UNSIGNED NOT NULL,
    id_paciente INT UNSIGNED NOT NULL,
    valor DECIMAL(10,2) DEFAULT NULL,
    pagamento_status ENUM('PENDENTE', 'PAGO', 'PARCIAL', 'CANCELADO') NOT NULL DEFAULT 'PENDENTE',
    pagamento_forma ENUM('DINHEIRO', 'PIX', 'CARTAO', 'CONVENIO') DEFAULT NULL,
    data_consulta DATE NOT NULL,
    horario TIME NOT NULL,
    prioridade ENUM('Urgente', 'Agendada', 'Preferencial', 'Espontânea') NOT NULL,
    diagnostico TEXT,
    status ENUM('Agendada', 'Realizada', 'Cancelada') NOT NULL,
    observacoes TEXT,
    data_registro TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    PRIMARY KEY (id),
    UNIQUE KEY Consulta_Medico (id_medico, data_consulta, horario),
    KEY FK_consulta_paciente (id_paciente),
    CONSTRAINT FK_consulta_medico FOREIGN KEY (id_medico) REFERENCES medico (id),
    CONSTRAINT FK_consulta_paciente FOREIGN KEY (id_paciente) REFERENCES paciente (id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

CREATE TABLE agendamento_procedimento (
    id INT UNSIGNED NOT NULL AUTO_INCREMENT,
    id_paciente INT UNSIGNED NOT NULL,
    id_procedimento INT UNSIGNED NOT NULL,
    id_medico INT UNSIGNED DEFAULT NULL,
    data_procedimento DATE NOT NULL,
    horario TIME NOT NULL,
    valor DECIMAL(10,2) NOT NULL,
    status ENUM('AGENDADO', 'REALIZADO', 'CANCELADO') NOT NULL,
    observacoes TEXT,
    data_registro TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    PRIMARY KEY (id),
    KEY fk_ap_paciente (id_paciente),
    KEY fk_ap_procedimento (id_procedimento),
    KEY fk_ap_medico (id_medico),
    CONSTRAINT fk_ap_paciente FOREIGN KEY (id_paciente) REFERENCES paciente (id),
    CONSTRAINT fk_ap_procedimento FOREIGN KEY (id_procedimento) REFERENCES procedimento (id),
    CONSTRAINT fk_ap_medico FOREIGN KEY (id_medico) REFERENCES medico (id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

CREATE TABLE exame (
    id INT UNSIGNED NOT NULL AUTO_INCREMENT,
    id_consulta INT UNSIGNED NOT NULL,
    id_tipo INT UNSIGNED NOT NULL,
    data_realizacao DATE NOT NULL,
    hora_realizacao TIME NOT NULL,
    resultado TEXT,
    status ENUM('Agendada', 'Realizada', 'Cancelada') NOT NULL,
    observacoes TEXT,
    data_registro TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    PRIMARY KEY (id),
    KEY FK_exame_consulta (id_consulta),
    KEY FK_exame_tipoexame (id_tipo),
    CONSTRAINT FK_exame_consulta FOREIGN KEY (id_consulta) REFERENCES consulta (id),
    CONSTRAINT FK_exame_tipoexame FOREIGN KEY (id_tipo) REFERENCES tipo_exame (id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

CREATE TABLE receita (
    id INT UNSIGNED NOT NULL AUTO_INCREMENT,
    id_consulta INT UNSIGNED NOT NULL,
    data_receita TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    PRIMARY KEY (id),
    KEY FK_receita_consulta (id_consulta),
    CONSTRAINT FK_receita_consulta FOREIGN KEY (id_consulta) REFERENCES consulta (id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

CREATE TABLE receita_medicamento (
    id INT UNSIGNED NOT NULL AUTO_INCREMENT,
    id_receita INT UNSIGNED NOT NULL,
    id_medicamento INT UNSIGNED NOT NULL,
    dosagem VARCHAR(50) NOT NULL,
    posologia VARCHAR(150) NOT NULL,
    quantidade INT NOT NULL,
    unidade VARCHAR(50) NOT NULL,
    data_registro TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    PRIMARY KEY (id),
    KEY FK_rm_receita (id_receita),
    KEY FK_rm_medicamento (id_medicamento),
    CONSTRAINT FK_rm_medicamento FOREIGN KEY (id_medicamento) REFERENCES medicamento (id),
    CONSTRAINT FK_rm_receita FOREIGN KEY (id_receita) REFERENCES receita (id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- Dados
INSERT INTO usuario (login, senha_hash, perfil, ativo) VALUES
('admin', '$2y$10$agXhNUeV6U.rz4Vtg8CDvuNeDUlGqcirLzpm8cIGNZ.ChbXnYEHsG', 'Administrador', 1);

INSERT INTO convenio (nome, cnpj, telefone, ativo) VALUES
('Unimed', '12.345.678/0001-90', '(11) 3333-4444', 1),
('Bradesco Saúde', '98.765.432/0001-10', '(11) 3333-5555', 1);

INSERT INTO medicamento (nome, forma, ativo) VALUES
('Medicamento A', 'Comprimido', 1),
('Medicamento B', 'Xarope', 1);

INSERT INTO tipo_exame (nome, ativo) VALUES
('Hemograma Completo', 1),
('Raio-X de Tórax', 1);

INSERT INTO paciente (id_convenio, nome, email, cpf, rg, data_nascimento, telefone, cep, estado, cidade, bairro, rua, numero, numero_convenio, condicoes_especiais, status) VALUES
(1, 'João Silva', 'joao.silva@email.com', '111.111.111-11', '1111111', '1985-03-15', '(11) 91111-1111', '01234-567', 'SP', 'São Paulo', 'Centro', 'Rua A', '100', '0123456789', 'Hipertenso', 'ATIVO'),
(1, 'Maria Santos', 'maria.santos@email.com', '222.222.222-22', '2222222', '1990-07-22', '(11) 92222-2222', '02345-678', 'SP', 'São Paulo', 'Vila Mariana', 'Rua B', '200', '9876543210', NULL, 'ATIVO'),
(2, 'Pedro Oliveira', 'pedro.oliveira@email.com', '333.333.333-33', '3333333', '1978-11-08', '(11) 93333-3333', '03456-789', 'SP', 'São Paulo', 'Ipiranga', 'Rua C', '300', '1122334455', 'Diabético', 'ATIVO'),
(NULL, 'Ana Costa', 'ana.costa@email.com', '444.444.444-44', '4444444', '2000-01-30', '(11) 94444-4444', '04567-890', 'SP', 'São Paulo', 'Butantã', 'Rua D', '400', NULL, NULL, 'ATIVO'),
(1, 'Lucas Ferreira', 'lucas.ferreira@email.com', '555.555.555-55', '5555555', '1992-05-10', '(11) 95555-5555', '05678-901', 'SP', 'São Paulo', 'Pinheiros', 'Rua E', '500', '1234567890', NULL, 'ATIVO'),
(2, 'Juliana Rocha', 'juliana.rocha@email.com', '666.666.666-66', '6666666', '1988-09-20', '(11) 96666-6666', '06789-012', 'SP', 'São Paulo', 'Moema', 'Rua F', '600', '0987654321', 'Asma', 'ATIVO'),
(NULL, 'Roberto Alves', 'roberto.alves@email.com', '777.777.777-77', '7777777', '1970-12-01', '(11) 97777-7777', '07890-123', 'SP', 'São Paulo', 'Tatuapé', 'Rua G', '700', NULL, NULL, 'ATIVO'),
(1, 'Fernanda Lima', 'fernanda.lima@email.com', '888.888.888-88', '8888888', '1995-04-12', '(11) 98888-8888', '08901-234', 'SP', 'São Paulo', 'Vila Madalena', 'Rua H', '800', '1122334455', NULL, 'INATIVO'),
(NULL, 'Thiago Martins', 'thiago.martins@email.com', '999.999.999-99', '9999999', '1982-08-30', '(11) 99999-8888', '09012-345', 'SP', 'São Paulo', 'Saúde', 'Rua I', '900', NULL, 'Hipertenso', 'ATIVO');

INSERT INTO medico (nome, email, crm, especialidade, cpf, rg, data_nascimento, telefone, cep, estado, cidade, bairro, rua, numero, salario, status) VALUES
('Dr. Carlos Silva', 'carlos.silva@clinica.com', 'CRM/SP 123456', 'Cardiologia', '123.456.789-00', '1234567', '1980-05-15', '(11) 91234-5678', '01234-567', 'SP', 'São Paulo', 'Centro', 'Av Paulista', '1000', 15000.00, 'ATIVO'),
('Dra. Ana Beatriz', 'ana.beatriz@clinica.com', 'CRM/SP 654321', 'Pediatria', '123.123.123-12', '1231231', '1985-02-14', '(11) 91234-5678', '01234-567', 'SP', 'São Paulo', 'Centro', 'Av Paulista', '1000', 18000.00, 'ATIVO'),
('Dr. Roberto Gomes', 'roberto.gomes@clinica.com', 'CRM/SP 987654', 'Ortopedia', '321.321.321-32', '3213213', '1975-11-22', '(11) 92345-6789', '02345-678', 'SP', 'São Paulo', 'Jardins', 'Rua Oscar Freire', '200', 22000.00, 'ATIVO');

INSERT INTO funcionario (nome, email, cpf, rg, data_nascimento, telefone, cep, estado, cidade, bairro, rua, numero, salario, cargo, status) VALUES
('Mariana Costa', 'mariana.costa@clinica.com', '222.333.444-55', '2345678', '1993-06-15', '(11) 93456-7890', '03456-789', 'SP', 'São Paulo', 'Vila Mariana', 'Rua Vergueiro', '300', 2800.00, 'Recepcionista', 'ATIVO'),
('Carlos Eduardo', 'carlos.eduardo@clinica.com', '333.444.555-66', '3456789', '1980-10-05', '(11) 94567-8901', '04567-890', 'SP', 'São Paulo', 'Ipiranga', 'Rua Silva Bueno', '400', 4500.00, 'Financeiro', 'ATIVO'),
('Patrícia Souza', 'patricia.souza@clinica.com', '444.555.666-77', '4567890', '1988-03-25', '(11) 95678-9012', '05678-901', 'SP', 'São Paulo', 'Butantã', 'Rua Vital Brasil', '500', 3500.00, 'Enfermeiro(a)', 'ATIVO');

INSERT INTO usuario (id_medico, login, senha_hash, perfil, ativo) VALUES
(1, 'carlos.silva', '$2y$10$agXhNUeV6U.rz4Vtg8CDvuNeDUlGqcirLzpm8cIGNZ.ChbXnYEHsG', 'Medico', 1);

INSERT INTO usuario (id_funcionario, login, senha_hash, perfil, ativo) VALUES
(1, 'mariana.costa', '$2y$10$agXhNUeV6U.rz4Vtg8CDvuNeDUlGqcirLzpm8cIGNZ.ChbXnYEHsG', 'Recepcionista', 1);

INSERT INTO procedimento (nome, descricao, valor, duracao, ativo) VALUES
('Limpeza de pele', 'Procedimento estético para remoção de impurezas', 150.00, '00:30:00', 1),
('Aplicação de Botox', 'Aplicação de toxina botulínica para rugas', 800.00, '00:30:00', 1),
('Drenagem Linfática', 'Massagem para redução de inchaço', 120.00, '01:00:00', 1),
('Curativo Simples', 'Limpeza e troca de curativo de feridas', 50.00, '00:15:00', 1);

INSERT INTO consulta (id_medico, id_paciente, data_consulta, horario, prioridade, status, valor, pagamento_status) VALUES
(1, 1, CURDATE(), '09:00:00', 'Agendada', 'Agendada', 200.00, 'PENDENTE'),
(2, 3, CURDATE(), '10:30:00', 'Urgente', 'Agendada', 250.00, 'PENDENTE'),
(1, 2, DATE_ADD(CURDATE(), INTERVAL 2 DAY), '14:00:00', 'Agendada', 'Agendada', 200.00, 'PENDENTE'),
(3, 4, DATE_ADD(CURDATE(), INTERVAL 3 DAY), '15:30:00', 'Preferencial', 'Agendada', 220.00, 'PENDENTE'),
(1, 5, DATE_SUB(CURDATE(), INTERVAL 1 DAY), '10:00:00', 'Agendada', 'Realizada', 200.00, 'PAGO');