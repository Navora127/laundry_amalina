<?php

namespace App\Http\Controllers\Admin;

use App\Enums\UserRole;
use App\Enums\UserStatus;
use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\StoreStaffRequest;
use App\Http\Requests\Admin\UpdateStaffRequest;
use App\Models\User;
use App\Services\AuditLogger;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Hash;
use Illuminate\View\View;

class StaffController extends Controller
{
    public function __construct(private readonly AuditLogger $auditLogger) {}

    public function index(): View
    {
        return view('admin.staff.index', [
            'staff' => User::query()->whereIn('role', [UserRole::Admin->value, UserRole::Cashier->value])->latest()->get(),
            'roles' => [UserRole::Admin, UserRole::Cashier],
        ]);
    }

    public function store(StoreStaffRequest $request): RedirectResponse
    {
        $data = $request->validated();
        $user = new User;
        $user->forceFill([
            'name' => trim($data['name']),
            'email' => mb_strtolower(trim($data['email'])),
            'password' => Hash::make($data['password']),
            'role' => UserRole::from($data['role']),
            'status' => UserStatus::Active,
            'email_verified_at' => now(),
        ])->save();

        $this->auditLogger->record($user, 'staff.created', $request->user(), [], [
            'role' => $user->role->value,
        ]);

        return back()->with('success', 'Akun staff berhasil dibuat.');
    }

    public function update(UpdateStaffRequest $request, User $user): RedirectResponse
    {
        if ($user->is($request->user()) && (
            $request->input('status') === UserStatus::Inactive->value
            || $request->input('role') !== UserRole::Admin->value
        )) {
            return back()->with('error', 'Anda tidak dapat mengubah role atau menonaktifkan akun sendiri.');
        }

        $data = $request->validated();
        $oldValues = ['role' => $user->role->value, 'status' => $user->status->value];
        $user->forceFill([
            'name' => trim($data['name']),
            'email' => mb_strtolower(trim($data['email'])),
            'role' => UserRole::from($data['role']),
            'status' => UserStatus::from($data['status']),
        ]);

        if (! empty($data['password'])) {
            $user->password = Hash::make($data['password']);
        }

        $user->save();
        $this->auditLogger->record($user, 'staff.updated', $request->user(), $oldValues, [
            'role' => $user->role->value,
            'status' => $user->status->value,
        ]);

        return back()->with('success', 'Akun staff berhasil diperbarui.');
    }
}
