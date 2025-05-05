<?php 
namespace App\Core\Contract;

interface CommandInterface {
    public function exe(array $args = []);
}