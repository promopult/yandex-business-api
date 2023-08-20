<?php

namespace Promopult\YandexBusinessApi\Dto;

class CampaignBeneficiary
{
    public function __construct(
        public CampaignClient $client,
        public CampaignContractor $contractor,
        public CampaignContract $contract
    ) {
    }

    public static function fromArray(array $data): self
    {
        return new self(
            client: CampaignClient::fromArray($data['client']),
            contractor: CampaignContractor::fromArray($data['contractor']),
            contract: CampaignContract::fromArray($data['contract']),
        );
    }
}
