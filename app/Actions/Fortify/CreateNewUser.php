<?php

namespace App\Actions\Fortify;

use App\Models\Customer;
use App\Models\User;
use App\Services\PhoneNumberService;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;
use Illuminate\Validation\ValidationException;
use InvalidArgumentException;
use Laravel\Fortify\Contracts\CreatesNewUsers;

class CreateNewUser implements CreatesNewUsers
{
    use PasswordValidationRules;

    public function __construct(private readonly PhoneNumberService $phoneNumbers) {}

    /**
     * @param  array<string, string>  $input
     *
     * @throws ValidationException
     */
    public function create(array $input): User
    {
        $validator = Validator::make($input, [
            'name' => ['required', 'string', 'max:255'],
            'email' => [
                'required',
                'string',
                'email',
                'max:255',
                Rule::unique(User::class),
            ],
            'phone' => ['required', 'string', 'max:30'],
            'password' => $this->passwordRules(),
        ]);

        $validator->validate();

        try {
            $phone = $this->phoneNumbers->normalize($input['phone']);
        } catch (InvalidArgumentException $exception) {
            throw ValidationException::withMessages([
                'phone' => [$exception->getMessage()],
            ]);
        }

        if (Customer::query()->where('phone_e164', $phone)->exists()) {
            throw ValidationException::withMessages([
                'phone' => ['Nomor telepon sudah digunakan.'],
            ]);
        }

        return DB::transaction(function () use ($input, $phone): User {
            $user = User::create([
                'name' => trim($input['name']),
                'email' => mb_strtolower(trim($input['email'])),
                'password' => Hash::make($input['password']),
            ]);

            $user->customer()->create([
                'phone_e164' => $phone,
            ]);

            return $user->load('customer');
        });
    }
}
