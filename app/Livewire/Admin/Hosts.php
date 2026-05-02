<?php

namespace App\Livewire\Admin;

use App\Models\Config;
use App\Models\User;
use Illuminate\Support\Facades\Redirect;
use Livewire\Component;
use Livewire\WithPagination;

class Hosts extends Component
{
    use WithPagination;

    public $search = '';
    public $walletForm = 'empty';
    public $walletMode = 'charge';
    public $selectedHostId = '';
    public $walletAmount = '';

    public function render()
    {
        $query = $this->getHostsBaseQuery()
            ->with([
                'residences:id,user_id,amount',
                'tours:id,user_id,amount',
                'foodstores:id,user_id',
            ])
            ->withCount([
                'residences',
                'tours',
                'foodstores',
            ]);

        if ($this->search !== '') {
            $search = trim($this->search);
            $query->where(function ($builder) use ($search) {
                $builder
                    ->where('name', 'like', '%' . $search . '%')
                    ->orWhere('family', 'like', '%' . $search . '%')
                    ->orWhere('phone', 'like', '%' . $search . '%');
            });
        }

        $list = $query->orderBy('id', 'DESC')->paginate(10);
        $list->setCollection(
            $list->getCollection()->map(function (User $user) {
                $user->row_full_name = $this->resolveFullName($user);
                $user->row_total_income = $this->calculateTotalIncome($user);
                $user->row_wallet_balance = $this->getHostWalletBalance($user->id);
                $user->row_settled_income = $this->getHostSettledIncome($user->id);
                $user->row_pending_settlement = max($user->row_total_income - $user->row_settled_income, 0);
                return $user;
            })
        );

        return view('livewire.admin.hosts', [
            'list' => $list,
            'hostOptions' => $this->getHostsBaseQuery()
                ->orderBy('name')
                ->orderBy('family')
                ->get(),
        ])
            ->extends('app')
            ->section('content');
    }

    public function updated($propertyName)
    {
        if ($propertyName === 'search') {
            $this->resetPage();
        }
    }

    public function openWalletModal($userId = null)
    {
        $this->resetErrorBag();
        $this->walletForm = 'wallet';
        $this->walletMode = 'charge';
        $this->selectedHostId = $userId ? (string) $userId : '';
        $this->walletAmount = '';
    }

    public function openSettlementModal($userId)
    {
        $host = $this->getHostsBaseQuery()
            ->with(['residences:id,user_id,amount', 'tours:id,user_id,amount'])
            ->findOrFail($userId);
        $pending = max($this->calculateTotalIncome($host) - $this->getHostSettledIncome($host->id), 0);

        if ($pending <= 0) {
            $this->dispatch('info');
            return;
        }

        $this->resetErrorBag();
        $this->walletForm = 'wallet';
        $this->walletMode = 'settlement';
        $this->selectedHostId = (string) $userId;
        $this->walletAmount = (string) $pending;
    }

    public function saveWalletAction()
    {
        $this->validate([
            'selectedHostId' => 'required|exists:users,id',
            'walletAmount' => 'required|numeric|min:1',
        ]);

        $hostId = (int) $this->selectedHostId;
        $amount = (int) $this->walletAmount;

        if ($this->walletMode === 'settlement') {
            $host = $this->getHostsBaseQuery()
                ->with(['residences:id,user_id,amount', 'tours:id,user_id,amount'])
                ->findOrFail($hostId);

            $pending = max($this->calculateTotalIncome($host) - $this->getHostSettledIncome($hostId), 0);
            if ($amount > $pending) {
                $this->addError('walletAmount', 'مبلغ تسویه بیشتر از مانده قابل تسویه است.');
                return;
            }

            Config::updateOrCreate(
                ['title' => $this->getSettledIncomeConfigKey($hostId)],
                ['value' => (string) ($this->getHostSettledIncome($hostId) + $amount)]
            );
        }

        Config::updateOrCreate(
            ['title' => $this->getWalletConfigKey($hostId)],
            ['value' => (string) ($this->getHostWalletBalance($hostId) + $amount)]
        );

        $this->resetWalletForm();

        $this->dispatch('saved');
    }

    protected function resolveFullName(User $user): string
    {
        $fullName = trim(($user->name ?? '') . ' ' . ($user->family ?? ''));
        return $fullName !== '' ? $fullName : ('کاربر #' . $user->id);
    }

    protected function calculateTotalIncome(User $user): int
    {
        return (int) round($user->residences->sum('amount') + $user->tours->sum('amount'));
    }

    protected function getHostWalletBalance(int $userId): int
    {
        return (int) (Config::where('title', $this->getWalletConfigKey($userId))->value('value') ?? 0);
    }

    protected function getHostSettledIncome(int $userId): int
    {
        return (int) (Config::where('title', $this->getSettledIncomeConfigKey($userId))->value('value') ?? 0);
    }

    protected function getHostsBaseQuery()
    {
        return User::query()->hosts();
    }

    protected function getWalletConfigKey(int $userId): string
    {
        return 'host_wallet_balance_' . $userId;
    }

    protected function getSettledIncomeConfigKey(int $userId): string
    {
        return 'host_settled_income_' . $userId;
    }

    protected function resetWalletForm(): void
    {
        $this->walletForm = 'empty';
        $this->walletMode = 'charge';
        $this->selectedHostId = '';
        $this->walletAmount = '';
        $this->resetErrorBag();
    }


}
