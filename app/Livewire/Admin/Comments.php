<?php

namespace App\Livewire\Admin;

use App\Models\Comment;
use App\Models\FoodStore;
use App\Models\Friend;
use App\Models\Residence;
use App\Models\Tour;
use App\Models\User;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\Schema;
use Livewire\Component;
use Livewire\WithPagination;

class Comments extends Component
{
    use WithPagination;

    public $search = '';
    public $villas;
    public $stores;
    public $tours;
    public $friends;
    public $users;
    public $supportsTourComments = false;
    public $supportsFriendComments = false;

    protected $listeners = ['remove'];

    public $form = 'empty';
    public $id;
    public $point;

    public function mount()
    {
        $this->villas = Residence::all()->keyBy('id');
        $this->stores = FoodStore::all()->keyBy('id');
        $this->tours = Tour::all()->keyBy('id');
        $this->friends = Friend::all()->keyBy('id');
        $this->users = User::all()->keyBy('id');
        $this->supportsTourComments = Schema::hasColumn('comments', 'tour_id');
        $this->supportsFriendComments = Schema::hasColumn('comments', 'friend_id');
    }

    public function render()
    {
        $query = Comment::query();

        if (!empty($this->search)) {
            $search = trim($this->search);

            $userIds = User::query()
                ->where(function ($builder) use ($search) {
                    $builder
                        ->where('name', 'like', '%' . $search . '%')
                        ->orWhere('family', 'like', '%' . $search . '%');
                })
                ->pluck('id');

            $residenceIds = Residence::query()
                ->where('title', 'like', '%' . $search . '%')
                ->pluck('id');

            $storeIds = FoodStore::query()
                ->where('title', 'like', '%' . $search . '%')
                ->pluck('id');

            $tourIds = $this->supportsTourComments
                ? Tour::query()->where('title', 'like', '%' . $search . '%')->pluck('id')
                : collect();

            $friendIds = $this->supportsFriendComments
                ? Friend::query()->where('title', 'like', '%' . $search . '%')->pluck('id')
                : collect();

            $hasSearchFilters = is_numeric($search)
                || $userIds->isNotEmpty()
                || $residenceIds->isNotEmpty()
                || $storeIds->isNotEmpty()
                || ($this->supportsTourComments && $tourIds->isNotEmpty())
                || ($this->supportsFriendComments && $friendIds->isNotEmpty());

            if ($hasSearchFilters) {
                $query->where(function ($builder) use ($search, $userIds, $residenceIds, $storeIds, $tourIds, $friendIds) {
                    if (is_numeric($search)) {
                        $builder
                            ->orWhere('id', $search)
                            ->orWhere('point', $search);
                    }

                    if ($userIds->isNotEmpty()) {
                        $builder->orWhereIn('user_id', $userIds);
                    }

                    if ($residenceIds->isNotEmpty()) {
                        $builder->orWhereIn('residence_id', $residenceIds);
                    }

                    if ($storeIds->isNotEmpty()) {
                        $builder->orWhereIn('store_id', $storeIds);
                    }

                    if ($this->supportsTourComments && $tourIds->isNotEmpty()) {
                        $builder->orWhereIn('tour_id', $tourIds);
                    }

                    if ($this->supportsFriendComments && $friendIds->isNotEmpty()) {
                        $builder->orWhereIn('friend_id', $friendIds);
                    }
                });
            } else {
                $query->whereRaw('1 = 0');
            }
        }

        $list = $query->orderBy('id', 'DESC')->paginate(10);
        $list->setCollection(
            $list->getCollection()->map(function (Comment $comment) {
                [$serviceName, $typeLabel, $serviceStatus] = $this->resolveServiceMeta($comment);

                $comment->row_user_name = $this->resolveUserName($comment->user_id);
                $comment->row_service_name = $serviceName;
                $comment->row_type_label = $typeLabel;
                $comment->row_review_text = $this->resolveReviewText($comment);
                $comment->row_status = $this->resolveStatusMeta($comment, $serviceStatus);

                return $comment;
            })
        );

        return view('livewire.admin.comments', [
            'list' => $list,
        ])
            ->extends('app')
            ->section('content');
    }

    public function updated($propertyName)
    {
        $this->resetPage();
    }

    public function remove($id)
    {
        $comment = Comment::findOrFail($id);
        $comment->delete();
        $this->dispatch('removed');
    }

    public function setForm($form, $id = null)
    {
        $this->form = $form;

        if ($form === 'edit') {
            $model = Comment::findOrFail($id);
            $this->id = $id;
            $this->point = $model->point;
            return;
        }

        $this->id = null;
        $this->point = null;
    }

    public function edit()
    {
        Comment::findOrFail($this->id)->update([
            'point' => $this->point,
        ]);

        $this->setForm('empty');
        $this->dispatch('edited');
    }

    protected function resolveUserName($userId): string
    {
        if (!isset($this->users[$userId])) {
            return 'کاربر حذف‌شده';
        }

        $user = $this->users[$userId];
        $fullName = trim(($user->name ?? '') . ' ' . ($user->family ?? ''));

        return $fullName !== '' ? $fullName : ('کاربر #' . $userId);
    }

    protected function resolveServiceMeta(Comment $comment): array
    {
        if ($comment->residence_id && isset($this->villas[$comment->residence_id])) {
            $model = $this->villas[$comment->residence_id];
            return [$model->title, 'اقامتگاه', $model->status ?? null];
        }

        if ($comment->store_id && isset($this->stores[$comment->store_id])) {
            $model = $this->stores[$comment->store_id];
            return [$model->title, 'رستوران', $model->status ?? null];
        }

        if ($this->supportsTourComments && $comment->getAttribute('tour_id') && isset($this->tours[$comment->getAttribute('tour_id')])) {
            $model = $this->tours[$comment->getAttribute('tour_id')];
            return [$model->title, 'تور', $model->status ?? null];
        }

        if ($this->supportsFriendComments && $comment->getAttribute('friend_id') && isset($this->friends[$comment->getAttribute('friend_id')])) {
            $model = $this->friends[$comment->getAttribute('friend_id')];
            return [$model->title, 'همسفر', $model->status ?? null];
        }

        return ['خدمت نامشخص', 'نامشخص', null];
    }

    protected function resolveReviewText(Comment $comment): string
    {
        $rawReview = $comment->getAttribute('review')
            ?? $comment->getAttribute('comment')
            ?? $comment->getAttribute('body')
            ?? $comment->getAttribute('text');

        if (is_string($rawReview) && trim($rawReview) !== '') {
            return trim($rawReview);
        }

        return match (true) {
            $comment->point >= 4.5 => 'عالی بود',
            $comment->point >= 3.5 => 'خوب بود',
            $comment->point >= 2.5 => 'معمولی بود',
            $comment->point >= 1.5 => 'ضعیف بود',
            default => 'نامناسب بود',
        };
    }

    protected function resolveStatusMeta(Comment $comment, $serviceStatus): array
    {
        $rawStatus = $comment->getAttribute('status');
        $isPublished = $rawStatus !== null
            ? (int) $rawStatus === 1
            : (int) ($serviceStatus ?? 0) === 1;

        return [
            'label' => $isPublished ? 'منتشر شده' : 'در انتظار',
            'class' => $isPublished ? 'active' : 'pending',
        ];
    }
}
