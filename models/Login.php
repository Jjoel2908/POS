<?php
require_once '../config/Connection.php';

class Login extends Connection
{

   public function __construct()
   {
   }

   private function validate_password(string $savedPassword, string $loginPassword): bool
   {
      return password_verify($loginPassword, $savedPassword);
   }

   public function validateUser(string $email, string $loginPassword): array|bool|null
   {
      $searchUser = $this->loginMySQL("usuarios", $email);

      if ($searchUser == null)
         return null;

      if (count($searchUser) > 0) {
         
         $savedPassword = $searchUser['password'];
         $validatePassword = $this->validate_password($savedPassword, $loginPassword);

         return $validatePassword 
            ? $searchUser 
            : false;
      }
      
      return null;
   }
}
