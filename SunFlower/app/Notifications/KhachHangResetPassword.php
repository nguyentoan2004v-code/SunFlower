<?php

namespace App\Notifications;

use Illuminate\Notifications\Notification;
use Illuminate\Notifications\Messages\MailMessage;

class KhachHangResetPassword extends Notification
{
    /**
     * Token đặt lại mật khẩu do Laravel tạo (đã được hash trong DB).
     */
    public string $token;

    public function __construct(string $token)
    {
        $this->token = $token;
    }

    public function via(object $notifiable): array
    {
        return ['mail'];
    }

    public function toMail(object $notifiable): MailMessage
    {
        // Tạo URL reset với token + email — cả 2 đều cần để verify
        $resetUrl = route('password.reset.khachhang', [
            'token' => $this->token,
            'email' => $notifiable->getEmailForPasswordReset(),
        ]);

        return (new MailMessage)
            ->subject('🌻 SunFlower — Yêu cầu đặt lại mật khẩu')
            ->greeting('Xin chào ' . ($notifiable->hoten ?? 'Quý khách') . '!')
            ->line('Chúng tôi nhận được yêu cầu đặt lại mật khẩu cho tài khoản của bạn tại SunFlower.')
            ->action('Đặt lại mật khẩu ngay', $resetUrl)
            ->line('Link này sẽ hết hạn sau **60 phút**.')
            ->line('Nếu bạn không thực hiện yêu cầu này, vui lòng bỏ qua email và mật khẩu của bạn sẽ không bị thay đổi.')
            ->salutation('Trân trọng, Đội ngũ SunFlower 🌻');
    }
}
