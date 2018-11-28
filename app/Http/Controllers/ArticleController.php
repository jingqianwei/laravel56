<?php
/**
 * Created by PhpStorm.
 * User: chinwe.jing
 * Date: 2018/11/28
 * Time: 15:50
 */

namespace App\Http\Controllers;

use App\Models\Article;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class ArticleController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @param Request $request
     * @return JsonResponse
     * @throws \Illuminate\Validation\ValidationException
     */
    public function index(Request $request) :JsonResponse
    {
        return Article::request('index', $request->all())
            ->fetch()
            ->response();
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request $request
     * @return JsonResponse
     * @throws \Illuminate\Validation\ValidationException
     */
    public function store(Request $request) :JsonResponse
    {
        return Article::request('store', $request->all())
            ->model()
            ->reserve()
            ->response('保存成功');
    }

    /**
     * Display the specified resource.
     *
     * @param Request $request
     * @param  Article $article
     * @return JsonResponse
     * @throws \Illuminate\Validation\ValidationException
     */
    public function show(Request $request, Article $article) :JsonResponse
    {
        return $article->request('show', $request->all())
            ->fetch()
            ->response();
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request $request
     * @param  Article $article
     * @return JsonResponse
     * @throws \Illuminate\Validation\ValidationException
     */
    public function update(Request $request, Article $article) :JsonResponse
    {
        return $article->request('update', $request->all())
            ->model()
            ->reserve()
            ->response('修改成功');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  Article $article
     * @throws
     * @return JsonResponse
     */
    public function destroy(Article $article) :JsonResponse
    {
        return $article->request('destroy', [])
            ->delete()
            ->response('删除成功');
    }
}