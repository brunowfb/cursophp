<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Resultado</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
   <main>
        <h1>Número gerado</h1>
        <p>
            <?php
                #include <stdio.h>
                #include <stdlib.h>
                #include <time.h>
  
                $aleat = mt_rand(0, 1000);

                echo "Gerando um número aleatório entre 0 e 1000...
               <br>
                O valor gerado é <strong>$aleat</strong>";
            ?>
        </p>
         <p><a href="javascript:history.go(0)">&#x1F504 Gerar outro</a></p>
   </main>
</body>
</html>