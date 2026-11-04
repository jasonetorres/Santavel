<?php

namespace App\Santa;

use Illuminate\Contracts\Cache\LockTimeoutException;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Str;

class WishInbox
{
    private const KEY = 'santa.donation-wishes';

    private const MAX = 100;

    public function add(
        string $donorName,
        string $wish,
        ?int $amountCents = null,
        string $currency = 'usd',
        string $source = 'webhook',
    ): DonationWish {
        $donation = new DonationWish(
            id: (string) Str::ulid(),
            donorName: $donorName,
            wish: $wish,
            amountCents: $amountCents,
            currency: strtolower($currency),
            source: $source,
            status: 'pending',
            createdAt: now()->toIso8601String(),
            readAt: null,
        );

        $this->update(function (array $wishes) use ($donation) {
            array_unshift($wishes, $donation);

            return array_slice($wishes, 0, self::MAX);
        });

        return $donation;
    }

    /**
     * @return list<DonationWish>
     */
    public function all(): array
    {
        return $this->read();
    }

    /**
     * @return list<DonationWish>
     */
    public function pending(): array
    {
        return array_values(array_filter(
            $this->read(),
            fn (DonationWish $wish) => $wish->status === 'pending',
        ));
    }

    public function find(string $id): ?DonationWish
    {
        foreach ($this->read() as $wish) {
            if ($wish->id === $id) {
                return $wish;
            }
        }

        return null;
    }

    public function markRead(string $id): ?DonationWish
    {
        $updated = null;

        $this->update(function (array $wishes) use ($id, &$updated) {
            foreach ($wishes as $index => $wish) {
                if ($wish->id !== $id) {
                    continue;
                }

                $updated = $wish->read();
                $wishes[$index] = $updated;
                break;
            }

            return $wishes;
        });

        return $updated;
    }

    /**
     * @return list<DonationWish>
     */
    private function read(): array
    {
        $stored = Cache::get(self::KEY, []);

        if (! is_array($stored)) {
            return [];
        }

        $wishes = [];

        foreach ($stored as $row) {
            if (is_array($row) && isset($row['id'], $row['wish'])) {
                $wishes[] = DonationWish::fromArray($row);
            }
        }

        return $wishes;
    }

    /**
     * @param  callable(list<DonationWish>): list<DonationWish>  $callback
     */
    private function update(callable $callback): void
    {
        try {
            Cache::lock(self::KEY.'.lock', 5)->block(3, function () use ($callback) {
                $wishes = $callback($this->read());

                Cache::forever(self::KEY, array_map(
                    fn (DonationWish $wish) => $wish->toArray(),
                    $wishes,
                ));
            });
        } catch (LockTimeoutException $exception) {
            throw new SantaException('The wish list is busy. Try again in a moment.', 503, $exception);
        }
    }
}
