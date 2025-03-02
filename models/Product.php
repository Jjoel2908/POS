<?php
require '../config/Connection.php';

class Product extends Connection
{

   public function __construct()
   {
   }

   public function dataTable(): array
   {
      return $this->queryMySQL("SELECT p.*, c.categoria AS nombre_categoria FROM productos p INNER JOIN categorias c ON p.id_categoria = c.id WHERE p.estado = 1");
   }

   public function selectProduct(int $id): array
   {
      return $this->select('productos', $id);
   }

   public function insertProduct(array $data): bool
   {
      $query = $this->insert('productos', $data);
      return $query;
   }

   public function updateProduct(array $data, int $id): bool
   {
      $query = $this->update('productos', $id, $data);
      return $query;
   }

   public function deleteProduct(int $id): bool
   {
      $query = $this->delete('productos', $id);
      return $query;
   }
}
