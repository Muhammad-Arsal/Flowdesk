# Data Transfer Objects (DTOs)

FlowDesk uses DTOs to carry data between layers (`Controller -> Service -> Repository -> Eloquent`) without passing raw arrays or `Request` objects deeper into the app.

## Rules

1. **Final, readonly classes.** Every DTO is declared `final class` with `public readonly` properties. Once built, a DTO cannot change. If a value needs to change, build a new DTO.

2. **Named constructors, not a public `__construct` full of guesswork.** The plain constructor just assigns properties. All the actual building happens through static methods named for where the data comes from:
   - `fromArray(array $data): self` — build from a plain array (tests, jobs, artisan commands).
   - `fromRequest(FormRequest $request): self` — build from validated HTTP input.

   Add more (`fromModel`, `fromCsvRow`, etc.) as new sources come up, following the same `from{Source}` naming.

3. **No Eloquent, no framework request/response objects, no facades inside the class body.** A DTO only knows about plain PHP types (`string`, `?string`, `int`, enums, other DTOs). The one exception is the `fromRequest` constructor's parameter type, which is allowed to type-hint `FormRequest` because converting a Request into plain data is the whole point of that method, but the constructor must not call anything on `$request` other than reading validated input.

4. **DTOs live under `app/DataTransferObjects/{Domain}/`**, one file per DTO, named for the action it represents, e.g. `CreateContactData`, `UpdateContactData`.

5. **Services accept and return DTOs, never arrays.** Controllers build a DTO from the request and hand it to a Service. Repositories accept plain scalars or DTOs and return Eloquent models. DTOs never touch the Repository or Eloquent layer directly, they're app to app, dumb data only.

## Why

Arrays have no shape, no autocomplete, and no way to know what's actually in them until you print one out. Passing a `Request` into a Service ties that Service to HTTP, so it can't be reused from a queued job or console command. A DTO is a plain, typed, immutable snapshot of "here's exactly the data this action needs", nothing more.

## Example

See `app/DataTransferObjects/Contacts/CreateContactData.php`.

```php
$data = CreateContactData::fromRequest($request);
$contact = $this->contactService->create($data);
```
