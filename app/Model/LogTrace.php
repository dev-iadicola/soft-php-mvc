<?php

declare(strict_types=1);

namespace App\Model;

use App\Core\DataLayer\Model;

class LogTrace extends Model
{
    protected string $table = 'logs';

    protected ?int $id = null;
    protected int $user_id;
    protected ?string $last_log = null;
    protected string $indirizzo;
    protected string $device;
    protected ?string $created_at = null;
    protected ?string $updated_at = null;

    protected function casts(): array
    {
        return ['user_id' => 'int'];
    }

    /**
     * @deprecated Use \App\Services\LogService::create() instead.
     */
    public static function ceateLog(int $id): mixed
    {
        $default = [
            'user_id'   => $id,
            'indirizzo' => $_SERVER['REMOTE_ADDR'],
            'last_log'  => date('Y-m-d H:i:s', time()),
            'device'    => $_SERVER['HTTP_USER_AGENT'],
        ];

        $log = LogTrace::query()->create($default);

        return $log;

    }
}
