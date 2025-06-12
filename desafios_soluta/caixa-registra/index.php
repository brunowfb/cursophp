<!DOCTYPE html>
<html lang="pt-br">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Caixa registradora</title>
    <link rel="stylesheet" href="style.css">
</head>

<body>
    <main style="display: flex; flex-direction: column;">
        <h1 style="color: black; font-size: 1.2em">Finalizar venda</h1>
        <h2 style="font-size: 2.5em; color: black;">TOTAL A PAGAR:</h2>
        <p style="font-weight: bolder; background-color: #b7b2d7; border-radius: 4px; padding: 5px">Informações do pagamento</p>
        <form style="display:flex; max-width:800px; justify-content: space-between;" action="" method="get">
            <div style="display:flex;  flex-direction: column;">
                <div>
                    <h1 style="font-size: 3.5em;">TROCO:</h1>
                    <div id="troco"></div>

                </div>
            </div>
            <div style="display:flex;  flex-direction: column;">
                <div style="display: flex; justify-content: flex-end; flex-direction: column;">
                    <label for="">Dinheiro</label>
                    <input type="number" name="dinheiro" id="idinheiro">
                    <label for="">Cartão de crédito</label>
                    <input type="number" name="credito" id="icredito">
                    <label for="">Cartão de débito</label>
                    <input type="number" name="debito" id="idebito">
                    <label for="">Cheque</label>
                    <input type="number" name="cheque" id="icheque">
                </div>
            </div>
        </form>
    </main>
    <?php
    $idinheiro = [[100, 5], [50, 5], [20, 5], [10, 5], [5, 5], [2, 5], [1, 5]]
    ?>
</body>

</html>