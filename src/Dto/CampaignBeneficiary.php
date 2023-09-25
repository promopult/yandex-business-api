<?php

namespace Promopult\YandexBusinessApi\Dto;

class CampaignBeneficiary
{
    public function __construct(
        public CampaignClient $client,
        public ?CampaignContractor $contractor = null,
        public ?CampaignContract $contract = null
    ) {
    }

    public static function fromArray(array $data): self
    {
        $contractor = !empty($data['contractor']) ? CampaignContractor::fromArray($data['contractor']) : null;
        $contract = !empty($data['contract']) ? CampaignContract::fromArray($data['contract']) : null;

        return new self(
            client: CampaignClient::fromArray($data['client']),
            contractor: $contractor,
            contract: $contract,
        );
    }
}
