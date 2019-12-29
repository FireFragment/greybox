<?php

namespace App\Http\Controllers;

use Laravel\Lumen\Routing\Controller as BaseController;

class Controller extends BaseController
{
    // TODO: write mass updateColumns function
    public function updateColumn($table, string $column, $value)
    {
        if (empty($value)) {
            $value = null;
        }
        return $table->update([$column => $value]);
    }
}
