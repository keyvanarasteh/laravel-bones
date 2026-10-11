<?php

namespace Keyvanarasteh\LaravelBones\Http\Controllers;

use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;
use Keyvanarasteh\LaravelBones\Services\Skeleton;

class BonesController
{
    public function __construct(protected Skeleton $skeleton)
    {
    }

    public function index(Request $request): View
    {
        $skeleton = $this->skeleton->inspect(fresh: $request->boolean('fresh'));

        return view('bones::dashboard', [
            'skeleton' => $skeleton,
            'config' => config('bones'),
        ]);
    }

    public function data(Request $request): JsonResponse
    {
        return response()->json([
            'data' => $this->skeleton->inspect(fresh: $request->boolean('fresh')),
        ]);
    }

    public function refresh(Request $request): RedirectResponse|JsonResponse
    {
        $this->skeleton->forget();

        if ($request->wantsJson()) {
            return response()->json(['ok' => true]);
        }

        return redirect()->route('bones.dashboard');
    }
}
