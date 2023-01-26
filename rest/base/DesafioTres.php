<?php
require_once '../Database.php';

class DesafioTres {

    public static function getLoteInfo(string $loteID): array {
        $data = array(
            "overDueDebts" => array(
                "lotes" => array(),
                "totalAmount" => 0
            ),
            "unmaturedDebts" => array(
                "lotes" => array(),
                "totalAmount" => 0
            ),
            "debtsWithoutExpiricy" => array(
                "lotes" => array(),
                "totalAmount" => 0
            ),
            "nextDue" => array(
                "lote" => null,
                "remainingDays" => null
            )
        );

        Database::setDB();
        $lotes = self::getAllDebtsForLote($loteID);
        $remainingDays = null;
        $today = new DateTime(); //fecha al momento de las comparaciones
        if (count($lotes) > 0) {
            foreach ($lotes as $loteInfo) {
                // si no tiene fecha de vencimiento agrupar y continuar
                if (!$loteInfo->vencimiento) {
                    $data['debtsWithoutExpiricy']['totalAmount'] += $loteInfo->precio;
                    array_push($data['debtsWithoutExpiricy']['lotes'], $loteInfo);
                    continue;
                }
                $vencimiento = new DateTime($loteInfo->vencimiento); // convertir fecha:string a fecha::Datetime para comapracion
                // si la fecha de vencimiento ya caducó entonces agrupar
                if ($vencimiento < $today) {
                    $data['overDueDebts']['totalAmount'] += $loteInfo->precio;
                    array_push($data['overDueDebts']['lotes'], $loteInfo);
                    // si la fecha de vencimiento aun no pasó entonces agrupar
                } else if ($vencimiento > $today) {
                    $data['unmaturedDebts']['totalAmount'] += $loteInfo->precio;
                    array_push($data['unmaturedDebts']['lotes'], $loteInfo);
                    $calculatedRemainingDays = (int) $today->diff($vencimiento)->format("%a"); // obtener intervalo entre 2 fechas en dias
                    // el proximo vencimiento es la que tiene menor intervalo de dias con respecto a la variable $today
                    if ($remainingDays === null || $calculatedRemainingDays < $remainingDays) {
                        $remainingDays = $calculatedRemainingDays;
                        $data['nextDue']['lote'] = $loteInfo;
                        $data['nextDue']['remainingDays'] = $remainingDays;
                    }
                }
            }
        }
        return $data;
    }

    private static function getAllDebtsForLote(string $loteID): array {
        $lotes = [];
        $sql = "SELECT * FROM debts WHERE lote = :loteID";
        $cnx = Database::getConnection();
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
