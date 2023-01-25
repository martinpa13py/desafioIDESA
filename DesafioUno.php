<?php

require_once 'Database.php';

class DesafioUno {


    public static function getClientDebt(int $clientID) {
        Database::setDB();

        $lotes = self::getLotes();

        $cobrar['status']            = true;
        $cobrar['message']           = "No hay Lotes para cobrar";
        $cobrar['data']['total']     = 0;
        $cobrar['data']['detail']    = [];

        $timeZone = new DateTimeZone('America/Asuncion');
        $now = new DateTime('now', $timeZone);
        $status = true;

        foreach ($lotes as $lote) {

            $vencimiento = new DateTime($lote->vencimiento, $timeZone);

            if (!$lote->vencimiento || $vencimiento > $now) continue;

            if ((int) $lote->clientID !== $clientID) continue;

            $status = false;
            $cobrar['data']['total']      += $lote->precio;
            $cobrar['data']['detail'][]   = (array) $lote;
        }
        $cobrar['status'] = $status;
        $cobrar['message'] = $status ? $cobrar['message'] : "Tienes Lotes para cobrar";

        echo json_encode($cobrar);
        exit;
    }



    private static function getLotes(): array {
        $lotes = [];
        $cnx = Database::getConnection();
        $stmt = $cnx->query("SELECT * FROM debts");
        while ($rows = $stmt->fetchArray(SQLITE3_ASSOC)) {
            $rows['clientID'] = (string) $rows['clientID'];
            $lotes[] = (object) $rows;
        }
        $cnx->close(); //cerrar conexion BD
        return $lotes;
    }
}

DesafioUno::getClientDebt(123456);
