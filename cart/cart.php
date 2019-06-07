<!DOCTYPE html>
<html>
  <head>
    <title>Carrinho de Compras</title>
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.6/css/bootstrap.min.css"/>
    <link rel="stylesheet" href="cart.css" />
  </head>
<body>


<?php
//conecta ao banco de dados e seleciona os dados da tabela products em ordem crescente
$connect = mysqli_connect('localhost', 'root', '1234567', 'cart');
$query = 'SELECT * FROM products ORDER BY id ASC';

//executa a query e salva dentro da variável result
$result = mysqli_query($connect, $query);

//checa se a tabela produtos não está vazia
if ($result){
    if (mysqli_num_rows($result)>0){
      while ($product = mysqli_fetch_assoc($result)){
        print_r($product);
      }
    }
}
?>






</body>
</html>
