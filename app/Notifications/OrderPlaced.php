<?php

namespace App\Notifications;

use App\Models\AgricultureOrder;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class OrderPlaced extends Notification implements ShouldQueue
{
    use Queueable;

    /**
     * The order instance.
     */
    protected $order;

    /**
     * Create a new notification instance.
     */
    public function __construct(AgricultureOrder $order)
    {
        $this->order = $order;
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
        
        return (new MailMessage)
            ->subject('Order Confirmation - ' . $this->order->order_number)
            ->greeting('Hello ' . $notifiable->name . '!')
            ->line('Thank you for your order! We have received your order and it is being processed.')
            ->line('')
            ->line('**Order Details:**')
            ->line('Order Number: **' . $this->order->order_number . '**')
            ->line('Order Date: ' . $this->order->created_at->format('F j, Y g:i A'))
            ->line('Total Amount: **₹' . number_format($this->order->total_amount, 2) . '**')
            ->line('Payment Method: ' . ucfirst($this->order->payment_method))
            ->line('Payment Status: ' . ucfirst($this->order->payment_status))
            ->line('')
            ->action('View Order Details', $orderUrl)
            ->line('')
            ->line('We will notify you once your order is shipped.')
            ->line('')
            ->line('If you have any questions, feel free to contact our support team.')
            ->line('')
            ->salutation('Best regards, ' . config('app.name'));
    }

    /**
     * Get the array representation of the notification for database storage.
     *
     * @return array<string, mixed>
     */
    public function toArray(object $notifiable): array
    {
        return [
            'type' => 'order_placed',
            'order_id' => $this->order->id,
            'order_number' => $this->order->order_number,
            'total_amount' => $this->order->total_amount,
            'message' => 'Your order ' . $this->order->order_number . ' has been placed successfully.',
            'icon' => 'shopping-cart',
            'url' => $this->getOrderUrl($notifiable),
        ];
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

















