<?php
// Activamos las sesiones para el funcionamiento de flash['']
@session_start();

require 'Slim/Slim.php';
// El framework Slim tiene definido un namespace llamado Slim
// Por eso aparece \Slim\ antes del Nombre de la clase.
\Slim\Slim::registerAutoloader();

// Creamos la aplicación.
$app = new \Slim\Slim();

// Configuramos la aplicación. http://docs.slimframework.com/#Configuration-Overview
// Se puede hacer en la línea anterior con:
// $app = new \Slim\Slim(array('templates.path' => 'vistas'));
// O bien con $app->config();
$app->config(array(
    'templates.path' => 'vistas',
));

// Indicamos el tipo de contenido y condificación que devolvemos desde el framework Slim.
$app->contentType('text/html; charset=utf-8');

// Definimos conexion de la base de datos.
// Lo haremos utilizando PDO con el driver mysql.
define('BD_SERVIDOR', 'localhost');
define('BD_NOMBRE', 'bsdqarmita');
define('BD_USUARIO', 'root');
define('BD_PASSWORD', 'root');

// Hacemos la conexión a la base de datos con PDO.
// Para activar las collations en UTF8 podemos hacerlo al crear la conexión por PDO
// o bien una vez hecha la conexión con
// $db->exec("set names utf8");
$db = new PDO('mysql:host=' . BD_SERVIDOR . ';dbname=' . BD_NOMBRE . ';charset=utf8', BD_USUARIO, BD_PASSWORD);

///////////////////////////////////////////////////////////////////////////////////
// Definición de rutas en la aplicación:
// Ruta por defecto de la aplicación /
///////////////////////////////////////////////////////////////////////////////////

$app->get('/', function() {
            echo "Pagina de gestión API REST de la Qarmita.";
        });



$app->get('/productos', function() use($db) {
            // Si necesitamos acceder a alguna variable global en el framework
            // Tenemos que pasarla con use() en la cabecera de la función. Ejemplo: use($db)
            // Va a devolver un objeto JSON con los datos de productos.
            // Preparamos la consulta a la tabla.

            $consulta = $db->prepare("select * from productos");
            $consulta->execute();
            // Almacenamos los resultados en un array asociativo.
            $resultados = $consulta->fetchAll(PDO::FETCH_ASSOC);
            //$result = ["productos"=>$resultados];
            // Devolvemos ese array asociativo como un string JSON.
            echo json_encode($resultados);
        });

        $app->get('/productoscategoria/:idcat', function($categoriaID) use($db) {
        
            $consulta = $db->prepare("select * from productos where idCategoria=:param1");

            // En el execute es dónde asociamos el :param1 con el valor que le toque.
            $consulta->execute(array(':param1' => $categoriaID));

            // Almacenamos los resultados en un array asociativo.
            $resultados = $consulta->fetchAll(PDO::FETCH_ASSOC);

            // Devolvemos ese array asociativo como un string JSON.
            echo json_encode($resultados);
        });


$app->get('/productos/:idproducto', function($productoID) use($db) {
        
            $consulta = $db->prepare("select * from productos where idProducto=:param1");

            // En el execute es dónde asociamos el :param1 con el valor que le toque.
            $consulta->execute(array(':param1' => $productoID));

            // Almacenamos los resultados en un array asociativo.
            $resultados = $consulta->fetchAll(PDO::FETCH_ASSOC);

            // Devolvemos ese array asociativo como un string JSON.
            echo json_encode($resultados);
        });


// Alta de productos en la API REST
$app->post('/productos',function() use($db,$app) {
    // Para acceder a los datos recibidos del formulario
    $datosform=$app->request;
    
    // Los datos serán accesibles de esta forma:
    // $datosform->post('Descricion')
    
    // Preparamos la consulta de insert.
    $consulta=$db->prepare("insert into productos(idCategoria,Nombre,Descripcion,Precio) 
					values (:idCategoria,:Nombre,:Descripcion,:Precio)");
    
    $estado=$consulta->execute(
            array(
                ':idCategoria'=> $datosform->post('idCategoria'),
                ':Nombre'=> $datosform->post('Nombre'),
                ':Descripcion'=> $datosform->post('Descripcion'),
                ':Precio'=> $datosform->post('Precio')
                )
            );
    if ($estado)
        echo json_encode(array('estado'=>true,'mensaje'=>'Datos insertados correctamente.'));
    else
        echo json_encode(array('estado'=>false,'mensaje'=>'Error al insertar datos en la tabla.'));
});


// Programamos la ruta de borrado en la API REST (DELETE)
$app->get('/productosdelete/:idProducto',function($idProducto) use($db)
{
   $consulta=$db->prepare("delete from productos where idProducto=:id");
   echo $idProducto;
   $consulta->execute(array(':id'=>$idProducto));
   
if ($consulta->rowCount() == 1)
   echo json_encode(array('estado'=>true,'mensaje'=>'El producto '.$idProducto.' ha sido borrado correctamente.'));
 else
   echo json_encode(array('estado'=>false,'mensaje'=>'ERROR: ese registro no se ha encontrado en la tabla.'));
    
});


// Actualización de datos de usuario (PUT)
$app->put('/usuarios/:idCategoria',function($idCategoria) use($db,$app) {
    // Para acceder a los datos recibidos del formulario
    $datosform=$app->request;
    
    // Los datos serán accesibles de esta forma:
    // $datosform->post('Descricion')
    
    // Preparamos la consulta de update.
    $consulta=$db->prepare("update soporte_usuarios set Nombre=:Nombre, Descricion=:Descricion, Precio=:Precio 
							where idCategoria=:idCategoria");
    
    $estado=$consulta->execute(
            array(
                ':idCategoria'=>$idCategoria,
                ':Nombre'=> $datosform->post('Nombre'),
                ':Descricion'=> $datosform->post('Descricion'),
                ':Precio'=> $datosform->post('Precio')
                )
            );
    
    // Si se han modificado datos...
    if ($consulta->rowCount()==1)
      echo json_encode(array('estado'=>true,'mensaje'=>'Datos actualizados correctamente.'));
    else
      echo json_encode(array('estado'=>false,'mensaje'=>'Error al actualizar datos, datos 
						no modificados o registro no encontrado.'));
});

////////////////////////////////////////////////////////////////////////////////////////////////
////////////////////////USUARIO////////////////////////USUARIO/////////////////////////////////
////////////////////////////////////////////////////////////////////////



                            $app->get('/usuarios', function() use($db) {
                        
                                $consulta = $db->prepare("select * from usuarios");
                                $consulta->execute();
                                // Almacenamos los resultados en un array asociativo.
                                $resultados = $consulta->fetchAll(PDO::FETCH_ASSOC);
                                // Devolvemos ese array asociativo como un string JSON.
                                echo json_encode($resultados);
                            });
                    
                    
                    $app->get('/usuarios/:idusuario', function($usuarioID) use($db) {
                            
                                $consulta = $db->prepare("select * from usuarios where idusuario=:param1");
                    
                                // En el execute es dónde asociamos el :param1 con el valor que le toque.
                                $consulta->execute(array(':param1' => $usuarioID));
                    
                                // Almacenamos los resultados en un array asociativo.
                                $resultados = $consulta->fetchAll(PDO::FETCH_ASSOC);
                    
                                // Devolvemos ese array asociativo como un string JSON.
                                echo json_encode($resultados);
                            });
                    
                    
                    // Alta de productos en la API REST
                    $app->post('/usuarios',function() use($db,$app) {
                        // Para acceder a los datos recibidos del formulario
                        $datosform=$app->request;
                        
                        // Los datos serán accesibles de esta forma:
                        // $datosform->post('Descricion')
                        
                        // Preparamos la consulta de insert.
                        $consulta=$db->prepare("insert into usuarios(Nombre,Apellidos,Nick,Password) 
                                        values (:Nombre,:Apellidos,:Nick,:Password)");
                        
                        $estado=$consulta->execute(
                                array(
                                    
                                    ':Nombre'=> $datosform->post('Nombre'),
                                    ':Apellidos'=> $datosform->post('Apellidos'),
                                    ':Nick'=> $datosform->post('Nick'),
                                    ':Password'=> $datosform->post('Password')
                                    )
                                );
                        if ($estado)
                            echo json_encode(array('estado'=>true,'mensaje'=>'Datos insertados correctamente.'));
                        else
                            echo json_encode(array('estado'=>false,'mensaje'=>'Error al insertar datos en la tabla.'));
                    });
                    
                    
                    // Programamos la ruta de borrado en la API REST (DELETE)
                    $app->get('/usuariosdelete/:idUsuario',function($idUsuario) use($db)
                    {
                       $consulta=$db->prepare("delete from usuarios where idUsuario=:id");
                       echo $idUsuario;
                       $consulta->execute(array(':id'=>$idUsuario));
                       
                    if ($consulta->rowCount() == 1)
                       echo json_encode(array('estado'=>true,'mensaje'=>'El Usuario'.$idUsuario.' ha sido borrado correctamente.'));
                     else
                       echo json_encode(array('estado'=>false,'mensaje'=>'ERROR: ese registro no se ha encontrado en la tabla.'));
                        
                    });





////////////////////////////////////////////////////////////////////////////////////////////////
////////////////////////CATEGORIAS////////////////////////CATEGORIAS/////////////////////////////////
////////////////////////////////////////////////////////////////////////

$app->get('/categorias', function() use($db) {
  
    $consulta = $db->prepare("select * from categorias");
    $consulta->execute();
    // Almacenamos los resultados en un array asociativo.
    $resultados = $consulta->fetchAll(PDO::FETCH_ASSOC);
    // Devolvemos ese array asociativo como un string JSON.
    echo json_encode($resultados);
});


$app->get('/categorias/:idcategoria', function($categoriaID) use($db) {

    $consulta = $db->prepare("select * from categorias where idcategoria=:param1");

    // En el execute es dónde asociamos el :param1 con el valor que le toque.
    $consulta->execute(array(':param1' => $categoriaID));

    // Almacenamos los resultados en un array asociativo.
    $resultados = $consulta->fetchAll(PDO::FETCH_ASSOC);

    // Devolvemos ese array asociativo como un string JSON.
    echo json_encode($resultados);
});


// Alta de productos en la API REST
$app->post('/categorias',function() use($db,$app) {
    // Para acceder a los datos recibidos del formulario
    $datosform=$app->request;

    // Los datos serán accesibles de esta forma:
    // $datosform->post('Descricion')

    // Preparamos la consulta de insert.
    $consulta=$db->prepare("insert into categorias(Nombre) 
                values (:Nombre)");

    $estado=$consulta->execute(
        array(      
            ':Nombre'=> $datosform->post('Nombre')
            )
        );
    if ($estado)
    echo json_encode(array('estado'=>true,'mensaje'=>'Datos insertados correctamente.'));
    else
    echo json_encode(array('estado'=>false,'mensaje'=>'Error al insertar datos en la tabla.'));
});


// Programamos la ruta de borrado en la API REST (DELETE)
$app->get('/categoriasdelete/:idcategoria',function($idcategoria) use($db)
{
    $consulta=$db->prepare("delete from categorias where idcategoria=:id");
    echo $idcategoria;
    $consulta->execute(array(':id'=>$idcategoria));

    if ($consulta->rowCount() == 1)
    echo json_encode(array('estado'=>true,'mensaje'=>'La categoria '.$idcategoria.' ha sido borrada correctamente.'));
    else
    echo json_encode(array('estado'=>false,'mensaje'=>'ERROR: ese registro no se ha encontrado en la tabla.'));

});


// Actualización de datos de usuario (PUT)
$app->put('/categorias/:idCategoria',function($idCategoria) use($db,$app) {

    $datosform=$app->request;
    $consulta=$db->prepare("update categorias set Nombre=:Nombre 
                        where idCategoria=:idCategoria");

    $estado=$consulta->execute(
        array(    
            ':Nombre'=> $datosform->post('Nombre'),
            )
        );

    // Si se han modificado datos...
    if ($consulta->rowCount()==1)
    echo json_encode(array('estado'=>true,'mensaje'=>'Datos actualizados correctamente.'));
    else
    echo json_encode(array('estado'=>false,'mensaje'=>'Error al actualizar datos, datos 
                    no modificados o registro no encontrado.'));
});

////////////////////////////////////////////////////////////////////////////////////////////////
////////////////////////COMANDAS////////////////////////COMANDAS/////////////////////////////////
////////////////////////////////////////////////////////////////////////



$app->get('/comandas', function() use($db) {

    $consulta = $db->prepare("select * from comandas");
    $consulta->execute();
    // Almacenamos los resultados en un array asociativo.
    $resultados = $consulta->fetchAll(PDO::FETCH_ASSOC);
    // Devolvemos ese array asociativo como un string JSON.
    echo json_encode($resultados);
});


$app->get('/comandas/:idcomanda', function($comandaID) use($db) {

    $consulta = $db->prepare("select * from comandas where idcomanda=:param1");

    // En el execute es dónde asociamos el :param1 con el valor que le toque.
    $consulta->execute(array(':param1' => $comandaID));

    // Almacenamos los resultados en un array asociativo.
    $resultados = $consulta->fetchAll(PDO::FETCH_ASSOC);

    // Devolvemos ese array asociativo como un string JSON.
    echo json_encode($resultados);
});


// Alta de productos en la API REST
$app->post('/comandas',function() use($db,$app) {
    // Para acceder a los datos recibidos del formulario
    $datosform=$app->request;
                                                            //Fechainicio, FechaFin
    $consulta=$db->prepare("insert into comandas(idMesa,idUsuario) 
                values (:idMesa,:idUsuario)");

    $estado=$consulta->execute(
        array(
            ':idMesa'=> $datosform->post('idMesa'),
            ':idUsuario'=> $datosform->post('idUsuario')
            )
        );
    if ($estado)
    echo json_encode(array('estado'=>true,'mensaje'=>'Datos insertados correctamente.'));
    else
    echo json_encode(array('estado'=>false,'mensaje'=>'Error al insertar datos en la tabla.'));
});


// Programamos la ruta de borrado en la API REST (DELETE)
$app->get('/comandasdelete/:idcomanda',function($idcomanda) use($db){

    $consulta=$db->prepare("delete from comandas where idcomanda=:id");
    echo $idcomanda;
    $consulta->execute(array(':id'=>$idcomanda));

    if ($consulta->rowCount() == 1)
    echo json_encode(array('estado'=>true,'mensaje'=>'La comanda '.$idcomanda.' ha sido borrada correctamente.'));
    else
    echo json_encode(array('estado'=>false,'mensaje'=>'ERROR: ese registro no se ha encontrado en la tabla.'));

});


// Actualización de datos  (PUT)
$app->put('/comandas/:idcomanda',function($idcomanda) use($db,$app) {
    // Para acceder a los datos recibidos del formulario
    $datosform=$app->request;

    $consulta=$db->prepare("update comandas set idMesa=:idMesa, idUsuario=:idUsuario 
                        where idcomanda=:idcomanda");

    $estado=$consulta->execute(
        array(
            ':idCategoria'=>$idCategoria,
            ':Nombre'=> $datosform->post('Nombre'),
            ':Descricion'=> $datosform->post('Descricion'),
            ':Precio'=> $datosform->post('Precio')
            )
        );

    // Si se han modificado datos...
    if ($consulta->rowCount()==1)
    echo json_encode(array('estado'=>true,'mensaje'=>'Datos actualizados correctamente.'));
    else
    echo json_encode(array('estado'=>false,'mensaje'=>'Error al actualizar datos, datos 
                    no modificados o registro no encontrado.'));
});

////////////////////////////////////////////////////////////////////////////////////////////////
////////////////////////COMANDAS Y PRODUCTOS////////////////////////COMANDAS Y PRODUCTOS////////
////////////////////////////////////////////////////////////////////////

$app->get('/comandasproductos', function() use($db) {

    $consulta = $db->prepare("select * from comandasproductos");
    $consulta->execute();
    // Almacenamos los resultados en un array asociativo.
    $resultados = $consulta->fetchAll(PDO::FETCH_ASSOC);
    // Devolvemos ese array asociativo como un string JSON.
    echo json_encode($resultados);
});


$app->get('/comandasproductos/:idcomandasproductos', function($comandasproductosID) use($db) {

    $consulta = $db->prepare("select * from comandasproductos where idcomandasproductos=:param1");

    // En el execute es dónde asociamos el :param1 con el valor que le toque.
    $consulta->execute(array(':param1' => $comandasproductosID));

    // Almacenamos los resultados en un array asociativo.
    $resultados = $consulta->fetchAll(PDO::FETCH_ASSOC);

    // Devolvemos ese array asociativo como un string JSON.
    echo json_encode($resultados);
});


// Alta de productos en la API REST
$app->post('/comandasproductos',function() use($db,$app) {
    // Para acceder a los datos recibidos del formulario
    $datosform=$app->request;
                                                            //Fechainicio, FechaFin
    $consulta=$db->prepare("insert into comandasproductos(idComanda,idProducto,Cantidad,Precio) 
                values (:idComanda,:idProducto,:Cantidad,Precio)");

    $estado=$consulta->execute(
        array(
            ':idComanda'=> $datosform->post('idComanda'),
            ':idProducto'=> $datosform->post('idProducto'),
            ':Cantidad'=> $datosform->post('Cantidad'),
            ':Precio'=> $datosform->post('Precio')
            )
        );
    if ($estado)
    echo json_encode(array('estado'=>true,'mensaje'=>'Datos insertados correctamente.'));
    else
    echo json_encode(array('estado'=>false,'mensaje'=>'Error al insertar datos en la tabla.'));
});


// Programamos la ruta de borrado en la API REST (DELETE)
$app->get('/comandasproductos/:idcomandasproductos',function($idcomandasproductos) use($db){

    $consulta=$db->prepare("delete from comandasproductos where idcomandasproductos=:id");
    echo $idcomanda;
    $consulta->execute(array(':id'=>$idcomandasproductos));

    if ($consulta->rowCount() == 1)
    echo json_encode(array('estado'=>true,'mensaje'=>'Comanda productos con id: '.$idcomandasproductos.' ha sido borrada correctamente.'));
    else
    echo json_encode(array('estado'=>false,'mensaje'=>'ERROR: ese registro no se ha encontrado en la tabla.'));

});


// Actualización de datos  (PUT)
$app->put('/comandas/:idcomanda',function($idcomanda) use($db,$app) {
    // Para acceder a los datos recibidos del formulario
    $datosform=$app->request;

    $consulta=$db->prepare("update comandas set idMesa=:idMesa, idUsuario=:idUsuario 
                        where idcomanda=:idcomanda");

    $estado=$consulta->execute(
        array(
            ':idCategoria'=>$idCategoria,
            ':Nombre'=> $datosform->post('Nombre'),
            ':Descricion'=> $datosform->post('Descricion'),
            ':Precio'=> $datosform->post('Precio')
            )
        );

    // Si se han modificado datos...
    if ($consulta->rowCount()==1)
    echo json_encode(array('estado'=>true,'mensaje'=>'Datos actualizados correctamente.'));
    else
    echo json_encode(array('estado'=>false,'mensaje'=>'Error al actualizar datos, datos 
                    no modificados o registro no encontrado.'));
});

////////////////////////////////////////////////////////////////////////////////////////////////
////////////////////////EVENTOS////////////////////////EVENTOS////////
////////////////////////////////////////////////////////////////////////

$app->get('/eventos', function() use($db) {
  
    $consulta = $db->prepare("select * from eventos");
    $consulta->execute();
    // Almacenamos los resultados en un array asociativo.
    $resultados = $consulta->fetchAll(PDO::FETCH_ASSOC);
    // Devolvemos ese array asociativo como un string JSON.
    echo json_encode($resultados);
});


$app->get('/eventos/:ideventos', function($eventosID) use($db) {

    $consulta = $db->prepare("select * from eventos where idEvento=:param1");

    // En el execute es dónde asociamos el :param1 con el valor que le toque.
    $consulta->execute(array(':param1' => $eventosID));

    // Almacenamos los resultados en un array asociativo.
    $resultados = $consulta->fetchAll(PDO::FETCH_ASSOC);

    // Devolvemos ese array asociativo como un string JSON.
    echo json_encode($resultados);
});


// Alta de productos en la API REST
$app->post('/eventos',function() use($db,$app) {
    // Para acceder a los datos recibidos del formulario
    $datosform=$app->request;

    // Los datos serán accesibles de esta forma:
    // $datosform->post('Descricion')

    // Preparamos la consulta de insert.
                                            //Fijarse en el insert con la fecha de inicio y fin ****
    $consulta=$db->prepare("insert into eventos(Nombre,Descripcion,Lugar,idUsuario) 
                values (:Nombre,:Descripcion,:Lugar,:idUsuario)");

    $estado=$consulta->execute(
        array(      
            ':Nombre'=> $datosform->post('Nombre'),
            ':Descripcion'=> $datosform->post('Descripcion'),
            ':Lugar'=> $datosform->post('Lugar'),
            ':idUsuario'=> $datosform->post('idUsuario')
            )
        );
    if ($estado)
    echo json_encode(array('estado'=>true,'mensaje'=>'Datos insertados correctamente.'));
    else
    echo json_encode(array('estado'=>false,'mensaje'=>'Error al insertar datos en la tabla.'));
});


// Programamos la ruta de borrado en la API REST (DELETE)
$app->get('/eventosdelete/:idEvento',function($idEvento) use($db)
{
    $consulta=$db->prepare("delete from eventos where idEvento=:id");
    echo $idEvento;
    $consulta->execute(array(':id'=>$idEvento));

    if ($consulta->rowCount() == 1)
    echo json_encode(array('estado'=>true,'mensaje'=>'El evento '.$idEvento.' ha sido borrado correctamente.'));
    else
    echo json_encode(array('estado'=>false,'mensaje'=>'ERROR: ese registro no se ha encontrado en la tabla.'));

});


// Actualización de datos de usuario (PUT)
$app->put('/eventos/:ideventos',function($ideventos) use($db,$app) {

    $datosform=$app->request;
    $consulta=$db->prepare("update eventos set Nombre=:Nombre,Descripcion=:Descripcion,Lugar=:Lugar,idUsuario=:idUsuario 
                        where idEvento=:idEvento");

    $estado=$consulta->execute(
        array(    
            ':Nombre'=> $datosform->post('Nombre'),
            ':Descripcion'=> $datosform->post('Descripcion'),
            ':Lugar'=> $datosform->post('Lugar'),
            ':idUsuario'=> $datosform->post('idUsuario')
            )
        );

    // Si se han modificado datos...
    if ($consulta->rowCount()==1)
    echo json_encode(array('estado'=>true,'mensaje'=>'Datos actualizados correctamente.'));
    else
    echo json_encode(array('estado'=>false,'mensaje'=>'Error al actualizar datos, datos 
                    no modificados o registro no encontrado.'));
});


////////////////////////////////////////////////////////////////////////////////////////////////
////////////////////////MESAS////////////////////////MESAS////////
////////////////////////////////////////////////////////////////////////

$app->get('/mesas', function() use($db) {
  
    $consulta = $db->prepare("select * from mesas");
    $consulta->execute();
    // Almacenamos los resultados en un array asociativo.
    $resultados = $consulta->fetchAll(PDO::FETCH_ASSOC);
    // Devolvemos ese array asociativo como un string JSON.
    echo json_encode($resultados);
});


$app->get('/mesas/:idMesas', function($mesasID) use($db) {

    $consulta = $db->prepare("select * from mesas where idMesas=:param1");

    // En el execute es dónde asociamos el :param1 con el valor que le toque.
    $consulta->execute(array(':param1' => $mesasID));

    // Almacenamos los resultados en un array asociativo.
    $resultados = $consulta->fetchAll(PDO::FETCH_ASSOC);

    // Devolvemos ese array asociativo como un string JSON.
    echo json_encode($resultados);
});


// Alta de productos en la API REST
$app->post('/mesas',function() use($db,$app) {
    // Para acceder a los datos recibidos del formulario
    $datosform=$app->request;

    // Los datos serán accesibles de esta forma:
    // $datosform->post('Descricion')

    // Preparamos la consulta de insert.
                                            //Fijarse en el insert con la fecha de inicio y fin ****
    $consulta=$db->prepare("insert into mesas(idMesa) 
                values (:idMesa)");

    $estado=$consulta->execute(
        array(      
            ':idMesa'=> $datosform->post('idMesa')
            )
        );
    if ($estado)
    echo json_encode(array('estado'=>true,'mensaje'=>'Datos insertados correctamente.'));
    else
    echo json_encode(array('estado'=>false,'mensaje'=>'Error al insertar datos en la tabla.'));
});


// Programamos la ruta de borrado en la API REST (DELETE)
$app->get('/mesasdelete/:idMesas',function($idmesas) use($db)
{
    $consulta=$db->prepare("delete from mesas where idMesa=:id");
    echo $idEvento;
    $consulta->execute(array(':id'=>$idmesas));

    if ($consulta->rowCount() == 1)
    echo json_encode(array('estado'=>true,'mensaje'=>'La mesa '.$idmesas.' ha sido borrada correctamente.'));
    else
    echo json_encode(array('estado'=>false,'mensaje'=>'ERROR: ese registro no se ha encontrado en la tabla.'));

});


// Actualización de datos de usuario (PUT)
$app->put('/eventos/:ideventos',function($ideventos) use($db,$app) {

    $datosform=$app->request;
    $consulta=$db->prepare("update eventos set Nombre=:Nombre,Descripcion=:Descripcion,Lugar=:Lugar,idUsuario=:idUsuario 
                        where idEvento=:idEvento");

    $estado=$consulta->execute(
        array(    
            ':Nombre'=> $datosform->post('Nombre'),
            ':Descripcion'=> $datosform->post('Descripcion'),
            ':Lugar'=> $datosform->post('Lugar'),
            ':idUsuario'=> $datosform->post('idUsuario')
            )
        );

    // Si se han modificado datos...
    if ($consulta->rowCount()==1)
    echo json_encode(array('estado'=>true,'mensaje'=>'Datos actualizados correctamente.'));
    else
    echo json_encode(array('estado'=>false,'mensaje'=>'Error al actualizar datos, datos 
                    no modificados o registro no encontrado.'));
});

$app->run();
?>