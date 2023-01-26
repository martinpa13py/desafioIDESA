<?php
require_once 'Database.php';

class DesafioDos {

    public static function retriveLotes(string $loteID) {
        Database::setDB();
        echo json_encode(self::getLotes($loteID));
        exit;
    }

    private static function getLotes(string $loteID) {
        $lotes = [];
        $cnx = Database::getConnection();
        $sql = "SELECT * FROM debts WHERE lote = :loteID ORDER BY precio ASC, vencimiento ASC";
        $stmt = $cnx->prepare($sql);
        $stmt->bindValue(':loteID', $loteID);
        $result = $stmt->execute();
        while ($rows = $result->fetchArray(SQLITE3_ASSOC)) {
            $lotes[] = (object) $rows;
        }
        $cnx->close();
        return $lotes;
    }
}

DesafioDos::retriveLotes('00148');
