<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Resultado</title>
</head>
<body>
    <main>
        <h1>Produtos adicionados</h1>
        <p>
            <?php 
            session_start();

$_SESSION['carrinho'][] = '';
$_SESSION = "produto";
echo "$_SESSION";
          
          
          
          //  $p = $_REQUEST["produto"] ?? 0;
           // $r = "res";

           // echo "$p"
            ?>
        </p>
    </main>
</body>
</html>