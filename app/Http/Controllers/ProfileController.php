<?php

namespace App\Http\Controllers;

use App\Http\Requests\ProfileUpdateRequest;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\Storage;
use Illuminate\View\View;

// intervention library
use Intervention\Image\Laravel\Facades\Image;
use Illuminate\Support\Facades\Response;
use Intervention\Image\Format;
use Intervention\Image\Alignment;
use Intervention\Image\Typography\FontFactory;

class ProfileController extends Controller
{
    // generate id card menggunakan Intervention php
    // docs: https://image.intervention.io
    // composer require intervention/image-laravel
    public function downloadIdCard()
    {
        $user = Auth::user();
        $image = Image::createImage(800, 450)->fill('dddddd');

        // ELEMEN LAIN PADA GAMBAR
        // foto
        $foto = Image::decode(Storage::disk('public')->path($user->photo_path));
        $foto->resize(300, 300);
        $image->insert($foto, 20, 20);

        // variabel font
        $font_size = 30;
        $first_line_y = 40;
        $next_line = 30;

        // teks nama
        $image->text($user->name, 350, $first_line_y, fn (FontFactory $font) => $this->imageFontStyle($font, $font_size));

        // teks gender
        $image->text($user->gender ? 'Laki-Laki' : 'Perempuan', 
        350, 
        $first_line_y + (1 * $next_line), 
        fn (FontFactory $font) => $this->imageFontStyle($font, $font_size));

        // teks alamat
        $address_lines = $this->splitText($user->address, 40);
        // looping tiap baris
        foreach ($address_lines as $key => $line) {
            $image->text(
                $line, 
                350, 
                $first_line_y + ((2 + $key) * $next_line), 
                fn (FontFactory $font) => $this->imageFontStyle($font, $font_size)
                );
        }
        $response = Response::make($image->encodeUsingFormat(Format::JPEG));
        $response->header('Content-Type', 'image/jpeg');
        return $response;
    }

    /**
     * Display the user's profile form.
     */
    public function edit(Request $request): View
    {
        return view('profile.edit', [
            'user' => $request->user(),
        ]);
    }

    /**
     * Update the user's profile information.
     */
    public function update(ProfileUpdateRequest $request): RedirectResponse
    {
        $request->user()->fill($request->validated());

        if ($request->user()->isDirty('email')) {
            $request->user()->email_verified_at = null;
        }

        // simpan file foto di storage
        if ($request->hasFile('photo_path')) {
            $request->user()->photo_path = Storage::disk('public')
                        ->put('foto-profil', $request->file('photo_path'));
        }

        $request->user()->save();

        return Redirect::route('profile.edit')->with('status', 'profile-updated');
    }

    /**
     * Delete the user's account.
     */
    public function destroy(Request $request): RedirectResponse
    {
        $request->validateWithBag('userDeletion', [
            'password' => ['required', 'current_password'],
        ]);

        $user = $request->user();

        Auth::logout();

        $user->delete();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return Redirect::to('/');
    }

    protected function imageFontStyle(FontFactory $font, int $size = 12)
    {
        $font->filepath(public_path('fonts/Bebas.ttf'));
        $font->size($size);
        $font->lineHeight(1.2);
        // use intervention\Image\Alignment
        $font->align(Alignment::LEFT);
        $font->color('#000000');

        return $font;
    }

    protected function splitText(string $text, $maxLength = 50) : array
    {
        $result = [];
        while (strlen($text) > $maxLength) {
            // ambil bagian awal sepanjang max length
            $chunk = substr($text, 0, $maxLength + 1);

            // cari posisi terakhir spasi, titik, atau koma
            $lastSpace = strrpos($chunk, ' ');
            $lastDot = strrpos($chunk, '.');
            $lastComma = strrpos($chunk, ',');

            // ambil posisi terbesar
            $cutPos = max($lastSpace, $lastDot, $lastComma);

            // jika tidak ditemukan separator
            if ($cutPos === false || $cutPos <= 0)
                $cutPos = $maxLength;

            // simpan hasil potongan
            $result[] = trim(substr($text, 0, $cutPos));

            // lanjutkan sisa teks
            $text = trim(substr($text, $cutPos));
        }

        // Sisa terakhir
        if (!empty($text)) {
            $result[] = $text;
        }
        return $result;
    }
    
}