<?php
session_start();

// Inicializa o jogo se não existir uma sessão
if (!isset($_SESSION['numero_secreto'])) {
    $_SESSION['numero_secreto'] = rand(1, 100);
    $_SESSION['tentativas'] = 0;
}

$mensagem = "";
$numero_secreto = $_SESSION['numero_secreto'];

// Verifica se o formulário foi enviado
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $palpite = intval($_POST['palpite']);
    $_SESSION['tentativas']++;

    if ($palpite < $numero_secreto) {
        $mensagem = "O número correto é maior que $palpite. ";
    } elseif ($palpite > $numero_secreto) {
        $mensagem = "O número correto é menor que $palpite. ";
    } else {
        $mensagem = "Parabéns! Você adivinhou o número $numero_secreto em " . $_SESSION['tentativas'] . " tentativas.";
        // Reinicia o jogo
        session_destroy();
    }

    // Dicas de proximidade
    $diferenca = abs($numero_secreto - $palpite);
    if ($diferenca > 20) {
        $mensagem .= "Você está longe!";
    } elseif ($diferenca > 10) {
        $mensagem .= "Você está perto!";
    } elseif ($diferenca > 0) {
        $mensagem .= "Você está muito perto!";
    }
}
?>

<!DOCTYPE html>
<html lang="pt-BR">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Jogo de Adivinhação</title>
    <link rel="stylesheet" href="style.css">
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
</head>

<body>
    <main style="display:flex; width: 20%; flex-direction:column; justify-content:center; align-items:center; text-align:center;" class="container mt-5">
        <h1 style="display: flex; font-weight:bold;">Jogo de Adivinhação</h1>
        <p>Adivinhe o número entre 1 e 100!</p>

        <!-- Botão para abrir o modal -->
        <button style="display:flex; width: 40%;" type="button" class="btn btn-primary" data-toggle="modal" data-target="#adivinharModal">
            Clique aqui para iniciar!
        </button>

        <!-- Modal -->
        <div  class="modal fade" id="adivinharModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
            <div class="modal-dialog" role="document">
                <div class="modal-content">
                    <form  method="POST">
                        <div class="form-group">
                            <label for="palpite">Seu palpite:</label>
                            <input type="number" name="palpite" class="form-control" min="1" max="100" required>
                        </div>
                        <button type="submit" class="btn btn-success">Adivinhar</button>
                    </form>
                </div>
            </div>
        </div>
        </div>


        <p style="display: flex; padding-top: 30px; font-weight:bold;"><?php echo $mensagem; ?></p>
    </main>

    <script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.9.2/dist/umd/popper.min.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
</body>

</html>