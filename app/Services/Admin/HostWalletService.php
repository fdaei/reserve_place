<?php

namespace App\Services\Admin;

use App\Models\HostWalletTransaction;
use App\Models\User;
use App\Models\WithdrawRequest;

class HostWalletService
{
    public function summaryForHost(User $host): array
    {
        $transactions = $host->relationLoaded('walletTransactions')
            ? $host->walletTransactions
            : $host->walletTransactions()->get();

        $pendingWithdrawals = $host->relationLoaded('withdrawRequests')
            ? $host->withdrawRequests->where('status', 'pending')->sum('amount')
            : $host->withdrawRequests()->where('status', 'pending')->sum('amount');

        $credits = $transactions->where('type', 'credit')->where('status', '!=', 'cancelled')->sum('amount');
        $debits = $transactions->where('type', 'debit')->where('status', '!=', 'cancelled')->sum('amount');
        $postedCredits = $transactions->where('type', 'credit')->whereIn('status', ['posted', 'available'])->sum('amount');
        $postedDebits = $transactions->where('type', 'debit')->where('status', 'posted')->sum('amount');

        return [
            'total' => (int) $credits - (int) $debits,
            'blocked' => (int) $transactions->where('type', 'credit')->where('status', 'blocked')->sum('amount'),
            'withdrawable' => max(0, (int) $postedCredits - (int) $postedDebits - (int) $pendingWithdrawals),
            'pending_withdrawals' => (int) $pendingWithdrawals,
        ];
    }

    public function systemStats(): array
    {
        $credits = HostWalletTransaction::query()->where('type', 'credit')->where('status', '!=', 'cancelled')->sum('amount');
        $debits = HostWalletTransaction::query()->where('type', 'debit')->where('status', '!=', 'cancelled')->sum('amount');
        $postedCredits = HostWalletTransaction::query()->where('type', 'credit')->whereIn('status', ['posted', 'available'])->sum('amount');
        $postedDebits = HostWalletTransaction::query()->where('type', 'debit')->where('status', 'posted')->sum('amount');
        $pendingWithdrawals = WithdrawRequest::query()->where('status', 'pending')->sum('amount');

        return [
            'total' => (int) $credits - (int) $debits,
            'blocked' => (int) HostWalletTransaction::query()->where('type', 'credit')->where('status', 'blocked')->sum('amount'),
            'withdrawable' => max(0, (int) $postedCredits - (int) $postedDebits - (int) $pendingWithdrawals),
            'pendingWithdrawals' => (int) $pendingWithdrawals,
        ];
    }

    public function currentBalance(int $hostId): int
    {
        $credits = HostWalletTransaction::query()
            ->where('host_id', $hostId)
            ->where('type', 'credit')
            ->where('status', '!=', 'cancelled')
            ->sum('amount');

        $debits = HostWalletTransaction::query()
            ->where('host_id', $hostId)
            ->where('type', 'debit')
            ->where('status', '!=', 'cancelled')
            ->sum('amount');

        return (int) $credits - (int) $debits;
    }

    public function createManualCharge(int $hostId, int $amount, ?string $description = null): HostWalletTransaction
    {
        return HostWalletTransaction::query()->create([
            'host_id' => $hostId,
            'type' => 'credit',
            'amount' => $amount,
            'balance_after' => $this->currentBalance($hostId) + $amount,
            'status' => 'posted',
            'reference_number' => 'MAN-'.now()->format('ymdHis'),
            'description' => filled($description) ? trim((string) $description) : 'شارژ دستی کیف پول میزبان',
            'available_at' => now(),
        ]);
    }

    public function normalizeAmount(string $value): int
    {
        $value = convertPersianToEnglishNumbers($value);

        return (int) preg_replace('/[^\d]+/', '', $value);
    }
}
