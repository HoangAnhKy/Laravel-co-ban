B1: Cài đặt môi trường

```sh
composer require league/oauth2-google
```

B2: Khởi tạo 2 Controller

-   `OAuthController` dùng để lấy `token` và khởi tạo liên kết giữa Google API tới SMTP
-   `MailController` dùng để gửi mail

B3: Vào file `env` khai báo các thông tin sau

-   MAIL_USERNAME
-   GMAIL_API_CLIENT_ID
-   GMAIL_API_CLIENT_SECRET

B4: Thêm một số route hỗ trợ

-   Để có thể hoạt động chúng ta phải cần `token` thông qua tài khoản chúng ta cấp thông qua route `get-token` với name là `generate.token`
-   Sau khi có được `token` sẽ được truyền vào ứng dụng web thông qua route `get-token` với name là `token.success`

```php
<?php

use App\Http\Controllers\MailController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\OAuthController;

//...

Route::prefix('/')->group(function() {
    Route::post('/get-token', [OAuthController::class, 'doGenerateToken'])->name('generate.token');
    Route::get('/get-token', [OAuthController::class, 'doSuccessToken'])->name('token.success');
    Route::post('/send', [MailController::class, 'doSendEmail'])->name('send.email');
});

//...
```

B5: Thao tác với Controller

-   Thao tác với `OAuthController`
    -   `__construct` chứa các thông tin khởi tạo cần thiết để tái sử dụng cho tiện
    -   `doGenerateToken` dùng lại dữ liệu được khởi tạo bên trên \_\_construct để lấy token và lưu token vào `doSuccessToken`

```php
<?php

namespace App\Http\Controllers;

use Exception;
use Illuminate\Http\Request;
use League\OAuth2\Client\Provider\Exception\IdentityProviderException;
use League\OAuth2\Client\Provider\Google;

class OAuthController extends Controller
{
    private $client_id;
    private $client_secret;
    private $redirect_uri;

    private $provider;
    private $google_options;

    /**
     * Default Constructor
     */
    public function __construct()
    {
        $this->client_id = env('GMAIL_API_CLIENT_ID');
        $this->client_secret = env('GMAIL_API_CLIENT_SECRET');
        $this->redirect_uri = route('token.success');
        $this->google_options = [
            'scope' => [
                'https://mail.google.com/'
            ]
        ];
        $params = [
            'clientId'      => $this->client_id,
            'clientSecret'  => $this->client_secret,
            'redirectUri'   => $this->redirect_uri,
            'accessType'    => 'offline'
        ];

        // Create Google Provider
        $this->provider = new Google($params);
    }

    /**
     * Generate url to retreive token
     */
    public function doGenerateToken()
    {
        $redirect_uri = $this->provider->getAuthorizationUrl($this->google_options);
        return redirect($redirect_uri);
    }

    /**
     * Retreive Token
     */
    public function doSuccessToken(Request $request)
    {
        $code = $request->get('code');

        try {
            // Generate Token From Code
            $tokenObj = $this->provider->getAccessToken(
                'authorization_code',
                [
                    'code' => $code
                ]
                );
                $token = $tokenObj->getToken();
                $refresh_token = $tokenObj->getRefreshToken();
                if( $refresh_token != null && !empty($refresh_token) ) {
                    return redirect()->back()->with('token', $refresh_token);
                } elseif ( $token != null && !empty($token) ) {
                    return redirect()->back()->with('token', $token);
                } else {
                    return redirect()->back()->with('error', 'Unable to retreive token.');
                }
        } catch(IdentityProviderException $e) {
            return redirect()->back()->with('error', 'Exception: ' . $e->getMessage());
        } catch(Exception $e) {
            return redirect()->back()->with('error', 'Exception: ' . $e->getMessage());
        }
    }
}

```

-   Thao tác với `MailController`

```php
<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use League\OAuth2\Client\Provider\Google;
use PHPMailer\PHPMailer\Exception;
use PHPMailer\PHPMailer\OAuth;
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\SMTP;

class MailController extends Controller
{

    private $email;
    private $name;
    private $client_id;
    private $client_secret;
    private $token;
    private $provider;

    /**
     * Default Constructor
     */
    public function __construct()
    {
        $this->email            = env('MAIL_USERNAME'); // ex. example@gmail.com
        $this->email_name       = 'SELECTED_USER_NAME';     // ex. Abidhusain
        $this->client_id        = env('GMAIL_API_CLIENT_ID');
        $this->client_secret    = env('GMAIL_API_CLIENT_SECRET');
        $this->provider         = new Google(
            [
                'clientId'      => $this->client_id,
                'clientSecret'  => $this->client_secret
            ]
        );

    }

    /**
     * Send Email via PHPMailer Library
     */
    public function doSendEmail(Request $request)
    {
        $this->token = $request->get('oauth_token');

        $mail = new PHPMailer(true);
        try {
            $mail->isSMTP();
            $mail->SMTPDebug = SMTP::DEBUG_OFF;
            $mail->Host = 'smtp.gmail.com';
            $mail->Port = 465;
            $mail->SMTPSecure = PHPMailer::ENCRYPTION_SMTPS;
            $mail->SMTPAuth = true;
            $mail->AuthType = 'XOAUTH2';
            $mail->setOAuth(
                new OAuth(
                    [
                        'provider'          => $this->provider,
                        'clientId'          => $this->client_id,
                        'clientSecret'      => $this->client_secret,
                        'refreshToken'      => $this->token,
                        'userName'          => $this->email
                    ]
                )
            );

            $mail->setFrom($this->email, $this->name);
            // $mail->addAddress('TO_EMAIL_ID', 'TO_USER_NAME');
            $mail->addAddress('hoanganhkyltt@gmail.com', 'TO_USER_NAME');
            $mail->Subject = 'Laravel PHPMailer OAuth2 Integration';
            $mail->CharSet = PHPMailer::CHARSET_UTF8;
            $body = 'Hello <b>Everyone</b>,<br><br>We successfully completed our PHPMailer Integration in Laravel Project with Gmail OAuth2.<br><br>Thank you,<br><b>Abidhusain Chidi</b>';
            $mail->msgHTML($body);
            $mail->AltBody = 'This is a plain text message body';
            if( $mail->send() ) {
                return redirect()->back()->with('success', 'Successfully send email!');
            } else {
                return redirect()->back()->with('error', 'Unable to send email.');
            }
        } catch(Exception $e) {
            return redirect()->back()->with('error', 'Exception: ' . $e->getMessage());
        }
    }
}

```

B6: Thử trên View

```php
<div class="mt-2 text-gray-600 dark:text-gray-400 text-sm">
    <h3>Generate Token</h3>
    <p>click vào button để lấy token</p>
</div>

<div class="mt-2 text-gray-600 dark:text-gray-400 text-sm flex justify-between">
    <form action="{{ route('generate.token') }}" method="post" class="mr-2">
        @csrf
        <button type="submit" class="cursor p-2 px-6 bg-gray-900 text-gray-600 font-semibold">Generate Token</button>
    </form>

	// Nếu có token thì sẽ hiện button senmail với đường dẫn qua Controller Mail

    @if ( session()->has('token') )
    <form action="{{ route('send.email') }}" method="post">
        @csrf
        <input type="hidden" name="oauth_token" value="{{ session()->get('token') }}">
        <button type="submit" class="cursor p-2 px-6 bg-gray-100 text-gray-700 font-semibold">Send Test Email</button>
    </form>
    @endif

</div>
// Thông báo lỗi hoặc đã có token
@if ( session()->has('error') || session()->has('token') )

<div class="mt-2 text-gray-600 dark:text-gray-400 text-sm">
    <h4 style="margin-bottom: 5px;">Token</h4>
    @if ( session()->has('error') )
         <p class="font-semibold">{{ session()->get('error') }}</p>
    @endif

    @if ( session()->has('token') )
        <p class="font-semibold" style="font-size: 10px; margin: 0px;">{{ session()->get('token') }}</p>
    @endif
</div>

@endif
// Gửi thành công sẽ báo về
@if ( session()->has('success') )
<div class="mt-2 text-gray-600 dark:text-gray-400 text-sm">
    <p class="font-semibold">{{ session()->get('success') }}</p>
</div>
@endif

```
