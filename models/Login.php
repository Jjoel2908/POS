<?php
require '../config/Connection.php';

class Login extends Connection
{

   public function __construct(private string $user, private string $password)
   {
   }

   private function validate_password(string $password): bool
   {
      return password_verify($this->password, $password);
   }

   public function validateUser(): array|null
   {

      $search = $this->loginMySQL("usuarios", "user", $this->user);

      if (count($search) > 0) {

         $password = $search['password'];
         $validatePassword = $this->validate_password($password);
         $data = $validatePassword ? $search : NULL;

         return $data;
      } else return NULL;
   }
}
