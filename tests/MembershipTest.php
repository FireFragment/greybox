<?php

declare(strict_types=1);

use App\Membership;

class MembershipTest extends TestCase
{

    /**
     * A basic test example.
     *
     * @return void
     */
    public function testEndThisYear(): void
    {
        $endDate = Membership::setEndDate(2020, rand(1, 6));
        $this->assertSame('2020-08-31', $endDate);
    }

    public function testEndNextYear(): void
    {
        $endDate = Membership::setEndDate(2020, rand(7, 12));
        $this->assertSame('2021-08-31', $endDate);
    }



}