<?php
require_once '../config/Connection.php';

class User extends Connection
{

   public function __construct() {}

   public function hashPassword(string|int $password)
   {
      return password_hash($password, PASSWORD_BCRYPT);
   }

   public function updateUserPermissions(int $userId, string $newPermissions): bool
   {
      return $this->queryMySQL("UPDATE usuarios SET permisos = '{$newPermissions}' WHERE id = $userId");
   }
}
