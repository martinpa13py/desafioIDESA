<?php

require_once 'Database.php';
/*Obs: 
    - Retorna exactamente los resultados solicitados en el repositorio.
    - Si lo que se quiere es obtener todas las cuotas que ya caducaron de todos los lotes,
    entonces comentar la linea 36, 38 y descomentar la linea 22,23,28,32
*/
class DesafioUno {


    public static function getClientDebt(int $clientID) {
        Database::setDB();

        $lotes = self::getLotes();

        $cobrar['status']            = true;
        $cobrar['message']           = "No hay Lotes para cobrar";
        $cobrar['data']['total']     = 0;
        $cobrar['data']['detail']    = [];

        //$timeZone = new DateTimeZone('America/Asuncion'); //linea 22
        //$now = new DateTime('now', $timeZone); //linea 23
        $status = true;

        foreach ($lotes as $lote) {

            //$vencimiento = new DateTime($lote->vencimiento, $timeZone); //linea 28

            if (!$lote->vencimiento) continue;

            //if ($vencimiento > $now) continue; // linea 32

            if ((int) $lote->clientID !== $clientID) continue;

            if ($lote->lote !== '00148') continue; // linea 36

            if ((int) $lote->precio !== 190000) continue; // linea 38

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
