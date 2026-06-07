# Cài đặt thư viện

```sh
composer require spatie/laravel-google-calendar
```

- Publish config Spatie

```sh
php artisan vendor:publish --provider="Spatie\GoogleCalendar\GoogleCalendarServiceProvider"
```

# Chuẩn bị Service Account

Để  Service Account có thể truy cập Calendar API và đẩy sự kiện:

- `Enable API`

  - Đăng nhập vào Google Cloud Console: https://console.cloud.google.com/

  - Chọn dự án (Project) hoặc tạo mới.

  - Vào AP`Is & Services > Library`, tìm `Google Calendar API`, nhấn `Enable`.

- Tạo `Service Account`

  - Vào `APIs & Services > Credentials > Create credentials > Service account`.

  - Nhập tên (ví dụ laravel-calendar-sa), mô tả, nhấn Create and continue.

  - Phần `Grant this service account access to project`, `gán Role Editor` hoặc chỉ chọn `Service Account Token Creator và Viewer`.

  - Nhấn Done.

- Tạo `key`

  - Trong danh sách `Service Accounts`, click vào `laravel-calendar-sa` vừa tạo.

  - Sang tab `Keys`, nhấn `Add Key > Create new key`.

  - Chọn JSON, nhấn Create để  `tải file service-account.json về máy`.

  - Đặt file này vào `storage/app/google/service-account.json` của Laravel.
  
- `Cấu hình`
  
  - vô `calendar` >  `tên google` > `Integrate calendar` > copy `Calendar ID`
  - vô `calendar` >  `tên google` > `Shared with` > dán `Email` đã tạo trong `console.cloud.google.com` 

 - `.env`:
  
    ```env
    GOOGLE_CALENDAR_ID=your_calendar_id@group.calendar.google.com
    GOOGLE_APPLICATION_CREDENTIALS=/app/google/service-account.json
    ```

# chỉnh sửa file `google-calendar.php`
Mục đích thêm: `storage_path(env("GOOGLE_APPLICATION_CREDENTIALS", "app/google/service-account.json"))` vào thôi
```php
<?php

return [

    'default_auth_profile' => env('GOOGLE_CALENDAR_AUTH_PROFILE', 'service_account'),

    'auth_profiles' => [

        /*
         * Authenticate using a service account.
         */
        'service_account' => [
            /*
             * Path to the json file containing the credentials.
             */
            'credentials_json' => storage_path(env("GOOGLE_APPLICATION_CREDENTIALS", "app/google/service-account.json")),
        ],

        /*
         * Authenticate with actual google user account.
         */
        'oauth' => [
            /*
             * Path to the json file containing the oauth2 credentials.
             */
            'credentials_json' => storage_path(env("GOOGLE_APPLICATION_CREDENTIALS", "app/google/service-account.json")),

            /*
             * Path to the json file containing the oauth2 token.
             */
            'token_json' => storage_path(env("GOOGLE_APPLICATION_CREDENTIALS", "app/google/service-account.json")),
        ],
    ],

    /*
     *  The id of the Google Calendar that will be used by default.
     */
    'calendar_id' => env('GOOGLE_CALENDAR_ID'),

     /*
     *  The email address of the user account to impersonate.
     */
    'user_to_impersonate' => env('GOOGLE_CALENDAR_IMPERSONATE'),
];

```

# Dùng lệnh trong code

### Tạo thông tin

```php
use Spatie\GoogleCalendar\Event;

$event = Event::create([
    'name'          => 'Cuộc họp nhóm dev',
    'startDateTime' => now()->addDays(1),
    'endDateTime'   => now()->addDays(1)->addHour(),
]);

// Lấy ID Google Event để lưu vào database
$googleEventId = $event->id;
```

**Lưu ý**

- **`Recurrence (sự kiện lặp lại)`**

    ```php
    Event::create([
    'name' => 'Weekly Sync',
    'startDateTime' => now()->startOfWeek()->setTime(9,0),
    'endDateTime' => now()->startOfWeek()->setTime(10,0),
    'recurrence' => ['RRULE:FREQ=WEEKLY;COUNT=10'],
    ]);
    ```
- **`Gửi kèm thư mời`**

    ```php
    Event::create([
    ...,
    'attendees' => [
        ['email' => 'user1@example.com'],
        ['email' => 'user2@example.com'],
    ],
    'sendUpdates' => 'all', // gửi mail mời attendee
    ]);
    ```
- **`Nhắc nhở và thêm màu`**

    ```php
    Event::create([
    ...,
        'reminders'     => [
            'useDefault' => false,
            'overrides' => [
            ['method' => 'email', 'minutes' => 60],
            ['method' => 'popup', 'minutes' => 10],
            ],
        ],
        'colorId'       => 5, // chọn màu từ 0–11
    ]);
    ```

### Để  xóa thông tin

```php
use Spatie\GoogleCalendar\Event;

// Tìm event theo ID và xóa
$event = Event::find($googleEventId);
if ($event) {
    $event->delete();
}
```

### Chỉnh sửa thông tin

```php
use Spatie\GoogleCalendar\Event;
use Carbon\Carbon;

// Lấy event cần sửa
$event = Event::find($googleEventId);

if ($event) {
    // Gán lại các thuộc tính
    $event->name          = 'Họp dev – Thảo luận API';
    $event->startDateTime = Carbon::now()->addDays(2)->setTime(14, 0, 0);
    $event->endDateTime   = Carbon::now()->addDays(2)->setTime(15, 30, 0);
    $event->description   = 'Nội dung: Review thiết kế API mới';

    // Gửi request cập nhật
    $event->save();
}
```

# Chia sẻ tự động qua API (ACL rule)

Mục đích để  mọi người trong cty đều có thể  vô xem, bằng cách ghi đè


### Tạo Service Account và cấp quyền Domain-wide Delegation

Tích vào Enable Google Workspace Domain-wide Delegation, lưu lại, và note Client ID.

### Tạo file `app/Services/GoogleCalendarService.php`:


```php
<?php
namespace App\Services;

use Google_Client;
use Google_Service_Calendar;
use Google_Service_Calendar_AclRule;
use Google_Service_Calendar_AclRuleScope;

class GoogleCalendarService
{
    protected Google_Service_Calendar $service;
    protected string $calendarId;

    public function __construct(Google_Client $client)
    {
        // Giả sử  đã bind Google_Client trong service provider
        $this->service    = new Google_Service_Calendar($client);
        $this->calendarId = config('google-calendar.calendar_id', 'primary');
    }

    /**
     * Share calendar cho toàn domain với quyền reader
     */
    public function shareToDomain(string $domain, string $role = 'reader'): Google_Service_Calendar_AclRule
    {
        $rule = new Google_Service_Calendar_AclRule([
            'scope' => [
                'type'  => 'domain',
                'value' => $domain,
            ],
            'role'  => $role,
        ]);

        return $this->service->acl->insert($this->calendarId, $rule);
    }

    /**
     * Xóa một ACL rule theo ruleId
     */
    public function revokeRule(string $ruleId): void
    {
        $this->service->acl->delete($this->calendarId, $ruleId);
    }

    // ...  có thể thêm methods khác như shareToUser(), listRules(), v.v.
}
```

### Bind Google_Client (ví dụ trong AppServiceProvider hoặc một provider riêng):


```php
use Google_Client;

public function register()
{
    $this->app->singleton(Google_Client::class, function () {
        $client = new Google_Client();
        $client->setAuthConfig(storage_path('app/google/service-account-credentials.json'));
        $client->setSubject('admin@congty.com');
        $client->addScope(Google_Service_Calendar::CALENDAR);
        return $client;
    });
}
```

### Sử dụng ở Controller / Job / Command:

```php
namespace App\Http\Controllers;

use App\Services\GoogleCalendarService;

class CalendarController extends Controller
{
    protected GoogleCalendarService $calendarService;

    public function __construct(GoogleCalendarService $calendarService)
    {
        $this->calendarService = $calendarService;
    }

    public function shareCompanyCalendar()
    {
        $domain = 'congty.com';
        $rule   = $this->calendarService->shareToDomain($domain);

        return response()->json([
            'message' => "Đã chia sẻ cho domain {$domain}",
            'ruleId'  => $rule->getId(),
        ]);
    }
}   
```