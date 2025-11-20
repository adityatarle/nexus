<?php

namespace App\Notifications;

use App\Models\AgricultureOrder;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class OrderStatusUpdated extends Notification implements ShouldQueue
{
    use Queueable;

    /**
     * The order instance.
     */
    protected $order;

    /**
     * The old status.
     */
    protected $oldStatus;

    /**
     * Create a new notification instance.
     */
    public function __construct(AgricultureOrder $order, string $oldStatus)
    {
        $this->order = $order;
        $this->oldStatus = $oldStatus;
    }

    /**
     * Get the notification's delivery channels.
     *
     * @return array<int, string>
     */
    public function via(object $notifiable): array
    {
        return ['mail', 'database'];
    }

    /**
     * Get the mail representation of the notification.
     */
    public function toMail(object $notifiable): MailMessage
    {
        $orderUrl = $this->getOrderUrl($notifiable);
        $statusMessage = $this->getStatusMessage();
        
        $mail = (new MailMessage)
            ->subject('Order Update - ' . $this->order->order_number)
            ->greeting('Hello ' . $notifiable->name . '!')
            ->line('Your order status has been updated.')
            ->line('')
            ->line('**Order Details:**')
            ->line('Order Number: **' . $this->order->order_number . '**')
            ->line('Status: **' . ucfirst($this->order->order_status) . '**');

        // Add status-specific message
        if ($statusMessage) {
            $mail->line('')
                ->line($statusMessage);
        }

        // Add tracking information if shipped
        if ($this->order->order_status === 'shipped' && !empty($this->order->tracking_number)) {
            $mail->line('')
                ->line('Tracking Number: **' . $this->order->tracking_number . '**');
        }

        $mail->line('')
            ->action('Track Your Order', $orderUrl)
            ->line('')
            ->line('Thank you for shopping with us!')
            ->salutation('Best regards, ' . config('app.name'));

        return $mail;
    }

    /**
     * Get the array representation of the notification for database storage.
     *
     * @return array<string, mixed>
     */
    public function toArray(object $notifiable): array
    {
        return [
            'type' => 'order_status_updated',
            'order_id' => $this->order->id,
            'order_number' => $this->order->order_number,
            'old_status' => $this->oldStatus,
            'new_status' => $this->order->order_status,
            'message' => 'Your order ' . $this->order->order_number . ' status has been updated to ' . ucfirst($this->order->order_status) . '.',
            'icon' => $this->getStatusIcon(),
            'url' => $this->getOrderUrl($notifiable),
        ];
    }

    /**
     * Get status-specific message
     */
    protected function getStatusMessage(): ?string
    {
        return match($this->order->order_status) {
            'confirmed' => 'Great news! Your order has been confirmed and is being prepared for shipment.',
            'shipped' => 'Your order has been shipped and is on its way to you!',
            'delivered' => 'Your order has been delivered successfully. We hope you enjoy your purchase!',
            'cancelled' => 'Your order has been cancelled. If this was unexpected, please contact our support team.',
            default => null,
        };
    }

    /**
     * Get icon based on status
     */
    protected function getStatusIcon(): string
    {
        return match($this->order->order_status) {
            'confirmed' => 'check-circle',
            'shipped' => 'truck',
            'delivered' => 'box-check',
            'cancelled' => 'x-circle',
            default => 'info-circle',
        };
    }

    /**
     * Get the order URL based on user role
     */
    protected function getOrderUrl($notifiable): string
    {
        if ($notifiable->role === 'dealer' && $notifiable->is_dealer_approved) {
            return route('dealer.orders.show', $this->order->order_number);
        }
        
        return route('customer.orders.show', $this->order->order_number);
    }
}

















