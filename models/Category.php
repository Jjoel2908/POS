<?php
require '../config/Connection.php';

class Category extends Connection
{

   public function __construct()
   {
   }

   public function dataTable(): array
   {
      return $this->selectAll('categorias');
   }

   public function selectCategory(int $id): array
   {
      return $this->select('categorias', $id);
   }

   public function insertCategory(array $data): bool
   {
      $query = $this->insert('categorias', $data);
      return $query;
   }

   public function updateCategory(array $data, int $id): bool
   {
      $query = $this->update('categorias', $id, $data);
      return $query;
   }

   public function deleteCategory(int $id): bool
   {
      $query = $this->delete('categorias', $id);
      return $query;
   }
}
