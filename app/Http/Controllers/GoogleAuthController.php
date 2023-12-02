<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;

class GoogleAuthController extends Controller
{
    public function signIn(Request $request)
    {
        // get json request, token jwt
        // pecah token dengan separator .
        // decode base64 ke string menggunakan base64_decode, yg didecode adl hasil pecahan dengan index 1 (urutan ke 2, payloadnya)
        // decode string json ke object php
        // cek apakah proses json decode berhasil
        // jika tidak maka kembalikan response 422, token invalid
        // (jika kode ini dieksekusi artinya berhasil decode) cari user dengan social id berupa id dari hasil decode payload jwt
        // cek apakah pencarian user berhasil
        // jika iya maka login kan user berdasarkan data yg diperoleh dari payload
        // lalu return success beserta data token dan data user
        // (jika kode ini dieksekusi maka pencarian user gagal, tidak ditemukan) buat user baru berdasarkan data payload
        // login kan user berdasarkan data hasil tambah data user
        // return response success beserta data token dan data user

        $request = $request->json()->all();

        $tokenParts = explode('.', $request['token']);

        $tokenPayload = base64_decode($tokenParts[1]);

        $jwtPayload = json_decode($tokenPayload, true);

        if ($jwtPayload === null)
        {
            return response()->json([
                'meta' => [
                    'code' => 422,
                    'status' => 'error',
                    'message' => 'Token invalid',
                ],
                'data' => [],
            ], 422);
        }

        $findUser = User::where('social_id', $jwtPayload['sub'])->first();

        if ($findUser)
        {
            $token = auth()->login($findUser);

            return response()->json([
                'meta' => [
                    'code' => 200,
                    'status' => 'success',
                    'message' => 'Signed in successfully',
                ],
                'data' => [
                    'user' => [
                        'name' => $findUser->name,
                        'email' => $findUser->email,
                        'picture' => $findUser->picture,
                    ],
                    'access_token' => [
                        'token' => $token,
                        'type' => 'Bearer',
                        'expires_in' => strtotime('+' . auth()->factory()->getTTL() . ' minutes'),
                    ],
                ],
            ]);
        }

        $newUser = User::create([
            'name' => $jwtPayload['name'],
            'email' => $jwtPayload['email'],
            'password' => bcrypt('my-google'),
            'picture' => $jwtPayload['picture'],
            'social_id' => $jwtPayload['sub'],
            'social_type' => 'google',
        ]);

        $token = auth()->login($newUser);

        return response()->json([
            'meta' => [
                'code' => 200,
                'status' => 'success',
                'message' => 'Signed in successfully',
            ],
            'data' => [
                'user' => [
                    'name' => $newUser->name,
                    'email' => $newUser->email,
                    'picture' => $newUser->picture,
                ],
                'access_token' => [
                    'token' => $token,
                    'type' => 'Bearer',
                    'expires_in' => strtotime('+' . auth()->factory()->getTTL() . ' minutes'),
                ],
            ],
        ]);

    }
}
