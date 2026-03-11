<?php

declare(strict_types=1);

namespace App\Model;

use App\Core\DataLayer\Model;

class LogTrace extends Model
{
    protected string $table = 'logs';

    protected int|string|null $id = null;
    protected ?int $user_id = null;
    protected ?string $last_log = null;
    protected ?string $indirizzo = null;
    protected ?string $device = null;
    protected ?string $created_at = null;
    protected ?string $updated_at = null;

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
