<?php
require '../config/Connection.php';

class Cashbox extends Connection
{
   public function __construct()
   {
   }

   public function selectOne($idRegister)
   {
      return $this::queryMySQL("SELECT id, nombre FROM cajas WHERE id = $idRegister AND estado = 1");
   }











   

   public function dataTable(): array
   {
      return $this->selectAll('cajas');
   }

   public function insertCashbox(array $data): bool
   {
      return $this->insert('cajas', $data);
   }

   public function updateCashbox(array $data, int $id): bool
   {
      return $this->update('cajas', $id, $data);
   }

   public function deleteCashbox(int $id): bool
   {
      return $this->delete('cajas', $id);
   }

   public function openCashbox(array $data): bool
   {
      return $this->insert('arqueo_caja', $data);
   }

   public function dataTableCashboxes(): array
   {
      return $this->queryMySQL("SELECT ac.*, c.caja AS nombre_caja FROM arqueo_caja ac INNER JOIN cajas c ON ac.id_caja = c.id WHERE fecha_fin IS NULL");
   }

   public function creditSales($idArqueo): int {
      $total = $this->queryMySQL("SELECT SUM(pagado) AS total FROM credito WHERE id_arqueo = $idArqueo");
      return (float) $total[0]['total'];
   }

   public function closeCashbox(array $data, int $id): bool
   {
      return $this->update('arqueo_caja', $id, $data);
   }
}
