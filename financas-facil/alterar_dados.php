<?php
// Conectar ao banco de dados
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "financas_facil";

$conn = new mysqli($servername, $username, $password, $dbname);

// Verificar conexão
if ($conn->connect_error) {
    die("Conexão falhou: " . $conn->connect_error);
}

// Verificar se o formulário foi enviado
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Capturar dados do formulário
    $name = isset($_POST['name']) ? $_POST['name'] : '';
    $email = isset($_POST['username']) ? $_POST['username'] : '';
    $cpf = isset($_POST['cpf']) ? $_POST['cpf'] : '';
    $phone = isset($_POST['phone']) ? $_POST['phone'] : '';
    $password = isset($_POST['password']) ? password_hash($_POST['password'], PASSWORD_DEFAULT) : '';
    $confirmPassword = isset($_POST['confirm-password']) ? $_POST['confirm-password'] : '';

    // Verificar se todos os campos obrigatórios estão preenchidos
    if (empty($name) || empty($email) || empty($cpf) || empty($phone) || empty($password) || empty($confirmPassword)) {
        die("Por favor, preencha todos os campos.");
    }

    // Verificar se as senhas coincidem
    if (!password_verify($confirmPassword, $password)) {
        die("As senhas não coincidem.");
    }

    // Iniciar a sessão para obter o ID do usuário
    session_start();
    $userId = $_SESSION['user_id'];

    // Preparar a atualização dos dados no banco de dados
    $sql = $conn->prepare("UPDATE usuarios SET nome=?, email=?, cpf=?, telefone=?, senha=? WHERE id=?");
    $sql->bind_param("sssssi", $name, $email, $cpf, $phone, $password, $userId);

    if ($sql->execute()) {
        echo "Dados alterados com sucesso.";
        header("Location: perfil.html"); // Redirecionar para a página de perfil
    } else {
        echo "Erro ao alterar os dados: " . $sql->error;
    }

    $sql->close();
}

$conn->close();
?>
