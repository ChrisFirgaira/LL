<?php

namespace App\Support\Import;

use App\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Lunar\Models\Country;
use Lunar\Models\Customer;
use Lunar\Models\CustomerGroup;

class WordpressCustomerImporter
{
    public function importCsv(string $csvPath, bool $dryRun = true): array
    {
        if (! is_file($csvPath)) {
            throw new \InvalidArgumentException("CSV file not found at [{$csvPath}]");
        }

        $handle = fopen($csvPath, 'r');

        if (! $handle) {
            throw new \RuntimeException("Unable to open CSV file [{$csvPath}]");
        }

        $header = fgetcsv($handle);

        if (! is_array($header)) {
            fclose($handle);
            throw new \RuntimeException('CSV file is empty or missing a header row.');
        }

        $columns = array_map(
            fn ($value) => Str::of((string) $value)->trim()->lower()->replace(' ', '_')->value(),
            $header
        );

        $required = ['email', 'first_name', 'last_name'];

        foreach ($required as $requiredColumn) {
            if (! in_array($requiredColumn, $columns, true)) {
                fclose($handle);
                throw new \RuntimeException("Missing required CSV column [{$requiredColumn}]");
            }
        }

        $countries = Country::query()->get()->keyBy(fn (Country $country) => strtoupper((string) $country->iso2));
        $defaultCountry = Country::query()->where('iso2', 'AU')->first() ?: Country::query()->first();
        $defaultCustomerGroup = CustomerGroup::getDefault();

        $stats = [
            'rows_total' => 0,
            'users_created' => 0,
            'users_updated' => 0,
            'customers_created' => 0,
            'customers_updated' => 0,
            'addresses_created' => 0,
            'addresses_updated' => 0,
            'failed' => 0,
            'errors' => [],
        ];

        while (($row = fgetcsv($handle)) !== false) {
            if (! is_array($row)) {
                continue;
            }

            $stats['rows_total']++;

            $data = [];
            foreach ($columns as $index => $column) {
                $data[$column] = isset($row[$index]) ? trim((string) $row[$index]) : null;
            }

            $email = strtolower((string) ($data['email'] ?? ''));
            $firstName = (string) ($data['first_name'] ?? '');
            $lastName = (string) ($data['last_name'] ?? '');

            if (! filter_var($email, FILTER_VALIDATE_EMAIL)) {
                $stats['failed']++;
                $stats['errors'][] = "Row {$stats['rows_total']}: invalid email [{$email}]";
                continue;
            }

            try {
                $importRow = function () use (
                    $data,
                    $email,
                    $firstName,
                    $lastName,
                    $countries,
                    $defaultCountry,
                    $defaultCustomerGroup,
                    &$stats
                ): void {
                    $existingUser = User::query()->where('email', $email)->first();
                    $user = $existingUser ?: new User();

                    $isNewUser = ! $user->exists;
                    $displayName = trim("{$firstName} {$lastName}");

                    $user->email = $email;
                    $user->name = $displayName !== '' ? $displayName : $email;

                    if ($isNewUser) {
                        // Password is intentionally random; accounts should reset password on first login.
                        $user->password = Str::random(40);
                    }

                    $user->save();

                    $stats[$isNewUser ? 'users_created' : 'users_updated']++;

                    $customer = $user->customers()->first() ?: new Customer();
                    $isNewCustomer = ! $customer->exists;

                    $customer->first_name = $firstName ?: 'Customer';
                    $customer->last_name = $lastName ?: 'Imported';
                    $customer->company_name = $data['company_name'] ?: null;
                    $customer->meta = [
                        ...(array) ($customer->meta ?? []),
                        'source' => 'wordpress',
                    ];
                    $customer->save();

                    if (! $user->customers()->whereKey($customer->id)->exists()) {
                        $user->customers()->attach($customer->id);
                    }

                    if ($defaultCustomerGroup && ! $customer->customerGroups()->whereKey($defaultCustomerGroup->id)->exists()) {
                        $customer->customerGroups()->attach($defaultCustomerGroup->id);
                    }

                    $stats[$isNewCustomer ? 'customers_created' : 'customers_updated']++;

                    $countryCode = strtoupper((string) ($data['country_code'] ?? ''));
                    $country = $countries->get($countryCode) ?: $defaultCountry;

                    $address = $customer->addresses()
                        ->where(fn ($query) => $query->where('billing_default', true)->orWhere('shipping_default', true))
                        ->first() ?: $customer->addresses()->first();

                    $isNewAddress = ! $address;
                    $address ??= $customer->addresses()->make();

                    $address->first_name = $firstName ?: 'Customer';
                    $address->last_name = $lastName ?: 'Imported';
                    $address->company_name = $data['company_name'] ?: null;
                    $address->line_one = $data['line_one'] ?: 'Not provided';
                    $address->line_two = $data['line_two'] ?: null;
                    $address->city = $data['city'] ?: 'Unknown';
                    $address->state = $data['state'] ?: null;
                    $address->postcode = $data['postcode'] ?: null;
                    $address->country_id = $country?->id;
                    $address->contact_email = $email;
                    $address->contact_phone = $data['contact_phone'] ?: null;
                    $address->billing_default = true;
                    $address->shipping_default = true;
                    $address->save();

                    if ($isNewAddress) {
                        $stats['addresses_created']++;
                    } else {
                        $stats['addresses_updated']++;
                    }
                };

                if ($dryRun) {
                    DB::transaction(function () use ($importRow): void {
                        $importRow();
                        throw new \RuntimeException('__DRY_RUN_ROLLBACK__');
                    });
                } else {
                    DB::transaction($importRow);
                }
            } catch (\RuntimeException $exception) {
                if ($exception->getMessage() === '__DRY_RUN_ROLLBACK__') {
                    continue;
                }

                $stats['failed']++;
                $stats['errors'][] = "Row {$stats['rows_total']}: {$exception->getMessage()}";
            } catch (\Throwable $exception) {
                $stats['failed']++;
                $stats['errors'][] = "Row {$stats['rows_total']}: {$exception->getMessage()}";
            }
        }

        fclose($handle);

        return $stats;
    }
}

