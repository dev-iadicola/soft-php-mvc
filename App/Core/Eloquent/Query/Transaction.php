<?php 
namespace App\Core\Eloquent\Query;
 
trait Transaction  {
    private int $transactionLevel = 0;

    /**
     * Avvia una transazione, gestendo livelli annidati.
     */
    public function beginTransaction(): void
    {
        try {
            if ($this->transactionLevel === 0) {
                $this->pdo->beginTransaction();
            } else {
                // Usa SAVEPOINT per le transazioni annidate (se supportate)
                $this->pdo->exec("SAVEPOINT LEVEL{$this->transactionLevel}");
            }

            $this->transactionLevel++;
        } catch (\PDOException $e) {
            \App\Core\Helpers\Log::exception($e);
            throw $e;
        }
    }

    /**
     * Conferma la transazione o rilascia un savepoint.
     */
    public function commit(): void
    {
        if ($this->transactionLevel === 0) {
            return; // nessuna transazione attiva
        }

        $this->transactionLevel--;

        try {
            if ($this->transactionLevel === 0) {
                $this->pdo->commit();
            } else {
                // Rilascia il savepoint invece di committare tutto
                $this->pdo->exec("RELEASE SAVEPOINT LEVEL{$this->transactionLevel}");
            }
        } catch (\PDOException $e) {
            \App\Core\Helpers\Log::exception($e);
            throw $e;
        }
    }

    /**
     * Annulla la transazione o ripristina un savepoint.
     */
    public function rollBack(): void
    {
        if ($this->transactionLevel === 0) {
            return;
        }

        $this->transactionLevel--;

        try {
            if ($this->transactionLevel === 0) {
                $this->pdo->rollBack();
            } else {
                // Ripristina lo stato al savepoint precedente
                $this->pdo->exec("ROLLBACK TO SAVEPOINT LEVEL{$this->transactionLevel}");
            }
        } catch (\PDOException $e) {
            \App\Core\Helpers\Log::exception($e);
            throw $e;
        }
    }

    /**
     * Restituisce true se c'è una transazione attiva.
     */
    public function inTransaction(): bool
    {
        return $this->transactionLevel > 0 && $this->pdo->inTransaction();
    }
}