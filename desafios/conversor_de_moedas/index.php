<?php
$result = null; // Inicializa a variável $result
$error = null; // Inicializa a variável $error

if (isset($_POST['value']) && isset($_POST['from_currency']) && isset($_POST['to_currency'])) {
    $api_Key = 'fca_live_AhDwPZhpdyVs1ioFpvXfgIs8X6CbLCAAlpJPEMKB';
    $endpoint = "https://api.freecurrencyapi.com/v1/latest?apikey={$api_Key}";

    // Tenta fazer a requisição à API
    $request = @file_get_contents($endpoint);

    // Verifica se a requisição foi bem-sucedida
    if ($request === FALSE) {
        $error = "Erro ao acessar a API. Tente novamente mais tarde.";
    } else {
        $parsed = json_decode($request, true);

        // Verifica se a resposta da API é válida
        if (isset($parsed['data'])) {
            $value = floatval(str_replace(',', '.', $_POST['value']));
            $from_currency = $_POST['from_currency']; // Moeda de origem
            $to_currency = $_POST['to_currency']; // Moeda de destino

            // Verifica se a moeda de origem e destino estão disponíveis
            if (isset($parsed['data'][$from_currency]) && isset($parsed['data'][$to_currency])) {
                $from_rate = $parsed['data'][$from_currency];
                $to_rate = $parsed['data'][$to_currency];
                // Conversão
                $result = ($value / $from_rate) * $to_rate;
            } else {
                $error = "Uma das moedas selecionadas não está disponível.";
            }
        } else {
            $error = "Dados da API não válidos. Resposta da API: " . htmlspecialchars(json_encode($parsed));
        }
    }
}
?>
<!DOCTYPE html>
<html lang="pt-br">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Conversor de moedas</title>
    <link rel="stylesheet" href="style.css">
</head>

<body>
    <main>
        <form action="" method="post">
            <label for="ivalor">Valor:</label>
            <input value="<?php if (isset($_POST['value'])) echo htmlspecialchars($_POST['value']); ?>" type="text" name="value" id="ivalor" placeholder="Digite o valor" step="0.02" required>

            <label for="from_currency">Converter de:</label>
            <select name="from_currency" id="from_currency" required>
                <option value="BRL">Real (BRL)</option>
                <option value="USD">Dólar (USD)</option>
                <option value="EUR">Euro (EUR)</option>
            </select>
            <label for="to_currency">Converter para:</label>
            <select name="to_currency" id="to_currency" required>
                <option value="USD">Dólar (USD)</option>
                <option value="EUR">Euro (EUR)</option>
                <option value="GBP">Libra Esterlina (GBP)</option>
                <option value="JPY">Iene Japonês (JPY)</option>
                <option value="CAD">Dólar Canadense (CAD)</option>
                <option value="AUD">Dólar Australiano (AUD)</option>
                <option value="BRL">Real Brasileiro (BRL)</option>
                <!-- Adicione mais moedas conforme necessário -->
            </select>

            <button type="submit">Converter</button>
        </form>
    </main>

    <?php
    // Exibe o resultado ou um erro, se houver
    if (isset($result)) {
        $formated = number_format($result, 2, ',', '.');
        echo "<h1 style='display: flex; padding: 10px; max-width: 50%; text-align: center; justify-content: center; background-color: white; border-radius:10px; color:black; font-size:3em;'>{$to_currency} {$formated}</h1>";
    } elseif (isset($error)) {
        echo "<p style='color:red;'>{$error}</p>";
    }
    ?>
</body>

</html>