<?php
// Iniciar a sessão
session_start();

// Verificar se o usuário está logado
if (!isset($_SESSION['user_id'])) {
    header("Location: login.html");
    exit();
}

// Conectar ao banco de dados
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "financas_facil";

$conn = new mysqli($servername, $username, $password, $dbname);

// Verificar a conexão
if ($conn->connect_error) {
    die("Conexão falhou: " . $conn->connect_error);
}

// Verificar se o formulário foi enviado
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Pegando o ID do usuário logado
    $user_id = $_SESSION['user_id'];

    // Recuperando os valores do formulário e garantindo que sejam convertidos para os tipos corretos
    $salario = isset($_POST['salario']) ? floatval(str_replace(',', '.', $_POST['salario'])) : 0;
    $despesas = isset($_POST['despesas']) ? floatval(str_replace(',', '.', $_POST['despesas'])) : 0;
    $outras_despesas = isset($_POST['outras_despesas']) ? floatval(str_replace(',', '.', $_POST['outras_despesas'])) : 0;
    $percapita = isset($_POST['percapita']) ? intval($_POST['percapita']) : 1;
    $data = isset($_POST['data']) ? $_POST['data'] : date('Y-m-d');

    // Consulta SQL para inserir os dados no banco de dados
    $sql = "INSERT INTO finance_calculations (user_id, salario, despesas, outras_despesas, percapita, data) 
            VALUES (?, ?, ?, ?, ?, ?)";

    // Preparar a consulta
    $stmt = $conn->prepare($sql);
    if ($stmt === false) {
        die("Erro ao preparar a consulta: " . $conn->error);
    }

    // Associar os parâmetros à consulta
    $stmt->bind_param("idddis", $user_id, $salario, $despesas, $outras_despesas, $percapita, $data);

    // Executar a consulta
    if ($stmt->execute()) {
        // Redirecionar para a página de histórico após a inserção bem-sucedida
        header("Location: historico.php");
        exit();
    } else {
        // Exibir mensagem de erro caso haja falha na execução
        echo "Erro ao registrar cálculo financeiro: " . $stmt->error;
    }

    // Fechar a consulta e a conexão
    $stmt->close();
    $conn->close();
}
?>
