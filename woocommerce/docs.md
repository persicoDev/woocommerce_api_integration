project-root/
├── app/
│   ├── Console/
│   ├── Exceptions/
│   ├── Http/
│   │   ├── Controllers/
│   │   ├── Middleware/
│   │   └── Requests/
│   ├── Domain/
│   │   ├── User/
│   │   │   ├── Models/
│   │   │   ├── Services/
│   │   │   ├── Repositories/
│   │   │   ├── Events/
│   │   │   ├── Listeners/
│   │   │   └── Policies/
│   │   ├── Product/
│   │   │   ├── Models/
│   │   │   ├── Services/
│   │   │   ├── Repositories/
│   │   │   └── ...
│   │   └── ... (other domains)
│   ├── Infrastructure/
│   │   ├── Persistence/
│   │   │   └── MongoDB/
│   │   ├── ExternalServices/
│   │   └── Logging/
│   ├── Providers/
│   └── Support/
├── config/
├── database/
│   └── migrations/
├── public/
├── resources/
├── routes/
│   ├── api.php
│   └── web.php
├── storage/
├── tests/
│   ├── Unit/
│   └── Feature/
└── vendor/