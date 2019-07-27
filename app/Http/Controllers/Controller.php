<?php

namespace App\Http\Controllers;

use Laravel\Lumen\Routing\Controller as BaseController;

class Controller extends BaseController
{
    public function updateColumn($table, string $column, $value)
    {
        if (empty($value)) {
            $value = null;
        }
        return $table->update([$column => $value]);
    }

    /*
     * @return Fakturoid\Client
     */
    public function getFakturoidClient()
    {
        return new \Fakturoid\Client(getenv('FAKTUROID_SLUG'), getenv('FAKTUROID_EMAIL'), getenv('FAKTUROID_API_KEY'), getenv('FAKTUROID_USER_AGENT'));
    }
}
