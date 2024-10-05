-- Cria o banco de dados se não existir
CREATE DATABASE IF NOT EXISTS financas_facil;
USE financas_facil;

-- Cria a tabela 'usuarios' se não existir
CREATE TABLE IF NOT EXISTS usuarios (
    id INT(11) AUTO_INCREMENT PRIMARY KEY,
    nome VARCHAR(255) NOT NULL,
    telefone VARCHAR(20),
    cpf VARCHAR(20),
    email VARCHAR(255) UNIQUE,
    imagem_perfil LONGBLOB,
    imagem_perfil_tipo VARCHAR(50) -- Armazena o tipo MIME da imagem
);

-- Cria a tabela 'categories' se não existir
CREATE TABLE IF NOT EXISTS categories (
    id INT(11) AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(255) NOT NULL UNIQUE
);

-- Cria a tabela 'accounts' se não existir
CREATE TABLE IF NOT EXISTS accounts (
    id INT(11) AUTO_INCREMENT PRIMARY KEY,
    user_id INT(11),
    name VARCHAR(255) NOT NULL,
    balance DECIMAL(15, 2) DEFAULT 0.00,
    FOREIGN KEY (user_id) REFERENCES usuarios(id) ON DELETE CASCADE
);

-- Cria a tabela 'finance_calculations' se não existir
CREATE TABLE IF NOT EXISTS finance_calculations (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    salario DECIMAL(10, 2) NOT NULL,
    despesas DECIMAL(10, 2) NOT NULL,
    outras_despesas DECIMAL(10, 2) NOT NULL,
    percapita INT NOT NULL,
    data DATE NOT NULL,
    FOREIGN KEY (user_id) REFERENCES usuarios(id) ON DELETE CASCADE
);

-- Cria a tabela 'transactions' se não existir
CREATE TABLE IF NOT EXISTS transactions (
    id INT(11) AUTO_INCREMENT PRIMARY KEY,
    account_id INT(11),
    category_id INT(11),
    amount DECIMAL(15, 2) NOT NULL,
    transaction_date DATE NOT NULL,
    description TEXT,
    FOREIGN KEY (account_id) REFERENCES accounts(id) ON DELETE CASCADE,
    FOREIGN KEY (category_id) REFERENCES categories(id) ON DELETE CASCADE
);
DESCRIBE usuarios;
ALTER TABLE usuarios ADD imagem_perfil_tipo VARCHAR(255);
