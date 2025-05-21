<!DOCTYPE html>
<html lang="pt-br">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Conversor de Moedas</title>
    <link rel="stylesheet" href="../d003/style.css">
</head>

<body>
    <main>
        <h1>Conversor de Moedas v1.0</h1>
        <form method="post">
            <input type="number" name="valor" id="iconverter" step="0.01" placeholder="Valor em BRL" required>
            <button id="botao" type="submit">Converter</button>
        </form>
        <?php
        if (isset($_POST['valor'])) {
            $valorEmReais = $_POST['valor'];

            if ($valorEmReais <= 0) {
                echo "<p style='color:red;'>Por favor, insira um valor positivo.</p>";
            } else {
                $cotacao = 5.65; // Cotação fixa

                $valorEmDolares = $valorEmReais / $cotacao; // Converte de BRL para USD

                // Formatação dos valores
                $valorFormatadoReais = "R$ " . number_format($valorEmReais, 2, ',', '.'); // Usado pra valores decimais
                $valorFormatadoDolares = "USD " . number_format($valorEmDolares, 2, ',', '.'); // usado pra valores decimais

                echo "<p>Seus <strong>$valorFormatadoReais</strong> equivalem a <strong>$valorFormatadoDolares</strong> <br>
            <strong style='font-size:0.7em;'>Cotação fixa de R$ $cotacao</strong>.</p>";
            }
        }
        ?>

    </main>
</body>

</html>