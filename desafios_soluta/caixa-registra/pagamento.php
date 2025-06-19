<?php
session_start();

// Inicializa o total a pagar com o valor enviado do carrinho
$totalPagar = isset($_GET['total']) ? (float)$_GET['total'] : 0; 
$produtosCarrinho = isset($_SESSION['carrinho']) ? $_SESSION['carrinho'] : [];
?>

<!DOCTYPE html>
<html lang="pt-br">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Caixa Registradora</title>
    <link rel="stylesheet" href="style.css">

    <style>
        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
        }

        th,
        td {
            text-align: center;
            border: 1px solid #ddd;
            padding: 8px;
        }

        th {
            background-color: #f2f2f2;
        }

        .pagamento {
            display: none;
        }

        .invisible {
            display: none;
        }

        .cedulas-moedas-table {
            margin-top: 20px;
        }
    </style>
    <script>
        function formatarMoeda(valor) {
            return valor.toLocaleString('pt-BR', { style: 'currency', currency: 'BRL' }).trim();
        }

        function calcularTotalRecebido() {
            const dinheiro = parseFloat(document.getElementById('idinheiro').value.replace('R$ ', '').replace(',', '.') || 0);
            const credito = parseFloat(document.getElementById('icredito').value.replace('R$ ', '').replace(',', '.') || 0);
            const debito = parseFloat(document.getElementById('idebito').value.replace('R$ ', '').replace(',', '.') || 0);
            const cheque = parseFloat(document.getElementById('icheque').value.replace('R$ ', '').replace(',', '.') || 0);
            const pix = parseFloat(document.getElementById('ipix').value.replace('R$ ', '').replace(',', '.') || 0);

            return isNaN(dinheiro) ? 0 : dinheiro + credito + debito + cheque + pix; 
        }

        function calcularTroco() {
            const totalPagar = parseFloat(document.getElementById('totalPagar').value);
            const totalRecebido = calcularTotalRecebido();
            const troco = totalRecebido - totalPagar;

            // Formata o troco para ter ponto como separador decimal
            document.getElementById('troco').innerText = formatarMoeda(troco).replace('.', ',');
            return troco; 
        }

        function formatarCampoMoeda(campo) {
            const valor = campo.value.replace('R$ ', '').replace(',', '.');
            if (valor) {
                campo.value = `R$ ${parseFloat(valor).toFixed(2).replace('.', ',')}`;
            } else {
                campo.value = '';
            }
            calcularTroco();
        }

        function mostrarFormaPagamento() {
            const metodo = document.getElementById('metodoPagamento').value;
            const formas = document.getElementsByClassName('pagamento');

            for (let i = 0; i < formas.length; i++) {
                formas[i].style.display = 'none'; 
            }

            if (metodo) {
                document.getElementById(metodo).style.display = 'block'; 
            }
            calcularTroco(); 
        }

        function calcularCedulasTroco(troco) {
            const cedulas = [200, 100, 50, 20, 10, 5, 2]; 
            const moedas = [1, 0.5, 0.25, 0.1, 0.01]; 
            let resultado = [];

            cedulas.forEach(cedula => {
                if (troco >= cedula) {
                    const quantidade = Math.floor(troco / cedula);
                    troco -= quantidade * cedula;
                    resultado.push({ denom: cedula, qtd: quantidade });
                }
            });

            moedas.forEach(moeda => {
                if (troco >= moeda) {
                    const quantidade = Math.floor(troco / moeda);
                    troco -= quantidade * moeda;
                    resultado.push({ denom: moeda, qtd: quantidade });
                }
            });

            return resultado; 
        }

        function concluirVenda() {
            const totalPagar = parseFloat(document.getElementById('totalPagar').value);
            const totalRecebido = calcularTotalRecebido(); 
            const metodoPagamento = document.getElementById('metodoPagamento').value;

            if (!metodoPagamento) {
                alert("Por favor, selecione uma forma de pagamento.");
                return;
            }

            const valorValido = totalRecebido > 0;
            if (!valorValido) {
                alert("Por favor, insira um valor válido para a forma de pagamento.");
                return;
            }

            const troco = totalRecebido - totalPagar; 

            const produtos = JSON.parse(document.getElementById('produtosCarrinho').value);
            let produtosResumo = `
                <h2>Produtos Comprados:</h2>
                <table class="table table-striped">
                    <thead>
                        <tr>
                            <th>Produto</th>
                            <th>Quantidade</th>
                            <th>Preço Unitário</th>
                            <th>Preço Total</th>
                        </tr>
                    </thead>
                    <tbody>
            `;
            produtos.forEach(produto => {
                produtosResumo += `
                    <tr>
                        <td>${produto.produto}</td>
                        <td>${produto.quantidade}</td>
                        <td>R$ ${((produto.preco / produto.quantidade)).toFixed(2).replace('.', ',')}</td>
                        <td>R$ ${formatarMoeda(produto.preco).replace('.', ',')}</td>
                    </tr>
                `;
            });
            produtosResumo += `
                    </tbody>
                </table>
            `;

            let historico = `
                <h1>Compra concluída</h1>
                <h2>Histórico da Compra</h2>
                <p><strong>Total a Pagar(-):</strong> R$ ${totalPagar.toFixed(2).replace('.', ',')}</p>
                <p><strong>Total Pago(+):</strong> R$ ${totalRecebido.toFixed(2).replace('.', ',')} (${metodoPagamento})</p>
                <p><strong>Troco(+):</strong> R$ ${troco.toFixed(2).replace('.', ',')}</p>
                <h2>Cédulas/moedas usadas no troco:</h2>
                <table class="cedulas-moedas-table">
                    <thead>
                        <tr>
                            <th style='text-align: center;'>Quantidade</th>
                            <th style='text-align: center;'>Cédula/moeda</th>
                        </tr>
                    </thead>
                    <tbody>
            `;

            const cedulasMoedas = calcularCedulasTroco(troco);
            cedulasMoedas.forEach(item => {
                historico += `
                    <tr>
                        <td>${item.qtd}</td>
                        <td>R$ ${item.denom.toFixed(2).replace('.', ',')}</td>
                    </tr>
                `;
            });

            historico += `
                    </tbody>
                </table>
            `;

            historico += produtosResumo;

            document.getElementById('formulario').style.display = 'none';
            document.getElementById('historico').innerHTML = historico;

            document.getElementById('botaoInicio').classList.remove('invisible');

            <?php unset($_SESSION['carrinho']); ?>
        }
    </script>
</head>

<body>
    <main style="display: flex; flex-direction: column; align-items: center;">
        
        <form id="formulario" style="display:flex; flex-direction: column; max-width: 800px; justify-content: center;">
            <p style="font-weight: bolder; background-color:rgb(255, 255, 255); border-radius: 4px; padding: 5px">Informações do pagamento</p>

            <h2 style="font-size: 2.5em; color: black;">TOTAL A PAGAR:</h2>

            <input type='hidden' id='totalPagar' value='<?php echo $totalPagar; ?>'> 
            <h2 style='color: black;'>R$ <?php echo number_format($totalPagar, 2, ',', ''); ?></h2>

            <input type='hidden' id='produtosCarrinho' value='<?php echo json_encode($produtosCarrinho); ?>'>

            <div>
                <h1 style="font-size: 3.2em;">TROCO:</h1>
                <div id="troco" style="font-size: 1.5em; color: black;">R$ 0,00</div>
            </div>

            <label for="metodoPagamento">Escolha a forma de pagamento:</label>
            <select id="metodoPagamento" onchange="mostrarFormaPagamento()">
                <option value="">Selecione</option>
                <option value="dinheiro">Dinheiro</option>
                <option value="credito">Cartão de Crédito</option>
                <option value="debito">Cartão de Débito</option>
                <option value="cheque">Cheque</option>
                <option value="pix">PIX</option>
            </select>

            <div id="dinheiro" class="pagamento">
                <label for="idinheiro">Valor em Dinheiro</label>
                <input type="text" name="dinheiro" id="idinheiro" step="0.01" onfocus="this.value=''" onblur="formatarCampoMoeda(this)">
            </div>

            <div id="credito" class="pagamento">
                <label for="icredito">Valor em Cartão de Crédito</label>
                <input type="text" name="credito" id="icredito" step="0.01" onfocus="this.value=''" onblur="formatarCampoMoeda(this)">
            </div>

            <div id="debito" class="pagamento">
                <label for="idebito">Valor em Cartão de Débito</label>
                <input type="text" name="debito" id="idebito" step="0.01" onfocus="this.value=''" onblur="formatarCampoMoeda(this)">
            </div>

            <div id="cheque" class="pagamento">
                <label for="icheque">Valor em Cheque</label>
                <input type="text" name="cheque" id="icheque" step="0.01" onfocus="this.value=''" onblur="formatarCampoMoeda(this)">
            </div>

            <div id="pix" class="pagamento">
                <label for="ipix">Valor em PIX</label>
                <input type="text" name="pix" id="ipix" step="0.01" onfocus="this.value=''" onblur="formatarCampoMoeda(this)">
            </div>

            <button type="button" onclick="concluirVenda()" style="margin-top: 20px;">Concluir Venda</button>
        </form>

        <div id="historico"></div>

        <button id="botaoInicio" class="invisible" type="button" onclick="window.location.href='carrinho.php'" style="margin-top: 20px;">Início</button>
    </main>
</body>

</html>