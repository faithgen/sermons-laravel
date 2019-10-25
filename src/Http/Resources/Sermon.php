<?php

namespace FaithGen\Sermons\Http\Resources;

use Carbon\Carbon;
use FaithGen\Sermons\SermonHelper;
use Illuminate\Http\Resources\Json\JsonResource;

class Sermon extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param \Illuminate\Http\Request $request
     * @return array
     */
    public function toArray($request)
    {
        return [
            'id' => $this->id,
            'title' => $this->title,
            'comments' => [
                'count' => number_format($this->comments()->count()),
            ],
            'preacher' => [
                'name' => $this->preacher,
                'avatar' => SermonHelper::getAvatar($this->resource),
            ],
            'date' => SermonHelper::getDates(Carbon::parse($this->date)),
            'resource' => $this['resource'],
            'verses' => [
                'main' => $this->main_verses,
                'reference' => $this->reference_verses,
            ],
            'sermon' => $this->sermon
        ];
    }
}
