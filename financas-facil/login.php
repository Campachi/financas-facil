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

// Capturar dados do formulário
$email = $_POST['username'];
$senha = $_POST['password'];

// Buscar usuário no banco de dados
$sql = "SELECT * FROM usuarios WHERE email='$email'";
$result = $conn->query($sql);

if ($result->num_rows > 0) {
    $user = $result->fetch_assoc();
    
    // Verificar senha
    if (password_verify($senha, $user['senha'])) {
        // Iniciar sessão e redirecionar
        session_start();
        $_SESSION['user_id'] = $user['id'];
        header("Location: pagina_inicial.html");
        exit(); // Certifique-se de que o script não continua a ser executado após o redirecionamento
    } else {
        echo "Senha incorreta.";
    }
} else {
    echo "Usuário não encontrado.";
}

$conn->close();
?>
