<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Article;

class ArticleController extends Controller
{
    public function index(Request $request)
    {
        // get title yg di search
        // cek apakah search kosong
        // jika ada maka get article berdasarkan title yg disearch / cari dan buat pagination nya
        // jika tidak ada maka get article dan buat paginationnya
        // kembalikan response json nya

        $searchQuery = $request->query('search');

        if ($searchQuery !== null)
        {
            $articles = Article::with(['category', 'user:id,name,email,picture'])->select(['id', 'user_id', 'category_id', 'title', 'slug', 'content_preview', 'featured_image', 'created_at', 'updated_at'])->where('title', 'like', '%' . $searchQuery . '%')->paginate();
        }
        else {
            $articles = Article::with(['category', 'user:id,name,email,picture'])->select(['id', 'user_id', 'category_id', 'title', 'slug', 'content_preview', 'featured_image', 'created_at', 'updated_at'])->paginate();
        }

        return response()->json([
            'meta' => [
                'code' => 200,
                'status' => 'success',
                'message' => 'Article fetched successfully',
            ],
            'data' => $articles,
        ]);
    }

    public function show($slug)
    {
        // get article berdasarkan slug
        // cek apakah query get article berhasil
        // jika iya kembalikan response success
        // (dieksekusi jika get article gagal) kembalikan response 404 not found

        $article = Article::with(['category', 'user:id,name,email,picture'])->where('slug', $slug)->first();

        if ($article)
        {
            return response()->json([
                'meta' => [
                    'code' => 200,
                    'status' => 'success',
                    'message' => 'Article fetched successfully',
                ],
                'data' => $article,
            ]);
        }

        return response()->json([
            'meta' => [
                'code' => 404,
                'status' => 'error',
                'message' => 'Article not found',
            ],
            'data' => [],
        ], 404);
    }

}
