<?php

require_once 'Database.php';

class DesafioUno {


    public static function getClientDebt(int $clientID) {

        /* utilizaria el filtrado por software como en el principio
         si el query de la BD 
         tarda demasiado o si se vuelve muy largo, complejo y dificil de mantener, 
         pero en este caso es simple */

        Database::setDB();

        $lotes = self::getLotesWithDebtByClientID($clientID);
        $totalLotes = count($lotes);

        $cobrar = array();
        $cobrar['status']            = $totalLotes > 0;
        $cobrar['message']           = $totalLotes > 0 ? "Tienes Lotes para cobrar" : "No hay Lotes para cobrar";
        $cobrar['data']['total']     = 0;
        $cobrar['data']['detail']    = $lotes;

        if ($totalLotes > 0) {
            foreach ($lotes as $lote) {
                $cobrar['data']['total'] += $lote->precio;
            }
        }
        echo json_encode($cobrar);
        exit;
    }


    // como se requiere la deuda por lotes de una persona entonces filtro por esa persona
    private static function getLotesWithDebtByClientID(int $clientID): array {
        $lotes = [];
        $cnx = Database::getConnection();
        $sql =
            "SELECT
            *
        FROM
            debts
        WHERE
            clientID = :clientID
            AND vencimiento IS NOT NULL
            AND vencimiento < :vencimiento
        ORDER BY vencimiento ASC";

        $stmt = $cnx->prepare($sql);
        $stmt->bindValue(':clientID', $clientID);
        $stmt->bindValue(':vencimiento', date("Y-m-d"));
        $result = $stmt->execute();

        while ($rows = $result->fetchArray(SQLITE3_ASSOC)) {
            $rows['clientID'] = (string) $rows['clientID'];
            $lotes[] = (object) $rows;
        }
        $cnx->close();
        return $lotes;
    }
}

DesafioUno::getClientDebt(123456);
