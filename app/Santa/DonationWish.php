<?php

namespace App\Santa;

final class DonationWish
{
    public function __construct(
        public readonly string $id,
        public readonly string $donorName,
        public readonly string $wish,
        public readonly ?int $amountCents,
        public readonly string $currency,
        public readonly string $source,
        public readonly string $status,
        public readonly string $createdAt,
        public readonly ?string $readAt,
    ) {}

    /**
     * @param  array<string, mixed>  $row
     */
    public static function fromArray(array $row): self
    {
        return new self(
            id: (string) $row['id'],
            donorName: (string) ($row['donor_name'] ?? 'A viewer'),
            wish: (string) ($row['wish'] ?? ''),
            amountCents: isset($row['amount_cents']) ? (int) $row['amount_cents'] : null,
            currency: (string) ($row['currency'] ?? 'usd'),
            source: (string) ($row['source'] ?? 'webhook'),
            status: (string) ($row['status'] ?? 'pending'),
            createdAt: (string) ($row['created_at'] ?? ''),
            readAt: isset($row['read_at']) ? (string) $row['read_at'] : null,
        );
    }

    /**
     * @return array<string, mixed>
     */
    public function toArray(): array
    {
        return [
            'id' => $this->id,
            'donor_name' => $this->donorName,
            'wish' => $this->wish,
            'amount_cents' => $this->amountCents,
            'currency' => $this->currency,
            'source' => $this->source,
            'status' => $this->status,
            'created_at' => $this->createdAt,
            'read_at' => $this->readAt,
        ];
    }

    public function read(?string $readAt = null): self
    {
        return new self(
            id: $this->id,
            donorName: $this->donorName,
            wish: $this->wish,
            amountCents: $this->amountCents,
            currency: $this->currency,
            source: $this->source,
            status: 'read',
            createdAt: $this->createdAt,
            readAt: $readAt ?? now()->toIso8601String(),
        );
    }
}
