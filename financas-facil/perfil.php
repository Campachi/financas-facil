<?php
session_start();

// Verificar se o usuário está logado
if (!isset($_SESSION['user_id'])) {
    header("Location: login.html");
    exit();
}

// Configurações do banco de dados
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "financas_facil";

// Conectar ao banco de dados
$conn = new mysqli($servername, $username, $password, $dbname);

// Verificar conexão
if ($conn->connect_error) {
    die("Conexão falhou: " . $conn->connect_error);
}

// Obter dados do usuário logado
$user_id = $_SESSION['user_id'];

$sql = "SELECT nome, telefone, cpf, email, imagem_perfil, imagem_perfil_tipo FROM usuarios WHERE id = ?";
$stmt = $conn->prepare($sql);

if (!$stmt) {
    die("Erro na preparação da consulta: " . $conn->error);
}

$stmt->bind_param("i", $user_id);
$stmt->execute();
$stmt->bind_result($nome, $telefone, $cpf, $email, $imagem_perfil, $imagem_perfil_tipo);
$stmt->fetch();
$stmt->close();

// Atualizar os dados se o formulário for enviado
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $novo_nome = $_POST['name'];
    $novo_telefone = $_POST['phone'];
    $novo_cpf = $_POST['cpf'];
    $novo_email = $_POST['email'];

    // Inicializar variável para imagem
    $novo_imagem_perfil = $imagem_perfil;
    $novo_imagem_perfil_tipo = $imagem_perfil_tipo;

    // Verificar se uma imagem foi enviada
    if (isset($_FILES['avatar']) && $_FILES['avatar']['error'] == UPLOAD_ERR_OK) {
        $fileTmpPath = $_FILES['avatar']['tmp_name'];
        $fileName = $_FILES['avatar']['name'];
        $fileSize = $_FILES['avatar']['size'];
        $fileType = mime_content_type($fileTmpPath); // Obter o tipo MIME real
        $fileNameCmps = explode(".", $fileName);
        $fileExtension = strtolower(end($fileNameCmps));

        // Extensões permitidas
        $allowedfileExtensions = array('jpg', 'jpeg', 'png', 'gif');

        if (in_array($fileExtension, $allowedfileExtensions)) {
            // Limite de tamanho (ex: 2MB)
            if ($fileSize <= 2 * 1024 * 1024) { // 2MB
                // Verificar se o MIME type corresponde à extensão
                $allowedMimeTypes = array('image/jpeg', 'image/png', 'image/gif');
                if (in_array($fileType, $allowedMimeTypes)) {
                    // Ler o conteúdo do arquivo
                    $imagem_dados = file_get_contents($fileTmpPath);

                    // Atualizar as variáveis com a nova imagem
                    $novo_imagem_perfil = $imagem_dados;
                    $novo_imagem_perfil_tipo = $fileType;
                } else {
                    $message = "Tipo de arquivo não corresponde à extensão.";
                }
            } else {
                $message = "O tamanho do arquivo excede o limite de 2MB.";
            }
        } else {
            $message = "Tipo de arquivo não permitido. Apenas JPG, JPEG, PNG e GIF são aceitos.";
        }
    }

    // Atualizar os dados no banco de dados
    $sql_update = "UPDATE usuarios SET nome=?, telefone=?, cpf=?, email=?, imagem_perfil=?, imagem_perfil_tipo=? WHERE id=?";
    $stmt_update = $conn->prepare($sql_update);

    if (!$stmt_update) {
        die("Erro na preparação da consulta: " . $conn->error);
    }

    $stmt_update->bind_param("ssssssi", $novo_nome, $novo_telefone, $novo_cpf, $novo_email, $novo_imagem_perfil, $novo_imagem_perfil_tipo, $user_id);

    if ($stmt_update->execute()) {
        $message = "Dados atualizados com sucesso.";
        // Atualizar as variáveis para refletir as mudanças
        $nome = $novo_nome;
        $telefone = $novo_telefone;
        $cpf = $novo_cpf;
        $email = $novo_email;
        $imagem_perfil = $novo_imagem_perfil;
        $imagem_perfil_tipo = $novo_imagem_perfil_tipo;
    } else {
        $message = "Erro ao atualizar os dados: " . $stmt_update->error;
    }
    $stmt_update->close();
}

$conn->close();
?>

<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Editar Perfil</title>
    <link rel="stylesheet" href="perfil.css">
</head>
<body>
    <header>
        <nav class="navbar">
            <img class="logotip" src="logotipo.png.png" alt="Logotipo">
            <ul class="nav-links">
                <li><a href="pagina_inicial.html">Início</a></li>
                <li><a href="historico.php">Histórico</a></li>
                <li class="dropdown">
                    <a href="perfil.php" class="dropbtn">Ver meu perfil</a>
                    <div class="dropdown-content">
                        <a href="perfil.php">Perfil</a>
                        <a href="configuraçao.html">Configurações</a>
                        <a href="logout.php">Sair</a>
                    </div>
                </li>
            </ul>
            <div class="search-container">
                <input type="text" placeholder="Buscar...">
                <button type="button">Buscar</button>
            </div>
        </nav>
    </header>
    <main class="main-content">
        <h1>Editar Perfil</h1>
        <div class="profile-container">
            <div class="profile-header">
                <?php if (!empty($imagem_perfil)) : ?>
                    <img class="profile-avatar" src="get_image.php?id=<?php echo $user_id; ?>" alt="Avatar do Usuário" id="profileAvatar">
                <?php else : ?>
                    <img class="profile-avatar" src="default_avatar.png" alt="Avatar do Usuário" id="profileAvatar">
                <?php endif; ?>
                <div class="upload-avatar">
                    <label for="uploadAvatar">Adicionar/Alterar Imagem de Perfil</label>
                    <!-- O input de arquivo está dentro do formulário -->
                </div>
            </div>
            <form action="perfil.php" method="post" enctype="multipart/form-data">
                <h2 class="titulo_informa">Informações Pessoais</h2>
                <table class="profile-table">
                    <tr>
                        <th>Nome Completo:</th>
                        <td><?php echo htmlspecialchars($nome); ?></td>
                    </tr>
                    <tr>
                        <th>Telefone:</th>
                        <td><?php echo htmlspecialchars($telefone); ?></td>
                    </tr>
                    <tr>
                        <th>CPF:</th>
                        <td><?php echo htmlspecialchars($cpf); ?></td>
                    </tr>
                    <tr>
                        <th>Email:</th>
                        <td><?php echo htmlspecialchars($email); ?></td>
                    </tr>
                </table>

                <h2 class="titulo_informa">Atualizar Informações</h2>
                <table class="profile-table">
                    <tr>
                        <th>Nome Completo:</th>
                        <td><input type="text" name="name" value="<?php echo htmlspecialchars($nome); ?>" required></td>
                    </tr>
                    <tr>
                        <th>Telefone:</th>
                        <td><input type="text" name="phone" value="<?php echo htmlspecialchars($telefone); ?>" required></td>
                    </tr>
                    <tr>
                        <th>CPF:</th>
                        <td><input type="text" name="cpf" value="<?php echo htmlspecialchars($cpf); ?>" required></td>
                    </tr>
                    <tr>
                        <th>Email:</th>
                        <td><input type="email" name="email" value="<?php echo htmlspecialchars($email); ?>" required></td>
                    </tr>
                    <tr>
                        <th>Imagem de Perfil:</th>
                        <td><input type="file" name="avatar" accept="image/*" onchange="previewImage(event)"></td>
                    </tr>
                </table>
                <button type="submit" class="update-button">Atualizar Dados</button>
                <?php if (isset($message)) { echo "<p class='message'>$message</p>"; } ?>
            </form>
        </div>
    </main>
    <footer>
        <p>&copy; 2024 Finança Fácil. Todos os direitos reservados.</p>
    </footer>

    <script>
        // Função para pré-visualizar a imagem antes do upload
        function previewImage(event) {
            var reader = new FileReader();
            reader.onload = function() {
                var output = document.getElementById('profileAvatar');
                output.src = reader.result;
            };
            reader.readAsDataURL(event.target.files[0]);
        }
    </script>
</body>
</html>
