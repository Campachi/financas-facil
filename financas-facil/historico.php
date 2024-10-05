<?php
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

// Verificar conexão
if ($conn->connect_error) {
    die("Conexão falhou: " . $conn->connect_error);
}

// Pegar o ID do usuário logado
$user_id = $_SESSION['user_id'];

// Preparar a consulta para obter o histórico de cálculos do usuário
$sql = "SELECT * FROM finance_calculations WHERE user_id = ? ORDER BY data DESC";
$stmt = $conn->prepare($sql);

if ($stmt === false) {
    die("Erro ao preparar a consulta: " . $conn->error);
}

$stmt->bind_param("i", $user_id);

if ($stmt->execute()) {
    $result = $stmt->get_result();
} else {
    echo "Erro ao obter histórico: " . $stmt->error;
}

// Fechar a consulta, mas manter a conexão aberta para uso posterior
$stmt->close();

// Verificar se o último cálculo foi passado via GET (ou POST) da página anterior
$ultimo_calculo = [];
if (isset($_GET['salario']) && isset($_GET['despesas']) && isset($_GET['outras_despesas']) && isset($_GET['percapita']) && isset($_GET['data'])) {
    $ultimo_calculo = [
        'salario' => floatval($_GET['salario']),
        'despesas' => floatval($_GET['despesas']),
        'outras_despesas' => floatval($_GET['outras_despesas']),
        'percapita' => intval($_GET['percapita']),
        'data' => $_GET['data']
    ];
}

$conn->close();
?>

<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Histórico - Finanças Fácil</title>
    <link rel="stylesheet" href="historico.css">
</head>
<body>
    <header>
        <nav class="navbar">
            <img class="logotip" src="logotipo.png.png" alt="Logotipo">
            <ul class="nav-links">
                <li><a href="pagina_inicial.html">Início</a></li>
                <li><a href="historico.php">Histórico</a></li>
                <li class="dropdown">
                    <a href="#" class="dropbtn">Ver meu perfil</a>
                    <div class="dropdown-content">
                        <a href="perfil.php">Perfil</a>
                        <a href="configuraçao.html">Configurações</a>
                        <a href="login.html">Sair</a>
                    </div>
                </li>
            </ul>
            <div class="search-container">
                <input type="text" placeholder="Buscar...">
                <button type="button">Buscar</button>
            </div>
        </nav>
    </header>

    <main class="historico-page">
        <fieldset>
            <?php if (!empty($ultimo_calculo)): ?>
                <table>
                    <thead>
                        <tr>
                            <th>Data</th>
                            <th>Salário</th>
                            <th>Despesas</th>
                            <th>Outras Despesas</th>
                            <th>Per Capita</th>
                            <th>Saldo Final</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td><?= date('d/m/Y', strtotime($ultimo_calculo['data'])) ?></td>
                            <td>R$ <?= number_format($ultimo_calculo['salario'], 2, ',', '.') ?></td>
                            <td>R$ <?= number_format($ultimo_calculo['despesas'], 2, ',', '.') ?></td>
                            <td>R$ <?= number_format($ultimo_calculo['outras_despesas'], 2, ',', '.') ?></td>
                            <td><?= number_format($ultimo_calculo['percapita'], 0, ',', '.') ?></td>
                            <td>R$ <?= number_format($ultimo_calculo['salario'] - ($ultimo_calculo['despesas'] + $ultimo_calculo['outras_despesas']), 2, ',', '.') ?></td>
                        </tr>
                    </tbody>
                </table>
            <?php else: ?>
                <p>Não há cálculos recentes registrados.</p> <!-- Mensagem corrigida -->
            <?php endif; ?>
        </fieldset>

        <fieldset>
            <legend>Histórico de Cálculos</legend>
            <?php if ($result->num_rows > 0): ?>
                <table>
                    <thead>
                        <tr>
                            <th>Data</th>
                            <th>Salário</th>
                            <th>Despesas</th>
                            <th>Outras Despesas</th>
                            <th>Per Capita</th>
                            <th>Saldo Final</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php while ($row = $result->fetch_assoc()): ?>
                            <tr>
                                <td><?= date('d/m/Y', strtotime($row['data'])) ?></td>
                                <td>R$ <?= number_format($row['salario'], 2, ',', '.') ?></td>
                                <td>R$ <?= number_format($row['despesas'], 2, ',', '.') ?></td>
                                <td>R$ <?= number_format($row['outras_despesas'], 2, ',', '.') ?></td>
                                <td><?= number_format($row['percapita'], 0, ',', '.') ?></td>
                                <td>R$ <?= number_format($row['salario'] - ($row['despesas'] + $row['outras_despesas']), 2, ',', '.') ?></td>
                            </tr>
                        <?php endwhile; ?>
                    </tbody>
                </table>
            <?php else: ?>
                <p>Não há registros de cálculos financeiros.</p>
            <?php endif; ?>
        </fieldset>
        
        <a href="pagina_inicial.html" class="button">Voltar ao Início</a>
    </main>

    <footer>
        <p>&copy; 2024 Finança Fácil. Todos os direitos reservados.</p>
    </footer>
</body>
</html>
