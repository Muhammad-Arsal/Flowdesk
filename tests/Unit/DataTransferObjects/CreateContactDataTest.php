<?php

use App\DataTransferObjects\Contacts\CreateContactData;
use Illuminate\Foundation\Http\FormRequest;

it('builds correctly from a plain array', function (): void {
    $data = CreateContactData::fromArray([
        'firstName' => 'Ada',
        'lastName' => 'Lovelace',
        'email' => 'ada@example.com',
        'phone' => '555-0100',
    ]);

    expect($data->firstName)->toBe('Ada');
    expect($data->lastName)->toBe('Lovelace');
    expect($data->email)->toBe('ada@example.com');
    expect($data->phone)->toBe('555-0100');
});

it('defaults email and phone to null when missing from the array', function (): void {
    $data = CreateContactData::fromArray([
        'firstName' => 'Grace',
        'lastName' => 'Hopper',
    ]);

    expect($data->email)->toBeNull();
    expect($data->phone)->toBeNull();
});

it('builds correctly from a form request\'s validated data', function (): void {
    $request = new class extends FormRequest
    {
        public function validated($key = null, $default = null): array
        {
            return [
                'firstName' => 'Alan',
                'lastName' => 'Turing',
                'email' => 'alan@example.com',
                'phone' => null,
            ];
        }
    };

    $data = CreateContactData::fromRequest($request);

    expect($data->firstName)->toBe('Alan');
    expect($data->lastName)->toBe('Turing');
    expect($data->email)->toBe('alan@example.com');
    expect($data->phone)->toBeNull();
});

it('is immutable, writing to a property after construction throws', function (): void {
    $data = CreateContactData::fromArray([
        'firstName' => 'Ada',
        'lastName' => 'Lovelace',
    ]);

    expect(fn () => $data->firstName = 'Changed')->toThrow(Error::class);
});
