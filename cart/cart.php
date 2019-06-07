<?php
//inicia a sessão
session_start();
$product_ids = array();
//session_destroy();

//checa se o botão "adicionar para o carrinho" foi enviado
if (filter_input(INPUT_POST, 'add_to_cart')) {
    if(isset($_SESSION['shopping_cart'])) {
              //checa quantos produtos estão no carrinho
              $count = count($_SESSION['shopping_cart']);

              //cria um array sequencial que liga a chave do array com o id do produto
              $product_ids = array_column($_SESSION['shopping_cart'], 'id');

              //checa se o produto com o id especificado já existe dentro do array
              if (!in_array(filter_input(INPUT_GET, 'id'), $product_ids)){
                $_SESSION['shopping_cart'][$count] = array
                  (
                    'id' => filter_input(INPUT_GET, 'id'),
                    'name' => filter_input(INPUT_POST, 'name'),
                    'price' => filter_input(INPUT_POST, 'price'),
                    'quantity' => filter_input(INPUT_POST, 'quantity')
                  );
              }
              else { //caso produto já exista dentro da variável carrinho, aumentar a quantidade da "chave" do produto
                //iguala a chave do array com o id do produto adicionado
                for ($i = 0; $i < count($product_ids); $i++){
                    if ($product_ids[$i] == filter_input(INPUT_GET, 'id')){
                      $_SESSION['shopping_cart'][$i]['quantity'] += filter_input(INPUT_POST, 'quantity');
                    }
                }
              }
    }
    else { //se o carrinho não existir, cria um array com o primeiro produto com chave 0
      //cria um array usando os dados enviados pelo formulário, começa da chave 0 e enche com valores dependendo do produto selecionado
      $_SESSION['shopping_cart'][0] = array
        (
          'id' => filter_input(INPUT_GET, 'id'),
          'name' => filter_input(INPUT_POST, 'name'),
          'price' => filter_input(INPUT_POST, 'price'),
          'quantity' => filter_input(INPUT_POST, 'quantity')
        );
    }
}

//método para deletar um item do carrinho
if (filter_input(INPUT_GET, 'action') == 'delete'){

  //faz um loop por todo os produtos do carrinho até encontrar um que corresponda a variável GET ID
  foreach($_SESSION['shopping_cart'] as $key => $sproduct){
    if ($product['id'] == filter_input(INPUT_GET, 'id')){
      //remove o produto do carrinho quando corresponde a variável GET ID
      unset($_SESSION['shopping_cart'][$key]);
    }
  }
  //reseta as chaves do array da sessão para que elas correspondam com array númerico do $product_ids
  $_SESSION['shopping_cart'] = array_values($_SESSION['shopping_cart']);
}


//pre_r($_SESSION);

function pre_r($array) {
  echo '<pre>';
  print_r($array);
  echo  '</pre>';
}

?>

<!DOCTYPE html>
<html>
  <head>
    <title>Carrinho de Compras</title>
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.6/css/bootstrap.min.css"/>
    <link rel="stylesheet" href="cart.css" />
  </head>
<body>
<!--printa todos os produtos dentro de um container bootstrap-->
  <div class="container">
  <?php
  //conecta ao banco de dados e seleciona os dados da tabela products em ordem crescente
  $connect = mysqli_connect('localhost', 'root', '1234567', 'cart');
  $query = 'SELECT * FROM products ORDER BY id ASC';

  //executa a query e salva dentro da variável result
  $result = mysqli_query($connect, $query);

  //checa se a tabela produtos não está vazia
  if ($result):
      if (mysqli_num_rows($result)>0):
        while ($product = mysqli_fetch_assoc($result)):
        ?>
        <!--printa os dados da tabela products dentro de colunas bootstrap responsivas-->
        <div class="col-sm-4 col-md-3">
          <form method="post" action="cart.php?action=add&id=<?php echo $product['id']; ?>">
              <div class="products">
                <img src="<?php echo $product['image']; ?>" class="img-responsive"/>
                <h4 class="text-info"><?php echo $product['name'];?></h4>
                <h4>$<?php echo $product['price']; ?></h4>
                <input type="text" name="quantity" class="form-control" value="1" />
                <input type="hidden" name="name" value="<?php echo $product['name'];?>"/>
                <input type="hidden" name="price" value="<?php echo $product['price'];?>"/>
                <input type="submit" name="add_to_cart" style="margin-top:5px" class="btn btn-info" value="Add to Cart" />

              </div>
          </form>
        </div>

        <?php
        endwhile;
      endif;
  endif;
  ?>
  <div style="clear:both"> </div>
  <br/>
  <!-- Tabela que exibe o nome, quantidade, preço, total e ação do item dentro do carrinho -->
  <div class="table-responsive">
  <table class="table">
    <tr><th colspan="5"><h3>Detalhes do Pedido</h3></th></tr>
  <tr>
      <th width="40%">Nome do Produto</th>
      <th width="10%">Quantidade</th>
      <th width="20%">Preço</th>
      <th width="15%">Total</th>
      <th width="5%">Ação</th>
  </tr>
  <?php
  //certifica que o array da sessão não está vazio
  if(!empty($_SESSION['shopping_cart'])):

  //variável que servirá para somar o valor total
  $total = 0;

  //faz um loop no array da sessão
  foreach($_SESSION['shopping_cart'] as $key => $product):
  ?>
  <tr>
    <!-- Imprime os valores do carrinho na tabela -->
    <td><?php echo $product['name'];  ?></td>
    <td><?php echo $product['quantity'];  ?></td>
    <td><?php echo $product['price'];  ?></td>
    <td><?php echo number_format($product['quantity'] * $product['price'], 2); ?></td>
    <td>
        <a href="cart.php?action=delete&id=<?php echo $product['id']; ?>">
            <div class="btn-danger">Remover</div>
        </a>
    </td>
  <tr>
  <?php
      //conta do total
      $total = $total + ($product['quantity'] * $product['price']);
    endforeach;
  ?>
  <tr>
      <td colspan="3" align="right">Total</td>
      <td align="right">$<?php echo number_format($total, 2); ?></td>
      <td></td>
  </tr>
  <tr>
      <!-- Mostra o botão de comprar só se o carrinho não estiver vazio -->
      <td colspan="5">
      <?php
          if (isset($_SESSION['shopping_cart'])):
          if (count($_SESSION['shopping_cart']) > 0):
      ?>
        <a href="#" class="button">Comprar</a>
      <?php endif; endif; ?>
    </td>
  </tr>
      <?php
      endif;
      ?>
    </table>
  </div>
</div>


</body>
</html>
