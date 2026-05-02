<?php

namespace App\Services\Admin;

use App\Models\Booking;
use App\Models\BookingRequest;
use App\Models\Commission;
use App\Models\HostWalletTransaction;
use App\Support\Admin\AdminSiteSettings;
use Illuminate\Support\Facades\DB;

class ReservationRequestService
{
    public function approve(BookingRequest $bookingRequest): Booking
    {
        return DB::transaction(function () use ($bookingRequest) {
            $bookingRequest->refresh();

            if ($bookingRequest->booking) {
                if (! in_array($bookingRequest->status, ['approved', 'paid', 'staying', 'ended', 'releasable', 'settled'], true)) {
                    $bookingRequest->update([
                        'status' => 'approved',
                        'host_approval_status' => $bookingRequest->host_approval_status === 'manual_approved' ? 'manual_approved' : 'approved',
                    ]);
                }

                return $bookingRequest->booking;
            }

            $booking = Booking::query()->create([
                'booking_request_id' => $bookingRequest->id,
                'customer_id' => $bookingRequest->customer_id,
                'host_id' => $bookingRequest->host_id,
                'bookable_type' => $bookingRequest->bookable_type,
                'bookable_id' => $bookingRequest->bookable_id,
                'booking_number' => $this->makeBookingNumber(),
                'starts_at' => $bookingRequest->starts_at,
                'ends_at' => $bookingRequest->ends_at,
                'guests_count' => $bookingRequest->guests_count ?: 1,
                'subtotal' => $bookingRequest->total_amount ?: 0,
                'discount_amount' => 0,
                'commission_amount' => $bookingRequest->commission_amount ?: 0,
                'host_share_amount' => $bookingRequest->host_share_amount ?: $bookingRequest->total_amount ?: 0,
                'total_amount' => $bookingRequest->total_amount ?: 0,
                'status' => 'awaiting_payment',
                'payment_status' => 'unpaid',
                'settlement_status' => 'pending',
                'notes' => $bookingRequest->notes,
            ]);

            $bookingRequest->update([
                'status' => 'approved',
                'host_approval_status' => $bookingRequest->host_approval_status === 'manual_approved' ? 'manual_approved' : 'approved',
                'payment_status' => $bookingRequest->payment_status ?: 'unpaid',
            ]);

            return $booking;
        });
    }

    public function recordPayment(BookingRequest $bookingRequest): Booking
    {
        return DB::transaction(function () use ($bookingRequest) {
            $booking = $this->approve($bookingRequest);
            $bookingRequest->refresh();

            $totalAmount = (int) $bookingRequest->total_amount;
            $commissionRate = $this->commissionRate();
            $commissionAmount = (int) round($totalAmount * ($commissionRate / 100));
            $hostShareAmount = max(0, $totalAmount - $commissionAmount);

            $bookingRequest->update([
                'status' => 'paid',
                'payment_status' => 'paid',
                'stay_status' => $bookingRequest->stay_status ?: 'not_started',
                'settlement_status' => 'blocked',
                'commission_amount' => $commissionAmount,
                'host_share_amount' => $hostShareAmount,
            ]);

            $booking->update([
                'status' => 'paid',
                'payment_status' => 'paid',
                'paid_at' => $booking->paid_at ?: now(),
                'commission_amount' => $commissionAmount,
                'host_share_amount' => $hostShareAmount,
                'total_amount' => $totalAmount,
                'settlement_status' => 'blocked',
            ]);

            Commission::query()->updateOrCreate(
                ['booking_id' => $booking->id],
                [
                    'host_id' => $bookingRequest->host_id,
                    'rate' => $commissionRate,
                    'amount' => $commissionAmount,
                    'host_share_amount' => $hostShareAmount,
                    'status' => 'pending',
                ]
            );

            if ($bookingRequest->host_id && $hostShareAmount > 0) {
                HostWalletTransaction::query()->updateOrCreate(
                    [
                        'booking_id' => $booking->id,
                        'type' => 'credit',
                    ],
                    [
                        'host_id' => $bookingRequest->host_id,
                        'amount' => $hostShareAmount,
                        'balance_after' => $this->hostBalanceAfter($bookingRequest->host_id, $hostShareAmount),
                        'status' => 'blocked',
                        'reference_number' => $booking->booking_number,
                        'available_at' => $this->availableAt($bookingRequest),
                        'description' => 'مبلغ رزرو تا پایان اقامت مسدود شده است.',
                        'meta' => [
                            'commission_amount' => $commissionAmount,
                            'total_amount' => $totalAmount,
                        ],
                    ]
                );
            }

            return $booking->refresh();
        });
    }

    public function releaseAmount(BookingRequest $bookingRequest): void
    {
        DB::transaction(function () use ($bookingRequest) {
            $booking = $this->approve($bookingRequest);

            $bookingRequest->update([
                'status' => 'releasable',
                'stay_status' => 'ended',
                'settlement_status' => 'releasable',
            ]);

            $booking->update([
                'status' => 'completed',
                'settlement_status' => 'releasable',
                'released_at' => $booking->released_at ?: now(),
            ]);

            HostWalletTransaction::query()
                ->where('booking_id', $booking->id)
                ->where('type', 'credit')
                ->update([
                    'status' => 'posted',
                    'available_at' => now(),
                    'description' => 'مبلغ رزرو پس از پایان اقامت قابل برداشت شد.',
                ]);
        });
    }

    public function settleWithHost(BookingRequest $bookingRequest): void
    {
        DB::transaction(function () use ($bookingRequest) {
            $booking = $this->approve($bookingRequest);

            $bookingRequest->update([
                'status' => 'settled',
                'settlement_status' => 'settled',
            ]);

            $booking->update([
                'status' => 'settled',
                'settlement_status' => 'settled',
                'settled_at' => $booking->settled_at ?: now(),
            ]);

            HostWalletTransaction::query()
                ->where('booking_id', $booking->id)
                ->where('type', 'credit')
                ->update(['status' => 'settled']);

            Commission::query()
                ->where('booking_id', $booking->id)
                ->update([
                    'status' => 'settled',
                    'settled_at' => now(),
                ]);
        });
    }

    protected function makeBookingNumber(): string
    {
        do {
            $number = 'INJ-'.now()->format('ymd').'-'.random_int(1000, 9999);
        } while (Booking::query()->where('booking_number', $number)->exists());

        return $number;
    }

    protected function commissionRate(): float
    {
        if (AdminSiteSettings::revenueMode() === 'free') {
            return 0;
        }

        return (float) getConfigs('payment_commission_percent', 10);
    }

    protected function hostBalanceAfter(int $hostId, int $incomingAmount): int
    {
        $credit = HostWalletTransaction::query()
            ->where('host_id', $hostId)
            ->where('type', 'credit')
            ->where('status', '!=', 'cancelled')
            ->sum('amount');

        $debit = HostWalletTransaction::query()
            ->where('host_id', $hostId)
            ->where('type', 'debit')
            ->where('status', '!=', 'cancelled')
            ->sum('amount');

        return (int) $credit - (int) $debit + $incomingAmount;
    }

    protected function availableAt(BookingRequest $bookingRequest): ?\Illuminate\Support\Carbon
    {
        if (! $bookingRequest->ends_at) {
            return null;
        }

        $hours = (int) getConfigs('payment_release_hours', 48);

        return $bookingRequest->ends_at->copy()->addHours($hours);
    }
}
