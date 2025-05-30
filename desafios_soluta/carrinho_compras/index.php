<?php
session_start();

// Iniciar carrinho se ainda não existir
if (!isset($_SESSION['carrinho'])) {
    $_SESSION['carrinho'] = [];
}

// Adicionar produto
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $produto = trim($_POST['produto'] ?? '');
    $quantidade = max(0, intval(trim($_POST['quantidade'] ?? '')));
    $preco = max(0, floatval(trim($_POST['preco'] ?? '')));

    if ($_POST['acao'] == 'Adicionar' && !empty($produto) && $quantidade > 0 && $preco > 0) {
        $encontrado = false;

        // Verifica se o produto já existe no carrinho
        foreach ($_SESSION['carrinho'] as &$item) {
            if ($item['produto'] === $produto) {
                $item['quantidade'] += $quantidade;
                $item['preco'] += $preco;
                $encontrado = true;
                break;
            }
        }

        // Se não encontrado, adiciona um novo item
        if (!$encontrado) {
            $_SESSION['carrinho'][] = ['produto' => $produto, 'quantidade' => $quantidade, 'preco' => $preco];
        }
    }
    // Excluir tudo
    if ($_POST['acao'] == 'Excluir tudo') {
        $_SESSION['carrinho'] = [];
    }

    // Redireciona para evitar reenvio do formulário
    header("Location: " . $_SERVER['PHP_SELF']);
    exit;
}
?>
<!DOCTYPE html>
<html lang="pt-br">

<head>
    <meta charset="UTF-8">
    <title>Carrinho</title>
    <link rel="stylesheet" href="style.css">
</head>

<body>
    <h1 style="color: white">Carrinho de Compras</h1>

    <main>
        <form method="post">
            <div style="display: flex; justify-content: center; gap: 10px">
                <input style="max-width: 45%;" type="text" name="produto" placeholder="Digite um produto">
                <input style="width: 20%;" type="number" name="quantidade" id="quantidade" placeholder="QT." min="0">
                <input style="width: 30%;"  type="number" name="preco" id="preco" placeholder="Valor" step="0.01" min="0">
            </div>
            <div style="display: flex; justify-content: flex-end; gap: 10px">
                <button style="width: 25%; background-color: green" type="submit" name="acao" value="Adicionar">Adicionar</button>
                <button style="width: 25%; background-color: red" type="submit" name="acao" value="Excluir tudo">Excluir tudo</button>
            </div>
        </form>
        <h2 style="padding-top: 20px;">Produtos adicionados:</h2>
        <ul style="display: flex; font-size: 1.6em; font-weight:bold; gap: 10px; flex-direction: column;">
            <?php
            if (empty($_SESSION['carrinho'])) {
                echo "<li>Carrinho vazio</li>";
            } else {
                foreach ($_SESSION['carrinho'] as $item) {
                    if (is_array($item) && isset($item['produto']) && isset($item['quantidade']) && isset($item['preco'])) {
                        $preco_formatado = number_format($item['preco'], 2, ',', '.');
                        echo "<li>" . htmlspecialchars($item['quantidade']) . " UN " . htmlspecialchars($item['produto']) . " : R$ " . $preco_formatado . "</li>";
                    }
                }
            }
            ?>
        </ul>
    </main>
</body>

</html>