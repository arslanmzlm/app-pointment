<?php

namespace App\Http\Controllers\Dashboard;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreContentRequest;
use App\Http\Requests\UpdateContentRequest;
use App\Models\Content;
use App\Services\ContentService;
use Inertia\Inertia;

class ContentController extends Controller
{
    public function __construct(private ContentService $contentService)
    {
    }

    public function list()
    {
        return Inertia::render('Dashboard/Content/List', [
            'contents' => $this->contentService->filter(),
            'sections' => $this->contentService->getSections(),
        ]);
    }

    public function create()
    {
        return Inertia::render('Dashboard/Content/Create', [
            'sections' => $this->contentService->getSections(),
        ]);
    }

    public function store(StoreContentRequest $request)
    {
        $this->contentService->store($request->validated());

        session()->flash('toast.success', trans('messages.content.created'));

        return to_route('dashboard.content.list');
    }

    public function edit(Content $content)
    {
        return Inertia::render('Dashboard/Content/Edit', [
            'content' => $content,
            'sections' => $this->contentService->getSections(),
        ]);
    }

    public function update(UpdateContentRequest $request, Content $content)
    {
        $this->contentService->update($content, $request->validated());

        session()->flash('toast.success', trans('messages.content.updated'));

        return to_route('dashboard.content.list');
    }

    public function destroy(Content $content)
    {
        $this->contentService->delete($content);

        session()->flash('toast.success', trans('messages.content.deleted'));
    }
}
