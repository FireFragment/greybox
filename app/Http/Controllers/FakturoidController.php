<?php

namespace App\Http\Controllers;

abstract class FakturoidController extends Controller
{
    /*
     * @return Fakturoid\Client
     */
    public function getFakturoidClient()
    {
        return new \Fakturoid\Client(getenv('FAKTUROID_SLUG'), getenv('FAKTUROID_EMAIL'), getenv('FAKTUROID_API_KEY'), getenv('FAKTUROID_USER_AGENT'));
    }
}
