<?php

namespace App\Http\Controllers\Me;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Http\Requests\Me\Profile\UpdateRequest;
use App\Models\User;
use ImageKit\ImageKit;


class ProfileController extends Controller
{
    public function show()
    {
        $user = auth()->user();

        return response()->json([
            'meta' => [
                'code' => 200,
                'status' => 'success',
                'message' => 'User data fetched successfully',
            ],
            'data' => [
                'email' => $user->email,
                'name' => $user->name,
                'picture' => $user->picture,
            ]
        ]);
    }

    public function update(UpdateRequest $request)
    {
        $validated = $request->validated();
        $user = User::find(auth()->id());

        // get semua request
        // cek apakah ada request picture
        // jika iya proses, cara proses buat objek instance imagekit
        // ubah dulu gambar ke base64
        // upload, masukkan file, file name, dan folder
        // dapatkan url nya
        // masukkan url nya ke tabel
        // jika tidak ada request picture maka lanjut proses update

        if ($request->hasFile('picture'))
        {
            $imageKit = new ImageKit(
                env('IMAGEKIT_PUBLIC_KEY'),
                env('IMAGEKIT_PRIVATE_KEY'),
                env('IMAGEKIT_URL_ENDPOINT'),
            );

            $image = base64_encode(file_get_contents($request->file('picture')));

            $uploadImage = $imageKit->uploadFile([
                'file' => $image,
                'fileName' => $user->email,
                'folder' => '/user/profile',
            ]);

            $validated['picture'] = $uploadImage->result->url;
        }

        // masukkan semua request yang sudah divalidasi

        $update = $user->update($validated);

        return response()->json([
            'meta' => [
                'code' => 200,
                'status' => 'success',
                'message' => 'User data updated successfully'
            ],
            'data' => [
                'email' => $user->email,
                'name' => $user->name,
                'picture' => $user->picture,
            ]
        ]);
    }
}
