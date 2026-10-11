<?php

namespace Keyvanarasteh\LaravelBones\Http\Controllers;

use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;
use Keyvanarasteh\LaravelBones\Services\Skeleton;
use Keyvanarasteh\LaravelBones\Support\PackageCatalog;

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
            'explore' => $this->explore($skeleton),
            'config' => config('bones'),
        ]);
    }

    public function data(Request $request): JsonResponse
    {
        $skeleton = $this->skeleton->inspect(fresh: $request->boolean('fresh'));

        return response()->json([
            'data' => $skeleton,
            'explore' => $this->explore($skeleton),
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

    /**
     * Annotate the curated shelf with what is installed in this app.
     *
     * @param  array<string, mixed>  $skeleton
     * @return array<int, array<string, mixed>>
     */
    protected function explore(array $skeleton): array
    {
        $installed = collect($skeleton['packages']['data']['items'] ?? [])->keyBy('name');

        return collect(PackageCatalog::all())
            ->map(function (array $item) use ($installed) {
                $match = $installed->get($item['name']);

                return $item + [
                    'installed' => $match !== null,
                    'installed_version' => $match['version'] ?? null,
                ];
            })
            ->sortBy([['installed', 'desc'], ['category'], ['name']])
            ->values()
            ->all();
    }
}
