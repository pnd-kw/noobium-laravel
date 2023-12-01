<?php

namespace App\Http\Controllers\Me;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Http\Requests\Me\Article\StoreRequest;
use Str;
use ImageKit\ImageKit;
use App\Models\User;
use App\Models\Article;

class ArticleController extends Controller
{
    public function index()
    {
        // get user id yg saat ini sedang login
        // get article dimana user id nya yg saat ini sedang login
        // get juga category dan usernya siapa
        // buat paginationnya

        $userId = auth()->id();

        $articles = Article::with(['category', 'user:id,name,email,picture'])->select([
            'id', 
            'user_id', 
            'category_id', 
            'title', 
            'slug', 
            'content_preview', 
            'featured_image', 
            'created_at', 
            'updated_at',
        ])->where('user_id', $userId)->paginate();

        return response()->json([
            'meta' => [
                'code' => 200,
                'status' => 'success',
                'message' => 'Articles fetched successfully',
            ],
            'data' => $articles,
        ]);
    }
    public function store(StoreRequest $request)
    {
        // category_id
        // title
        // content
        // featured_image

        // validasi semua request yg masuk
        // terima di controller
        // generate slug dari request title
        // generate content preview dari request content
        // get file image lalu ubah ke base64 untuk dikirim ke imagekit
        // get url imagekit dari image yg kita kirim tadi
        // get user id yg login
        // lalu create article berdasarkan data yg sudah kita proses dimana usernya adalah user yg login

        $validated = $request->validated();

        $validated['slug'] = Str::of($validated['title'])->slug('-') . '-' . time();
        $validated['content_preview'] = substr($validated['content'], 0, 218) . '...';

        $imageKit = new ImageKit(
            env('IMAGEKIT_PUBLIC_KEY'),
            env('IMAGEKIT_PRIVATE_KEY'),
            env('IMAGEKIT_URL_ENDPOINT'),
        );

        $image = base64_encode(file_get_contents($request->file('featured_image')));

        $uploadImage = $imageKit->uploadFile([
            'file' => $image,
            'fileName' => $validated['slug'],
            'folder' => '/article',
        ]);

        $validated['featured_image'] = $uploadImage->result->url;

        $userId = auth()->id();

        $createArticle = User::find($userId)->articles()->create($validated);

        if ($createArticle)
        {
            return response()->json([
                'meta' => [
                    'code' => 200,
                    'status' => 'success',
                    'message' => 'Article created successfully.',
                ],
                'data' => [],
            ]);
        }

        return response()->json([
            'meta' => [
                'code' => 500,
                'status' => 'error',
                'message' => 'Error! Article failed to create.',
            ],
            'data' => [],
        ], 500);
    }
}
