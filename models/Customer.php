<?php 
require '../config/Connection.php';

class Customer extends Connection {

   public function __construct()
   {
      
   }

   public function dataTable (): array {
      return $this->selectAll('clientes');
   }

   public function selectCustomer (int $id_customer): array {
      return $this->select('clientes', $id_customer);
   }

   public function insertCustomer (array $data): bool {
      return $this->insert('clientes', $data);
   }

   public function updateCustomer (int $id_customer, array $data): bool {
      return $this->update('clientes', $id_customer, $data);
   }

   public function deleteCustomer (int $id_customer): bool {
      return $this->delete('clientes', $id_customer);
   }

}