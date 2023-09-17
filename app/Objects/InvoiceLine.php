<?php

namespace App\Objects;

use App\Price;

class InvoiceLine
{
    const PERSON_CZ = 'osoba';
    const PEOPLE_CZ = 'osoby';
    const MORE_PEOPLE_CZ = 'osob';

    const PERSON_EN = 'person';
    const PEOPLE_EN = 'people';

    /* @var string */
    public $name = '';

    /* @var int */
    public $quantity;

    // TODO: rewrite to CamelCase
    /* @var string */
    public $unit_name;

    // TODO: rewrite to CamelCase
    /* @var float */
    public $unit_price;

    // TODO: rewrite to CamelCase
    /* @var float */
    public $total_price;

    /* @var string */
    public $currency;

    public function __construct(Price $price, int $quantity, string $lang = 'cs', bool $showRole = true)
    {
        $this->quantity = $quantity;
        $this->unit_price = $price->getAmount();
        $this->currency = $price->getCurrency();

        $this->calculateTotalPrice();
        $this->setUnitName($lang);
        $this->setName($price, $lang, $showRole);
    }

    private function calculateTotalPrice(): void
    {
        $this->total_price = $this->quantity * $this->unit_price;
    }

    private function setUnitName(string $lang): void
    {
        switch ($this->quantity)
        {
            case 1:
                $this->unit_name = $lang === 'cs' ? self::PERSON_CZ : self::PERSON_EN;
                break;
            case 2:
            case 3:
            case 4:
                $this->unit_name = $lang === 'cs' ? self::PEOPLE_CZ : self::PEOPLE_EN;
                break;
            default:
                $this->unit_name = $lang === 'cs' ? self::MORE_PEOPLE_CZ : self::PEOPLE_EN;
                break;
        }
    }

    private function setName(Price $price, string $lang, bool $showRole): void
    {
        if ($showRole) {
            $role = $price->role()->first();
            if (null !== $role) {
                $this->name = strtolower($role->translation()->first()->$lang);
                $this->name .= ' - ';
            }
        }
        $this->name .= strtolower($price->translation()->first()->$lang);
    }
}