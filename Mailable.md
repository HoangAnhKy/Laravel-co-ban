1) Khởi tạo mail

```sh
php artisan make:mail {name}
# php artisan make:mail sendMailAddUser
```
2) Lớp Mailable mới sẽ được tạo tại `app/Mail/`

ví dụ: 
```php
<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class sendMailAddUser extends Mailable
{
    use Queueable, SerializesModels;

    /**
     * Create a new message instance.
     */
    public $user;

    public function __construct($user)
    {
        $this->user = $user;  // Truyền thông tin người dùng vào email
    }

    /**
     * Get the message envelope.
     */
    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'Send Mail Add User',
        );
    }

    /**
     * Get the message content definition.
     */
    public function content(): Content
    {
        return new Content(
            view: 'mail.passwordUser',
            with: ['user' => $this->user]
        );
    }

    /**
     * Get the attachments for the message.
     *
     * @return array<int, \Illuminate\Mail\Mailables\Attachment>
     */
    public function attachments(): array
    {
        return [];
    }
}
```

- `envelope()` để đặt tiêu đề và thông tin email.
    
    Gồm các object: 
    - `from`: 'noreply@domain.com' (Địa chỉ email của người gửi.)
    - `to`: ['recipient@domain.com'] (Danh sách người nhận chính của email)
    - `cc`: ['cc@domain.com'] (Danh sách người nhận)
    - `bcc`: ['bcc@domain.com'] (Danh sách người nhận, không thấy người nhận)
    - `replyTo`: 'support@domain.com' (Địa chỉ email dùng cho phản hồi thay vì `from`)
    - `subject`: 'Welcome to Our Platform!' (Tiêu đề email)
    - `tags`: ['welcome', 'user-registration'] (Mảng các tag để phân loại email)
    - `metadata`: ['user_id' => 12345] (Mảng metadata cho email)

- `content()` để xác định nội dung view của email.
    
    Gồm các object: `view`,`html`, `text`, `markdown`, `with` dùng dể truyền dữ liệu và `htmlString` truyền một chuỗi HTML trực tiếp vào email thay vì dùng `file view`

- `attachments()` để đính kèm file.
```php
public function attachments(): array
{
    return [
        // Đính kèm file từ đường dẫn
        Attachment::fromPath(storage_path('app/public/report.pdf'))
            ->as('Báo cáo tháng.pdf')
            ->withMime('application/pdf'),

        // Đính kèm từ storage disk
        Attachment::fromStorageDisk('s3', 'reports/annual_report.pdf')
            ->as('Annual_Report.pdf')
            ->withMime('application/pdf'),

        // Đính kèm từ dữ liệu chuỗi
        Attachment::withData('Đây là nội dung của file', 'info.txt', 'text/plain'),

        // Đính kèm từ Closure
        Attachment::fromData(fn () => 'Nội dung file được tạo động', 'generated_report.txt', 'text/plain'),
    ];
}

```

3) Gửi Email

```php
use App\Mail\WelcomeMail;
use Illuminate\Support\Facades\Mail;

public function sendWelcomeEmail($user)
{
    Mail::to($user->email)->send(new WelcomeMail($user));
    // hoặc Mail::send(new WelcomeMail($user)); nếu có setup cho envelope
}
```
