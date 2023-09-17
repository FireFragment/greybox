<?php

namespace App\Services;

use App\Event,
    App\Events\MembershipInvoiced,
    App\Objects\InvoiceLine,
    App\Objects\RegistrationGroup,
    App\Price;
use Illuminate\Support\Facades\Event as EventFacade;

class PriceCalculatingService
{
    /* @var RegistrationGroup*/
    private $group;

    /* @var Event */
    private $event;

    /* @var Price[] */
    private $priceList;

    /* @var bool */
    private $requiresMembership;

    /* @var float */
    private $totalPrice;

    /* @var InvoiceLine[] */
    private $invoiceLines;

    public function __construct(RegistrationGroup $registrationGroup, Event $event, string $lang = 'cs')
    {
        $this->group = $registrationGroup;
        $this->event = $event;
        $this->priceList = $event->prices()->get();

        $this->calculateRegistrationFees($lang);
        $this->calculateAccommodationFees($lang);
        $this->calculateMealsFees($lang);
        $this->calculateMembershipFees($lang);
    }

    public function getTotalPrice(): float
    {
        return $this->totalPrice;
    }

    public function getInvoiceLines(): array
    {
        return $this->invoiceLines;
    }

    private function calculateRegistrationFees($lang): void
    {
        $prices = $this->priceList->where('type', 'fee');
        foreach ($prices as $price)
        {
            $peopleCount = $this->group->countPeopleByRole($price->role);
            if (0 < $peopleCount) {
                $invoiceLine = new InvoiceLine($price, $peopleCount, $lang);
                $this->totalPrice += $invoiceLine->total_price;
                $this->invoiceLines[] = $invoiceLine;
            }
        }
    }

    private function calculateAccommodationFees($lang): void
    {
        $prices = $this->priceList->where('type', 'accommodation');
        foreach ($prices as $price)
        {
            $peopleCount = $this->group->countAccommodatedPeopleByRole($price->role);
            if (0 < $peopleCount) {
                $invoiceLine = new InvoiceLine($price, $peopleCount, $lang);
                $this->totalPrice += $invoiceLine->total_price;
                $this->invoiceLines[] = $invoiceLine;
            }
        }
    }

    private function calculateMealsFees($lang): void
    {
        $prices = $this->priceList->where('type', 'meals');
        foreach ($prices as $price)
        {
            $peopleCount = $this->group->countEatingPeopleByRole($price->role);
            if (0 < $peopleCount) {
                $invoiceLine = new InvoiceLine($price, $peopleCount, $lang);
                $this->totalPrice += $invoiceLine->total_price;
                $this->invoiceLines[] = $invoiceLine;
            }
        }
    }

    private function calculateMembershipFees($lang): void
    {
        if (!$this->event->requiresMembership()) return;

        $membershipCount = 0;
        $people = $this->group->getPeople();

        foreach ($people as $person) {
            $membership = $person->membership()->first();
            if (null === $membership or $membership->isExpired()) {
                $membershipCount++;
                EventFacade::dispatch(new MembershipInvoiced($person, $membership));
            }
        }

        if (0 < $membershipCount) {
            // TODO: create DB seeder
            $membershipPrice = Price::find(1);

            $invoiceLine = new InvoiceLine($membershipPrice, $membershipCount, $lang, false);
            $this->totalPrice += $invoiceLine->total_price;
            $this->invoiceLines[] = $invoiceLine;
        }
    }
}