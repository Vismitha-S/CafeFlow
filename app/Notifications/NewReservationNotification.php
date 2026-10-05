<?php

namespace App\Notifications;

use App\Models\Reservation;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;

class NewReservationNotification extends Notification
{
    use Queueable;

    public function __construct(public Reservation $reservation)
    {
        $this->reservation->loadMissing(['user', 'cafe', 'cafeTable']);
    }

    /**
     * Get the notification's delivery channels.
     *
     * @return array<int, string>
     */
    public function via(object $notifiable): array
    {
        return ['database'];
    }

    /**
     * Get the array representation of the notification.
     *
     * @return array<string, mixed>
     */
    public function toArray(object $notifiable): array
    {
        $customerName = $this->reservation->user?->name ?? 'Guest Customer';
        $cafeName = $this->reservation->cafe?->name ?? 'Your Cafe';
        $tableName = $this->reservation->cafeTable?->name ?: ('Table '.$this->reservation->cafeTable?->table_number);

        return [
            'type' => 'new_reservation',
            'title' => 'New Reservation Received',
            'reservation_id' => $this->reservation->id,
            'customer_name' => $customerName,
            'customer_email' => $this->reservation->user?->email,
            'cafe_name' => $cafeName,
            'cafe_id' => $this->reservation->cafe_id,
            'reservation_date' => $this->reservation->reservation_date?->format('Y-m-d') ?? (string) $this->reservation->reservation_date,
            'start_time' => $this->reservation->start_time,
            'end_time' => $this->reservation->end_time,
            'guest_count' => $this->reservation->guest_count,
            'table_name' => $tableName,
            'table_id' => $this->reservation->cafe_table_id,
            'reservation_fee' => (float) $this->reservation->reservation_fee,
            'status' => $this->reservation->status,
            'message' => "{$customerName} has booked {$tableName} for {$this->reservation->guest_count} guests on {$this->reservation->reservation_date}.",
        ];
    }
}
