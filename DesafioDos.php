<?php 
require_once 'Database.php';

class DesafioDos {

    public static function retriveLotes(string $loteID):void { //bug corregido, se cambio tipo de parametro a string

        Database::setDB(); 

        echo(json_encode(self::getLotes($loteID)));
    }

    private static function getLotes (string $loteID){ //bug corregido, se cambio tipo de parametro a string
        $lotes = [];
        $cnx = Database::getConnection();
        $stmt = $cnx->query("SELECT * FROM debts WHERE lote = '$loteID' LIMIT 2");
        while($rows = $stmt->fetchArray(SQLITE3_ASSOC)){
            $lotes[] = (object) $rows;
        }
        return $lotes;
    }
}

DesafioDos::retriveLotes('00148');