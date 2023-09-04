<?php

    require_once "config/config.php";
    
    class Main {
        protected static function connect() {
			try {
				$options = [ PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES UTF8; SET time_zone='America/Santiago'" ];
				$conexion = new PDO(SGBD, USER, PASS, $options);
				$conexion->exec("SET CHARACTER SET utf8");
				return $conexion;
			} catch (PDOException $e) {
				echo "Error de conexión: " . $e->getMessage();
			}
        }

        protected static function sql($query) {
			$sql = self::connect()->prepare($query);
            $sql->execute();
            return $sql;
        }
		
		protected static function probar() {
			return self::connect();
		}

		
		protected static function sqlQueryFecthAll($query) {
			return self::connect()->query($query)->fetchAll(PDO::FETCH_ASSOC);
		}

        protected static function addData($tabla, $datos){
			$query = "INSERT INTO $tabla (";
			$c = 0;
			foreach ($datos as $campo => $indice) {
				if($c <= 0) {
					$query .= $campo;
				}else{
					$query .= ",".$campo;
				}
				$c++;
			}

			$query .= ") VALUES(";
			$z = 0;
			foreach ($datos as $campo => $indice){
				if($z <= 0) {
					$query .= $indice["campo_marcador"];
				}else{
					$query .= ",".$indice["campo_marcador"];
				}
				$z++;
			}

			$query .= ")";

            $sql = self::connect()->prepare($query);
			foreach ($datos as $campo => $indice){
                $sql->bindParam($indice["campo_marcador"], $indice["campo_valor"]);
			}

			$sql->execute();
			
			return $sql;
		}

		protected static function updateData($tabla, $datos, $condicion){
			$query = "UPDATE $tabla SET";

			$z = 0;
			foreach ($datos as $campo => $indice){
				if($z <= 0) {
					$query .= ' '.$campo.'='.$indice["campo_marcador"];
				}else{
					$query .= ', '.$campo.'='.$indice["campo_marcador"];
				}
				$z++;
			}

			$query .= ' '.$condicion;

            $sql = self::connect()->prepare($query);
			foreach ($datos as $campo => $indice){
                $sql->bindParam($indice["campo_marcador"], $indice["campo_valor"]);
			}

			$sql->execute();

			return $sql;
		}

        protected static function executeProcedure($nombre, $params){
            $query = "CALL $nombre(";
		
			$z = 0;
			foreach ($params as $param){
				if($z <= 0) {
					$query .= "'".$param."'";
				}else{
					$query .= ",'".$param."'";
				}
				$z++;
			}
            
			$query .= ")";
            $sql = self::connect()->prepare($query);

			$sql->execute();

			return $sql;
        }

        public static function encryption($string) {
			$output=FALSE;
			$key=hash('sha256', SECRET_KEY);
			$iv=substr(hash('sha256', SECRET_IV), 0, 16);
			$output=openssl_encrypt($string, METHOD, $key, 0, $iv);
			$output=base64_encode($output);
			return $output;
		}

        public static function decryption($string) {
            $key=hash('sha256', SECRET_KEY);
			$iv=substr(hash('sha256', SECRET_IV), 0, 16);
			$output=openssl_decrypt(base64_decode($string), METHOD, $key, 0, $iv);
			return $output;
		}

        public static function createToken() {
            $t1 = bin2hex(openssl_random_pseudo_bytes(4));
            $t2 = bin2hex(openssl_random_pseudo_bytes(2));
            $t3 = bin2hex(openssl_random_pseudo_bytes(2));
            $t4 = bin2hex(openssl_random_pseudo_bytes(2));
            $t5 = bin2hex(openssl_random_pseudo_bytes(6));
            $token = $t1."-".$t2."-".$t3."-".$t4."-".$t5;
            return $token;
		}
    }