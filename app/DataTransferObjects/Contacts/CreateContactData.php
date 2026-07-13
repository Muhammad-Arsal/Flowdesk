<?php

namespace App\DataTransferObjects\Contacts;

use Illuminate\Foundation\Http\FormRequest;

/**
 * Plain, immutable snapshot of the data needed to create a contact.
 */
final readonly class CreateContactData
{
    public function __construct(
        public string $firstName,
        public string $lastName,
        public ?string $email = null,
        public ?string $phone = null,
    ) {}

    /**
     * Build from a plain array (tests, jobs, artisan commands).
     */
    public static function fromArray(array $data): self
    {
        return new self(
            firstName: $data['firstName'],
            lastName: $data['lastName'],
            email: $data['email'] ?? null,
            phone: $data['phone'] ?? null,
        );
    }

    /**
     * Build from validated HTTP input.
     */
    public static function fromRequest(FormRequest $request): self
    {
        return self::fromArray($request->validated());
    }
}
