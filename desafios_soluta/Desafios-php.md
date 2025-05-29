# Issue 1: Simulador de Carrinho de Compras

## Descrição do Problema
Criar um simulador de carrinho de compras simples, onde o usuário pode adicionar itens ao carrinho repetidamente. Cada item deve conter:
- Nome
- Quantidade
- Preço

### Requisitos

1. O sistema deve exibir o total acumulado do carrinho.
2. Deve haver um menu que permita ao usuário:
   - Continuar adicionando itens.
   - Finalizar a compra.
3. Ao finalizar, exibir uma tabela com os itens do carrinho e o preço total.
Exemplo:

| Nome        | Quantidade| Preço         |
|-------------|-----------|---------------|
| Produto 1   | 1         | R$ 10,00      |
| Produto 2   | 1         | R$ 20,00      |
|             | **Total** | **R$ 30,00**  |

### Observações
- É necessário validar se o preço informado é um número.
- Os itens do carrinho devem ser armazenados em uma estrutura que permita fácil manipulação e exibição.

---

# Issue 2: Jogo da Adivinhação

## Descrição do Problema
Criar um jogo onde o programa escolhe aleatoriamente um número entre 1 e 100. O jogador deve adivinhar o número com base nas dicas fornecidas:
- O número correto é maior ou menor que o palpite atual.

### Requisitos

1. Gerar um número aleatório e solicitar palpites do jogador.
2. Exibir mensagens indicando se o número correto é maior ou menor que o palpite fornecido.
3. Finalizar o jogo quando o número for adivinhado e exibir o número de tentativas realizadas.

### Observações
- É necessário validar as entradas do jogador.
- Considere um sistema de pontuação opcional.

---

# Issue 3: Caixa Registradora com Troco

## Descrição do Problema
Criar um programa que simule uma caixa registradora, calculando o troco com base no valor da compra e no valor pago pelo cliente. 

### Requisitos

### Inicialização
1. Informar a quantidade de cada cedula no caixa considerando:
- R$ 200, R$ 100, R$ 50, R$ 20, R$ 10, R$ 5, R$ 2.

### Funcionamento
1. Solicitar o valor da compra e as cedulas fornecidas pelo cliente.
2. Calcular e exibir o troco:
   - Caso o valor pago seja menor, exibir uma mensagem e solicitar outro valor.
3. Determinar a quantidade de cédulas necessárias para o troco.
4. Tentar dar o troco de maneira a usar as cedulas mais altas primeiro.


### Observações
- Validar se os valores informados são numéricos.
- Exibir mensagens claras para cada etapa do processo.

---

# Issue 4: Gerenciamento de Estoque

## Descrição do Problema
Uma loja precisa de um sistema para gerenciar seu estoque de produtos. Atualmente, os dados dos produtos incluem:

- ID único do produto.
- Nome do produto.
- Categoria à qual o produto pertence (Ex.: Eletrônicos, Vestuário, Alimentos).
- Quantidade em estoque.
- Preço unitário do produto.

### Requisitos

1. Permitir a visualização de todos os produtos cadastrados, organizados por categoria.
2. Adicionar novos produtos ao estoque. Certifique-se de que não haja duplicação de IDs.
3. Remover produtos existentes do estoque com base no ID do produto.
4. Atualizar a quantidade de um produto específico. Caso a quantidade fique abaixo de um limite, como 5 unidades, exibir um alerta indicando "Estoque Baixo".
5. Calcular o valor total do estoque para uma categoria específica.
6. Filtrar produtos com base no preço, exibindo apenas aqueles acima de um valor informado.

### Observações
- O sistema deve lidar com categorias variadas e múltiplos produtos por categoria.
- É necessário evitar redundância e garantir a integridade dos dados do estoque.

---

# Issue 5: Gerenciamento de Salas de Cinema

## Descrição do Problema
Um cinema precisa de um sistema para gerenciar suas salas e sessões de filmes. As informações atuais incluem:

- ID único da sala.
- Nome do filme em exibição.
- Horários das sessões disponíveis.
- Capacidade total da sala.
- Lugares ocupados em cada sessão.

### Requisitos

1. Exibir a lista de todas as salas com seus filmes em exibição e horários das sessões.
2. Registrar novos filmes e suas respectivas salas e horários de exibição.
3. Atualizar a ocupação de lugares em uma sessão específica.
4. Exibir uma mensagem de "Sessão Lotada" caso a ocupação alcance a capacidade total da sala.
5. Permitir a busca por filmes para localizar as salas e horários de exibição.
6. Calcular a quantidade de lugares disponíveis para uma sessão específica.

### Observações
- O sistema deve permitir múltiplas sessões por sala em horários diferentes.
- É essencial gerenciar corretamente a ocupação e evitar overbooking.

---
