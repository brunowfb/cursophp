<?php
session_start();

// Iniciar carrinho se ainda não existir
if (!isset($_SESSION['carrinho'])) {
    $_SESSION['carrinho'] = [];
}

// Adicionar ou editar produto
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $produto = strtoupper(trim($_POST['produto'] ?? '')); // Convertendo para maiúsculas
    $quantidade = max(1, intval(trim($_POST['quantidade'] ?? ''))); // Mudei para mínimo de 1
    $preco = max(0, floatval(str_replace(',', '.', trim($_POST['preco'] ?? '')))); // Convertendo vírgula para ponto

    // Ação de Adicionar
    if ($_POST['acao'] == 'Adicionar' && !empty($produto) && $quantidade >= 1 && $preco > 0) {
        $encontrado = false;

        // Verifica se o produto já existe no carrinho
        foreach ($_SESSION['carrinho'] as &$item) {
            if ($item['produto'] === $produto) {
                // Atualiza a quantidade e recalcula o preço total
                $item['quantidade'] += $quantidade;
                $item['preco'] = $item['quantidade'] * $preco; // Preço total é quantidade * preço unitário
                $encontrado = true;
                break;
            }
        }

        // Se não encontrado, adiciona um novo item
        if (!$encontrado) {
            $_SESSION['carrinho'][] = ['produto' => $produto, 'quantidade' => $quantidade, 'preco' => $quantidade * $preco]; // Calcula o preço total
        }
    }

    // Excluir item específico do carrinho
    if ($_POST['acao'] == 'Excluir') {
        $index = intval($_POST['index']);
        if (isset($_SESSION['carrinho'][$index])) {
            unset($_SESSION['carrinho'][$index]);
            $_SESSION['carrinho'] = array_values($_SESSION['carrinho']); // Reindexar o carrinho
        }
    }

    // Editar item específico do carrinho
    if ($_POST['acao'] == 'Editar') {
        $index = intval($_POST['index']);
        if (isset($_SESSION['carrinho'][$index])) {
            $_SESSION['carrinho'][$index]['produto'] = strtoupper($produto); // Atualizando o nome do produto para maiúsculas
            $_SESSION['carrinho'][$index]['quantidade'] = $quantidade;
            $_SESSION['carrinho'][$index]['preco'] = $quantidade * $preco; // Atualiza preço total
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

$total = 0; // Inicializa o total
?>

<!DOCTYPE html>
<html lang="pt-br">

<head>
    <meta charset="UTF-8">
    <title>Carrinho</title>
    <link rel="stylesheet" href="estilo.css">
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css"> <!-- Bootstrap CSS -->
    <style>
        .botoes {
            display: flex;
            justify-content: flex-end;
            gap: 5px;
            /* Espaçamento entre os botões */
            align-items: center;
        }

        .botoes button {
            font-size: 0.8em;
            /* Tamanho pequeno para os botões */
            padding: 5px;
            /* Ajuste de padding */
            cursor: pointer;
        }

        /* Estilização da tabela */
        .table-custom {
            border-collapse: collapse;
        }

        .table-custom th,
        .table-custom td {
            border: 1px solid #d3d3d3;
            /* Cor cinza para as linhas */
            padding: 8px;
            /* Espaçamento interno */
        }

        .table-custom th {
            background-color: #f8f9fa;
            /* Cor de fundo das cabeçalhos */
        }

        .table-custom tbody tr:nth-child(even) {
            background-color: #f2f2f2;
            /* Cor de fundo para linhas pares */
        }

        .table-custom tbody tr:hover {
            background-color: #e0e0e0;
            /* Cor de fundo ao passar o mouse */
        }
    </style>
</head>

<body>
    <h1 style="color: white">Carrinho de Compras</h1>
    <main>
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
                                <input type="number" class="form-control" name="preco" step="0.01" min="0" value="0.00" required>
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
            <button style="width: 38%; min-width: 38%;" class="btn btn-primary" data-toggle="modal" data-target="#myModal">
                Novo produto
            </button>
            <form method="POST">
                <input type="hidden" name="acao" value="Excluir tudo">
                <button style="background-color: red;" class='btn btn-primary'>Excluir tudo</button>
            </form>
        </div>

        <h2 id="fundo" style="display:flex; justify-content:center; padding-bottom: 20px; padding-top: 20px;">Produtos adicionados:</h2>

        <!-- Tabela para exibir os produtos -->

        <table style="text-align: center; border-collapse: separate; border: 2px solid rgb(60, 106, 255);" class="table table-striped table-custom">
            <thead>
                <tr>
                    <th style="text-align: center; color: white; background-color:rgb(60, 106, 255);">QTD.</th>
                    <th style="text-align: center; color: white; background-color: rgb(60, 106, 255);">UN.</th>
                    <th style="text-align: center; color: white; background-color: rgb(60, 106, 255);">PRODUTO</th>
                    <th style="text-align: center; color: white; background-color: rgb(60, 106, 255);">V. UNITARIO</th>
                    <th style="text-align: center; color: white; background-color: rgb(60, 106, 255);">V. TOTAL</th>
                    <th style="text-align: center; color: white; background-color: rgb(60, 106, 255);">-</th>
                </tr>
            </thead>

            <tbody>
                <?php
                if (empty($_SESSION['carrinho'])) {
                    echo "<tr><td colspan='6' class='text-center'>Carrinho vazio</td></tr>";
                } else {
                    foreach ($_SESSION['carrinho'] as $index => $item) {
                        if (is_array($item) && isset($item['produto']) && isset($item['quantidade']) && isset($item['preco'])) {
                            $preco_unitario_formatado = number_format($item['preco'] / $item['quantidade'], 2, ',', '.'); // Calcula o preço unitário
                            $preco_total_formatado = number_format($item['preco'], 2, ',', '.'); // Formatação para exibição
                            $total += $item['preco']; // Adiciona ao total
                            echo "<tr>
                                    <td>" . htmlspecialchars($item['quantidade']) . "</td>
                                    <td>UN</td>
                                    <td>" . htmlspecialchars($item['produto']) . "</td>
                                    <td>R$ " . $preco_unitario_formatado . "</td>
                                    <td>R$ " . $preco_total_formatado . "</td>
                                    <td class='botoes'>
                                        <button type='button' class='btn btn-primary' data-toggle='modal' data-target='#editModal' data-index='$index' data-nome='" . htmlspecialchars($item['produto']) . "' data-quantidade='" . $item['quantidade'] . "' data-preco='" . number_format($item['preco'] / $item['quantidade'], 2, '.', '') . "'>📝</button>
                                        <form method='post' style='display:inline;'>
                                            <input type='hidden' name='index' value='$index'>
                                            <button class='btn btn-danger' name='acao' value='Excluir' style='background-color: red; color:white; width:30px;'>X</button>
                                        </form>
                                    </td>
                                  </tr>";
                        }
                    }
                }
                ?>
            </tbody>
        </table>
        <h3 class="tot">Total: R$ <?php echo number_format($total, 2, ',', '.'); ?></h3>
     
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
                                            <input type="number" class="form-control" id="editValue" name="preco" required>
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

    <script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script> <!-- jQuery -->
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.9.2/dist/umd/popper.min.js"></script> <!-- Popper.js -->
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script> <!-- Bootstrap JS -->
    <script>
        // Quando o modal for aberto, preenche os campos com os dados do item
        $('#editModal').on('show.bs.modal', function(event) {
            var button = $(event.relatedTarget); // Botão que abriu o modal
            var index = button.data('index'); // Extrai os dados do atributo data-*
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
            var index = document.getElementById('itemId').value; // Obter o índice do item
            var nome = document.getElementById('editName').value; // Obter o nome editado
            var quantidade = document.getElementById('editQuantity').value; // Obter quantidade editada
            var preco = document.getElementById('editValue').value; // Obter preço editado

            // Criar um formulário para enviar os dados
            var form = document.createElement("form");
            form.method = "post";
            form.action = ""; // Ação para o mesmo arquivo

            // Criar e adicionar campos ocultos ao formulário
            var inputNome = document.createElement("input");
            inputNome.type = "hidden";
            inputNome.name = "produto";
            inputNome.value = nome.toUpperCase(); // Converter nome para maiúsculas
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
            inputIndex.name = "index"; // Para identificar qual item está sendo editado
            inputIndex.value = index;
            form.appendChild(inputIndex);

            var inputAcao = document.createElement("input");
            inputAcao.type = "hidden";
            inputAcao.name = "acao"; // Ação de edição
            inputAcao.value = "Editar";
            form.appendChild(inputAcao);

            document.body.appendChild(form); // Anexar o formulário ao corpo do documento
            form.submit(); // Enviar o formulário
        };
    </script>

</body>

</html>