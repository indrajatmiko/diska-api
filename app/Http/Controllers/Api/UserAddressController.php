<?php

namespace App\Http\Controllers\Api;

// =========================================================
// ==> BAGIAN KRUSIAL #1: 'USE' STATEMENT YANG BENAR <==
// =========================================================
use App\Http\Controllers\Controller; // <-- Ini menghubungkan ke base controller
use App\Http\Requests\Api\StoreUserAddressRequest;
use App\Http\Resources\UserAddressResource;
use App\Models\UserAddress;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests; // Tambahkan baris ini

// =========================================================
// ==> BAGIAN KRUSIAL #2: 'EXTENDS CONTROLLER' <==
// =========================================================
class UserAddressController extends Controller
{
    use AuthorizesRequests;
    public function index(Request $request)
    {
        $addresses = $request->user()->addresses()->latest()->get();
        return UserAddressResource::collection($addresses);
    }

    public function store(StoreUserAddressRequest $request)
    {
        $user = $request->user();
        $validatedData = $request->validated();

        return DB::transaction(function () use ($user, $validatedData) {
            if ($user->addresses()->doesntExist()) {
                $validatedData['is_primary'] = true;
            }
            elseif (isset($validatedData['is_primary']) && $validatedData['is_primary']) {
                $user->addresses()->update(['is_primary' => false]);
            }

            $address = $user->addresses()->create($validatedData);
            return (new UserAddressResource($address))->response()->setStatusCode(201);
        });
    }

    public function show(UserAddress $address)
    {
        // Panggilan ini sekarang akan berfungsi
        $this->authorize('view', $address); 
        return new UserAddressResource($address);
    }

    public function update(StoreUserAddressRequest $request, UserAddress $address)
    {
        // Panggilan ini sekarang akan berfungsi
        $this->authorize('update', $address);
        $address->update($request->validated());
        return new UserAddressResource($address);
    }

    public function destroy(UserAddress $address)
    {
        // Panggilan ini sekarang akan berfungsi
        $this->authorize('delete', $address);
        $address->delete();
        return response()->noContent();
    }

    public function setPrimary(Request $request, UserAddress $address)
    {
        // Panggilan ini sekarang akan berfungsi
        $this->authorize('update', $address);
        DB::transaction(function () use ($request, $address) {
            $request->user()->addresses()->update(['is_primary' => false]);
            $address->update(['is_primary' => true]);
        });
        return response()->json(['message' => 'Alamat utama berhasil diperbarui.']);
    }
}