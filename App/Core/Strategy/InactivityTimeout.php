<?php 
namespace App\Core\Strategy;

use App\Core\Contract\ITimeoutStrategy;

class InactivityTimeout implements ITimeoutStrategy {
    /**
     * Summary of __construct
     * @param int $seconds durata della sessione
     */  
    public function __construct(private int $seconds = 600){}
    /**
     * Summary of IsExpired
     * @param array<string,int> $session contiene l'ultima attivtà svolta dall'utente con numero del tempo.
     * @return bool
     */
    public function IsExpired(): bool{

        return isset($_SESSION['LAST_PING']) && time() - $_SESSION['LAST_PING'] > $this->seconds;
    }

}