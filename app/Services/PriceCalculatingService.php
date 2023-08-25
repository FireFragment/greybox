<?php

namespace App\Services;

use App\Event,
    App\Events\MembershipInvoiced,
    App\Objects\RegistrationGroup;
use Illuminate\Support\Facades\Event as EventFacade;

class PriceCalculatingService
{
    /* @var RegistrationGroup*/
    private $group;

    /* @var Event */
    private $event;

    /* @var \App\Price[]
     * maybe unnecessary
     */
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
        foreach ($this->priceList as $price)
        {
            $peopleCount = $this->group->countPeopleByRole($price->role);
            if (0 < $peopleCount) {
                $invoiceLine = new \App\Objects\InvoiceLine($price, $peopleCount, $lang);
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
            $invoiceLine = new \App\Objects\InvoiceLine('Členství v České asociaci studentů', $peopleCount, $this->event->membership_fee, $lang);
            $this->totalPrice += $invoiceLine->total_price;
            $this->invoiceLines[] = $invoiceLine;
    }
}