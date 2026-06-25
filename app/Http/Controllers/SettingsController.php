<?php

namespace App\Http\Controllers;

use App\Http\Requests\SettingsUpdateRequest;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Illuminate\Support\Facades\Hash;

class SettingsController extends Controller
{
    public function show(Request $request)
    {
        $user = $request->user();

        return Inertia::render('settings', [
            'preferences' => $user->preferences ?? null,
        ]);
    }

    public function update(SettingsUpdateRequest $request)
    {
        $user = $request->user();

        $data = $request->validated();

        $preferences = $user->preferences ?? [];
        $preferences['theme'] = $data['theme'] ?? ($preferences['theme'] ?? 'adultos');
        $preferences['mode'] = $data['mode'] ?? ($preferences['mode'] ?? 'light');
        $preferences['font_size'] = $data['font_size'] ?? ($preferences['font_size'] ?? 16);
        $preferences['contrast'] = $data['contrast'] ?? ($preferences['contrast'] ?? 'normal');

        $user->preferences = $preferences;

        if (!empty($data['new_password'])) {
            $user->password = Hash::make($data['new_password']);
        }

        $user->save();

        return redirect()->back()->with('success', 'Configuración guardada correctamente.');
    }
}
