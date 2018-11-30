<?php
require 'Slim/Slim.php';
require 'includes/clases/pdf.php';
require_once "includes/clases/conexionBD.php";

require 'PHPMailer/Exception.php';
require 'PHPMailer/PHPMailer.php';
require 'PHPMailer/SMTP.php';

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

\Slim\Slim::registerAutoloader();
global $app;
$database = new Database("dbo734116025", "tyERhtEjvry#98h9", "db734116025");
//$database = new Database("root", "root", "zarko");
$app = new \Slim\Slim();
$app->response->headers->set('Content-Type', 'application/json; charset=utf-8');
$app->response->headers->set('Access-Control-Allow-Origin', '*');
$app->response->headers->set('Access-Control-Allow-Origin', '*');

//ROUTES

$app->post('/login',
    function () use ($app)
    {
        $username = $password = $cod_tablet = "";
        $json = $app->request->getBody();
        $data = json_decode($json, true);
        
        if(isset($data['username'])){
            $username = $data["username"];
        }
        if(isset($data['password'])){
            $password = $data["password"];
        }
        if(isset($data['cod_tablet'])){
            $cod_tablet = $data["cod_tablet"];
        }

        if(isset($username) AND isset($password) AND isset($cod_tablet) AND $username != "" AND $password != "" AND $cod_tablet != ""){

            global $database;
            $database->query("SELECT c.password FROM commercials c WHERE c.cod_commercial = :username");

            $database->bind(":username", $username);

            $numeroFilas = $database->numeroFilas();

            if($numeroFilas > 0){
                $userP = $database->obtenerFila();
                $encryptPass = $userP['password'];
                if (!empty($encryptPass)) {
                    $array = explode(":", $encryptPass);
                    $salt = $array[1];
                    $hash = md5($password.$salt);
                    $passCompleta = $hash.":".$salt;

                    $database->query("SELECT c.id, c.cod_commercial, c.name AS nombreComercial, c.cod_tablet, r.name AS rol FROM commercials c, roles r WHERE c.cod_commercial = :username AND c.password = :password AND r.id=c.id_roles");
                    $database->bind(":username", $username);
                    $database->bind(":password", $passCompleta);

                    $nFilas = $database->numeroFilas();

                    if($nFilas > 0){
                        $user = $database->obtenerFila();
                        $database->query("UPDATE commercials SET cod_tablet='1232121' WHERE cod_commercial = 'estela'");
                        $database->bind(":cod_tablet", $cod_tablet);
                        $database->bind(":username", $username);
                        /*var_dump($database->obtenerTabla());*/
                        echo 
                            '{
                                "status": true,
                                "code":100,
                                "data":' . json_encode($user, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE) . '
                            }';
                    }else{
                        echo 
                            '{
                                "error":101,
                                "mensaje":"Usuario o contraseña incorrectos"
                            }';
                    }
                }
            }else{
                echo 
                    '{
                        "error":101,
                        "mensaje":"Usuario o contraseña incorrectos"
                    }';
            }
        }else{
            echo '{"error":102,"mensaje":"Introduce usuario y contraseña para proseguir"}';
        }
    }
);

/*$app->post('/get/products',//ORIGINAL
    function () use ($app)
    {
        $categoryId = "";
        $json = $app->request->getBody();
        $data = json_decode($json, true);
        if(isset($data['id_categories'])){
            $categoryId = $data["id_categories"];
        }
        
        if(isset($categoryId) AND $categoryId != ""){
            //CUANDO EXISTA UN ID DE CATEGORIA, LISTO TODOS LOS PRODUCTOS DE ESA CATEGORÍA.
            global $database;
            $database->query("SELECT 
                                p.id,
                                p.cod_product,
                                p.id_manufacturers,
                                p.image,
                                p.name,
                                p.description,
                                p.presentation,
                                p.price,
                                p.unit,
                                p.publish,
                                p.add_date,
                                p.last_modified
                            FROM 
                                rel_products_categories rel, 
                                products p
                            WHERE 
                                rel.id_categories = :categoryId
                            AND
                                p.id = rel.id_products");

            $database->bind(":categoryId", $categoryId);

            $numeroFilas = $database->numeroFilas();

            if($numeroFilas > 0){
                $products = $database->obtenerTablaAsociativa();
                echo 
                    '{
                        "status": true,
                        "code":100,
                        "data":' . json_encode($products, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE) . '
                    }';
                
            }else{
                echo 
                    '{
                        "status": false,
                        "code":101,
                        "mensaje":"No se encuentra ningún producto de esa categoría"
                    }'; 
            }
        }else {
            //EN CASO CONTRARIO(CUANDO NO ME ENVÍE NINGUNA ID DE CATEGORÍA, LISTO TODOS LOS PRODUCTOS)

            //CUANDO EXISTA UN ID DE CATEGORIA, LISTO TODOS LOS PRODUCTOS DE ESA CATEGORÍA.
            global $database;
            $database->query("SELECT 
                                p.id,
                                p.cod_product,
                                p.id_manufacturers,
                                p.image,
                                p.name,
                                p.description,
                                p.presentation,
                                p.price,
                                p.unit,
                                p.publish,
                                p.add_date,
                                p.last_modified
                            FROM 
                                products p");

            $numeroFilas = $database->numeroFilas();

            if($numeroFilas > 0){
                $products = $database->obtenerTablaAsociativa();
                echo 
                    '{
                        "status" : true,
                        "code" : 100
                        "data" :' . json_encode($products, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE) . '
                    }';
                
            }else{
                echo 
                    '{"
                        "status" : false,
                        "code" : 101,
                        "mensaje" : "No existe ningún producto."
                    }'; 
            }
        }
    }
);*/

/*$app->post('/get/categories',//PRE-MEJORA 
    function () use ($app)
    {
        $json = $app->request->getBody();
        $data = json_decode($json, true);
        
        global $database;
        $database->query("SELECT 
                            c.id,
                            c.name,
                            c.description,
                            c.image,
                            c.date
                        FROM 
                            categories c
                        WHERE 
                            c.publish = 1
                        AND
                            c.id_parent IS NULL");

        $numeroFilas = $database->numeroFilas();

        if($numeroFilas > 0){
            $categories = $database->obtenerTablaAsociativa();
            echo 
                '{
                    "status": true,
                    "code":100,
                    "data":' . json_encode($categories, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE) . '
                }';
            
        }else{
            echo 
                '{
                    "status": false,
                    "code":101,
                    "mensaje":"No hay categorías padres en la base de datos"
                }'; 
        }
    }
);*/

$app->post('/get/categories',//POST-MEJORA
    function () use ($app)
    {
        $id_commercials = "";
        $json = $app->request->getBody();
        $data = json_decode($json, true);

        if(isset($data['id_commercials'])){
            $id_commercials = $data["id_commercials"];
        }

        if(!empty($id_commercials) AND isset($id_commercials)){
            global $database;
            $database->query("SELECT 
                                c.id,
                                c.name,
                                c.description,
                                c.image,
                                c.date
                            FROM 
                                categories c,
                                rel_commercials_categories rel
                            WHERE 
                                c.publish = 1
                            AND
                                c.id_parent IS NULL
                            AND 
                                rel.id_categories = c.id
                            AND
                                rel.id_commercials = :commercialsId");
    
            $database->bind(":commercialsId", $id_commercials);
            $numeroFilas = $database->numeroFilas();
    
            if($numeroFilas > 0){
                $categories = $database->obtenerTablaAsociativa();
                echo 
                    '{
                        "status": true,
                        "code":100,
                        "data":' . json_encode($categories, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE) . '
                    }';
                
            } else {
                global $database;
                $database->query("SELECT 
                                c.id,
                                c.name,
                                c.description,
                                c.image,
                                c.date
                            FROM 
                                categories c
                            WHERE 
                                c.publish = 1
                            AND
                                c.id_parent IS NULL");

                $database->bind(":commercialsId", $id_commercials);
                $numeroFilas = $database->numeroFilas();

                if($numeroFilas > 0){
                    $categories = $database->obtenerTablaAsociativa();
                    echo
                        '{
                        "status": true,
                        "code":100,
                        "data":' . json_encode($categories, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE) . '
                    }';

                } else {
                    echo
                    '{
                        "status": false,
                        "code":101,
                        "mensaje":"No hay categorías padres en la base de datos"
                    }';
                }
            }
        }else{
            global $database;
            $database->query("SELECT 
                                c.id,
                                c.name,
                                c.description,
                                c.image,
                                c.date
                            FROM 
                                categories c
                            WHERE 
                                c.publish = 1
                            AND
                                c.id_parent IS NULL");

            $numeroFilas = $database->numeroFilas();

            if($numeroFilas > 0){
                $categories = $database->obtenerTablaAsociativa();
                echo 
                    '{
                        "status": true,
                        "code":100,
                        "data":' . json_encode($categories, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE) . '
                    }';
                
            }else{
                echo 
                    '{
                        "status": false,
                        "code":101,
                        "mensaje":"No hay categorías padres en la base de datos"
                    }'; 
            }
        }
        
    }
);

$app->post('/get/products',//PRE-MEJORA
    function () use ($app)
    {
        $categoryId = "";
        $categories = null;
        $products = null;
        $json = $app->request->getBody();
        $data = json_decode($json, true);
        if(isset($data['id_categories'])){
            $categoryId = $data["id_categories"];
        }
        
        if(isset($categoryId) AND $categoryId != ""){
            //CUANDO EXISTA UN ID DE CATEGORIA, LISTO TODOS LOS PRODUCTOS DE ESA CATEGORÍA.
            global $database;
            $database->query("SELECT 
                                c.id,
                                c.name,
                                c.description,
                                c.image,
                                c.date
                            FROM 
                                categories c
                            WHERE 
                                c.id_parent = :categoryId
                            AND
                                c.publish = 1");

            $database->bind(":categoryId", $categoryId);

            $numeroFilasCategorias = $database->numeroFilas();

            if($numeroFilasCategorias > 0){
                $categories = $database->obtenerTablaAsociativa();
            }

            $database->query("SELECT
                                p.id,
                                p.cod_product,
                                p.image,
                                p.name,
                                p.description,
                                p.presentation,
                                p.price,
                                p.add_date,
                                p.unit
                            FROM
                                products p,
                                rel_products_categories rel
                            WHERE
                                rel.id_categories = :categoryId AND p.id = rel.id_products AND p.publish = 1");

            $database->bind(":categoryId", $categoryId);

            $numeroFilasProductosCategorias = $database->numeroFilas();

            if($numeroFilasProductosCategorias > 0){
                $products = $database->obtenerTablaAsociativa();
            }

            echo 
                '{
                    "status" : true,
                    "code" : 100,
                    "data" : [{
                        "categorias": ' . json_encode($categories, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE) . ',
                        "productos": ' . json_encode($products, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE) . '
                    }]
                }';
        }else {
            //EN CASO CONTRARIO(CUANDO NO ME ENVÍE NINGUNA ID DE CATEGORÍA, LISTO TODOS LOS PRODUCTOS)
            global $database;
            $database->query("SELECT 
                                p.id,
                                p.cod_product,
                                p.id_manufacturers,
                                p.image,
                                p.name,
                                p.description,
                                p.presentation,
                                p.price,
                                p.unit,
                                p.publish,
                                p.add_date,
                                p.last_modified
                            FROM 
                                products p");

            $numeroFilas = $database->numeroFilas();

            if($numeroFilas > 0){
                $products = $database->obtenerTablaAsociativa();
                echo 
                '{
                    "status" : true,
                    "code" : 100,
                    "data" : '. json_encode($products, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE) . '
                }';
                
            }else{
                echo 
                    '{"
                        "status" : false,
                        "code" : 101,
                        "mensaje" : "No existe ningún producto."
                    }'; 
            }
        }
    }
);

/*$app->post('/get/products',//POST-MEJORA
    function () use ($app)
    {
        $id_commercials = $categoryId = "";
        $categories = null;
        $products = null;
        $json = $app->request->getBody();
        $data = json_decode($json, true);
        if(isset($data['id_categories'])){
            $categoryId = $data["id_categories"];
        }
        if(isset($data['id_commercials'])){
            $id_commercials = $data["id_commercials"];
        }
        
        if(!empty($id_commercials) AND isset($id_commercials) AND
         isset($categoryId) AND !empty($categoryId)){
            //CUANDO EXISTA UN ID DE CATEGORIA, LISTO TODOS LOS PRODUCTOS DE ESA CATEGORÍA.
            global $database;
            $database->query("SELECT 
                                c.id,
                                c.name,
                                c.description,
                                c.image,
                                c.date
                            FROM 
                                categories c,
                                rel_commercials_categories rel                                
                            WHERE 
                                c.id_parent = :categoryId
                            AND
                                c.publish = 1
                            AND 
                                rel.id_categories = c.id
                            AND
                                rel.id_commercials = :commercialsId");

            $database->bind(":categoryId", $categoryId);
            $database->bind(":commercialsId", $id_commercials);
            $numeroFilasCategorias = $database->numeroFilas();

            if($numeroFilasCategorias > 0){
                $categories = $database->obtenerTablaAsociativa();
            }

            $database->query("SELECT
                                p.cod_product,
                                p.image,
                                p.name,
                                p.description,
                                p.presentation,
                                p.price,
                                p.add_date,
                                p.unit
                            FROM
                                products p,
                                rel_products_categories rel
                            WHERE
                                rel.id_categories = :categoryId
                            AND
                                p.id = rel.id_products 
                            AND 
                                p.publish = 1");

            $database->bind(":categoryId", $categoryId);
            $numeroFilasProductosCategorias = $database->numeroFilas();

            if($numeroFilasProductosCategorias > 0){
                $products = $database->obtenerTablaAsociativa();
            }

            echo 
                '{
                    "status" : true,
                    "code" : 100,
                    "data" : [{
                        "categorias": ' . json_encode($categories, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE) . ',
                        "productos": ' . json_encode($products, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE) . '
                    }]
                }';
        }else {
            //EN CASO CONTRARIO(CUANDO NO ME ENVÍE NINGUNA ID DE CATEGORÍA, LISTO TODOS LOS PRODUCTOS)
            global $database;
            $database->query("SELECT 
                                p.id,
                                p.cod_product,
                                p.id_manufacturers,
                                p.image,
                                p.name,
                                p.description,
                                p.presentation,
                                p.price,
                                p.unit,
                                p.publish,
                                p.add_date,
                                p.last_modified
                            FROM 
                                products p");

            $numeroFilas = $database->numeroFilas();

            if($numeroFilas > 0){
                $products = $database->obtenerTablaAsociativa();
                echo 
                    '{
                        "status" : true,
                        "code" : 100
                        "data" : [
                            {
                                "categorias": ' . json_encode($categories, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE) . ',
                                "productos": ' . json_encode($products, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE) . '
                            }
                        ]
                    }';
                
            }else{
                echo 
                    '{"
                        "status" : false,
                        "code" : 101,
                        "mensaje" : "No existe ningún producto."
                    }'; 
            }
        }
    }
);*/

$app->get('/get/customers', 
    function () use ($app)
    {      
        global $database;
        $database->query("SELECT 
                            c.id,
                            c.cod_customer,
                            c.name,
                            c.company,
                            c.contact,
                            c.firma,
                            c.street,
                            c.city,
                            c.state,
                            c.country,
                            c.zip,
                            c.phone,
                            c.email,
                            c.registerDate
                        FROM 
                            customers c
                        WHERE 
                            c.block = 0");

        $numeroFilas = $database->numeroFilas();

        if($numeroFilas > 0){
            $customers = $database->obtenerTablaAsociativa();
            echo 
                '{
                    "status": true,
                    "code":100,
                    "data":' . json_encode($customers, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE) . '
                }';
            
        }else{
            echo 
                '{
                    "status": false,
                    "code":101,
                    "mensaje":"No hay clientes en la base de datos"
                }'; 
        }
    }
);

$app->post('/get/orders',
    function () use ($app)
    {
        $commercialId = "";
        $json = $app->request->getBody();

        $data = json_decode($json, true);
        if(isset($data['id_commercials'])){
            $commercialId = $data["id_commercials"];
        }
        
        if(isset($commercialId) AND $commercialId != ""){
            //CUANDO EXISTA UN ID DE COMERCIAL, LISTO TODOS LOS PEDIDOS DE ESE COMERCIAL.
            global $database;
            //SELECT p.cod_product, p.name, p.image, rel.quantity, rel.total, rel.real_total FROM products p, rel_orders_products rel WHERE rel.id_products = p.id AND rel.id_orders = "13381"
            //SELECT id FROM orders WHERE id_commercials=86 
            $database->query("SELECT o.id, o.cod_order, o.status, o.observations, o.total, o.pdf, o.add_date FROM orders o WHERE o.id_commercials=:id_commercials");

            $database->bind(":id_commercials", $commercialId);

            $numeroFilas = $database->numeroFilas();

            if($numeroFilas > 0){
                $orders = $database->obtenerTablaAsociativa();


                echo 
                    '{
                        "status": true,
                        "code":100,
                        "pedidos":' . json_encode($orders, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE) . '
                    }';
                
            }else{
                echo 
                    '{
                        "status": false,
                        "code":101,
                        "mensaje":"No se encuentra ningún pedido del comercial dado"
                    }'; 
            }
        }else{
            echo 
                    '{
                        "status": false,
                        "code":101,
                        "mensaje":"No se encuentra ningún comercial con ese identificador"
                    }'; 
        }
    }
);

$app->post('/get/productsByOrder', 
    function () use ($app)
    {
        $orderId = "";
        $json = $app->request->getBody();
        $data = json_decode($json, true);
        if(isset($data['id_order'])){
            $orderId = $data["id_order"];
        }
        
        if(isset($orderId) AND $orderId != ""){
            //CUANDO EXISTA UN ID DE PEDIDO, LISTO TODOS LOS PRODUCTOS DE ESE PEDIDO.
            global $database;
            //SELECT p.cod_product, p.name, p.image, rel.quantity, rel.total, rel.real_total FROM products p, rel_orders_products rel WHERE rel.id_products = p.id AND rel.id_orders = "13381"
            //SELECT id FROM orders WHERE id_commercials=86 
            $database->query('
                            SELECT 
                                p.cod_product, 
                                p.name, 
                                p.unit,
                                p.image, 
                                rel.quantity, 
                                rel.product_total, 
                                rel.real_total 
                            FROM 
                                products p, 
                                rel_orders_products rel 
                            WHERE 
                                rel.id_products = p.id 
                            AND 
                                rel.id_orders = :orderId');

            $database->bind(":orderId", $orderId);

            $numeroFilas = $database->numeroFilas();

            if($numeroFilas > 0){
                $products = $database->obtenerTablaAsociativa();


                echo 
                    '{
                        "status": true,
                        "code":100,
                        "data":' . json_encode($products, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE) . '
                    }';
                
            }else{
                echo 
                    '{
                        "status": false,
                        "code":101,
                        "mensaje":"No se encuentra ningún producto asociado a ese pedido"
                    }'; 
            }
        }else{
            echo 
                '{
                    "status": false,
                    "code":101,
                    "mensaje":"No se encuentra ningún pedido con ese identificador"
                }'; 
        }
    }
);

$app->post('/get/updates', 
    function () use ($app)
    {
        $json = $app->request->getBody();
        $data = json_decode($json, true);
        $addDate = "";

        if(isset($data['add_date'])){//FECHA
            $addDate = $data["add_date"];
        }
        
        global $database;
        $database->query("SELECT * FROM updates WHERE tableName NOT LIKE 'users' AND tableName NOT LIKE 'manufacturers' AND add_date BETWEEN :add_date AND NOW()");
        $database->bind(":add_date", $addDate);

        $numeroFilas = $database->numeroFilas();

        if($numeroFilas > 0){
            $updates = $database->obtenerTablaAsociativa();
            echo 
                '{
                    "status": true,
                    "code":100,
                    "data":' . json_encode($updates, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE) . '
                }';
            
        }else{
            echo 
                '{
                    "status": false,
                    "code":101,
                    "mensaje":"No hay actualizaciones en la base de datos"
                }'; 
        }
    }
);

/*$app->post('/get/product', 
    function () use ($app)
    {
        $name = "";
        $json = $app->request->getBody();
        $data = json_decode($json, true);
        if(isset($data['name'])){
            $name = $data["name"];
        }
        
        if(isset($name) AND $name != ""){
            //CUANDO EXISTA UN NOMBRE, LISTO LOS PRODUCTOS QUE CONTENGAN ESA CADENA EN SU NOMBRE.
            global $database;
            $database->query('
                            SELECT 
                                p.id,
                                p.cod_product,
                                p.id_manufacturers,
                                p.image,
                                p.name,
                                p.description,
                                p.presentation,
                                p.price,
                                p.unit,
                                p.publish,
                                p.add_date,
                                p.last_modified
                            FROM 
                                products p 
                            WHERE 
                                p.name LIKE :name');

            $database->bind(":name", "%" . $name . "%");

            $numeroFilas = $database->numeroFilas();

            if($numeroFilas > 0){
                $products = $database->obtenerTablaAsociativa();


                echo 
                    '{
                        "status": true,
                        "code":100,
                        "data":' . json_encode($products, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE) . ',
                    }';
                
            }else{
                echo 
                    '{
                        "status": false,
                        "code":101,
                        "mensaje":"No se encuentra ningún producto que contenga lo introducido"
                    }'; 
            }
        }else{
            echo 
                '{
                    "status": false,
                    "code":101,
                    "mensaje":"No se encuentra ningún producto con ese identificador"
                }'; 
        }
    }
);*/

$app->post('/insert/order', 
    function () use ($app)
    {
        $customerId = $commercialId = $codOrder = $observations = $addDate = $lastModified = /*$orderTotal =*/ $msg = $latitude = $longitude ="";
        $orderTotal = 0;
        $error = false;
        $products = array();
        $json = $app->request->getBody();
        $data = json_decode($json, true);

        if(isset($data['id_customers'])){
            $customerId = $data["id_customers"];
        }
        if(isset($data['id_commercials'])){
            $commercialId = $data["id_commercials"];
        }
        /*if(isset($data['cod_order'])){
            $codOrder = $data["cod_order"];
        }*/
        if(isset($data['observations'])){
            $observations = $data["observations"];
        }
        if(isset($data['add_date'])){
            $addDate = $data["add_date"];
        }
        if(isset($data['last_modified'])){
            $lastModified = $data["last_modified"];
        }
        /*if(isset($data['total'])){
            $orderTotal = $data["total"];
        }*/
        if (isset($data['products'])) {
            $products = $data['products'];
        }
        if (isset($data['latitude'])) {
            $latitude = $data['latitude'];
        }
        if (isset($data['longitude'])) {
            $longitude = $data['longitude'];
        }
        /*if (isset($data['pdf'])) {
            $pdf = $data['pdf'];
        }*/

        
        
        if($customerId != "" AND $commercialId != "" AND
            /*$codOrder != "" AND*/ $observations != "" AND
            $addDate != "" AND $lastModified  != "" /*AND 
            $orderTotal !=""*/){
            //CUANDO EXISTA UN NOMBRE, LISTO LOS PRODUCTOS QUE CONTENGAN ESA CADENA EN SU NOMBRE.
            global $database;
            $database->query('
                            INSERT 
                            INTO 
                                orders(
                                    id_customers, 
                                    id_commercials, 
                                    cod_order,
                                    status, 
                                    observations, 
                                    add_date, 
                                    last_modified
                                ) 
                            VALUES(
                                :customerId,
                                :commercialId,
                                1,
                                2,
                                :observations, 
                                :addDate ,
                                :lastModified
                            )');

            $database->bind(":customerId", $customerId);
            $database->bind(":commercialId", $commercialId);
            //$database->bind(":codOrder", $codOrder);
            //$database->bind(":statusId", "2"); STATUS DEL PEDIDO
            $database->bind(":observations", $observations);
            $database->bind(":addDate", $addDate);
            $database->bind(":lastModified", $lastModified);
            //$database->bind(":total", $orderTotal);
            //$database->bind(":pdf", $pdf);

            $numeroFilas = $database->numeroFilas();

            if($numeroFilas > 0){
                $orderId = $database->ultimaIdInsertada();
                $codOrder = intval($orderId) + 2243;

                //GENERAR NOMBRE DEL PDF---
                $pdfName = $codOrder. '_' . cadenaAleatoria() . '.pdf';
                
                $database->query('
                                UPDATE
                                    `orders`
                                SET
                                    `pdf` = :pdf
                                WHERE
                                    `id` = :orderId');

                $database->bind(":pdf", $pdfName);
                $database->bind(":orderId", $orderId);

                $database->execute();
                
                //</>

                if(!empty($products)){

                    foreach ($products as $key => $product) {
                        $productId = $quantity = $productTotal = "";
                        $realTotal = null;

                        if(isset($product['id_products'])){                            
                            $productId = $product["id_products"];
                        }
                        if(isset($product['quantity'])){
                            $quantity = $product["quantity"];
                        }
                        if(isset($product['product_total'])){
                            $productTotal = $product["product_total"];
                        }
                        if(isset($product['real_total'])){
                            $realTotal = $product["real_total"];
                        }
                        if($orderId != "" AND $productId != "" AND 
                            $quantity != "" AND $productTotal != "" /*AND 
                            $realTotal != ""*/){

                            $database->query('
                                INSERT 
                                INTO 
                                    rel_orders_products
                                    (id_orders, id_products, quantity, product_total, real_total) 
                                VALUES
                                    (:orderId, :productId, :quantity, :productTotal, :realTotal)'
                            );

                            $database->bind(":orderId", $orderId);
                            $database->bind(":productId", $productId);
                            $database->bind(":quantity", $quantity);
                            $database->bind(":productTotal", $productTotal);
                            $database->bind(":realTotal", $realTotal);
                            $database->obtenerTablaAsociativa();
                            $orderTotal+= ($quantity * $productTotal);
                        }else{
                            $msg = 
                            '{
                                "status": false,
                                "code":101,
                                "data":"Uno o varios campos del producto no fueron enviados"
                            },';
                            $error = true;
                        }
                    }
                    $database->query('
                                    UPDATE
                                        `orders`
                                    SET
                                        `total` = :total,
                                        `cod_order` = :codOrder
                                    WHERE
                                        `id` = :orderId');

                    $database->bind(":total", $orderTotal);
                    $database->bind(":orderId", $orderId);
                    $database->bind(":codOrder", $codOrder);

                    $database->execute();


                    //RECUPERAR EL CÓDIGO DE TABLET
                    $database->query('SELECT c.cod_commercial, c.cod_tablet FROM commercials c WHERE id=:commercialId');
                    $database->bind(":commercialId", $commercialId);
                    $commercialCod = $database->obtenerTablaAsociativa()[0]['cod_commercial'];
                    $commercialTablet = $database->obtenerTablaAsociativa()[0]['cod_tablet'];
                    /*var_dump($commercialCod);
                    var_dump($commercialTablet);*/

                    //RECUPERAR EL NOMBRE, CÓDIGO, CALLE, CIUDAD, ESTADO Y CODIGO POSTAL DEL CLIENTE
                    $database->query('SELECT c.cod_customer, c.name, c.street, c.city, c.state, c.zip FROM customers c WHERE id=:customerId');
                    $database->bind(":customerId", $customerId);
                    $customerCod = $database->obtenerTablaAsociativa()[0]['cod_customer'];
                    $customerName = $database->obtenerTablaAsociativa()[0]['name'];
                    $customerStreet = $database->obtenerTablaAsociativa()[0]['street'];
                    $customerCity = $database->obtenerTablaAsociativa()[0]['city'];
                    $customerState = $database->obtenerTablaAsociativa()[0]['state'];
                    $customerZip = $database->obtenerTablaAsociativa()[0]['zip'];
                    /*var_dump($customerCod);
                    var_dump($customerName);
                    var_dump($customerStreet);
                    var_dump($customerCity);
                    var_dump($customerState);
                    var_dump($customerZip);*/

                    //FUNCIÓN PARA GENERAR PDF CON TODOS LOS CAMPOS DEL MISMO PASADOS POR PARÁMETRO
                    generarpdfBase($orderId, "000" . $codOrder, $addDate, $customerCod, $commercialCod, $commercialTablet, $customerName, $customerStreet, $customerZip, $customerCity, $customerState, $pdfName, $orderTotal, $observations, $latitude, $longitude);
                    //</>

                    if(isset($error) AND !$error){
                        $msg = 
                        '{
                            "status": true,
                            "code":100,
                            "data":"Productos del pedido insertados correctamente",
                            "order": '.$orderId. '
                        }';
                    }
                }else{
                    $msg =  
                    '{
                        "status": false,
                        "code":101,
                        "data":"No hay ningún producto asociado a este pedido"
                    }';
                }
            }else{
                $msg = 
                    '{
                        "status": false,
                        "code":101,
                        "mensaje":"No se ha podido insertar el pedido"
                    }'; 
            }
        }else{
            $msg =  
                '{
                    "status": false,
                    "code":101,
                    "mensaje":"Un campo o varios no han sido enviados por lo que no pudo añadirse el pedido"
                }'; 
        }
        echo $msg;
    }
);

$app->post('/insert/customer',
    function () use ($app)
    {
        $codCustomer = $password = $name = $company = $contact =
        $firma = $street = $city = $zip = $country =
        $state = $email = $registerDate = $lastVisitDate =
        $rolId = $phone = $mobil = $fax = $block = "";

        $json = $app->request->getBody();
        $data = json_decode($json, true);

        if(isset($data['cod_customer'])){//CODIGO CLIENTE
            $codCustomer = $data["cod_customer"];
        }
        //PASSWORD
        $password = $data["cod_customer"];

        if(isset($data['name'])){//NOMBRE CLIENTE
            $name = $data["name"];
        }
        if(isset($data['company'])){//COMPANY
            $company = $data["company"];
        }
        if(isset($data['phone'])){//TELEFONO
            $phone = $data["phone"];
        }
        if(isset($data['mobil'])){//MOVIL
            $mobil = $data["mobil"];
        }
        if(isset($data['fax'])){//CONTACTO
            $fax = $data["fax"];
        }
        if(isset($data['contact'])){//CONTACTO
            $contact = $data["contact"];
        }
        if(isset($data['firma'])){//DNI / CIF
            $firma = $data["firma"];
        }
        if(isset($data['street'])){//DIRECCION
            $street = $data["street"];
        }
        if (isset($data['city'])) {//CIUDAD
            $city = $data['city'];
        }
        if (isset($data['zip'])) {//CODIGO POSTAL
            $zip = $data['zip'];
        }
        if (isset($data['state'])) {//ESTADO
            $state = $data['state'];
        }
        if (isset($data['country'])) {//CIUDAD
            $country = $data['country'];
        }
        if (isset($data['email'])) {//EMAIL
            $email = $data['email'];
        }
        if (isset($data['registerDate'])) {//REGISTER DATE
            $registerDate = $data['registerDate'];
        }
        if (isset($data['lastVisitDate'])) {//LAST VISIT DATE
            $lastVisitDate = $data['lastVisitDate'];
        }
        if (isset($data['id_commercial'])) {//REGISTER DATE
            $createdBy = $data['id_commercial'];
        }

        $block = 0;
        $rolId = 4;

        if($password != "" AND $name != "" AND $firma != ""
            AND $street != "" AND $city != "" AND $zip != ""
            AND $phone != "" AND $country != "" AND $state != "" AND $registerDate != ""
            AND $lastVisitDate != "" AND $createdBy != ""){

            $salt = cadenaAleatoria();
            $hash = md5($password.$salt);
            $encryptedPass = $hash.":".$salt;

            global $database;
            $database->query('
                            INSERT
                            INTO
                            `customers`(
                                `cod_customer`,
                                `password`,
                                `name`,
                                `company`,
                                `firma`,
                                `street`,
                                `city`,
                                `zip`,
                                `state`,
                                `country`,
                                `phone`,
                                `mobil`,
                                `fax`,
                                `email`,
                                `block`,
                                `registerDate`,
                                `lastvisitDate`,
                                `id_roles`,
                                `created_by`
                            )
                            VALUES(
                                :cod_customer,
                                :password,
                                :name,
                                :company,
                                :firma,
                                :street,
                                :city,
                                :zip,
                                :state,
                                :country,
                                :phone,
                                :mobil,
                                :fax,
                                :email,
                                :block,
                                :registerDate,
                                :lastvisitDate,
                                :id_roles,
                                :created_by
                            )');

            $database->bind(":cod_customer", $codCustomer);
            $database->bind(":password", $encryptedPass);
            $database->bind(":name", $name);
            $database->bind(":company", $company);
            $database->bind(":firma", $firma);
            $database->bind(":street", $street);
            $database->bind(":city", $city);
            $database->bind(":zip", $zip);
            $database->bind(":state", $state);
            $database->bind(":country", $country);
            $database->bind(":phone", $phone);
            $database->bind(":mobil", $mobil);
            $database->bind(":fax", $fax);
            $database->bind(":email", $email);
            $database->bind(":block", $block);
            $database->bind(":registerDate", $registerDate);
            $database->bind(":lastvisitDate", $lastVisitDate);
            $database->bind(":id_roles", $rolId);
            $database->bind(":created_by", $createdBy);
            $numeroFilas = $database->numeroFilas();

            if($numeroFilas > 0){
                $customerId = $database->ultimaIdInsertada();
                $msg =
                    '{
                        "status": true,
                        "code":100,
                        "mensaje":"El cliente se añadió con éxito",
                        "id": '. $customerId . '
                    }';
            }else{
                $msg =
                    '{
                        "status": false,
                        "code":101,
                        "mensaje":"No se ha podido insertar el cliente"
                    }';
            }
        }else{
            $msg =
                '{
                    "status": false,
                    "code":101,
                    "mensaje":"Un campo o varios no han sido enviados por lo que no pudo añadirse el cliente"
                }';
        }
        echo $msg;
    }
);

$app->post('/update/customer', 
    function () use ($app)
    {
        $id = $password = $name = $company = $phone = 
        $firma = $street = $city = $zip = $country =
        $state = $email = $lastVisitDate  = "";

        $json = $app->request->getBody();
        $data = json_decode($json, true);

        if(isset($data['id'])){//ID DEL CLIENTE
            $id = $data["id"];
        }
        if(isset($data['name'])){//NOMBRE CLIENTE
            $name = $data["name"];
        }
        if(isset($data['password'])){//PASSWORD
            $password = $data["password"];
        }
        if(isset($data['company'])){//COMPANY
            $company = $data["company"];
        }
        if(isset($data['phone'])){//FIRMA
            $phone = $data["phone"];
        }
        if(isset($data['firma'])){//DIRECCION
            $firma = $data["firma"];
        }
        if(isset($data['street'])){//CIUDAD
            $street = $data["street"];
        }
        if (isset($data['city'])) {//CIUDAD
            $city = $data['city'];
        }
        if (isset($data['zip'])) {//CODIGO POSTAL
            $zip = $data['zip'];
        }
        if (isset($data['state'])) {//ESTADO
            $state = $data['state'];
        }
        if (isset($data['country'])) {//CIUDAD
            $country = $data['country'];
        }
        if (isset($data['email'])) {//EMAIL
            $email = $data['email'];
        }
        if (isset($data['lastVisitDate'])) {//LAST VISIT DATE
            $lastVisitDate = $data['lastVisitDate'];
        }
        
        if($name != "" AND $password != "" AND $firma != "" AND $street != "" AND $city != "" AND $zip != "" AND
           $state != "" AND $phone != "" AND $email != "" AND $lastVisitDate != ""){

            $salt = cadenaAleatoria();
            $hash = md5($password.$salt);
            $encryptedPass = $hash.":".$salt;

            global $database;
            $database->query('
                            UPDATE
                                `customers`
                            SET
                                `password` = :password,
                                `name` = :name,
                                `company` = :company,
                                `phone` = :phone,
                                `firma` = :firma,
                                `street` = :street,
                                `city` = :city,
                                `zip` = :zip,
                                `state` = :state,
                                `country` = :country,
                                `email` = :email,
                                `lastvisitDate` = :lastvisitDate
                            WHERE
                                `id` = :id');

            $database->bind(":password", $encryptedPass);
            $database->bind(":name", $name);
            $database->bind(":company", $company);
            $database->bind(":phone", $phone);
            $database->bind(":firma", $firma);
            $database->bind(":street", $street);
            $database->bind(":city", $city);
            $database->bind(":zip", $zip);
            $database->bind(":state", $state);
            $database->bind(":country", $country);
            $database->bind(":email", $email);
            $database->bind(":lastvisitDate", $lastVisitDate);
            $database->bind(":id", $id);
            $numeroFilas = $database->numeroFilas();

            if($numeroFilas > 0){
                $msg = 
                    '{
                        "status": true,
                        "code":100,
                        "mensaje":"El cliente se editó con éxito"
                    }'; 
            }else{
                $msg = 
                    '{
                        "status": false,
                        "code":101,
                        "mensaje":"No se ha podido editar el cliente"
                    }'; 
            }
        }else{
            $msg =  
                '{
                    "status": false,
                    "code":101,
                    "mensaje":"Un campo o varios no han sido enviados por lo que no se pudo editar el cliente"
                }'; 
        }
        echo $msg;
    }
);

$app->post('/insert/location', 
    function () use ($app)
    {
        $id_commercials = $latitude = $longitude = $add_date = "";

        $json = $app->request->getBody();
        $data = json_decode($json, true);

        if(isset($data['id_commercials'])){//ID DEL COMERCIAL
            $id_commercials = $data["id_commercials"];
        }
        if (isset($data['latitude'])) {//LATITUD
            $latitude = $data['latitude'];
        }
        if (isset($data['longitude'])) {//LONGITUD
            $longitude = $data['longitude'];
        }
        if (isset($data['add_date'])) {//FECHA
            $add_date = $data['add_date'];
        }
        
        if($id_commercials != "" AND $latitude != "" AND $longitude != ""){
            global $database;
            $database->query('
                            INSERT 
                            INTO 
                                locations(
                                    id_commercials, 
                                    latitude, 
                                    longitude,
                                    add_date
                                ) 
                            VALUES(
                                :id_commercials,
                                :latitude,
                                :longitude,
                                :add_date
                            )');

            $database->bind(":id_commercials", $id_commercials);
            $database->bind(":latitude", $latitude);
            $database->bind(":longitude", $longitude);
            $database->bind(":add_date", $add_date);

            $numeroFilas = $database->numeroFilas();

            if($numeroFilas > 0){
                $msg = 
                    '{
                        "status": true,
                        "code":100,
                        "mensaje":"Localización guardada"
                    }'; 
            }else{
                $msg = 
                    '{
                        "status": false,
                        "code":101,
                        "mensaje":"No se ha podido guardar la localización"
                    }'; 
            }
        }else{
            $msg =  
                '{
                    "status": false,
                    "code":101,
                    "mensaje":"Un campo o varios no han sido enviados por lo que no se 
                    pudo guardar la localización"
                }'; 
        }
        echo $msg;
    }
);

$app->get('/get/locations', 
    function () use ($app)
    {        
        global $database;
        $database->query("SELECT 
                            c.id, 
                            c.cod_commercial, 
                            c.name
                        FROM 
                            commercials c
                        WHERE 
                            c.id IN (
                                SELECT id_commercials 
                                FROM locations 
                                WHERE add_date BETWEEN date_sub(now(), interval 1 day) AND NOW())
                        AND
                            c.block = 0");

        $numeroFilas = $database->numeroFilas();

        if($numeroFilas > 0){
            $commercialsLocations = array();
            $commercials = $database->obtenerTablaAsociativa();
            foreach ($commercials as $commercial) {                
                $database->query("SELECT l.latitude, l.longitude, l.add_date FROM commercials c, locations l WHERE l.id_commercials = c.id AND l.id_commercials = :commercialId AND l.add_date BETWEEN date_sub(now(), interval 1 month) AND NOW()");
                $database->bind(":commercialId", $commercial['id']);
                
                $locations = $database->obtenerTablaAsociativa();
                $locationArray = array();
                foreach ($locations as $key => $location) {
                    array_push($locationArray, $location);
                }
                $arrayCommercial = [
                    "commercial" => $commercial,
                    "locations" => $locationArray
                ];
                array_push($commercialsLocations, $arrayCommercial);
            }

            echo 
                '{
                    "status": true,
                    "code":100,
                    "data":' . json_encode($commercialsLocations, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE) . '
                }';
            
        }else{
            echo 
                '{
                    "status": false,
                    "code":101,
                    "mensaje":"No hay clientes en la base de datos"
                }'; 
        }
    }
);

//PARA ACTUALIZAR PRIMERA VEZ
$app->get('/get/allCustomers', 
    function () use ($app)
    {
        global $database;
        $database->query("SELECT * FROM customers");

        $numeroFilas = $database->numeroFilas();

        if($numeroFilas > 0){
            $customers = $database->obtenerTablaAsociativa();
            echo 
                '{
                    "status": true,
                    "code":100,
                    "data":' . json_encode($customers, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE) . '
                }';
            
        }else{
            echo 
                '{
                    "status": false,
                    "code":101,
                    "mensaje":"No hay clientes en la base de datos"
                }'; 
        }
    }
);

$app->get('/get/relCommercialsCategories', 
    function () use ($app)
    {        
        global $database;
        $database->query("SELECT * FROM rel_commercials_categories");

        $numeroFilas = $database->numeroFilas();

        if($numeroFilas > 0){
            $relCommCat = $database->obtenerTablaAsociativa();
            echo 
                '{
                    "status": true,
                    "code":100,
                    "data":' . json_encode($relCommCat, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE) . '
                }';
            
        }else{
            echo 
                '{
                    "status": false,
                    "code":101,
                    "mensaje":"No hay relaciones de categorias con comerciales en la base de datos"
                }'; 
        }
    }
);

$app->get('/get/relOrdersProducts', 
    function () use ($app)
    {        
        global $database;
        $database->query("SELECT * FROM rel_orders_products");

        $numeroFilas = $database->numeroFilas();

        if($numeroFilas > 0){
            $relOrderProd = $database->obtenerTablaAsociativa();
            echo 
                '{
                    "status": true,
                    "code":100,
                    "data":' . json_encode($relOrderProd, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE) . '
                }';
            
        }else{
            echo 
                '{
                    "status": false,
                    "code":101,
                    "mensaje":"No hay relaciones de productos con pedidos en la base de datos"
                }'; 
        }
    }
);

$app->get('/get/relProductsCategories', 
    function () use ($app)
    {        
        global $database;
        $database->query("SELECT * FROM rel_products_categories");

        $numeroFilas = $database->numeroFilas();

        if($numeroFilas > 0){
            $relProdCat = $database->obtenerTablaAsociativa();
            echo 
                '{
                    "status": true,
                    "code":100,
                    "data":' . json_encode($relProdCat, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE) . '
                }';
            
        }else{
            echo 
                '{
                    "status": false,
                    "code":101,
                    "mensaje":"No hay relaciones de productos con categorías en la base de datos"
                }'; 
        }
    }
);

$app->get('/get/allOrders',
    function () use ($app)
    {        
        global $database;
        $database->query("SELECT * FROM orders");
        $numeroFilas = $database->numeroFilas();

        if($numeroFilas > 0){
            $orders = $database->obtenerTablaAsociativa();


            echo 
                '{
                    "status": true,
                    "code":100,
                    "pedidos":' . json_encode($orders, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE) . '
                }';
            
        }else{
            echo 
                '{
                    "status": false,
                    "code":101,
                    "mensaje":"No se encuentra ningún pedido en la base de datos"
                }'; 
        }

    }
);

$app->get('/get/allProducts',
    function () use ($app)
    {
        global $database;
        $database->query("SELECT * FROM products");

        $numeroFilas = $database->numeroFilas();

        if($numeroFilas > 0){
            $products = $database->obtenerTablaAsociativa();
            echo 
            '{
                "status" : true,
                "code" : 100,
                "data" : '. json_encode($products, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE) . '
            }';
            
        }else{
            echo 
                '{"
                    "status" : false,
                    "code" : 101,
                    "mensaje" : "No existe ningún producto en la base de datos"
                }'; 
        }
    }
);

$app->get('/get/allCategories',
    function () use ($app)
    {
        global $database;
        $database->query("SELECT * FROM categories");

        $numeroFilas = $database->numeroFilas();

        if($numeroFilas > 0){
            $categories = $database->obtenerTablaAsociativa();
            echo 
                '{
                    "status": true,
                    "code":100,
                    "data":' . json_encode($categories, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE) . '
                }';
        } else {
            echo 
                '{
                    "status": false,
                    "code":101,
                    "mensaje":"No hay categorías en la base de datos"
                }'; 
        }
    }
);

$app->get('/get/allLocations', 
    function () use ($app)
    {        
        global $database;
        $database->query("SELECT * FROM locations");

        $numeroFilas = $database->numeroFilas();

        if($numeroFilas > 0){
            $locations = $database->obtenerTablaAsociativa();
            echo 
                '{
                    "status": true,
                    "code":100,
                    "data":' . json_encode($locations, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE) . '
                }';
            
        }else{
            echo 
                '{
                    "status": false,
                    "code":101,
                    "mensaje":"No hay localizaciones en la base de datos"
                }'; 
        }
    }
);

$app->get('/get/allCommercials', 
    function () use ($app)
    {        
        global $database;
        $database->query("SELECT * FROM commercials");

        $numeroFilas = $database->numeroFilas();

        if($numeroFilas > 0){
            $commercials = $database->obtenerTablaAsociativa();
            echo 
                '{
                    "status": true,
                    "code":100,
                    "data":' . json_encode($commercials, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE) . '
                }';
            
        }else{
            echo 
                '{
                    "status": false,
                    "code":101,
                    "mensaje":"No hay comerciales en la base de datos"
                }'; 
        }
    }
);

//</>

/*$app->get('/generate/pdf',
    function () use ($app)
    {
        $pdf = new PDF();

        $pdf->AddPage();
        $pdf->SetAutoPageBreak(true, 33);
        $pdf->SetLeftMargin(15);
        $pdf->SetFont('Arial','',12);

        $pdf->SetFont('Arial','B',12);
        $pdf->Cell(0,10,utf8_decode('Factura'),0,0,'R');
        $pdf->ln();

        $pdf->SetFont('Arial','',10);
        $pdf->Cell(0,7,utf8_decode('Núm. ' . '00016441'),0,0,'R');
        $pdf->ln();
        $pdf->Cell(0,7,utf8_decode('Pedido de ' . '03.04.2018'),0,0,'R');
        $pdf->ln();
        $pdf->Cell(0,7,utf8_decode('Id cliente: ' . 'DON00909'));
        $pdf->ln();
        $pdf->Cell(0,7,utf8_decode('Id comercial: ' . 'JAVIER FELI'));
        $pdf->ln();
        $pdf->Cell(0,7,utf8_decode('Id tablet: ' . '88f1235b19c'));
        $pdf->ln();
        $pdf->Cell(0,7,utf8_decode('FRANCISCO DONOSO PEREZ-CABRERO'));
        $pdf->ln();
        $pdf->Cell(0,7,utf8_decode('C/ FUENCARRAL, 80 (ESTANCO) 28004'));
        $pdf->ln();
        $pdf->Cell(0,7,utf8_decode('MADRID MADRID'));
        $pdf->ln();
        $pdf->ln();
        $pdf->ln();
        //AQUI VA LA TABLA!!!!!!

        // Colores, ancho de línea y fuente en negrita
        $pdf->SetFillColor(199,199,199);
        $pdf->SetTextColor(0);
        $pdf->SetDrawColor(0,0,0);
        $pdf->SetLineWidth(.3);
        $pdf->SetFont('','B');

        // Cabecera
        $header = array(utf8_decode('Nombre de producto'), utf8_decode('Código'), utf8_decode('Cantidad'), utf8_decode('Precio unidad'), utf8_decode('Total'));
        $w = array(80, 17, 17, 25, 35);
        //for($i=0;$i<count($header);$i++){
        //    $pdf->SetFont('Arial','B',8);
        //    $pdf->Cell($w[$i],5,$header[$i],1,0,'L',true);
        //} 
        $pdf->SetFont('Arial','B',8);
        
        $pdf->Cell($w[0], 5, $header[0], 1, 0, 'L', true);
        $pdf->Cell($w[1], 5, $header[1], 1, 0, 'C', true);
        $pdf->Cell($w[2], 5, $header[2], 1, 0, 'C', true);
        $pdf->Cell($w[3], 5, $header[3], 1, 0, 'R', true);
        $pdf->Cell($w[4], 5, $header[4], 1, 0, 'R', true);
        $pdf->Ln();

        // Restauración de colores y fuentes
        $pdf->SetFillColor(224,235,255);
        $pdf->SetTextColor(0);
        $pdf->SetFont('');
        // Datos
        $pdf->SetFont('Arial','',8);        
        for($i=1;$i<=69;$i++)
        {
            $pdf->Cell($w[0],8,'PAPEL DE FUMAR EXP ABADIE NATURA REG 240',1);
            $pdf->Cell($w[1],8,'7471',1);
            $pdf->Cell($w[2],8,'1',1);
            $pdf->Cell($w[3],8,'*32,640 EUR',1);
            $pdf->Cell($w[4],8,'32,640 EUR',1,0,'R');
            $pdf->Ln();
        }
        // TOTAL
        $pdf->SetFillColor(199,199,199);
        $pdf->Cell($w[0], 6, '', 'LTB', 0, 'C', true);
        $pdf->Cell($w[1], 6, '', 'TB', 0, 'C', true);
        $pdf->Cell($w[2], 6, '', 'TB', 0, 'C', true);
        $pdf->Cell($w[3], 6, 'TOTAL', 'TBR', 0, 'R', true);
        $pdf->Cell($w[4], 6, '3000,360€', 'TBR', 0, 'R');

        $pdf->Output("F", "123124_asdasc80ascaSc2213.pdf");
    }
);*/

$app->get('/generate/pdf',
    function () use ($app)
    {
        /*global $database;
        $totalFinal = 0;
        //RECUPERAMOS TODAS LAS CATEGORÍAS DE LOS PRODUCTOS QUE ESTÁN EN EL PEDIDO. SEPARÁNDOLOS POR CATEGORÍAS
        $database->query("
                        SELECT 
                             c.id AS idC, c.name AS nameC
                        FROM 
                            categories c, rel_products_categories rel 
                        WHERE 
                            c.id = rel.id_categories 
                        AND 
                            rel.id_products IN (SELECT p.id FROM products p, rel_orders_products relOP WHERE p.id = relOP.id_products AND relOP.id_orders = :orderId)
                        GROUP BY
                            c.name");
        $database->bind(":orderId", 13811);

        $categories = $database->obtenerTablaAsociativa();

        foreach ($categories as $key => $category) {
            var_dump($category);
        
            $database->query('SELECT  
                                p.id,
                                p.name, 
                                p.cod_product, 
                                r.quantity, 
                                r.product_total, 
                                (r.product_total*r.quantity) AS total 
                            FROM 
                                products as p, 
                                rel_orders_products as r 
                            WHERE 
                                p.id=r.id_products 
                            AND 
                                r.id_orders=:orderId 
                            AND 
                                p.id IN (SELECT rel.id_products FROM rel_products_categories rel WHERE rel.id_categories = :categoryId) 
                            ');

            $database->bind(":orderId", 13811);
            $database->bind(":categoryId", $category['idC']);
            $productsByCategory = $database->obtenerTablaAsociativa();
            foreach ($productsByCategory as $key => $product) {
                var_dump($product);
            }
        }*/

        $a = array();
        $a[] = "paco";
        //var_dump($a);
        $a["paco"][] = "paca";
        $a["paco"][] = "pepito";
        echo "<br>";
        //var_dump($a["paco"]);
    }
);

$app->run();

function sendEmail($pdfName, $codOrder){//ESTA ES LA BUENA!!!!!!
    $email = "estelamuco@gmail.com";
    $emailAddress = "info@zarko.es";
    $nombre = "ZARKO ARTICULOS DE FUMADOR";
    try { 
        $mail = new PHPMailer(true);
        $mail->IsMail();
        $mail->CharSet = 'UTF-8';
        $mail->AddAddress($email);
        $mail->SetFrom($emailAddress, $nombre);
        $mail->MsgHTML("<b>Adjuntado PDF con el contenido del pedido Nº" . $codOrder . "</b>");
        $mail->Subject = "Pedido Nº" . $codOrder;
        $mail->AddAttachment("../intranet/assets/pdf/" . $pdfName);
        $mail->Send();
    } catch (Exception $e) { 
        echo $e->errorMessage();
    }
}

function cadenaAleatoria(){
    $caracteres = "ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz1234567890"; 	   
    $cadena = "";
    for($i=0; $i<33; $i++){
       $cadena .= substr($caracteres,rand(0,strlen($caracteres)),1); 
    }
    return $cadena;
}

function arrayFactura($array){
    $arrayFactura = array();
    foreach ($array as $key => $value) {
        if(!array_key_exists($value["nameC"],$arrayFactura)){
            $arrayFactura[$value["nameC"]] = array();
            array_push($arrayFactura[$value["nameC"]],$value["id_products"]);
        }else{
            array_push($arrayFactura[$value["nameC"]],$value["id_products"]);
        }
        
    }

    return $arrayFactura;
}

function cargarProducto($idProducto, $idPedido){
    global $database;
    $totalFinal = 0;
    //RECUPERAMOS TODAS LAS CATEGORÍAS DE LOS PRODUCTOS QUE ESTÁN EN EL PEDIDO. SEPARÁNDOLOS POR CATEGORÍAS
    $database->query("
                    SELECT 
                    p.image,
                    p.name, 
                    p.cod_product, 
                    r.quantity, 
                    r.product_total,
                    r.real_total,
                    (r.product_total*r.quantity) AS total 
                        FROM 
                            products as p INNER JOIN rel_orders_products as r ON  p.id=r.id_products
                        WHERE 
                            p.id = :productId
                        AND
                            r.id_orders = :orderId");
    $database->bind(":productId", $idProducto);
    $database->bind(":orderId", $idPedido);
    
    return $database->obtenerTablaAsociativa();
}

function generarpdfBase($orderId, $codOrder, $addDate, $customerCod, $commercialCod, $commercialTablet, $customerName, $customerStreet, $customerZip, $customerCity, $customerState, $pdfName, $orderTotal, $observations, $latitude, $longitude){//ORIGINAL
	define('EURO',chr(128));//AÑADE
    global $database;
    $totalFinal = 0;
    //RECUPERAMOS TODAS LAS CATEGORÍAS DE LOS PRODUCTOS QUE ESTÁN EN EL PEDIDO. SEPARÁNDOLOS POR CATEGORÍAS
    $database->query("
                    SELECT 
                        rel.id_products, MIN(rel.id_categories) as categories, c.id AS idC, c.name AS nameC
                    FROM 
                        categories c, rel_products_categories rel 
                    WHERE 
                        c.id = rel.id_categories 
                    AND 
                        rel.id_products IN (SELECT p.id FROM products p, rel_orders_products relOP WHERE p.id = relOP.id_products AND
                         relOP.id_orders = :orderId)
                    GROUP BY
                        rel.id_products");
    $database->bind(":orderId", $orderId);

    $categories = $database->obtenerTablaAsociativa();

    $factura = arrayFactura($categories);

    //FORMATO FECHA
    $fechaCreacion = new DateTime($addDate);
    $addDate = $fechaCreacion->format('Y.m.d H:i:s');

    $pdf = new PDF();
    $pdf->AddPage();
    $pdf->SetAutoPageBreak(true, 2); //33
    $pdf->SetLeftMargin(20);
    $pdf->SetFont('Arial','',12);

    $pdf->SetFont('Arial','B',12);
    $pdf->Cell(0,10,utf8_decode('Factura'),0,0,'R');
    $pdf->ln();

    $pdf->SetFont('Arial','',10);
    $pdf->ln();
    $pdf->Cell(150,7,utf8_decode('Id comercial: ' . $commercialCod));
    $pdf->Cell(20,7,utf8_decode('Núm. ' . $codOrder),0,0,'R');
    $pdf->ln();
    if ($latitude != "" AND $longitude != "") {
        $pdf->Cell(150,7,utf8_decode('Ubicación del comercial: '). $latitude . ", " . $longitude);
        $pdf->Cell(20,7,utf8_decode('Pedido de ' . $addDate),0,0,'R');
        $pdf->ln();
        $pdf->Cell(0,7,utf8_decode('Id cliente: ' . $customerCod));
        $pdf->ln();        
    }else{
        $pdf->Cell(150,7,utf8_decode('Id cliente: ' . $customerCod));
        $pdf->Cell(20,7,utf8_decode('Pedido de ' . $addDate),0,0,'R');
        $pdf->Ln();
    }
    $pdf->Cell(0,7,utf8_decode($customerName));
    $pdf->ln();
    $pdf->Cell(0,7,utf8_decode($customerStreet . ' ' . $customerZip));
    $pdf->ln();
    $pdf->Cell(0,7,utf8_decode($customerCity . ' ' . $customerState));
    $pdf->ln();
    $pdf->ln();
    $pdf->ln();
    
    //AQUI VA LA TABLA!!!!!!
    foreach ($factura as $key => $value) {
        $pdf->SetFont('Arial','B',10);
        $pdf->Cell(0,7,utf8_decode($key), 0, 0, 'L');
        $pdf->Ln();

        // Colores, ancho de línea y fuente en negrita
        $pdf->SetFillColor(199,199,199);
        $pdf->SetTextColor(0);
        $pdf->SetDrawColor(0,0,0);
        $pdf->SetLineWidth(.3);
        $pdf->SetFont('','B');

        // Cabecera
        /*$header = array(utf8_decode('Imagen'), utf8_decode('Nombre de producto'), utf8_decode('Código'), utf8_decode('Cantidad'), utf8_decode('Precio unidad'), utf8_decode('Sin Dto'), utf8_decode('Total'));
        //$w = array(16,72, 12, 15, 25, 30);
        $w = array(16,74, 12, 15, 20, 14, 20);*/
        $header = array(utf8_decode('Imagen'), utf8_decode('Nombre de producto'), utf8_decode('Código'), utf8_decode('Cantidad'), utf8_decode('Precio unidad'),utf8_decode('Sin Dto') ,utf8_decode('Total'));
        $w = array(16,74, 12, 15, 20, 14, 20);//EDITADO LOS DOS

        //CABECERA TABLA
        $pdf->SetFont('Arial','B',8);
        $pdf->Cell($w[0], 5, $header[0], 1, 0, 'C', true);
        $pdf->Cell($w[1], 5, $header[1], 1, 0, 'L', true);
        $pdf->Cell($w[2], 5, $header[2], 1, 0, 'R', true);
        $pdf->Cell($w[3], 5, $header[3], 1, 0, 'R', true);
        $pdf->Cell($w[4], 5, $header[4], 1, 0, 'R', true);
        $pdf->Cell($w[5], 5, $header[5], 1, 0, 'R', true);
        $pdf->Cell($w[6], 5, $header[6], 1, 0, 'R', true);//AÑADIDA
        $pdf->Ln();
    
        // Datos
        $pdf->SetFont('Arial','',8);

        foreach ($value as $productArray) {
            $product = cargarProducto($productArray, $orderId);
            //var_dump($product[0]);
            if(isset($product[0]['image']) AND $product[0]['image'] != "" AND is_file('../intranet/assets/images/products/' . $product[0]['image'])){
                $pdf->Cell($w[0], 15, $pdf->Image('../intranet/assets/images/products/' . $product[0]['image'], $pdf->GetX(), $pdf->GetY(),16, 15), 1);
            }else{
                $pdf->Cell($w[0], 15, $pdf->Image('assets/images/products/logo_zarko_ma.jpg', $pdf->GetX(), $pdf->GetY(),16, 15), 1);
            }
            $pdf->Cell($w[1], 15, utf8_decode($product[0]['name']), 1, 0, 'L');
            $pdf->Cell($w[2], 15, $product[0]['cod_product'], 1, 0, 'R');
            $pdf->Cell($w[3], 15, $product[0]['quantity'], 1, 0, 'R');
            //$pdf->Cell($w[4], 15, number_format($product[0]['product_total'], 3, ',', '') . ' EUR', 1, 0, 'R');
            $pdf->Cell($w[4], 15, number_format($product[0]['product_total'], 3, ',', '') . ' ' . EURO, 1, 0, 'R');//AÑADIDO
            if (!empty($product[0]['real_total'])) {//AÑADIDO
                $pdf->Cell($w[5], 15, number_format($product[0]['real_total'], 3, ',', '') . ' ' . EURO, 1, 0, 'R');//AÑADIDO
            }else{//AÑADIDO
                $pdf->Cell($w[5], 15,'', 1, 0, 'R');//AÑADIDO
            }//AÑADIDO
            $pdf->SetFont('Arial','B',8);            
            //$pdf->Cell($w[5], 15, number_format($product[0]['total'], 3, ',', '') . ' EUR', 1, 0, 'R');
            $pdf->Cell($w[6], 15, number_format($product[0]['total'], 3, ',', '') . ' ' . EURO, 1, 0, 'R');//AÑADIDO
            $pdf->SetFont('Arial','', 8);
            $totalFinal += $product[0]['total'];
            $pdf->Ln();
            $pdf->Cell(0, 20, '', 0, 0, 'R');//AÑADIDO
            $pdf->Ln(5);//AÑADIDO
        }
        $pdf->Ln();
    }
    

    // TOTAL - PIE DE TABLA
    $pdf->SetFillColor(199,199,199);
    $pdf->Cell($w[0], 6, '', 'LTB', 0, 'C', true);
    $pdf->Cell($w[1], 6, '', 'TB', 0, 'C', true);
    $pdf->Cell($w[2], 6, '', 'TB', 0, 'C', true);
    $pdf->Cell($w[3], 6, '', 'TB', 0, 'C', true);
    //$pdf->Cell($w[4], 6, 'TOTAL', 'TBR', 0, 'R', true);
    $pdf->SetFont('Arial','B',10);
    $pdf->Cell($w[4], 6, 'TOTAL', 'TBR', 0, 'C', true);//AÑADIDO
    $pdf->Cell($w[5], 6, '', 'TB', 0, 'C', false);//AÑADIDO
    $pdf->Cell($w[6], 6, number_format($totalFinal, 3, ',', '') . ' EUR', 'TBR', 0, 'R');//AÑADIDO
    //$pdf->Cell($w[5], 6, number_format($totalFinal, 3, ',', '') . ' EUR', 'TBR', 0, 'R');
    $pdf->SetFont('Arial','B',9);
    $pdf->Ln();
    $pdf->Ln();
    $pdf->Cell(0,4,utf8_decode("Observaciones:"),0,'L');
    $pdf->Ln();
    $pdf->Ln();
    $pdf->SetFont('Arial','',8);
    $pdf->SetLeftMargin(30);
    $pdf->MultiCell(0,5,utf8_decode($observations),0,'L');
    $pdf->SetLeftMargin(20);
    $pdf->Ln();  //AÑADIDO
    $pdf->Ln();//AÑADIDO
    $pdf->Ln();//AÑADIDO
    $pdf->SetFont('Arial','B',10);//AÑADIDO
    $pdf->MultiCell(0,4,utf8_decode('REVISAR LOS PAQUETES A LA RECEPCIÓN DE LA MERCANCÍA ANTES DE SELLAR EL ALBARÁN AL TRANSPORTISTA, EN CASO DE OBSERVAR ALGUNA ANOMALÍA O DESPERFECTO, ANÓTELO EN EL ALBARÁN, SI NO SE HACE ASÍ, NO PODREMOS ATENDER RECLAMACIONES DE DESPERFECTOS'),0,'C');//AÑADIDO
    $pdf->SetFont('Arial','I',9);    //AÑADIDO    
    $pdf->Cell(0,5,utf8_decode('Zarko Artículos de fumador'),0,0,'C');//AÑADIDO

    $pdf->Output("F", "../intranet/assets/pdf/" . $pdfName);
    
    //ENVÍO DE EMAIL
    sendEmail($pdfName, $codOrder);
}


function generarpdf2($orderId, $codOrder, $addDate, $customerCod, $commercialCod, $commercialTablet, $customerName, $customerStreet, $customerZip, $customerCity, $customerState, $pdfName, $orderTotal){//ORIGINAL
    global $database;
    $totalFinal = 0;
    //RECUPERAMOS TODAS LAS CATEGORÍAS DE LOS PRODUCTOS QUE ESTÁN EN EL PEDIDO. SEPARÁNDOLOS POR CATEGORÍAS
    $database->query("
                    SELECT 
                        rel.id_products, rel.id_categories, c.id AS idC, c.name AS nameC
                    FROM 
                        categories c, rel_products_categories rel 
                    WHERE 
                        c.id = rel.id_categories 
                    AND 
                        rel.id_products IN (SELECT p.id FROM products p, rel_orders_products relOP WHERE p.id = relOP.id_products AND relOP.id_orders = :orderId)
                    GROUP BY
                        c.name");
    $database->bind(":orderId", $orderId);

    $categories = $database->obtenerTablaAsociativa();
    //var_dump($categories);

    //FORMATO FECHA
    $fechaCreacion = new DateTime($addDate);
    $addDate = $fechaCreacion->format('Y.m.d');

    $pdf = new PDF();
    $pdf->AddPage();
    $pdf->SetAutoPageBreak(true, 33);
    $pdf->SetLeftMargin(20);
    $pdf->SetFont('Arial','',12);

    $pdf->SetFont('Arial','B',12);
    $pdf->Cell(0,10,utf8_decode('Factura'),0,0,'R');
    $pdf->ln();

    $pdf->SetFont('Arial','',10);
    //$pdf->ln();
    $pdf->ln();
    $pdf->Cell(150,7,utf8_decode('Id cliente: ' . $customerCod));
    $pdf->Cell(20,7,utf8_decode('Núm. ' . $codOrder),0,0,'R');
    $pdf->ln();
    $pdf->Cell(150,7,utf8_decode('Id comercial: ' . $commercialCod));
    $pdf->Cell(20,7,utf8_decode('Pedido de ' . $addDate),0,0,'R');
    $pdf->ln();
    $pdf->Cell(0,7,utf8_decode('Id tablet: ' . $commercialTablet));
    $pdf->ln();
    $pdf->Cell(0,7,utf8_decode($customerName));
    $pdf->ln();
    $pdf->Cell(0,7,utf8_decode($customerStreet . ' ' . $customerZip));
    $pdf->ln();
    $pdf->Cell(0,7,utf8_decode($customerCity . ' ' . $customerState));
    $pdf->ln();
    $pdf->ln();
    $pdf->ln();
    
    $w = array(75, 17, 17, 25, 35);
    //AQUI VA LA TABLA!!!!!!
    foreach ($categories as $category) {
        //var_dump("CATEGORIA " . $category['nameC']);
        $pdf->SetFont('Arial','B',10);
        $pdf->Cell(0,7,utf8_decode($category['nameC']), 0, 0, 'L');
        $pdf->Ln();

        $database->query('SELECT 
                            p.name, 
                            p.cod_product, 
                            r.quantity, 
                            r.product_total, 
                            (r.product_total*r.quantity) AS total 
                        FROM 
                            products as p, 
                            rel_orders_products as r 
                        WHERE 
                            p.id=r.id_products 
                        AND 
                            r.id_orders=:orderId 
                        AND 
                            p.id IN (SELECT rel.id_products FROM rel_products_categories rel WHERE rel.id_categories = :categoryId) 
                        GROUP BY 
                            p.id');

        /*$database->query('SELECT 
                            p.name, 
                            p.cod_product, 
                            r.quantity, 
                            r.product_total, 
                            (r.product_total*r.quantity) AS total 
                        FROM 
                            products as p, 
                            rel_orders_products as r,
                            categories as c
                        WHERE 
                            c.id = :categoryId
                        AND
                            p.id=r.id_products 
                        AND 
                            r.id_orders=:orderId 
                        AND 
                            p.id IN (SELECT rel.id_products FROM rel_products_categories rel WHERE rel.id_categories = :categoryId) 
                         GROUP BY 
                            p.name');*/
        $database->bind(":orderId", $orderId);
        $database->bind(":categoryId", $category['idC']);
        $productsByCategory = $database->obtenerTablaAsociativa();

        // Colores, ancho de línea y fuente en negrita
        $pdf->SetFillColor(199,199,199);
        $pdf->SetTextColor(0);
        $pdf->SetDrawColor(0,0,0);
        $pdf->SetLineWidth(.3);
        $pdf->SetFont('','B');

        // Cabecera
        $header = array(utf8_decode('Nombre de producto'), utf8_decode('Código'), utf8_decode('Cantidad'), utf8_decode('Precio unidad'), utf8_decode('Total'));

        //CABECERA TABLA
        $pdf->SetFont('Arial','B',8);
        $pdf->Cell($w[0], 5, $header[0], 1, 0, 'L', true);
        $pdf->Cell($w[1], 5, $header[1], 1, 0, 'L', true);
        $pdf->Cell($w[2], 5, $header[2], 1, 0, 'L', true);
        $pdf->Cell($w[3], 5, $header[3], 1, 0, 'L', true);
        $pdf->Cell($w[4], 5, $header[4], 1, 0, 'R', true);
        $pdf->Ln();
    
        // Datos
        $pdf->SetFont('Arial','',8);
 
        //var_dump($productsByCategory);
        foreach ($productsByCategory as $product) {
            $pdf->Cell($w[0], 8, utf8_decode($product['name']), 1);
            $pdf->Cell($w[1], 8, $product['cod_product'], 1);
            $pdf->Cell($w[2], 8, /*$count +*/$product['quantity'],1);
            $pdf->Cell($w[3], 8, number_format($product['product_total'], 3, ',', '') . ' EUR', 1);
            $pdf->Cell($w[4], 8, number_format($product['total'], 3, ',', '') . ' EUR', 1, 0, 'R');
            $pdf->Ln();
            $totalFinal += $product['total'];
            //$count += $product['quantity'];
        }
        $pdf->Ln();        
    }
    

    // TOTAL - PIE DE TABLA
    $pdf->SetFillColor(199,199,199);
    $pdf->Cell($w[0], 6, '', 'LTB', 0, 'C', true);
    $pdf->Cell($w[1], 6, '', 'TB', 0, 'C', true);
    $pdf->Cell($w[2], 6, '', 'TB', 0, 'C', true);
    $pdf->Cell($w[3], 6, 'TOTAL', 'TBR', 0, 'R', true);
    $pdf->Cell($w[4], 6, number_format($totalFinal, 3, ',', '') . ' EUR', 'TBR', 0, 'R');


    $pdf->Output("F", "../intranet/assets/pdf/" . $pdfName);
}

function generarpdfBase2($orderId, $codOrder, $addDate, $customerCod, $commercialCod, $commercialTablet, $customerName, $customerStreet, $customerZip, $customerCity, $customerState, $pdfName, $orderTotal, $observations, $latitude, $longitude){//ORIGINAL
    global $database;
    $totalFinal = 0;
    $database->query("
                    SELECT 
                        p.id,
                        p.name, 
                        p.cod_product, 
                        p.image,
                        r.quantity, 
                        r.product_total, 
                        (r.product_total*r.quantity) AS total 
                    FROM 
                        products as p, 
                        rel_orders_products as r 
                    WHERE 
                        p.id=r.id_products 
                    AND 
                        r.id_orders=:orderId 
                    GROUP BY 
                        p.id");
    $database->bind(":orderId", $orderId);

    $products = $database->obtenerTablaAsociativa();

    //FORMATO FECHA
    $fechaCreacion = new DateTime($addDate);
    $addDate = $fechaCreacion->format('Y.m.d H:i:s');

    $pdf = new PDF();
    $pdf->AddPage();
    $pdf->SetAutoPageBreak(true, 33);
    $pdf->SetLeftMargin(20);
    $pdf->SetFont('Arial','',12);

    $pdf->SetFont('Arial','B',12);
    $pdf->Cell(0,10,utf8_decode('Factura'),0,0,'R');
    $pdf->ln();

    /*$pdf->SetFont('Arial','',10);
    $pdf->ln();
    $pdf->Cell(150,7,utf8_decode('Id cliente: ' . $customerCod));
    $pdf->Cell(20,7,utf8_decode('Núm. ' . $codOrder),0,0,'R');
    $pdf->ln();
    $pdf->Cell(150,7,utf8_decode('Id comercial: ' . $commercialCod));
    $pdf->Cell(20,7,utf8_decode('Pedido de ' . $addDate),0,0,'R');
    $pdf->ln();*/


    //PARA COLOCAR DOS EN UNA MISMA CELDA...  
    //$pdf->Cell(150,7,utf8_decode('Id cliente: ' . $customerCod));
    //$pdf->Cell(20,7,utf8_decode('Núm. ' . $codOrder),0,0,'R');
    //</>




    $pdf->SetFont('Arial','',10);
    $pdf->ln();
    $pdf->Cell(150,7,utf8_decode('Id comercial: ' . $commercialCod));
    $pdf->Cell(20,7,utf8_decode('Núm. ' . $codOrder),0,0,'R');
    $pdf->ln();
    if ($latitude != "" AND $longitude != "") {
        $pdf->Cell(150,7,utf8_decode('Ubicación del comercial: '). $latitude . ", " . $longitude);
        $pdf->Cell(20,7,utf8_decode('Pedido de ' . $addDate),0,0,'R');
        $pdf->ln();
        $pdf->Cell(0,7,utf8_decode('Id cliente: ' . $customerCod));
        $pdf->ln();        
    }else{
        $pdf->Cell(150,7,utf8_decode('Id cliente: ' . $customerCod));
        $pdf->Cell(20,7,utf8_decode('Pedido de ' . $addDate),0,0,'R');
        $pdf->Ln();
    }
    $pdf->Cell(0,7,utf8_decode($customerName));
    $pdf->ln();
    $pdf->Cell(0,7,utf8_decode($customerStreet . ' ' . $customerZip));
    $pdf->ln();
    $pdf->Cell(0,7,utf8_decode($customerCity . ' ' . $customerState));
    $pdf->ln();
    $pdf->ln();
    $pdf->ln();

    // Colores, ancho de línea y fuente en negrita
    $pdf->SetFillColor(199,199,199);
    $pdf->SetTextColor(0);
    $pdf->SetDrawColor(0,0,0);
    $pdf->SetLineWidth(.3);
    $pdf->SetFont('','B');

    // Cabecera
    $header = array(utf8_decode('Imagen'), utf8_decode('Nombre de producto'), utf8_decode('Código'), utf8_decode('Cantidad'), utf8_decode('Precio unidad'), utf8_decode('Total'));
    $w = array(16,72, 12, 15, 25, 30);

    //CABECERA TABLA
    $pdf->SetFont('Arial','B',8);
    $pdf->Cell($w[0], 5, $header[0], 1, 0, 'C', true);
    $pdf->Cell($w[1], 5, $header[1], 1, 0, 'L', true);
    $pdf->Cell($w[2], 5, $header[2], 1, 0, 'R', true);
    $pdf->Cell($w[3], 5, $header[3], 1, 0, 'R', true);
    $pdf->Cell($w[4], 5, $header[4], 1, 0, 'R', true);
    $pdf->Cell($w[5], 5, $header[5], 1, 0, 'R', true);
    //$pdf->Cell($w[6], 5, $header[6], 1, 0, 'R', true);
    $pdf->Ln();

    // Datos
    $pdf->SetFont('Arial','',8);
    //AQUI VA LA TABLA!!!!!!
    foreach ($products as $product) {
        $database->query("SELECT 
                            c.id AS idC, c.name AS nameC
                        FROM 
                            products p, rel_products_categories rel, categories c
                        WHERE 
                            p.id = rel.id_products
                        AND
                            c.id = rel.id_categories 
                        AND
                            p.id = :idProducto
                        GROUP BY
                            c.name");

        $database->bind(":idProducto", $product['id']);
        $categories = $database->obtenerTablaAsociativa();   
        //$cadena = "";
        $pdf->MultiCell(0,5,utf8_decode("Categoría/s: "),0,'L');
        $pdf->SetFont('Arial','B',8);
        foreach ($categories as $category) {
            //if($category === end($categories)){
                if($category === reset($categories)){
                    $pdf->MultiCell(0, 5, $category['nameC'] , 0, 'L');
                }else if($category === end($categories)){
                    $pdf->MultiCell(0, 5, $category['nameC'] , 'B', 'L');
                }
                //$cadena .= $category['nameC'];

            //}else{
                //$cadena .= $category['nameC'] . ", ";
            //}
        }
        $pdf->SetFont('Arial','',8);        
        //$pdf->MultiCell(0,5,"Categoria/s: " . $cadena ,1,'L');
        if(isset($product['image']) AND $product['image'] != "" AND is_file('../intranet/assets/images/products/' . $product['image'])){
            $pdf->Cell($w[0], 15, $pdf->Image('../intranet/assets/images/products/' . $product['image'], $pdf->GetX(), $pdf->GetY(),16, 15), 1);
        }else{
            $pdf->Cell($w[0], 15, $pdf->Image('assets/images/products/logo_zarko_ma.jpg', $pdf->GetX(), $pdf->GetY(),16, 15), 1);
        }
        $pdf->Cell($w[1], 15, utf8_decode($product['name']), 1, 0, 'L');
        $pdf->Cell($w[2], 15, $product['cod_product'], 1, 0, 'R');
        $pdf->Cell($w[3], 15, /*$count +*/$product['quantity'], 1, 0, 'R');
        $pdf->Cell($w[4], 15, number_format($product['product_total'], 3, ',', '') . ' EUR', 1, 0, 'R');
        $pdf->SetFont('Arial','B',8);
        $pdf->Cell($w[5], 15, number_format($product['total'], 3, ',', '') . ' EUR', 1, 0, 'R');
        $pdf->SetFont('Arial','', 8);
        if($product !== end($products)){
            $pdf->Ln(6);
        }
        $pdf->Ln();
        $totalFinal += $product['total'];
    }
    
    // TOTAL - PIE DE TABLA
    $pdf->SetFillColor(199,199,199);
    $pdf->Cell($w[0], 6, '', 'LTB', 0, 'C', true);
    $pdf->Cell($w[1], 6, '', 'TB', 0, 'C', true);
    $pdf->Cell($w[2], 6, '', 'TB', 0, 'C', true);
    $pdf->Cell($w[3], 6, '', 'TB', 0, 'C', true);
    $pdf->Cell($w[4], 6, 'TOTAL', 'TBR', 0, 'R', true);
    $pdf->SetFont('Arial','B',10);
    $pdf->Cell($w[5], 6, number_format($totalFinal, 3, ',', '') . ' EUR', 'TBR', 0, 'R');
    $pdf->SetFont('Arial','B',9);
    $pdf->Ln();
    $pdf->Ln();
    $pdf->Cell(0,4,utf8_decode("Observaciones:"),0,'L');
    $pdf->Ln();
    $pdf->Ln();
    $pdf->SetFont('Arial','',8);
    $pdf->SetLeftMargin(30);
    $pdf->MultiCell(0,5,utf8_decode($observations),0,'L');
    $pdf->SetLeftMargin(20);

    $pdf->Output("F", "../intranet/assets/pdf/" . $pdfName);

    //ENVÍO DE EMAIL
    sendEmail($pdfName, $codOrder);
}

function generarpdf11_4($orderId, $codOrder, $addDate, $customerCod, $commercialCod, $commercialTablet, $customerName, $customerStreet, $customerZip, $customerCity, $customerState, $pdfName, $orderTotal){//CAMBIADA
    global $database;
    $totalFinal = 0;
    //RECUPERAMOS TODAS LAS CATEGORÍAS DE LOS PRODUCTOS QUE ESTÁN EN EL PEDIDO. SEPARÁNDOLOS POR CATEGORÍAS
    $database->query("
                    SELECT 
                        rel.id_products, rel.id_categories, c.id AS idC, c.name AS nameC
                    FROM 
                        categories c, rel_products_categories rel 
                    WHERE 
                        c.id = rel.id_categories 
                    AND 
                        rel.id_products IN (SELECT p.id FROM products p, rel_orders_products relOP WHERE p.id = relOP.id_products AND relOP.id_orders = :orderId)
                    GROUP BY
                        c.name");
    $database->bind(":orderId", $orderId);

    $categories = $database->obtenerTablaAsociativa();
    //var_dump($categories);

    //FORMATO FECHA
    $fechaCreacion = new DateTime($addDate);
    $addDate = $fechaCreacion->format('Y.m.d');

    $pdf = new PDF();
    $pdf->AddPage();
    $pdf->SetAutoPageBreak(true, 33);
    $pdf->SetLeftMargin(20);
    $pdf->SetFont('Arial','',12);

    $pdf->SetFont('Arial','B',12);
    $pdf->Cell(0,10,utf8_decode('Factura'),0,0,'R');
    $pdf->ln();

    $pdf->SetFont('Arial','',10);
    //$pdf->ln();
    $pdf->ln();
    $pdf->Cell(150,7,utf8_decode('Id cliente: ' . $customerCod));
    $pdf->Cell(20,7,utf8_decode('Núm. ' . $codOrder),0,0,'R');
    $pdf->ln();
    $pdf->Cell(150,7,utf8_decode('Id comercial: ' . $commercialCod));
    $pdf->Cell(20,7,utf8_decode('Pedido de ' . $addDate),0,0,'R');
    $pdf->ln();
    $pdf->Cell(0,7,utf8_decode('Id tablet: ' . $commercialTablet));
    $pdf->ln();
    $pdf->Cell(0,7,utf8_decode($customerName));
    $pdf->ln();
    $pdf->Cell(0,7,utf8_decode($customerStreet . ' ' . $customerZip));
    $pdf->ln();
    $pdf->Cell(0,7,utf8_decode($customerCity . ' ' . $customerState));
    $pdf->ln();
    $pdf->ln();
    $pdf->ln();
    
    $w = array(75, 17, 17, 25, 35);
    //AQUI VA LA TABLA!!!!!!
    foreach ($categories as $category) {
        //var_dump("CATEGORIA " . $category['nameC']);
        $pdf->SetFont('Arial','B',10);
        //$pdf->Cell(0,7,utf8_decode($category['nameC']), 0, 0, 'L');
        $pdf->Ln();

        $database->query('SELECT 
                            p.id,
                            p.name, 
                            p.cod_product, 
                            r.quantity, 
                            r.product_total, 
                            (r.product_total*r.quantity) AS total 
                        FROM 
                            products as p, 
                            rel_orders_products as r 
                        WHERE 
                            p.id=r.id_products 
                        AND 
                            r.id_orders=:orderId 
                        AND 
                            p.id IN (SELECT rel.id_products FROM rel_products_categories rel WHERE rel.id_categories = :categoryId) 
                        GROUP BY 
                            p.id');

        /*$database->query('SELECT 
                            p.name, 
                            p.cod_product, 
                            r.quantity, 
                            r.product_total, 
                            (r.product_total*r.quantity) AS total 
                        FROM 
                            products as p, 
                            rel_orders_products as r,
                            categories as c
                        WHERE 
                            c.id = :categoryId
                        AND
                            p.id=r.id_products 
                        AND 
                            r.id_orders=:orderId 
                        AND 
                            p.id IN (SELECT rel.id_products FROM rel_products_categories rel WHERE rel.id_categories = :categoryId) 
                         GROUP BY 
                            p.name');*/
        $database->bind(":orderId", $orderId);
        $database->bind(":categoryId", $category['idC']);
        $productsByCategory = $database->obtenerTablaAsociativa();

        // Colores, ancho de línea y fuente en negrita
        $pdf->SetFillColor(199,199,199);
        $pdf->SetTextColor(0);
        $pdf->SetDrawColor(0,0,0);
        $pdf->SetLineWidth(.3);
        $pdf->SetFont('','B');

        // Cabecera
        $header = array(utf8_decode('Nombre de producto'), utf8_decode('Código'), utf8_decode('Cantidad'), utf8_decode('Precio unidad'), utf8_decode('Total'));

        //CABECERA TABLA
        $pdf->SetFont('Arial','B',8);
        $pdf->Cell($w[0], 5, $header[0], 1, 0, 'L', true);
        $pdf->Cell($w[1], 5, $header[1], 1, 0, 'L', true);
        $pdf->Cell($w[2], 5, $header[2], 1, 0, 'L', true);
        $pdf->Cell($w[3], 5, $header[3], 1, 0, 'L', true);
        $pdf->Cell($w[4], 5, $header[4], 1, 0, 'R', true);
        $pdf->Ln();
    
        // Datos
        $pdf->SetFont('Arial','',8);
 
        //var_dump($productsByCategory);
        foreach ($productsByCategory as $product) {
            $database->query('SELECT 
                                c.id, 
                                c.name 
                            from 
                                categories as c, 
                                products as p, 
                                rel_products_categories as r 
                            where 
                                c.id=r.id_categories 
                            and 
                                p.id=r.id_products 
                            and 
                                p.id=:producto');
            $database->bind(":producto", $product['id']);
            $catByP = $database->obtenerTablaAsociativa();
            
            foreach ($catByP as $cat) {
                $pdf->SetFont('Arial','B',10);
                $pdf->Cell(0,7,utf8_decode($cat['name']), 0, 0, 'L');
                $pdf->Ln();
            }


            $pdf->Cell($w[0], 8, utf8_decode($product['name']), 1);
            $pdf->Cell($w[1], 8, $product['cod_product'], 1);
            $pdf->Cell($w[2], 8, /*$count +*/$product['quantity'],1);
            $pdf->Cell($w[3], 8, number_format($product['product_total'], 3, ',', '') . ' EUR', 1);
            $pdf->Cell($w[4], 8, number_format($product['total'], 3, ',', '') . ' EUR', 1, 0, 'R');
            $pdf->Ln();
            $totalFinal += $product['total'];
            //$count += $product['quantity'];
        }
        $pdf->Ln();        
    }
    

    // TOTAL - PIE DE TABLA
    $pdf->SetFillColor(199,199,199);
    $pdf->Cell($w[0], 6, '', 'LTB', 0, 'C', true);
    $pdf->Cell($w[1], 6, '', 'TB', 0, 'C', true);
    $pdf->Cell($w[2], 6, '', 'TB', 0, 'C', true);
    $pdf->Cell($w[3], 6, 'TOTAL', 'TBR', 0, 'R', true);
    $pdf->Cell($w[4], 6, number_format($totalFinal, 3, ',', '') . ' EUR', 'TBR', 0, 'R');


    $pdf->Output("F", "assets/pdf/" . $pdfName);
}

function generarpdfFake($orderId, $codOrder, $addDate, $customerCod, $commercialCod, $commercialTablet, $customerName, $customerStreet, $customerZip, $customerCity, $customerState, $pdfName, $orderTotal){//CAMBIADA
    global $database;
    $totalFinal = 0;
    //RECUPERAMOS TODAS LAS CATEGORÍAS DE LOS PRODUCTOS QUE ESTÁN EN EL PEDIDO. SEPARÁNDOLOS POR CATEGORÍAS
    $database->query("
                    SELECT 
                        rel.id_products, rel.id_categories, c.id AS idC, c.name AS nameC
                    FROM 
                        categories c, rel_products_categories rel 
                    WHERE 
                        c.id = rel.id_categories 
                    AND 
                        rel.id_products IN (SELECT p.id FROM products p, rel_orders_products relOP WHERE p.id = relOP.id_products AND relOP.id_orders = :orderId)
                    GROUP BY
                        c.name");
    $database->bind(":orderId", $orderId);

    $categories = $database->obtenerTablaAsociativa();
    //var_dump($categories);

    //FORMATO FECHA
    $fechaCreacion = new DateTime($addDate);
    $addDate = $fechaCreacion->format('Y.m.d');

    $pdf = new PDF();
    $pdf->AddPage();
    $pdf->SetAutoPageBreak(true, 33);
    $pdf->SetLeftMargin(20);
    $pdf->SetFont('Arial','',12);

    $pdf->SetFont('Arial','B',12);
    $pdf->Cell(0,10,utf8_decode('Factura'),0,0,'R');
    $pdf->ln();

    $pdf->SetFont('Arial','',10);
    //$pdf->ln();
    $pdf->ln();
    $pdf->Cell(150,7,utf8_decode('Id cliente: ' . $customerCod));
    $pdf->Cell(20,7,utf8_decode('Núm. ' . $codOrder),0,0,'R');
    $pdf->ln();
    $pdf->Cell(150,7,utf8_decode('Id comercial: ' . $commercialCod));
    $pdf->Cell(20,7,utf8_decode('Pedido de ' . $addDate),0,0,'R');
    $pdf->ln();
    $pdf->Cell(0,7,utf8_decode('Id tablet: ' . $commercialTablet));
    $pdf->ln();
    $pdf->Cell(0,7,utf8_decode($customerName));
    $pdf->ln();
    $pdf->Cell(0,7,utf8_decode($customerStreet . ' ' . $customerZip));
    $pdf->ln();
    $pdf->Cell(0,7,utf8_decode($customerCity . ' ' . $customerState));
    $pdf->ln();
    $pdf->ln();
    $pdf->ln();
    
    $w = array(75, 17, 17, 25, 35);
    //AQUI VA LA TABLA!!!!!!
    foreach ($categories as $category) {
        //var_dump("CATEGORIA " . $category['nameC']);
        $pdf->SetFont('Arial','B',10);
        //$pdf->Cell(0,7,utf8_decode($category['nameC']), 0, 0, 'L');
        $pdf->Ln();

        $database->query('SELECT  
                            p.id,
                            p.name, 
                            p.cod_product, 
                            r.quantity, 
                            r.product_total, 
                            (r.product_total*r.quantity) AS total 
                        FROM 
                            products as p, 
                            rel_orders_products as r 
                        WHERE 
                            p.id=r.id_products 
                        AND 
                            r.id_orders=:orderId 
                        AND 
                            p.id IN (SELECT rel.id_products FROM rel_products_categories rel WHERE rel.id_categories = :categoryId) 
                        GROUP BY 
                            p.id');

        /*$database->query('SELECT 
                            p.name, 
                            p.cod_product, 
                            r.quantity, 
                            r.product_total, 
                            (r.product_total*r.quantity) AS total 
                        FROM 
                            products as p, 
                            rel_orders_products as r,
                            categories as c
                        WHERE 
                            c.id = :categoryId
                        AND
                            p.id=r.id_products 
                        AND 
                            r.id_orders=:orderId 
                        AND 
                            p.id IN (SELECT rel.id_products FROM rel_products_categories rel WHERE rel.id_categories = :categoryId) 
                         GROUP BY 
                            p.name');*/
        $database->bind(":orderId", $orderId);
        $database->bind(":categoryId", $category['idC']);
        $productsByCategory = $database->obtenerTablaAsociativa();

        // Colores, ancho de línea y fuente en negrita
        $pdf->SetFillColor(199,199,199);
        $pdf->SetTextColor(0);
        $pdf->SetDrawColor(0,0,0);
        $pdf->SetLineWidth(.3);
        $pdf->SetFont('','B');

        // Cabecera
        $header = array(utf8_decode('Nombre de producto'), utf8_decode('Código'), utf8_decode('Cantidad'), utf8_decode('Precio unidad'), utf8_decode('Total'));

        //CABECERA TABLA
        $pdf->SetFont('Arial','B',8);
        $pdf->Cell($w[0], 5, $header[0], 1, 0, 'L', true);
        $pdf->Cell($w[1], 5, $header[1], 1, 0, 'L', true);
        $pdf->Cell($w[2], 5, $header[2], 1, 0, 'L', true);
        $pdf->Cell($w[3], 5, $header[3], 1, 0, 'L', true);
        $pdf->Cell($w[4], 5, $header[4], 1, 0, 'R', true);
        $pdf->Ln();
    
        // Datos
        $pdf->SetFont('Arial','',8);
 
        //var_dump($productsByCategory);
        foreach ($productsByCategory as $product) {
            $database->query('SELECT 
                                c.id, 
                                c.name 
                            from 
                                categories as c, 
                                products as p, 
                                rel_products_categories as r 
                            where 
                                c.id=r.id_categories 
                            and 
                                p.id=r.id_products 
                            and 
                                p.id=:producto');
            $database->bind(":producto", $product['id']);
            $catByP = $database->obtenerTablaAsociativa();
            
            foreach ($catByP as $cat) {
                $pdf->SetFont('Arial','B',10);
                $pdf->Cell(0,7,utf8_decode($cat['name']), 0, 0, 'L');
                $pdf->Ln();
            }


            $pdf->Cell($w[0], 8, utf8_decode($product['name']), 1);
            $pdf->Cell($w[1], 8, $product['cod_product'], 1);
            $pdf->Cell($w[2], 8, /*$count +*/$product['quantity'],1);
            $pdf->Cell($w[3], 8, number_format($product['product_total'], 3, ',', '') . ' EUR', 1);
            $pdf->Cell($w[4], 8, number_format($product['total'], 3, ',', '') . ' EUR', 1, 0, 'R');
            $pdf->Ln();
            $totalFinal += $product['total'];
            //$count += $product['quantity'];
        }
        $pdf->Ln();        
    }
    

    // TOTAL - PIE DE TABLA
    $pdf->SetFillColor(199,199,199);
    $pdf->Cell($w[0], 6, '', 'LTB', 0, 'C', true);
    $pdf->Cell($w[1], 6, '', 'TB', 0, 'C', true);
    $pdf->Cell($w[2], 6, '', 'TB', 0, 'C', true);
    $pdf->Cell($w[3], 6, 'TOTAL', 'TBR', 0, 'R', true);
    $pdf->Cell($w[4], 6, number_format($totalFinal, 3, ',', '') . ' EUR', 'TBR', 0, 'R');


    $pdf->Output("F", "../intranet/assets/pdf/" . $pdfName);

    //ENVÍO DE EMAIL
    sendEmail($pdfName, $codOrder);
}


 //TODO: CONCRETAR CON JL EL TEMA DE LA SESIÓN, 
 //COMO SE HARÁ AL FINAL EL HECHO DE SABER SI EL USUARIO HA HECHO ALGUNA UPDATE DE ALGO ULTIMAMENTE.
 //Y todo estaria finito.
?>