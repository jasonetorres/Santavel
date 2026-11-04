<?php

namespace Tests\Unit;

use App\Santa\WishInbox;
use Tests\TestCase;

class WishInboxTest extends TestCase
{
    public function test_the_inbox_keeps_the_newest_hundred_wishes(): void
    {
        $inbox = app(WishInbox::class);

        $first = $inbox->add('First', 'A very old wish');

        for ($i = 0; $i < 100; $i++) {
            $inbox->add('Viewer '.$i, 'Wish '.$i);
        }

        $this->assertCount(100, $inbox->all());
        $this->assertNull($inbox->find($first->id));
        $this->assertSame('Viewer 99', $inbox->all()[0]->donorName);
    }
}
