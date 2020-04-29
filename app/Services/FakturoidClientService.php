<?php


namespace App\Services;

use Fakturoid\Client as FakturoidClient;
use Fakturoid\Response;

class FakturoidClientService // TODO: split into 2 services: subjects and invoices
{

    private $fakturoidClient;

    public function __construct()
    {
        $this->fakturoidClient = new FakturoidClient(
            getenv('FAKTUROID_SLUG'),
            getenv('FAKTUROID_EMAIL'),
            getenv('FAKTUROID_API_KEY'),
            getenv('FAKTUROID_USER_AGENT')
        );
    }

    public function createSubject(Array $subjectData): Response
    {
        return $this->fakturoidClient->createSubject($subjectData);
    }

    public function getAllSubjects(): array
    {
        $subjects = array();
        $link = $this->fakturoidClient->getSubjects()->getHeader('Link');

        for ($page = 1; $page <= $this->getPagesCount($link); $page++)
        {
            $subjectsPage = $this->fakturoidClient->getSubjects(["page" => $page])->getBody();
            foreach ($subjectsPage as $subject)
            {
                array_push($subjects, $subject);
            }
        }
        return $subjects;
    }

    public function createInvoice(Array $invoiceData): Response
    {
        return $this->fakturoidClient->createInvoice($invoiceData);
    }

    /*
     * Get array of all invoices from Fakturoid
     *
     * @return array
     */
    public function getAllInvoices(): array
    {
        $invoices = array();
        $link = $this->fakturoidClient->getInvoices()->getHeader('Link');

        for ($page = 1; $page <= $this->getPagesCount($link); $page++)
        {
            $invoicesPage = $this->fakturoidClient->getInvoices(['page' => $page])->getBody();
            foreach ($invoicesPage as $invoice)
            {
                array_push($invoices, $invoice);
            }
        }
        return $invoices;
    }

    private function getPagesCount($link = null): int
    {
        if (null !== $link) {
            $parts = explode('=', $link);
            preg_match('/[0-9]+/', $parts[1], $lastPage);
        } else {
            $lastPage[] = 1;
        }

        return $lastPage[0];
    }
}