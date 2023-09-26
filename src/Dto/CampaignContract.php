<?php

namespace Promopult\YandexBusinessApi\Dto;

class CampaignContract
{
    public function __construct(
        public string $type,
        public string $number,
        public string $date,
        public float $amount,
        public ?bool $isVat = null,
        public ?string $actionType = null,
        public ?string $subjectType = null,
    ) {
    }

    public static function fromArray(array $data): self
    {
        return new self(
            type: $data['type'],
            number: $data['number'],
            date: $data['date'],
            amount: $data['amount'] ?? 0,
            isVat: $data['isVat'] ?? null,
            actionType: $data['actionType'] ?? null,
            subjectType: $data['subjectType'] ?? null,
        );
    }

    public function toArray(): array
    {
        return array_filter((array) $this);
    }
}
