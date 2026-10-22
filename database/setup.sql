-- Dados
INSERT INTO usuario (login, senha_hash, perfil, ativo) VALUES
('admin', '$2y$10$agXhNUeV6U.rz4Vtg8CDvuNeDUlGqcirLzpm8cIGNZ.ChbXnYEHsG', 'Administrador', 1);

INSERT INTO convenio (nome, cnpj, telefone, ativo) VALUES
('Unimed', '12.345.678/0001-90', '(11) 3333-4444', 1),
('Bradesco Saúde', '98.765.432/0001-10', '(11) 3333-5555', 1);

INSERT INTO medicamento (nome, forma, ativo) VALUES
('Dipirona', 'Comprimido', 1),
('Amoxicilina', 'Cápsula', 1),
('Loratadina', 'Xarope', 1),
('Ibuprofeno', 'Comprimido', 1),
('Omeprazol', 'Cápsula', 1);

INSERT INTO tipo_exame (nome, ativo) VALUES
('Hemograma Completo', 1),
('Raio-X de Tórax', 1),
('Ultrassonografia Abdominal', 1),
('Eletrocardiograma (ECG)', 1);

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
(1, 'mariana.costa', '$2y$10$agXhNUeV6U.rz4Vtg8CDvuNeDUlGqcirLzpm8cIGNZ.ChbXnYEHsG', 'Recepcionista', 1),
(2, 'carlos.eduardo', '$2y$10$agXhNUeV6U.rz4Vtg8CDvuNeDUlGqcirLzpm8cIGNZ.ChbXnYEHsG', 'Financeiro', 1);

INSERT INTO procedimento (nome, descricao, valor, duracao, ativo) VALUES
('Limpeza de pele', 'Procedimento estético para remoção de impurezas', 150.00, '00:30:00', 1),
('Aplicação de Botox', 'Aplicação de toxina botulínica para rugas', 800.00, '00:30:00', 1),
('Drenagem Linfática', 'Massagem para redução de inchaço', 120.00, '01:00:00', 1),
('Curativo Simples', 'Limpeza e troca de curativo de feridas', 50.00, '00:15:00', 1),
('Microagulhamento', 'Estímulo de colágeno com agulhas', 350.00, '01:00:00', 1),
('Peeling de Diamante', 'Esfoliação profunda da pele', 200.00, '00:45:00', 1);

INSERT INTO consulta (id_medico, id_paciente, data_consulta, horario, prioridade, status, valor, pagamento_status) VALUES
(1, 1, CURDATE(), '09:00:00', 'Agendada', 'Agendada', 200.00, 'PENDENTE'),
(2, 3, CURDATE(), '10:30:00', 'Urgente', 'Agendada', 250.00, 'PENDENTE'),
(1, 2, DATE_ADD(CURDATE(), INTERVAL 2 DAY), '14:00:00', 'Agendada', 'Agendada', 200.00, 'PENDENTE'),
(3, 4, DATE_ADD(CURDATE(), INTERVAL 3 DAY), '15:30:00', 'Preferencial', 'Agendada', 220.00, 'PENDENTE'),
(1, 5, DATE_SUB(CURDATE(), INTERVAL 1 DAY), '10:00:00', 'Agendada', 'Realizada', 200.00, 'PAGO'),
(2, 6, DATE_SUB(CURDATE(), INTERVAL 2 DAY), '11:00:00', 'Agendada', 'Realizada', 250.00, 'PAGO'),
(3, 7, DATE_SUB(CURDATE(), INTERVAL 3 DAY), '14:30:00', 'Agendada', 'Realizada', 220.00, 'CONVENIO'),
(1, 9, DATE_ADD(CURDATE(), INTERVAL 1 DAY), '08:30:00', 'Preferencial', 'Agendada', 200.00, 'PENDENTE');

INSERT INTO exame (id_consulta, id_tipo, data_realizacao, hora_realizacao, resultado, status, observacoes) VALUES
(5, 1, DATE_SUB(CURDATE(), INTERVAL 1 DAY), '10:30:00', 'Hemograma dentro dos padrões de normalidade.', 'Realizada', 'Paciente em jejum.'),
(6, 2, DATE_SUB(CURDATE(), INTERVAL 2 DAY), '11:30:00', 'Pulmões limpos, sem sinais de infecção.', 'Realizada', NULL),
(1, 3, DATE_ADD(CURDATE(), INTERVAL 2 DAY), '15:00:00', NULL, 'Agendada', 'Preparo: 6 horas de jejum.'),
(2, 4, DATE_ADD(CURDATE(), INTERVAL 1 DAY), '09:30:00', NULL, 'Agendada', NULL);

INSERT INTO receita (id_consulta) VALUES
(5),
(6),
(7);

INSERT INTO receita_medicamento (id_receita, id_medicamento, dosagem, posologia, quantidade, unidade) VALUES
(1, 1, '500mg', 'Tomar 1 comprimido a cada 6 horas em caso de dor.', 20, 'comprimidos'),
(1, 5, '20mg', 'Tomar 1 cápsula em jejum, 30 minutos antes do café.', 30, 'cápsulas'),
(2, 2, '875mg', 'Tomar 1 cápsula a cada 12 horas por 7 dias.', 14, 'cápsulas'),
(2, 4, '600mg', 'Tomar 1 comprimido a cada 8 horas em caso de febre.', 15, 'comprimidos'),
(3, 3, '10mg', 'Tomar 10ml uma vez ao dia à noite.', 1, 'frasco');

INSERT INTO agendamento_procedimento (id_paciente, id_procedimento, id_medico, data_procedimento, horario, valor, status, observacoes) VALUES
(2, 1, 2, DATE_ADD(CURDATE(), INTERVAL 1 DAY), '09:00:00', 150.00, 'AGENDADO', 'Limpeza de pele profunda.'),
(5, 2, 2, DATE_ADD(CURDATE(), INTERVAL 3 DAY), '14:00:00', 800.00, 'AGENDADO', 'Aplicação de toxina botulínica na testa.'),
(1, 3, 1, DATE_SUB(CURDATE(), INTERVAL 5 DAY), '10:00:00', 120.00, 'REALIZADO', 'Paciente relatou melhora no inchaço.'),
(3, 4, 3, DATE_SUB(CURDATE(), INTERVAL 10 DAY), '15:30:00', 50.00, 'REALIZADO', 'Curativo pós-operatório.'),
(8, 5, 2, DATE_ADD(CURDATE(), INTERVAL 5 DAY), '11:00:00', 350.00, 'AGENDADO', 'Sessão de microagulhamento facial.');