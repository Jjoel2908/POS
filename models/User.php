<?php 
require '../config/Connection.php';

class User extends Connection {

   public function __construct()
   {
      
   }

   public function dataTable (): array {
      return $this->selectAll('usuarios');
   }

   public function selectUser (int $id_user): array {
      return $this->select('usuarios', $id_user);
   }

   public function insertUser (array $data): bool {
      return $this->insert('usuarios', $data);
   }

   public function updateUser (int $id_user, array $data): bool {
      return $this->update('usuarios', $id_user, $data);
   }

   public function deleteUser (int $id_user): bool {
      return $this->delete('usuarios', $id_user);
   }

   public function hashPassword(string|int $password) {
      return password_hash($password, PASSWORD_BCRYPT);
   }

}