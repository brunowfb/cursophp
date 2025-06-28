<!DOCTYPE html>
<html lang="pt-br">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gerenciador de Estoque</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">

    <style>
        * {
            margin: 0px !important;
            padding: 0px !important;
            box-sizing: border-box !important;
        }

        body {
            background-color: #f1f1f1 !important;
        }

        .container {
            display: flex !important;
            flex-direction: row !important;
        }

        .vert-esquerdo {
            display: flex !important;
            background-color: rgb(46, 54, 64) !important;
            max-width: 30% !important;
            height: 100vh !important;
        }

        .vert-direito {
            display: flex !important;
            background-color: white !important;
            height: 100vh;
            width: 100% !important;
        }

        .card-form {
            max-width: 600px !important;
            margin: 50px auto !important;
            background-color: #fff !important;
            padding: 30px !important;
            border-radius: 10px !important;
            box-shadow: 0 0 15px rgba(136, 116, 116, 0.1) !important;
        }

        .btn-primary {
            width: 100% !important;
        }

        form>ul>li {
            display: flex !important;
            font-size: 2em !important;
            padding: 10px !important;
            color: white !important;
            font-weight: bold !important;
            justify-content: center !important;
            align-items: center !important;
        }
    </style>
</head>

<body>
    <div class="container">
        <div class="vert-esquerdo">
            <form action="">
                <ul>
                    <li>Produtos</li>
                </ul>
            </form>
        </div>
        <div class="vert-direito">
            <div class="card-form">
                <h3 class="text-center mb-4">Cadastrar Produto</h3>
                <form action="cadastrar_produto.php" method="post">
                    <div class="form-group">
                        <label for="nome">Nome do Produto:</label>
                        <input type="text" class="form-control" id="nome" name="nome" required>
                    </div>
                    <div class="form-group">
                        <label for="descricao">Descrição:</label>
                        <textarea class="form-control" id="descricao" name="descricao" rows="3"></textarea>
                    </div>
                    <div class="form-row">
                        <div class="form-group col-md-6">
                            <label for="quantidade">Quantidade em Estoque:</label>
                            <input type="number" class="form-control" id="quantidade" name="quantidade" min="0" required>
                        </div>
                        <div class="form-group col-md-6">
                            <label for="preco">Preço (R$):</label>
                            <input type="text" class="form-control" id="preco" name="preco" placeholder="0,00" required>
                        </div>
                    </div>
                    <button type="submit" class="btn btn-primary">Salvar Produto</button>
                </form>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.9.2/dist/umd/popper.min.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
</body>

</html>