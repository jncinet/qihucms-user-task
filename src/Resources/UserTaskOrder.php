<?php

namespace Qihucms\UserTask\Resources;

use App\Http\Resources\User\User;
use Carbon\Carbon;
use Illuminate\Http\Resources\Json\JsonResource;

class UserTaskOrder extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request $request
     * @return array
     */
    public function toArray($request)
    {
        $files = [];

        if (is_array($this->files) && count($this->files)) {
            foreach ($this->files as $key => $file) {
                $files[$key] = \Storage::url($file);
            }
        } else {
            $files = null;
        }

        return [
            'id' => $this->id,
            'user' => new User($this->user),
            'task' => new UserTask($this->user_task),
            'files' => $files,
            'remark' => $this->remark,
            'status' => $this->status,
            'created_at' => Carbon::parse($this->created_at)->diffForHumans()
        ];
    }
}
