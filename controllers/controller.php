<?php
    require_once 'models/main.php';

    class Controller extends Main {
        public static function executeProcedure($nombre, $params) {
            $result = Main::executeProcedure($nombre, $params);
            return $result;
        }
        
        public static function addData($tabla, $data) {
            return Main::addData($tabla, $data);
        }
                
        public static function updateData($tabla, $data, $condicion) {
            return Main::updateData($tabla, $data, $condicion);
        }
        
        public static function sql($query) {
            return $result = Main::sql($query);
        }

        public static function selectOneRow($query) {
            $result = Main::sql($query);
            
            $response = array();
            
            if($result->rowCount() > 0) {
                $response = $result->fetchObject();
            }
            return $response;
        }

        public static function getMd5($valor) {
            $sqlMD5 = "SELECT MD5('$valor') as id_md5"; 
            $MD5Execute = self::selectOneRow($sqlMD5);
            return $MD5Execute->id_md5;
        }
        
        public static function sqlQueryFecthAll($query) {
            return Main::sqlQueryFecthAll($query);
        }

        public static function selectAll($query) {
            $result = Main::sql($query);
            
            $response = array();
            
            if($result->rowCount() > 0) {
                while ($data = $result->fetchObject()) {
                    $response[] = $data;
                }
            }
            return $response;
        }

        public static function encryption($string) {
            return Main::encryption($string);
        }

        public static function decryption($string) {
            return Main::decryption($string);
        }

        public static function createToken() {
            return Main::createToken();
        }

        public static function validaToken($token) {
            $qry = "SELECT * FROM solicitud WHERE token = '$token'";
            return self::selectOneRow($qry);
        }

        public static function getActivos($sucursal) {
            $activos = [];

            $accessToken = self::getTokenAuth();

            $url = API_URL."items_details?id_type_item=2&field_6=$sucursal";

            $headers = array(
                "Authorization: Bearer $accessToken",
                "Content-Type: application/json"
            );

            $ch = curl_init();
            curl_setopt($ch, CURLOPT_URL, $url);
            curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

            $response = curl_exec($ch);

            if ($response === false) {
                echo "Error: " . curl_error($ch);
            } else {
                
                $data = json_decode($response, true);                
                
                if ($data['success'] == 1) {
                    foreach ($data['data'] as $item) {
                        
                        $activo =  array(
                            'value' => $item['id'],
                            'label' => $item['description']
                        );
                        array_push($activos, $activo);
                    }
                }
            }

            curl_close($ch);

            return $activos;
        }

        public static function getNewTokenAuth() {
            // URL de la API OAuth
            $oauthUrl = ACCESS_TOKEN_URL;

            // Credenciales del cliente
            $client_id = CLIENT_ID;
            $client_secret = CLIENT_SECRET;

            // Datos para la solicitud POST
            $data = [
                'grant_type' => 'client_credentials'
            ];

            // Inicializar cURL            
            $ch = curl_init($oauthUrl);
            curl_setopt($ch, CURLOPT_POST, true);
            curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($data));
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($ch, CURLOPT_USERPWD, $client_id . ":" . $client_secret);

            // Ejecutar la solicitud
            $response = curl_exec($ch);

            // Cerrar la conexión cURL
            curl_close($ch);

            // Decodificar la respuesta JSON
            $data = json_decode($response, true);

            $token = $data['access_token'];

            return $token;
        }

        public static function getTokenAuth() {
            $qry = "SELECT token FROM token WHERE NOW() BETWEEN fecha_creacion AND fecha_expiracion";
            $execute = self::selectOneRow($qry);

            if(empty($execute)) {
                $token = self::getNewTokenAuth();

                $data['token'] = array(
                    'campo_marcador' => ':token',
                    'campo_valor' => $token
                );
                
                // Se setea zona horaria
                date_default_timezone_set('America/Santiago');

                // Obtener la fecha actual
                $fechaActual = new DateTime();

                // Sumar 7000 segundos
                $fechaActual->modify('+7000 seconds');

                // Formatear la fecha en el formato deseado (Año-Mes-Día Hora:Minuto:Segundo)
                $fechaExpiracion = $fechaActual->format('Y-m-d H:i:s');
                
                $data['fecha_expiracion'] = array(
                    'campo_marcador' => ':fecha_expiracion',
                    'campo_valor' => $fechaExpiracion
                );

                self::addData('token', $data);
            }else{
                $token = $execute->token;
            }

            return $token;
        }
        
        public static function getData($url) {
            $accessToken = self::getTokenAuth();
            
            $url = API_URL.$url;
            
            $headers = array(
                "Authorization: Bearer $accessToken",
                "Content-Type: application/json"
            );

            $ch = curl_init();
            curl_setopt($ch, CURLOPT_URL, $url);
            curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            
            $response = curl_exec($ch);
            
            if ($response === false) {
                echo "Error: " . curl_error($ch);
            } else {
                echo "<pre>";
                print_r(json_decode($response));
                echo "</pre>";
            }
            
            curl_close($ch);
        }

        public static function updateActivo($id, $token, $idActivo, $woFolio) {

            $accessToken = self::getTokenAuth();
            
            $url = API_URL."work_orders/$woFolio";
            
            $headers = array(
                "Authorization: Bearer $accessToken",
                "Content-Type: application/json"
            );

            $data = array(
                "note" => "$idActivo",
                "account_code" => "15.354.330-5"
            );
            
            // Inicializar cURL
            $ch = curl_init();

            // Configurar opciones de cURL
            curl_setopt($ch, CURLOPT_URL, $url);
            curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
            curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'PUT'); // Indicar que es una solicitud PUT
            curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data)); // Convertir los datos a JSON
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            
            $response = curl_exec($ch);
            
            if ($response === false) {
                echo "Error: " . curl_error($ch);
            }else{
                echo "<pre>";
                print_r($response);
                echo "</pre>";
                die();  
                $dataUpdate['id_activo'] = array(
                    'campo_marcador' => ':id_activo',
                    'campo_valor' => $idActivo
                );
                
                // Condición para actualizar el registro
                $condicion = "WHERE id = $id AND token = '$token'";
                Controller::updateData('solicitud', $dataUpdate, $condicion);
                
                header("Location: $token");
            }
            
            curl_close($ch);
        }
    }