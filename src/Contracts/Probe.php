<?php

namespace Keyvanarasteh\LaravelBones\Contracts;

interface Probe
{
    /**
     * Unique machine key for the section, e.g. "routes".
     */
    public function key(): string;

    /**
     * Human readable label shown on the tab and the overview card.
     */
    public function label(): string;

    /**
     * One sentence describing what this probe looks at.
     */
    public function description(): string;

    /**
     * Icon glyph name the dashboard renders for this section.
     */
    public function icon(): string;

    /**
     * Inspect the application and return the section payload.
     *
     * @return array{
     *     summary: array<string, int|string|float|null>,
     *     items: array<int, mixed>,
     *     graph?: array{nodes: array<int, mixed>, edges: array<int, mixed>}
     * }
     */
    public function scan(): array;
}
