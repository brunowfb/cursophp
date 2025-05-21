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
        
        // Validação de entrada
        if ($valorEmReais <= 0) {
            echo "<p style='color:red;'>Por favor, insira um valor positivo.</p>";
        } else {
            $apiKey = 'b5b761643425e06e1de3af24'; 
            $url = "https://v6.exchangerate-api.com/v6/$apiKey/latest/USD";

            // Obtendo a cotação em tempo real
            $response = file_get_contents($url);
            
            // Verificando se a resposta da API é válida
            if ($response === FALSE) {
                echo "<p style='color:red;'>Erro ao conectar à API. Tente novamente mais tarde.</p>";
            } else {
                $data = json_decode($response, true);
                
                if (isset($data['conversion_rates']['USD'])) {
                    
                    $cotacao = $data['conversion_rates']['BRL']; // Cotação em USD
                    $valor_com_virgula = str_replace(".", ",", $cotacao);
                    echo "<p>Cotação do dólar: R$$valor_com_virgula</p>"; // Depuração: Mostra a cotação
                    
                    // Verifique se a cotação é válida
                    if ($cotacao > 0) {
                        $valorEmDolares = $valorEmReais / $cotacao; // Converte de BRL para USD
                    // Formatação dos valores
                        $valorFormatadoReais = "R$ " . number_format($valorEmReais, 2, ',', '.');
                        $valorFormatadoDolares = "USD " . number_format($valorEmDolares, 2, ',', '.');

                        // Definindo o fuso horário para São Paulo
                        date_default_timezone_set('America/Sao_Paulo');
                        $dataHoraAtual = date('d/m/Y H:i:s');

                        // Exibir mensagem formatada
                        echo "<p>O valor informado de <strong>$valorFormatadoReais</strong> é equivalente a <strong>$valorFormatadoDolares</strong>.</p>";
                        echo "<strong style='font-size:0.7em;'>Cotação atualizada em: $dataHoraAtual</strong>.</p>";
                    } else {
                        echo "<p style='color:red;'>A cotação não é válida. Tente novamente mais tarde.</p>";
                    }
                } else {
                    echo "<p style='color:red;'>Erro ao obter a cotação. Tente novamente mais tarde.</p>";
                }
            }
        }
    }

        ?>
    </main>
</body>

</html>
