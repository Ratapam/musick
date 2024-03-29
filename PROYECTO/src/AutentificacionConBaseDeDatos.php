<?php
//NO funciona el requiere
//require_once('../config/bd_config.php');


$db_user = 'administrador';
$db_pass = '1234';
$db_name = 'musick';

class AutentificacionConBaseDeDatos
{
    private $dbPDO;

    public function __construct()
    {
        global $db_user;
        global $db_pass;
        global $db_name;
        try {
            $this->dbPDO = new PDO(
                "mysql:host=localhost;dbname=$db_name",
                $db_user,
                $db_pass
            );
        } catch (PDOException $e) {
            print "¡Error!: ".$e->getMessage()."<br/>";
            die();
        }
    }

    //Funcion que comprueba si existe un usuario en la base datos cuando se logea con la contraseña
    //  - Debuelve el id_usuario si esta logeado para utilizarlo en $_SESSION
    //  - Si no existe en la base de datos devuelve false.
    public function existeUsuario(string $user, string $pass) {
        try {            
            $sentenciaSQL = $this->dbPDO->query("SELECT * FROM Usuario WHERE
            nick = '$user'");
            $resultado = $sentenciaSQL->fetchAll(PDO::FETCH_ASSOC);
            if(count($resultado) == true && (password_verify($pass, $resultado[0]['contrasena']))) {
                    return $resultado[0]['id_usuario'];
            }
        } catch (PDOException $e) {
            print "¡Error!: " . $e->getMessage() . "<br/>";
            return false;
        }
    }


    // Funcion que inserta un token de tipo recuerdame cuando el usuario se logea y pulsa el check
    // de recuerdame.
    // la fecha se introduce con now() , debemos implemetar un script que borre los campos
    // de las cookie con tipo recuerdame con el la fecha que creamos conveniene o en este caso
    // al ser las cookie de recuerdame podrian quedarse hasta que el usuario pulse cerrar sesion.
    public function insertToken( $id_usuario,  $tipo,  $token) {
        try {
            $sentencia = $this -> dbPDO -> prepare("INSERT INTO Token (id_usuario, tipoToken,
             token,fecha_expira) VALUES (:id_usuario, :tipoToken, :token,now());");
            $sentencia -> bindParam(':id_usuario', $id_usuario);
            $sentencia -> bindParam(':tipoToken', $tipo);
            $sentencia -> bindParam(':token', $token);
            $sentencia -> execute();
           // $usuarioBD = null;
        } catch (PDOException $excepcion) {
            echo "</br>¡Error!: ".$excepcion -> getMessage()."</br>";
            die();
        }
    }

    // Funcion que compara el token y el id de la cookie recuerdame con la base datos
    // Comprueba si existe el token y el id en la tabla token, devuelve un boolean
    public function compruebaCookieRecuerdame($id,$token){
        try {
            $sentenciaSQL = $this-> dbPDO -> query("SELECT * FROM Token
            WHERE id_usuario = '$id'
            AND tipoToken='recuerdame'");

            //$sentenciaSQL -> prepare(':id_usuario', $id);
            //$sentencia -> execute();
            //$sentenciaSQL -> execute();

            $resultado = $sentenciaSQL->fetchAll(PDO::FETCH_ASSOC);

            if(count($resultado)>0 && strcmp($token, $resultado[0]['token'] == 0)){
                return true;
            }else{
                return false;
            }
        } catch (PDOException $e) {
            print "¡Error!: " . $e->getMessage() . "<br/>";
            return false;
        }

    }


    public function __destruct()
    {
        $this->dbPDO = null;
    }

///////////////////////////////////////////////////////////
    // Función que guarda el UsuarioNC en la base de datos
    public function guardarUsuarioNC(string $nombreNC, string $contrasenaNC, string $emailNC) {
        try {
            $sentencia = $this -> dbPDO -> prepare("INSERT INTO UsuariosNC (nombreNC, contrasenaNC, emailNC) VALUES (:nombreNC, :contrasenaNC, :emailNC)");
            $sentencia -> bindParam(':nombreNC', $nombreNC);
            $sentencia -> bindParam(':contrasenaNC', $contrasenaNC);
            $sentencia -> bindParam(':emailNC', $emailNC);
            $sentencia -> execute();
            $usuarioBD = null;
        } catch (PDOException $error) {
            echo "</br>¡Error!: ".$error -> getMessage()."</br>";
            die();
        }
    }

    // Función que guarda en la base de datos un Usuario ya confirmado.
    public function guardarUsuario(string $contrasena, string $nick, string $correo) {
        try {
            $sentencia = $this -> dbPDO -> prepare("INSERT INTO Usuario (contrasena, nick, correo) VALUES (:contrasena, :nick, :correo)");
            $sentencia -> bindParam(':contrasena', $contrasena);
            $sentencia -> bindParam(':nick', $nick);
            $sentencia -> bindParam(':correo', $correo);
            $sentencia -> execute();
            $usuarioBD = null;
        } catch (PDOException $excepcion) {
            echo "</br>¡Error!: ".$excepcion -> getMessage()."</br>";
            die();
        }
    }

    // Función que comprueba si el nick esta disponible.
    public function nickDisponible(string $nick) {
        try {
            $sentenciaSQL = $this -> dbPDO -> query("SELECT * FROM Usuario WHERE nick = '$nick'");
            $resultado = $sentenciaSQL -> fetchAll(PDO::FETCH_ASSOC);
            if(count($resultado)== 0){
                return true;
            }else{
                return false;
            }
            //return (count($resultado) == true);
        } catch (PDOException $error) {
            print "¡Error!: ". $error -> getMessage() ."<br/>";
            return false;
        }
    }
    
    // Función que devuelve los datos de un UsuarioNC
    public function obtenerUsuarioNC(int $id_usuarioNC) {
        try {
            $sentenciaSQL = $this -> dbPDO -> query("SELECT * FROM UsuariosNC WHERE id_usuarioNC = '$id_usuarioNC'");
            $usuarioNC = $sentenciaSQL -> fetchAll();
            return $usuarioNC;
            // $this -> dbPDO -> guardarUsuario($usuarioNC[0]['nombreNC'], $usuarioNC[0]['contrasenaNC'], $usuarioNC[0]['emailNC']);
        } catch (PDOException $error) {
            print "¡Error!: ".$error -> getMessage()."<br/>";
            return false;
        }

    }

    // Función que borra un token de la tabla Token
    public function borrarToken(int $id_usuario) {
        try {
            $sentencia = $this -> dbPDO -> prepare("DELETE FROM Token WHERE id_usuario = $id_usuario");
            $sentencia -> execute();
            $usuarioBD = null;
        } catch (PDOException $error) {
            echo "</br>¡Error!: ".$error -> getMessage()."</br>";
            die();
        }
    }    
    
    // Función que borra un usuario de la tabla UsuariosNC
    public function borrarUsuarioNC(int $id_usuario) {
        try {
            $sentencia = $this -> dbPDO -> prepare("DELETE FROM UsuariosNC WHERE id_usuarioNC = $id_usuario");
            $sentencia -> execute();
            $usuarioBD = null;
        } catch (PDOException $error) {
            echo "</br>¡Error!: ".$error -> getMessage()."</br>";
            die();
        }
    }

    // Función que devuelve el id_usuarioNC
    public function saberIdusuarioNC(string $email): int {
        try {
            $sentenciaSQL = $this -> dbPDO -> query("SELECT id_usuarioNC FROM UsuariosNC WHERE emailNC = '$email'");
            $id_usuarioNC = $sentenciaSQL -> fetchAll();
            // print_r($id_usuarioNC);
            $id_usu = $id_usuarioNC[0]['id_usuarioNC'];
            //echo $id_usu;
            return $id_usu;
        } catch (PDOException $error) {
            print "¡Error!: ".$error -> getMessage()."<br/>";
            return false;
        }
    }

    // Función que comprueba si el id_usuario y el token coinciden en la base de datos 
    public function comprobarToken(string $token, int $id_usuario): bool {
        try {
            $sentenciaSQL = $this -> dbPDO -> query("SELECT * FROM Token WHERE token = '$token' AND id_usuario = '$id_usuario';");
            $resultado = $sentenciaSQL -> fetchAll();
            print_r($resultado);
            echo "ggggggggggggggggggggggggggggggggggggggggggggggggggggggggg";
            return (count($resultado) == true);
        } catch (PDOException $error) {
            print "¡Error!: ".$error -> getMessage()."<br/>";
            return false;
        }
    }
     
    // Falta probar -- Función que devuelve una array con todos los nombre de autores de la base de datos
    public function saberTodosLosAutores() {
        try {
            $sentenciaSQL = $this -> dbPDO -> query("SELECT nombre FROM Autor");
            $autores = $sentenciaSQL -> fetchAll();
            return $autores;
        } catch (PDOException $error) {
            print "¡Error!: ".$error -> getMessage()."<br/>";
            return false;
        }
    }

    // Falta probar -- Función que devuelve una array con todos los nombre de autores de la base de datos
    public function saberTodosLosEstilos() {
        try {
            $sentenciaSQL = $this -> dbPDO -> query("SELECT nombre FROM Estilo");
            $estilos = $sentenciaSQL -> fetchAll();
            return $estilos;
        } catch (PDOException $error) {
            print "¡Error!: ".$error -> getMessage()."<br/>";
            return false;
        }
    }

    


}

?>
