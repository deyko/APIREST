
<!DOCTYPE html>
<html>
<body>




<form action="/Slim2/index.php/productos" method="POST">

  idCategoria: <input type="text" name="idCategoria"><br>
  Nombre: <input type="text" name="Nombre"><br>
  Descripcion: <input type="text" name="Descripcion"><br>
  Precio: <input type="text" name="Precio"><br>

  <input type="submit" value="Submit">
</form>

<form action="/Slim2/index.php/productos" method="get">
  
  <input type="submit" value="Enviar">

</form>

<?php
echo '¡Hola ' . ($_GET["Nombre"]) . '!';
?>

  



</body>
</html>

