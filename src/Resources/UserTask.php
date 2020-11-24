<?php

namespace Qihucms\UserTask\Resources;

use App\Http\Resources\User\User;
use Carbon\Carbon;
use Illuminate\Http\Resources\Json\JsonResource;
use Qihucms\Currency\Resources\Type\Type;

class UserTask extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request $request
     * @return array
     */
    public function toArray($request)
    {
        return [
            'id' => $this->id,
            'user' => new User($this->user),
            'title' => $this->title,
            'thumbnail' => !empty($this->thumbnail) ? \Storage::url($this->thumbnail) : null,
            'start_time' => $this->start_time ? Carbon::parse($this->start_time)->toDateTimeString() : null,
            'end_time' => $this->start_time ? Carbon::parse($this->end_time)->toDateTimeString() : null,
            'stock' => $this->stock,
            'currency_type' => new Type($this->currency_type),
            'amount' => $this->amount,
            'content' => $this->content,
            'btn_text' => $this->btn_text,
            'link' => $this->link,
            'pay_status' => $this->pay_status,
            'status' => $this->status,
            'created_at' => Carbon::parse($this->created_at)->diffForHumans()
        ];
    }
}
