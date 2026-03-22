<?php

declare(strict_types=1);

namespace App\Core\Controllers;

use App\Core\Mvc;

/**
 * Base class for admin-area controllers.
 *
 * Routing metadata is intentionally declared on concrete controllers,
 * while this class centralizes admin-specific runtime behavior such as
 * the default layout.
 */
abstract class AdminController extends BaseController
{
    
    protected string $defaultLayout = 'admin';

    public function __construct(?Mvc $mvc = null)
    {
        parent::__construct($mvc);
        mvc()->view->setLayout($this->defaultLayout);
    }
}
