<?php

namespace Promopult\YandexBusinessApi\Dto;

class CampaignClient
{
    public function __construct(
        public string $type,
        public string $name,
        public array $okveds,
        public ?string $inn = null,
        public ?string $phoneNum = null,
        public ?string $epayNumber = null,
        public ?string $oksmNumber = null,
        public ?string $vat = null,
        public ?string $regNumber = null,
        public ?string $kpp = null
    ) {
    }

    public static function fromArray(array $data): self
    {
        return new self(
            type: $data['type'],
            name: $data['name'],
            okveds: $data['okveds'],
            inn: $data['inn'] ?? null,
            phoneNum: $data['phoneNum'] ?? null,
            epayNumber: $data['epayNumber'] ?? null,
            oksmNumber: $data['oksmNumber'] ?? null,
            vat: $data['vat'] ?? null,
            regNumber: $data['regNumber'] ?? null,
            kpp: $data['kpp'] ?? null
        );
    }

    public function toArray(): array
    {
        return array_filter((array) $this);
    }
}
