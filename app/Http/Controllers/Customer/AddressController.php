<?php

namespace App\Http\Controllers\Customer;

use App\Http\Controllers\Controller;
use App\Http\Requests\Customer\StoreAddressRequest;
use App\Http\Requests\Customer\UpdateAddressRequest;
use App\Models\Address;
use App\Services\PhoneNumberService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;
use Illuminate\View\View;
use InvalidArgumentException;

class AddressController extends Controller
{
    public function __construct(private readonly PhoneNumberService $phoneNumbers) {}

    public function index(): View
    {
        $customer = request()->user()->customer;
        abort_if($customer === null, 403);

        return view('customer.addresses.index', [
            'addresses' => $customer->addresses()->orderByDesc('is_default')->orderBy('id')->get(),
        ]);
    }

    public function store(StoreAddressRequest $request): RedirectResponse
    {
        $customer = $request->user()->customer;
        abort_if($customer === null, 403);

        $validated = $request->validated();
        $phone = $this->normalizePhone($validated['phone']);
        $makeDefault = $request->boolean('is_default') || $customer->addresses()->doesntExist();

        DB::transaction(function () use ($customer, $validated, $phone, $makeDefault): void {
            if ($makeDefault) {
                $customer->addresses()->update(['is_default' => false]);
            }

            $customer->addresses()->create([
                'label' => $validated['label'],
                'recipient' => $validated['recipient'],
                'phone_e164' => $phone,
                'address_line' => $validated['address_line'],
                'city' => $validated['city'],
                'postal_code' => $validated['postal_code'] ?? null,
                'is_default' => $makeDefault,
            ]);
        });

        return back()->with('success', 'Alamat berhasil ditambahkan.');
    }

    public function update(UpdateAddressRequest $request, Address $address): RedirectResponse
    {
        $customer = $request->user()->customer;
        abort_if($customer === null || $address->customer_id !== $customer->getKey(), 403);

        $validated = $request->validated();
        $phone = $this->normalizePhone($validated['phone']);
        $makeDefault = $request->boolean('is_default');

        DB::transaction(function () use ($address, $customer, $validated, $phone, $makeDefault): void {
            if ($makeDefault) {
                $customer->addresses()->where('id', '!=', $address->getKey())->update(['is_default' => false]);
            }

            $address->update([
                'label' => $validated['label'],
                'recipient' => $validated['recipient'],
                'phone_e164' => $phone,
                'address_line' => $validated['address_line'],
                'city' => $validated['city'],
                'postal_code' => $validated['postal_code'] ?? null,
                'is_default' => $makeDefault ?: $address->is_default,
            ]);
        });

        return back()->with('success', 'Alamat berhasil diperbarui.');
    }

    public function destroy(Address $address): RedirectResponse
    {
        $customer = request()->user()->customer;
        abort_if($customer === null || $address->customer_id !== $customer->getKey(), 403);

        DB::transaction(function () use ($address, $customer): void {
            $wasDefault = $address->is_default;
            $address->delete();

            if ($wasDefault) {
                $customer->addresses()->orderBy('id')->first()?->update(['is_default' => true]);
            }
        });

        return back()->with('success', 'Alamat berhasil dihapus.');
    }

    private function normalizePhone(string $phone): string
    {
        try {
            return $this->phoneNumbers->normalize($phone);
        } catch (InvalidArgumentException $exception) {
            throw ValidationException::withMessages([
                'phone' => [$exception->getMessage()],
            ]);
        }
    }
}
