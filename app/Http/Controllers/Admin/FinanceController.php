<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Commission;
use App\Models\HostWalletTransaction;
use App\Models\User;
use App\Models\WithdrawRequest;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;

class FinanceController extends Controller
{
    public function hostWallet()
    {
        abort_unless(auth()->user()?->hasPermissionBySlug('finance-manage'), 403);

        $hosts = User::query()
            ->hosts()
            ->withCount(['residences', 'withdrawRequests as active_withdraw_requests_count' => fn (Builder $query) => $query->where('status', 'pending')])
            ->with('walletTransactions')
            ->orderBy('name')
            ->paginate(12);

        return view('admin.finance.host-wallet', [
            'hosts' => $hosts,
            'stats' => $this->walletStats(),
        ]);
    }

    public function commissions(Request $request)
    {
        abort_unless(auth()->user()?->hasPermissionBySlug('finance-manage'), 403);

        $query = Commission::query()
            ->with(['host', 'booking.bookable'])
            ->when($request->query('search'), fn (Builder $builder, string $search) => $builder->search($search))
            ->when($request->query('status'), fn (Builder $builder, string $status) => $builder->where('status', $status));

        $request->query('sort') === 'oldest' ? $query->oldest('id') : $query->latest('id');

        return view('admin.finance.commissions', [
            'commissions' => $query->paginate(12)->withQueryString(),
            'stats' => [
                'today' => Commission::query()->whereDate('created_at', today())->sum('amount'),
                'month' => Commission::query()->whereBetween('created_at', [now()->startOfMonth(), now()->endOfMonth()])->sum('amount'),
                'total' => Commission::query()->sum('amount'),
                'settled' => Commission::query()->where('status', 'settled')->sum('amount'),
            ],
        ]);
    }

    private function walletStats(): array
    {
        $credit = HostWalletTransaction::query()
            ->where('type', 'credit')
            ->where('status', '!=', 'cancelled')
            ->sum('amount');
        $debit = HostWalletTransaction::query()
            ->where('type', 'debit')
            ->where('status', '!=', 'cancelled')
            ->sum('amount');

        return [
            'total' => (int) $credit - (int) $debit,
            'blocked' => HostWalletTransaction::query()->where('type', 'credit')->where('status', 'blocked')->sum('amount'),
            'available' => HostWalletTransaction::query()->where('type', 'credit')->whereIn('status', ['posted', 'available'])->sum('amount')
                - HostWalletTransaction::query()->where('type', 'debit')->where('status', 'posted')->sum('amount'),
            'pendingWithdraws' => WithdrawRequest::query()->where('status', 'pending')->count(),
        ];
    }
}
