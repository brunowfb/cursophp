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
    if ($_POST['acao'] == 'Adicionar' && !empty($produto) && $quantidade >= 1 && $preco > 0) { // Verificação de quantidade
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
            $_SESSION['carrinho'][$index]['preco'] = $preco;
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

// Definir valores para edição
$produto_editar = '';
$quantidade_editar = 1; // Definido para 1 como padrão
$preco_editar = 0.00;
$index_editar = null;

if (isset($_POST['acao']) && $_POST['acao'] == 'Abrir Modal') {
    $index_editar = intval($_POST['index']);
    if (isset($_SESSION['carrinho'][$index_editar])) {
        $produto_editar = $_SESSION['carrinho'][$index_editar]['produto'];
        $quantidade_editar = $_SESSION['carrinho'][$index_editar]['quantidade'];
        $preco_editar = $_SESSION['carrinho'][$index_editar]['preco'];
    }
}

// Formatação do preço para o campo de entrada
$preco_formatado_para_input = number_format($preco_editar, 2, '.', ''); // Para input (ponto como separador decimal)
$preco_formatado_para_exibir = number_format($preco_editar, 2, ',', '.'); // Para exibição (vírgula como separador decimal)
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
            gap: 5px; /* Espaçamento entre os botões */
            align-items: center;
        }

        .botoes button {
            font-size: 0.8em; /* Tamanho pequeno para os botões */
            padding: 5px; /* Ajuste de padding */
            cursor: pointer;
        }
    </style>
</head>

<body>
    <h1 style="color: white">Carrinho de Compras</h1>
    <main>
        <form id="cabecalho" method="post">
            <div style="display: flex; justify-content: center; gap: 10px">
                <input style="max-width: 45%;" type="text" name="produto" placeholder="Digite um produto" value="<?php echo htmlspecialchars($produto_editar); ?>">
                <input style="width: 20%;" type="number" name="quantidade" id="quantidade" placeholder="QT." min="1" value="<?php echo $quantidade_editar; ?>" required>
                <input style="width: 30%;" type="number" name="preco" id="preco" placeholder="Valor" step="0.01" min="0" value="<?php echo $preco_formatado_para_input; ?>" required>
            </div>
            <div style="display: flex; justify-content: flex-end; gap: 10px">
                <button style="width: 25%; background-color: green" class='btn btn-primary' name="acao" value="Adicionar">Adicionar</button>
                <button style="width: 25%; background-color: red" class='btn btn-primary' name="acao" value="Excluir tudo">Excluir tudo</button>
            </div>
        </form>
        <h2 id="fundo" style="display:flex; justify-content:center; padding-bottom: 20px; padding-top: 20px;">Produtos adicionados:</h2>
        <ul style="display: flex; font-size: 1.6em; font-weight:bold; gap: 10px; flex-direction: column;">
            <?php
            $total = 0; // Inicializa o total
            if (empty($_SESSION['carrinho'])) {
                echo "<li>Carrinho vazio</li>";
            } else {
                foreach ($_SESSION['carrinho'] as $index => $item) {
                    if (is_array($item) && isset($item['produto']) && isset($item['quantidade']) && isset($item['preco'])) {
                        $preco_formatado = number_format($item['preco'], 2, ',', '.'); // Formatação para exibição
                        $total += $item['preco']; // Adiciona ao total
                        echo "<li style='display: flex; justify-content: space-between; align-items: center;'>" . htmlspecialchars($item['quantidade']) . " - " . htmlspecialchars($item['produto']) . " : R$ " . $preco_formatado .
                            "<div class='botoes'>
                                <button type='button' class='btn btn-primary' data-toggle='modal' data-target='#editModal' data-index='$index' data-nome='" . htmlspecialchars($item['produto']) . "' data-quantidade='" . $item['quantidade'] . "' data-preco='" . number_format($item['preco'], 2, '.', '') . "'>📝</button>
                                <form method='post' style='display:inline;'>
                                    <input type='hidden' name='index' value='$index'>
                                    <button class='btn btn-warning' name='acao' value='Excluir'>❌</button>
                                </form>
                              </div>
                             </li>";
                    }
                }
            }
            ?>
        </ul>
        <h3 class="tot">Total: R$ <?php echo number_format($total, 2, ',', '.'); ?></h3>
    </main>

    <!-- Modal -->
    <div class="modal fade" id="editModal" tabindex="-1" role="dialog" aria-labelledby="editModalLabel" aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="editModalLabel">Editar Item</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <form id="editForm" method="post">
                        <input type="hidden" id="itemId" name="index" value="">
                        <div class="form-group">
                            <label for="editName">Nome:</label>
                            <input type="text" class="form-control" id="editName" name="produto" required>
                        </div>
                        <div class="form-group">
                            <label for="editQuantity">Quantidade:</label>
                            <input type="number" class="form-control" id="editQuantity" name="quantidade" required>
                        </div>
                        <div class="form-group">
                            <label for="editValue">Valor:</label>
                            <input type="number" class="form-control" id="editValue" name="preco" required>
                        </div>
                    </form>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Fechar</button>
                    <button type="button" class="btn btn-primary" id="saveChangesBtn">Salvar Alterações</button>
                </div>
            </div>
        </div>
    </div>

    <script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script> <!-- jQuery -->
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.9.2/dist/umd/popper.min.js"></script> <!-- Popper.js -->
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script> <!-- Bootstrap JS -->
    <script>
        // Quando o modal for aberto, preenche os campos com os dados do item
        $('#editModal').on('show.bs.modal', function (event) {
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
            var index = document.getElementById('itemId').value;  // Obter o índice do item
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
