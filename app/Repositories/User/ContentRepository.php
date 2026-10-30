<?php

namespace App\Repositories\User;

class ContentRepository
{
    /**
     * @return array{title: string, last_updated: string, sections: array<int, array{title: string, body: string}>}
     */
    public function termsAndConditions(): array
    {
        return __('content.pages.terms-and-conditions');
    }

    /**
     * @return array{title: string, last_updated: string, sections: array<int, array{title: string, body: string}>}
     */
    public function privacyPolicy(): array
    {
        return __('content.pages.privacy-policy');
    }

    /**
     * @return array{title: string, last_updated: string, sections: array<int, array{title: string, body: string}>}
     */
    public function aboutUs(): array
    {
        return __('content.pages.about-us');
    }
}
