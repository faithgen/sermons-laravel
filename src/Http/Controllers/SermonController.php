<?php

namespace FaithGen\Sermons\Http\Controllers;


use App\Http\Controllers\Controller;
use FaithGen\Sermons\Events\Created;
use FaithGen\Sermons\Http\Requests\CreateRequest;
use FaithGen\Sermons\Http\Requests\GetRequest;
use FaithGen\Sermons\Http\Requests\IndexRequest;
use FaithGen\Sermons\Http\Requests\UpdatePictureRequest;
use FaithGen\Sermons\Http\Requests\UpdateRequest;
use FaithGen\Sermons\Http\Resources\SermonList as ListResource;
use FaithGen\Sermons\Http\Resources\Sermon as SermonResource;
use FaithGen\Sermons\Models\Sermon;
use FaithGen\Sermons\SermonService;

class SermonController extends Controller
{
    /**
     * @var SermonService
     */
    private $sermonService;

    public function __construct(SermonService $sermonService)
    {
        $this->sermonService = $sermonService;
    }

    function create(CreateRequest $request)
    {
        return $this->sermonService->createFromRelationship($request->validated());
    }

    function index(IndexRequest $request)
    {
        $sermons = auth()->user()->sermons()
            ->where('preacher', 'LIKE', '%' . $request->filter_text . '%')
            ->orWhere('title', 'LIKE', '%' . $request->filter_text . '%')
            ->orWhere('preacher', 'LIKE', '%' . $request->filter_text . '%')
            ->latest()->paginate($request->has('limit') ? $request->limit : 15);
        if ($request->has('full_sermons'))
            return SermonResource::collection($sermons);
        else
            return ListResource::collection($sermons);
    }

    function view($sermon)
    {
        $sermon = Sermon::findOrFail($sermon);
        $this->authorize('sermon.view', $sermon);
        SermonResource::withoutWrapping();
        return new SermonResource($sermon);
    }

    function updatePicture(UpdatePictureRequest $request)
    {
        if ($this->sermonService->getSermon()->image()->exists())
            $this->sermonService->deleteFiles($this->sermonService->getSermon());

        if ($request->hasImage && $request->has('image'))
            try {
                event(new Created($this->sermonService->getSermon()));
                return $this->successResponse('Preacher image updated successfully!');
            } catch (\Exception $e) {
                abort(500, $e->getMessage());
            }
        else {
            try {
                $this->sermonService->getSermon()->image()->delete();
                return $this->successResponse('Preacher image deleted successfully!');
            } catch (\Exception $e) {
                abort(500, $e->getMessage());
            }
        }
    }

    function delete(GetRequest $request)
    {
        $this->authorize('sermon.delete', $this->sermonService->getSermon());
        return $this->sermonService->destroy('Sermon deleted');
    }

    function update(UpdateRequest $request)
    {
        return $this->sermonService->update($request->validated(), 'Sermon updated successfully');
    }
}
