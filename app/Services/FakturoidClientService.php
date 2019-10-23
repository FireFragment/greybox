<?php


namespace App\Services;

use Fakturoid\Client as FakturoidClient;

class FakturoidClientService
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

    public function getAllSubjects()
    {
        $subjects = array();

        for ($page = 1; $page <= $this->getPagesCount(); $page++) {
            $subjectsPage = $this->fakturoidClient->getSubjects(["page" => $page])->getBody();
            foreach ($subjectsPage as $subject) {
                array_push($subjects, $subject);
            }
        }

        return $subjects;
    }

    private function getPagesCount(): int
    {
        $link = $this->fakturoidClient->getSubjects()->getHeader('Link');

        if (!empty($link)) {
            $parts = explode('=', $link);
            preg_match('/[0-9]+/', $parts[1], $lastPage);
        } else {
            $lastPage[] = 1;
        }

        return $lastPage[0];
    }
}