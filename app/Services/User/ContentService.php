<?php

namespace App\Services\User;

use App\Repositories\User\ContentRepository;

class ContentService
{
    public function __construct(private readonly ContentRepository $contentRepository) {}

    /**
     * @return array{title: string, last_updated: string, sections: array<int, array{title: string, body: string}>}
     */
    public function termsAndConditions(): array
    {
        return $this->contentRepository->termsAndConditions();
    }

    /**
     * @return array{title: string, last_updated: string, sections: array<int, array{title: string, body: string}>}
     */
    public function privacyPolicy(): array
    {
        return $this->contentRepository->privacyPolicy();
    }

    /**
     * @return array{title: string, last_updated: string, sections: array<int, array{title: string, body: string}>}
     */
    public function aboutUs(): array
    {
        return $this->contentRepository->aboutUs();
    }
}
