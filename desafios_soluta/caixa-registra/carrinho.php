<?php
session_start();

// Iniciar carrinho se ainda não existir
if (!isset($_SESSION['carrinho'])) {
    $_SESSION['carrinho'] = [];
}

// Função para calcular o total
function calcularTotal($carrinho)
{
    $total = 0;
    foreach ($carrinho as $item) {
        $total += $item['preco'];
    }
    return $total;
}

// Adicionar ou editar produto
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $produto = strtoupper(trim(filter_input(INPUT_POST, 'produto', FILTER_SANITIZE_STRING) ?? ''));
    $quantidade = max(1, intval(trim(filter_input(INPUT_POST, 'quantidade', FILTER_SANITIZE_NUMBER_INT) ?? '')));
    $preco = max(0, floatval(str_replace(',', '.', trim(filter_input(INPUT_POST, 'preco', FILTER_SANITIZE_STRING) ?? ''))));

    // Ação de Adicionar
    if (filter_input(INPUT_POST, 'acao') == 'Adicionar' && !empty($produto) && $quantidade >= 1 && $preco > 0) {
        $encontrado = false;

        foreach ($_SESSION['carrinho'] as &$item) {
            if ($item['produto'] === $produto) {
                $item['quantidade'] += $quantidade;
                $item['preco'] = $item['quantidade'] * $preco;
                $encontrado = true;
                break;
            }
        }

        if (!$encontrado) {
            $_SESSION['carrinho'][] = [
                'produto' => $produto,
                'quantidade' => $quantidade,
                'preco' => $quantidade * $preco
            ];
        }
    }

    // Excluir item específico do carrinho
    if (filter_input(INPUT_POST, 'acao') == 'Excluir') {
        $index = intval(filter_input(INPUT_POST, 'index'));
        if (isset($_SESSION['carrinho'][$index])) {
            unset($_SESSION['carrinho'][$index]);
            $_SESSION['carrinho'] = array_values($_SESSION['carrinho']);
        }
    }

    // Editar item específico do carrinho
    if (filter_input(INPUT_POST, 'acao') == 'Editar') {
        $index = intval(filter_input(INPUT_POST, 'index'));
        if (isset($_SESSION['carrinho'][$index])) {
            $_SESSION['carrinho'][$index]['produto'] = strtoupper($produto);
            $_SESSION['carrinho'][$index]['quantidade'] = $quantidade;
            $_SESSION['carrinho'][$index]['preco'] = $quantidade * $preco;
        }
    }

    // Excluir tudo
    if (filter_input(INPUT_POST, 'acao') == 'Excluir tudo') {
        $_SESSION['carrinho'] = [];
    }

    // Redireciona para evitar reenvio do formulário
    header("Location: " . $_SERVER['PHP_SELF']);
    exit;
}

$total = calcularTotal($_SESSION['carrinho']);

// Verificação de carrinho vazio antes do envio do formulário
if ($_SERVER['REQUEST_METHOD'] == 'GET' && empty($_SESSION['carrinho'])) {
    $erro = "Você deve adicionar pelo menos um produto ao carrinho antes de finalizar a compra.";
}
?>

<!DOCTYPE html>
<html lang="pt-br">

<head>
    <meta charset="UTF-8">
    <title>Carrinho</title>
    <link rel="stylesheet" href="style.css">
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
</head>

<body>
    <div>
        <main>
            <h1>Carrinho de Compras</h1>
            <div class="modal fade" id="myModal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
                <div class="modal-dialog" role="document">
                    <div class="modal-content">
                        <form id="cabecalho-modal" method="post" id="formAdicionarProduto">
                            <div class="modal-header" style="display: flex; justify-content: center;">
                                <h2 class="modal-title" id="myModalLabel">Novo Produto</h2>
                            </div>
                            <div class="modal-body" style="display:flex; flex-direction:row;">
                                <div class="form-group">
                                    <label for="produto">Produto:</label>
                                    <input type="text" class="form-control" name="produto" required>
                                </div>
                                <div class="form-group">
                                    <label for="quantidade">Quantidade:</label>
                                    <input type="number" class="form-control" name="quantidade" min="1" value="1" required>
                                </div>
                                <div class="form-group">
                                    <label for="preco">Valor unitário:</label>
                                    <input type="text" class="form-control" name="preco" value="0,00" required oninput="this.value = this.value.replace('.', ',');">
                                </div>
                                <input type="hidden" name="acao" value="Adicionar">
                            </div>
                            <div class="modal-footer" style="display:flex; justify-content: center; flex-direction:row;">
                                <button style="width: 45%" type="button" class="btn btn-secondary" data-dismiss="modal">Fechar</button>
                                <button style="width: 45%" name="acao" value="Adicionar" type="submit" class="btn btn-primary">Adicionar Produto</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>

            <div style="display: flex; justify-content: center; gap: 10px">
                <button style="max-width: 40%;" class="btn btn-primary" data-toggle="modal" data-target="#myModal">
                    Novo produto
                </button>
                <form style="padding: 0px;" method="POST">
                    <input type="hidden" name="acao" value="Excluir tudo">
                    <button style="background-color: red;" class='btn btn-primary'>Excluir tudo</button>
                </form>
            </div>

            <h2 style="padding-bottom: 20px; padding-top: 20px;">Produtos adicionados:</h2>

            <table class="table table-striped table-custom" required>
                <thead>
                    <tr>
                        <th>QTD.</th>
                        <th>UN.</th>
                        <th>PRODUTO</th>
                        <th>V. UNITARIO</th>
                        <th>V. TOTAL</th>
                        <th style="text-align: center;">*</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    if (empty($_SESSION['carrinho'])) {
                        echo "<tr><td colspan='6' class='text-center'>Carrinho vazio</td></tr>";
                    } else {
                        foreach ($_SESSION['carrinho'] as $index => $item) {
                            if (is_array($item) && isset($item['produto']) && isset($item['quantidade']) && isset($item['preco'])) {
                                $preco_unitario_formatado = number_format($item['preco'] / $item['quantidade'], 2, ',', '');
                                $preco_total_formatado = number_format($item['preco'], 2, ',', '');
                                echo "<tr>
                                        <td>" . htmlspecialchars($item['quantidade']) . "</td>
                                        <td>UN</td>
                                        <td>" . htmlspecialchars($item['produto']) . "</td>
                                        <td>R$ " . $preco_unitario_formatado . "</td>
                                        <td>R$ " . $preco_total_formatado . "</td>
                                        <td style='display:flex; flex-direction: row; gap: 10px;'>
                                            <button type='button' class='btn btn-primary' data-toggle='modal' data-target='#editModal' data-index='$index' data-nome='" . htmlspecialchars($item['produto']) . "' data-quantidade='" . $item['quantidade'] . "' data-preco='" . number_format($item['preco'] / $item['quantidade'], 2, ',', '') . "'>📝</button>
                                            <form method='post' style= 'padding: 0px;
                                            'margin: 0px;
                                            'display:inline;'>
                                                <input type='hidden' name='index' value='$index'>
                                                <button class='btn btn-danger' name='acao' value='Excluir' style='background-color: red; color:white; width:50px;'>🗑️</button>
                                            </form>
                                        </td>
                                      </tr>";
                            }
                        }
                    }
                    ?>
                </tbody>
            </table>
            <h3>Total: R$ <?php echo number_format($total, 2, ',', ''); ?></h3>

            <!-- Mensagem de erro -->
            <?php if (isset($erro)): ?>
                <div class="alert alert-danger" role="alert">
                    <?php echo htmlspecialchars($erro); ?>
                </div>
            <?php endif; ?>

            <!-- Formulário de finalização da compra -->
            <form method="GET" action="pagamento.php">
                <input type="hidden" name="total" value="<?php echo number_format($total, 2, ',', ''); ?>">
                <button type="submit" class="btn btn-success" <?php echo empty($_SESSION['carrinho']) ? 'disabled' : ''; ?>>Finalizar Compra</button>
            </form>
        </main>

        <!-- Modal para editar -->
        <div class="modal fade" id="editModal" tabindex="-1" role="dialog" aria-labelledby="editModalLabel" aria-hidden="true">
            <div class="modal-dialog" role="document">
                <div class="modal-content">
                    <div id="cabecalho-modal" style="display:flex;" class="modal-header">
                        <div class="modal-body">
                            <form method="post">
                                <input type="hidden" id="itemId" name="index" value="">
                                <div style="display: flex; flex-direction:column">
                                    <div style="display:flex; justify-content: space-between;">
                                        <h2>Editar produto</h2>
                                        <button style="display: flex; width:9%; height: 10%;" type="button" class="btn btn-secondary" data-dismiss="modal">X</button>
                                    </div>
                                    <div>
                                        <div style="display:flex; flex-direction:row; width:100%;">
                                            <div class="form-group">
                                                <label for="editName">Produto:</label>
                                                <input type="text" class="form-control" id="editName" name="produto" required>
                                            </div>
                                            <div class="form-group">
                                                <label for="editQuantity">Quantidade:</label>
                                                <input type="number" class="form-control" id="editQuantity" name="quantidade" required>
                                            </div>
                                            <div class="form-group">
                                                <label for="editValue">Valor unitário:</label>
                                                <input type="text" class="form-control" id="editValue" name="preco" required oninput="this.value = this.value.replace('.', ',');">
                                            </div>
                                        </div>
                                        <div style="display: flex; justify-content:flex-end;">
                                            <div style="display: flex; flex-direction: column; gap: 10px;">
                                                <button type="button" class="btn btn-primary" id="saveChangesBtn">Salvar Alterações</button>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
        <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.9.2/dist/umd/popper.min.js"></script>
        <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
        <script>
            // Quando o modal for aberto, preenche os campos com os dados do item
            $('#editModal').on('show.bs.modal', function(event) {
                var button = $(event.relatedTarget);
                var index = button.data('index');
                var nome = button.data('nome');
                var quantidade = button.data('quantidade');
                var preco = button.data('preco');

                var modal = $(this);
                modal.find('#itemId').val(index);
                modal.find('#editName').val(nome);
                modal.find('#editQuantity').val(quantidade);
                modal.find('#editValue').val(preco);
            });

            // Salvar alterações
            document.getElementById('saveChangesBtn').onclick = function() {
                var index = document.getElementById('itemId').value;
                var nome = document.getElementById('editName').value;
                var quantidade = document.getElementById('editQuantity').value;
                var preco = document.getElementById('editValue').value;

                var form = document.createElement("form");
                form.method = "post";
                form.action = ""; // Ação para o mesmo arquivo

                var inputNome = document.createElement("input");
                inputNome.type = "hidden";
                inputNome.name = "produto";
                inputNome.value = nome.toUpperCase();
                form.appendChild(inputNome);

                var inputQuantidade = document.createElement("input");
                inputQuantidade.type = "hidden";
                inputQuantidade.name = "quantidade";
                inputQuantidade.value = quantidade;
                form.appendChild(inputQuantidade);

                var inputPreco = document.createElement("input");
                inputPreco.type = "hidden";
                inputPreco.name = "preco";
                inputPreco.value = preco;
                form.appendChild(inputPreco);

                var inputIndex = document.createElement("input");
                inputIndex.type = "hidden";
                inputIndex.name = "index";
                inputIndex.value = index;
                form.appendChild(inputIndex);

                var inputAcao = document.createElement("input");
                inputAcao.type = "hidden";
                inputAcao.name = "acao";
                inputAcao.value = "Editar";
                form.appendChild(inputAcao);

                document.body.appendChild(form);
                form.submit();
            };
        </script>
    </div>
</body>

</html>
