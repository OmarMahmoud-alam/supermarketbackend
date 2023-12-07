<?php

namespace App\Http\Controllers\Api\User;

use App\Models\Addresse;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;

class AddresseController extends Controller
{
    public function getAllAddresses()
    {
        // Get the authenticated user
        $user = Auth::user();

        // Get all addresses for the user
        $addresses = $user->addresse;

        return response()->json(['addresses' => $addresses]);
    }
    public function createAddress(Request $request)
    {
        // Define validation rules
        $rules = [
            'name' => 'string',
            'street_address' => 'required|string',
            'city' => 'required|string',
            'state' => 'required|string',
            'postal_code' => 'required|string',
            'country' => 'required|string',
            'latitude' => 'nullable|numeric',
            'longitude' => 'nullable|numeric',
            'is_default' => 'boolean',
        ];

        // Run validation
        $validator = Validator::make($request->all(), $rules);

        // Check for validation failure
        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        // Get the authenticated user
        $user = Auth::user();

        // If the new address is set as default, update existing default addresses to false
        if ($request->input('is_default')) {
            $user->addresse()->update(['is_default' => false]);
        }

        // Create a new address for the user
        $address = new Addresse([
            'name' => $request->input('name')??"",
            'street_address' => $request->input('street_address'),
            'city' => $request->input('city'),
            'state' => $request->input('state'),
            'postal_code' => $request->input('postal_code'),
            'country' => $request->input('country'),
            'latitude' => $request->input('latitude'),
            'longitude' => $request->input('longitude'),
            'is_default' => $request->input('is_default', false),
        ]);

        // Save the address and associate it with the user
        $user->addresse()->save($address);

        return response()->json(['message' => 'Address created successfully']);
    }
    public function makeDefaultAddress(request $request)
    {
        $validator = Validator::make($request->all(), [
            'is_default' => 'required|boolean',
            'addresses_id' => 'required',
        ]);

        // Check for validation failure
        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        // Get the authenticated user
        $user = Auth::user();
        $address=Addresse::find('id','addresses_id');
        if($address===null ){
            return response()->json(['error' => 'Address does not belong to the user.'], 200);

        }
        // Ensure the address belongs to the user
        if ($user->id !== $address->user_id) {
            return response()->json(['error' => 'Address does not belong to the user.'], 200);
        }

        // Set is_default to true for the selected address
        $address->update(['is_default' => true]);

        // Set is_default to false for other addresses of the user
        $user->addresses()->where('id', '<>', $address->id)->update(['is_default' => false]);
        return response()->json(['message' => 'Default address updated successfully']);
    }
}
