<?php

class ModelEstilo extends BaseModel{
    protected static $lista_info = ['id_estilo','nombre_estilo']; 


    public function cuatroEstilos(){
		$db = App::getDB();
		$resultado = $db -> ejecutar("SELECT * FROM estilo LIMIT 4");
		return $resultado;
    }
    
    public static function nombresEstilos() {

        $db = App::getDB();
        $sentenciaSQL = "SELECT nombre_estilo FROM estilo ";
        $resultado = $db -> ejecutar($sentenciaSQL,$params);
    
        return $resultado;
    }





}
?>