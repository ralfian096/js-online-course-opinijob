@php
$baseUrl = rtrim((string) config('app.url'), '/');
$apiBaseUrl = $baseUrl.'/api';

$sections = [
    [
        'id' => 'overview',
        'title' => 'Overview',
        'description' => 'Ringkasan penggunaan API, konvensi request, dan alur autentikasi.',
        'items' => [
            ['id' => 'overview-general', 'label' => 'Informasi Umum'],
            ['id' => 'overview-auth-flow', 'label' => 'Alur Auth'],
            ['id' => 'overview-models', 'label' => 'Model Data'],
            ['id' => 'overview-errors', 'label' => 'Format Error'],
        ],
    ],
    [
        'id' => 'account',
        'title' => 'Account',
        'description' => 'Endpoint untuk register user dan login agar mendapatkan bearer token.',
        'items' => [
            ['id' => 'account-register', 'label' => 'POST /account/register'],
            ['id' => 'account-login', 'label' => 'POST /account/login'],
        ],
    ],
    [
        'id' => 'price-categories',
        'title' => 'Pricing Categories',
        'description' => 'Kelola daftar kategori pricing. Semua endpoint memerlukan bearer token.',
        'items' => [
            ['id' => 'price-categories-index', 'label' => 'GET /manage/prices/categories'],
            ['id' => 'price-categories-store', 'label' => 'POST /manage/prices/categories'],
            ['id' => 'price-categories-show', 'label' => 'GET /manage/prices/categories/{category}'],
            ['id' => 'price-categories-update', 'label' => 'PUT/PATCH /manage/prices/categories/{category}'],
            ['id' => 'price-categories-delete', 'label' => 'DELETE /manage/prices/categories/{category}'],
        ],
    ],
    [
        'id' => 'prices',
        'title' => 'Pricing',
        'description' => 'Kelola daftar pricing berdasarkan kategori. Semua endpoint memerlukan bearer token.',
        'items' => [
            ['id' => 'prices-index', 'label' => 'GET /manage/prices'],
            ['id' => 'prices-store', 'label' => 'POST /manage/prices'],
            ['id' => 'prices-show', 'label' => 'GET /manage/prices/{price}'],
            ['id' => 'prices-update', 'label' => 'PUT/PATCH /manage/prices/{price}'],
            ['id' => 'prices-delete', 'label' => 'DELETE /manage/prices/{price}'],
        ],
    ],
];

$endpoints = [
    [
        'id' => 'account-register',
        'section' => 'Account',
        'title' => 'Register User',
        'method' => 'POST',
        'path' => '/api/account/register',
        'auth' => 'Tidak',
        'description' => 'Mendaftarkan user baru dan langsung mengembalikan bearer token.',
        'headers' => [
            ['name' => 'Accept', 'required' => 'Ya', 'value' => 'application/json', 'description' => 'Meminta response JSON.'],
            ['name' => 'Content-Type', 'required' => 'Ya', 'value' => 'application/json', 'description' => 'Format body request.'],
        ],
        'fields' => [
            ['name' => 'name', 'location' => 'Body', 'type' => 'string', 'required' => 'Ya', 'rules' => 'max:255', 'example' => 'Budi Santoso'],
            ['name' => 'email', 'location' => 'Body', 'type' => 'string', 'required' => 'Ya', 'rules' => 'email, unique', 'example' => 'budi@example.com'],
            ['name' => 'password', 'location' => 'Body', 'type' => 'string', 'required' => 'Ya', 'rules' => 'min:8', 'example' => 'password123'],
        ],
        'payload' => <<<'JSON'
{
  "name": "Budi Santoso",
  "email": "budi@example.com",
  "password": "password123"
}
JSON,
        'examples' => [
            'curl' => <<<'TEXT'
curl -X POST {{ $apiBaseUrl }}/account/register \
  -H "Accept: application/json" \
  -H "Content-Type: application/json" \
  -d "{\"name\":\"Budi Santoso\",\"email\":\"budi@example.com\",\"password\":\"password123\"}"
TEXT,
            'php' => <<<'TEXT'
$payload = json_encode([
  'name' => 'Budi Santoso',
  'email' => 'budi@example.com',
  'password' => 'password123',
]);

$ch = curl_init('{{ $apiBaseUrl }}/account/register');
curl_setopt_array($ch, [
  CURLOPT_RETURNTRANSFER => true,
  CURLOPT_CUSTOMREQUEST => 'POST',
  CURLOPT_POSTFIELDS => $payload,
  CURLOPT_HTTPHEADER => [
    'Accept: application/json',
    'Content-Type: application/json',
  ],
]);
echo curl_exec($ch);
curl_close($ch);
TEXT,
            'js' => <<<'TEXT'
fetch('{{ $apiBaseUrl }}/account/register', {
  method: 'POST',
  headers: {
    'Accept': 'application/json',
    'Content-Type': 'application/json'
  },
  body: JSON.stringify({
    name: 'Budi Santoso',
    email: 'budi@example.com',
    password: 'password123'
  })
})
  .then(async (res) => ({ status: res.status, body: await res.json() }))
  .then(console.log);
TEXT,
        ],
        'responses' => [
            ['label' => 'Success (201)', 'body' => <<<'JSON'
{
  "message": "User registered.",
  "data": {
    "user": {
      "id": 1,
      "name": "Budi Santoso",
      "email": "budi@example.com",
      "created_at": "2026-07-19T10:00:00.000000Z",
      "updated_at": "2026-07-19T10:00:00.000000Z"
    },
    "token": "plain-text-api-token"
  }
}
JSON],
            ['label' => 'Error (422)', 'body' => <<<'JSON'
{
  "message": "The email has already been taken.",
  "errors": {
    "email": [
      "The email has already been taken."
    ]
  }
}
JSON],
        ],
    ],
    [
        'id' => 'account-login',
        'section' => 'Account',
        'title' => 'Login User',
        'method' => 'POST',
        'path' => '/api/account/login',
        'auth' => 'Tidak',
        'description' => 'Login dengan email dan password, lalu mengembalikan bearer token baru.',
        'headers' => [
            ['name' => 'Accept', 'required' => 'Ya', 'value' => 'application/json', 'description' => 'Meminta response JSON.'],
            ['name' => 'Content-Type', 'required' => 'Ya', 'value' => 'application/json', 'description' => 'Format body request.'],
        ],
        'fields' => [
            ['name' => 'email', 'location' => 'Body', 'type' => 'string', 'required' => 'Ya', 'rules' => 'email', 'example' => 'budi@example.com'],
            ['name' => 'password', 'location' => 'Body', 'type' => 'string', 'required' => 'Ya', 'rules' => 'string', 'example' => 'password123'],
        ],
        'payload' => <<<'JSON'
{
  "email": "budi@example.com",
  "password": "password123"
}
JSON,
        'examples' => [
            'curl' => <<<'TEXT'
curl -X POST {{ $apiBaseUrl }}/account/login \
  -H "Accept: application/json" \
  -H "Content-Type: application/json" \
  -d "{\"email\":\"budi@example.com\",\"password\":\"password123\"}"
TEXT,
            'php' => <<<'TEXT'
$payload = json_encode([
  'email' => 'budi@example.com',
  'password' => 'password123',
]);

$ch = curl_init('{{ $apiBaseUrl }}/account/login');
curl_setopt_array($ch, [
  CURLOPT_RETURNTRANSFER => true,
  CURLOPT_CUSTOMREQUEST => 'POST',
  CURLOPT_POSTFIELDS => $payload,
  CURLOPT_HTTPHEADER => [
    'Accept: application/json',
    'Content-Type: application/json',
  ],
]);
echo curl_exec($ch);
curl_close($ch);
TEXT,
            'js' => <<<'TEXT'
fetch('{{ $apiBaseUrl }}/account/login', {
  method: 'POST',
  headers: {
    'Accept': 'application/json',
    'Content-Type': 'application/json'
  },
  body: JSON.stringify({
    email: 'budi@example.com',
    password: 'password123'
  })
})
  .then(async (res) => ({ status: res.status, body: await res.json() }))
  .then(console.log);
TEXT,
        ],
        'responses' => [
            ['label' => 'Success (200)', 'body' => <<<'JSON'
{
  "message": "Login successful.",
  "data": {
    "user": {
      "id": 1,
      "name": "Budi Santoso",
      "email": "budi@example.com",
      "created_at": "2026-07-19T10:00:00.000000Z",
      "updated_at": "2026-07-19T10:00:00.000000Z"
    },
    "token": "plain-text-api-token"
  }
}
JSON],
            ['label' => 'Error (422)', 'body' => <<<'JSON'
{
  "message": "Email atau password tidak valid.",
  "errors": {
    "email": [
      "Email atau password tidak valid."
    ]
  }
}
JSON],
        ],
    ],
    [
        'id' => 'price-categories-index',
        'section' => 'Pricing Categories',
        'title' => 'List Price Categories',
        'method' => 'GET',
        'path' => '/api/manage/prices/categories',
        'auth' => 'Bearer token',
        'description' => 'Mengambil seluruh kategori pricing beserta jumlah pricing di tiap kategori.',
        'headers' => [
            ['name' => 'Accept', 'required' => 'Ya', 'value' => 'application/json', 'description' => 'Meminta response JSON.'],
            ['name' => 'Authorization', 'required' => 'Ya', 'value' => 'Bearer <token>', 'description' => 'Token dari endpoint login atau register.'],
        ],
        'fields' => [],
        'payload' => null,
        'examples' => [
            'curl' => 'curl -H "Accept: application/json" -H "Authorization: Bearer YOUR_TOKEN" {{ $apiBaseUrl }}/manage/prices/categories',
            'php' => <<<'TEXT'
$ch = curl_init('{{ $apiBaseUrl }}/manage/prices/categories');
curl_setopt_array($ch, [
  CURLOPT_RETURNTRANSFER => true,
  CURLOPT_HTTPHEADER => [
    'Accept: application/json',
    'Authorization: Bearer YOUR_TOKEN',
  ],
]);
echo curl_exec($ch);
curl_close($ch);
TEXT,
            'js' => <<<'TEXT'
fetch('{{ $apiBaseUrl }}/manage/prices/categories', {
  headers: {
    'Accept': 'application/json',
    'Authorization': 'Bearer YOUR_TOKEN'
  }
})
  .then(async (res) => ({ status: res.status, body: await res.json() }))
  .then(console.log);
TEXT,
        ],
        'responses' => [
            ['label' => 'Success (200)', 'body' => <<<'JSON'
{
  "data": [
    {
      "id": 1,
      "name": "Private Class",
      "description": "Kategori untuk kelas privat",
      "prices_count": 2,
      "created_at": "2026-07-19T10:00:00.000000Z",
      "updated_at": "2026-07-19T10:00:00.000000Z"
    }
  ]
}
JSON],
            ['label' => 'Error (401)', 'body' => <<<'JSON'
{
  "message": "Unauthorized."
}
JSON],
        ],
    ],
    [
        'id' => 'price-categories-store',
        'section' => 'Pricing Categories',
        'title' => 'Create Price Category',
        'method' => 'POST',
        'path' => '/api/manage/prices/categories',
        'auth' => 'Bearer token',
        'description' => 'Membuat kategori pricing baru.',
        'headers' => [
            ['name' => 'Accept', 'required' => 'Ya', 'value' => 'application/json', 'description' => 'Meminta response JSON.'],
            ['name' => 'Content-Type', 'required' => 'Ya', 'value' => 'application/json', 'description' => 'Format body request.'],
            ['name' => 'Authorization', 'required' => 'Ya', 'value' => 'Bearer <token>', 'description' => 'Token akses API.'],
        ],
        'fields' => [
            ['name' => 'name', 'location' => 'Body', 'type' => 'string', 'required' => 'Ya', 'rules' => 'max:255', 'example' => 'Private Class'],
            ['name' => 'description', 'location' => 'Body', 'type' => 'string|null', 'required' => 'Tidak', 'rules' => 'nullable', 'example' => 'Kategori untuk kelas privat'],
        ],
        'payload' => <<<'JSON'
{
  "name": "Private Class",
  "description": "Kategori untuk kelas privat"
}
JSON,
        'examples' => [
            'curl' => <<<'TEXT'
curl -X POST {{ $apiBaseUrl }}/manage/prices/categories \
  -H "Accept: application/json" \
  -H "Content-Type: application/json" \
  -H "Authorization: Bearer YOUR_TOKEN" \
  -d "{\"name\":\"Private Class\",\"description\":\"Kategori untuk kelas privat\"}"
TEXT,
            'php' => <<<'TEXT'
$payload = json_encode([
  'name' => 'Private Class',
  'description' => 'Kategori untuk kelas privat',
]);

$ch = curl_init('{{ $apiBaseUrl }}/manage/prices/categories');
curl_setopt_array($ch, [
  CURLOPT_RETURNTRANSFER => true,
  CURLOPT_CUSTOMREQUEST => 'POST',
  CURLOPT_POSTFIELDS => $payload,
  CURLOPT_HTTPHEADER => [
    'Accept: application/json',
    'Content-Type: application/json',
    'Authorization: Bearer YOUR_TOKEN',
  ],
]);
echo curl_exec($ch);
curl_close($ch);
TEXT,
            'js' => <<<'TEXT'
fetch('{{ $apiBaseUrl }}/manage/prices/categories', {
  method: 'POST',
  headers: {
    'Accept': 'application/json',
    'Content-Type': 'application/json',
    'Authorization': 'Bearer YOUR_TOKEN'
  },
  body: JSON.stringify({
    name: 'Private Class',
    description: 'Kategori untuk kelas privat'
  })
})
  .then(async (res) => ({ status: res.status, body: await res.json() }))
  .then(console.log);
TEXT,
        ],
        'responses' => [
            ['label' => 'Success (201)', 'body' => <<<'JSON'
{
  "message": "Price category created.",
  "data": {
    "id": 1,
    "name": "Private Class",
    "description": "Kategori untuk kelas privat",
    "prices_count": 0,
    "created_at": "2026-07-19T10:00:00.000000Z",
    "updated_at": "2026-07-19T10:00:00.000000Z"
  }
}
JSON],
            ['label' => 'Error (422)', 'body' => <<<'JSON'
{
  "message": "The name field is required.",
  "errors": {
    "name": [
      "The name field is required."
    ]
  }
}
JSON],
        ],
    ],
    [
        'id' => 'price-categories-show',
        'section' => 'Pricing Categories',
        'title' => 'Get Price Category Detail',
        'method' => 'GET',
        'path' => '/api/manage/prices/categories/{category}',
        'auth' => 'Bearer token',
        'description' => 'Mengambil detail satu kategori pricing.',
        'headers' => [
            ['name' => 'Accept', 'required' => 'Ya', 'value' => 'application/json', 'description' => 'Meminta response JSON.'],
            ['name' => 'Authorization', 'required' => 'Ya', 'value' => 'Bearer <token>', 'description' => 'Token akses API.'],
        ],
        'fields' => [
            ['name' => 'category', 'location' => 'Path', 'type' => 'integer', 'required' => 'Ya', 'rules' => 'id kategori', 'example' => '1'],
        ],
        'payload' => null,
        'examples' => [
            'curl' => 'curl -H "Accept: application/json" -H "Authorization: Bearer YOUR_TOKEN" {{ $apiBaseUrl }}/manage/prices/categories/1',
            'php' => <<<'TEXT'
$categoryId = 1;
$ch = curl_init("{{ $apiBaseUrl }}/manage/prices/categories/$categoryId");
curl_setopt_array($ch, [
  CURLOPT_RETURNTRANSFER => true,
  CURLOPT_HTTPHEADER => [
    'Accept: application/json',
    'Authorization: Bearer YOUR_TOKEN',
  ],
]);
echo curl_exec($ch);
curl_close($ch);
TEXT,
            'js' => <<<'TEXT'
const categoryId = 1;
fetch(`{{ $apiBaseUrl }}/manage/prices/categories/${categoryId}`, {
  headers: {
    'Accept': 'application/json',
    'Authorization': 'Bearer YOUR_TOKEN'
  }
})
  .then(async (res) => ({ status: res.status, body: await res.json() }))
  .then(console.log);
TEXT,
        ],
        'responses' => [
            ['label' => 'Success (200)', 'body' => <<<'JSON'
{
  "data": {
    "id": 1,
    "name": "Private Class",
    "description": "Kategori untuk kelas privat",
    "prices_count": 2,
    "created_at": "2026-07-19T10:00:00.000000Z",
    "updated_at": "2026-07-19T10:00:00.000000Z"
  }
}
JSON],
            ['label' => 'Error (404)', 'body' => <<<'JSON'
{
  "message": "Not Found"
}
JSON],
        ],
    ],
    [
        'id' => 'price-categories-update',
        'section' => 'Pricing Categories',
        'title' => 'Update Price Category',
        'method' => 'PUT / PATCH',
        'path' => '/api/manage/prices/categories/{category}',
        'auth' => 'Bearer token',
        'description' => 'Mengubah data kategori pricing.',
        'headers' => [
            ['name' => 'Accept', 'required' => 'Ya', 'value' => 'application/json', 'description' => 'Meminta response JSON.'],
            ['name' => 'Content-Type', 'required' => 'Ya', 'value' => 'application/json', 'description' => 'Format body request.'],
            ['name' => 'Authorization', 'required' => 'Ya', 'value' => 'Bearer <token>', 'description' => 'Token akses API.'],
        ],
        'fields' => [
            ['name' => 'category', 'location' => 'Path', 'type' => 'integer', 'required' => 'Ya', 'rules' => 'id kategori', 'example' => '1'],
            ['name' => 'name', 'location' => 'Body', 'type' => 'string', 'required' => 'Tidak', 'rules' => 'max:255 jika ada', 'example' => 'Group Class'],
            ['name' => 'description', 'location' => 'Body', 'type' => 'string|null', 'required' => 'Tidak', 'rules' => 'nullable', 'example' => 'Kategori untuk kelas grup'],
        ],
        'payload' => <<<'JSON'
{
  "name": "Group Class",
  "description": "Kategori untuk kelas grup"
}
JSON,
        'examples' => [
            'curl' => <<<'TEXT'
curl -X PATCH {{ $apiBaseUrl }}/manage/prices/categories/1 \
  -H "Accept: application/json" \
  -H "Content-Type: application/json" \
  -H "Authorization: Bearer YOUR_TOKEN" \
  -d "{\"name\":\"Group Class\",\"description\":\"Kategori untuk kelas grup\"}"
TEXT,
            'php' => <<<'TEXT'
$categoryId = 1;
$payload = json_encode([
  'name' => 'Group Class',
  'description' => 'Kategori untuk kelas grup',
]);

$ch = curl_init("{{ $apiBaseUrl }}/manage/prices/categories/$categoryId");
curl_setopt_array($ch, [
  CURLOPT_RETURNTRANSFER => true,
  CURLOPT_CUSTOMREQUEST => 'PATCH',
  CURLOPT_POSTFIELDS => $payload,
  CURLOPT_HTTPHEADER => [
    'Accept: application/json',
    'Content-Type: application/json',
    'Authorization: Bearer YOUR_TOKEN',
  ],
]);
echo curl_exec($ch);
curl_close($ch);
TEXT,
            'js' => <<<'TEXT'
const categoryId = 1;
fetch(`{{ $apiBaseUrl }}/manage/prices/categories/${categoryId}`, {
  method: 'PATCH',
  headers: {
    'Accept': 'application/json',
    'Content-Type': 'application/json',
    'Authorization': 'Bearer YOUR_TOKEN'
  },
  body: JSON.stringify({
    name: 'Group Class',
    description: 'Kategori untuk kelas grup'
  })
})
  .then(async (res) => ({ status: res.status, body: await res.json() }))
  .then(console.log);
TEXT,
        ],
        'responses' => [
            ['label' => 'Success (200)', 'body' => <<<'JSON'
{
  "message": "Price category updated.",
  "data": {
    "id": 1,
    "name": "Group Class",
    "description": "Kategori untuk kelas grup",
    "prices_count": 2,
    "created_at": "2026-07-19T10:00:00.000000Z",
    "updated_at": "2026-07-19T10:10:00.000000Z"
  }
}
JSON],
            ['label' => 'Error (422)', 'body' => <<<'JSON'
{
  "message": "The name field is required.",
  "errors": {
    "name": [
      "The name field is required."
    ]
  }
}
JSON],
        ],
    ],
    [
        'id' => 'price-categories-delete',
        'section' => 'Pricing Categories',
        'title' => 'Delete Price Category',
        'method' => 'DELETE',
        'path' => '/api/manage/prices/categories/{category}',
        'auth' => 'Bearer token',
        'description' => 'Menghapus kategori pricing. Kategori tidak bisa dihapus jika masih punya data pricing terkait.',
        'headers' => [
            ['name' => 'Accept', 'required' => 'Ya', 'value' => 'application/json', 'description' => 'Meminta response JSON.'],
            ['name' => 'Authorization', 'required' => 'Ya', 'value' => 'Bearer <token>', 'description' => 'Token akses API.'],
        ],
        'fields' => [
            ['name' => 'category', 'location' => 'Path', 'type' => 'integer', 'required' => 'Ya', 'rules' => 'id kategori', 'example' => '1'],
        ],
        'payload' => null,
        'examples' => [
            'curl' => 'curl -X DELETE {{ $apiBaseUrl }}/manage/prices/categories/1 -H "Accept: application/json" -H "Authorization: Bearer YOUR_TOKEN"',
            'php' => <<<'TEXT'
$categoryId = 1;
$ch = curl_init("{{ $apiBaseUrl }}/manage/prices/categories/$categoryId");
curl_setopt_array($ch, [
  CURLOPT_RETURNTRANSFER => true,
  CURLOPT_CUSTOMREQUEST => 'DELETE',
  CURLOPT_HTTPHEADER => [
    'Accept: application/json',
    'Authorization: Bearer YOUR_TOKEN',
  ],
]);
echo curl_exec($ch);
curl_close($ch);
TEXT,
            'js' => <<<'TEXT'
const categoryId = 1;
fetch(`{{ $apiBaseUrl }}/manage/prices/categories/${categoryId}`, {
  method: 'DELETE',
  headers: {
    'Accept': 'application/json',
    'Authorization': 'Bearer YOUR_TOKEN'
  }
})
  .then(async (res) => ({ status: res.status, body: await res.json() }))
  .then(console.log);
TEXT,
        ],
        'responses' => [
            ['label' => 'Success (200)', 'body' => <<<'JSON'
{
  "message": "Price category deleted."
}
JSON],
            ['label' => 'Error (422)', 'body' => <<<'JSON'
{
  "message": "Price category still has related pricing data."
}
JSON],
        ],
    ],
    [
        'id' => 'prices-index',
        'section' => 'Pricing',
        'title' => 'List Prices',
        'method' => 'GET',
        'path' => '/api/manage/prices',
        'auth' => 'Bearer token',
        'description' => 'Mengambil seluruh data pricing beserta kategori terkait.',
        'headers' => [
            ['name' => 'Accept', 'required' => 'Ya', 'value' => 'application/json', 'description' => 'Meminta response JSON.'],
            ['name' => 'Authorization', 'required' => 'Ya', 'value' => 'Bearer <token>', 'description' => 'Token akses API.'],
        ],
        'fields' => [],
        'payload' => null,
        'examples' => [
            'curl' => 'curl -H "Accept: application/json" -H "Authorization: Bearer YOUR_TOKEN" {{ $apiBaseUrl }}/manage/prices',
            'php' => <<<'TEXT'
$ch = curl_init('{{ $apiBaseUrl }}/manage/prices');
curl_setopt_array($ch, [
  CURLOPT_RETURNTRANSFER => true,
  CURLOPT_HTTPHEADER => [
    'Accept: application/json',
    'Authorization: Bearer YOUR_TOKEN',
  ],
]);
echo curl_exec($ch);
curl_close($ch);
TEXT,
            'js' => <<<'TEXT'
fetch('{{ $apiBaseUrl }}/manage/prices', {
  headers: {
    'Accept': 'application/json',
    'Authorization': 'Bearer YOUR_TOKEN'
  }
})
  .then(async (res) => ({ status: res.status, body: await res.json() }))
  .then(console.log);
TEXT,
        ],
        'responses' => [
            ['label' => 'Success (200)', 'body' => <<<'JSON'
{
  "data": [
    {
      "id": 1,
      "category_id": 1,
      "name": "Paket 1 Sesi",
      "description": "Satu kali pertemuan",
      "price": "150000.00",
      "created_at": "2026-07-19T10:00:00.000000Z",
      "updated_at": "2026-07-19T10:00:00.000000Z",
      "category": {
        "id": 1,
        "name": "Private Class",
        "description": "Kategori untuk kelas privat"
      }
    }
  ]
}
JSON],
            ['label' => 'Error (401)', 'body' => <<<'JSON'
{
  "message": "Unauthorized."
}
JSON],
        ],
    ],
    [
        'id' => 'prices-store',
        'section' => 'Pricing',
        'title' => 'Create Price',
        'method' => 'POST',
        'path' => '/api/manage/prices',
        'auth' => 'Bearer token',
        'description' => 'Membuat data pricing baru.',
        'headers' => [
            ['name' => 'Accept', 'required' => 'Ya', 'value' => 'application/json', 'description' => 'Meminta response JSON.'],
            ['name' => 'Content-Type', 'required' => 'Ya', 'value' => 'application/json', 'description' => 'Format body request.'],
            ['name' => 'Authorization', 'required' => 'Ya', 'value' => 'Bearer <token>', 'description' => 'Token akses API.'],
        ],
        'fields' => [
            ['name' => 'category_id', 'location' => 'Body', 'type' => 'integer', 'required' => 'Ya', 'rules' => 'exists:price_categories,id', 'example' => '1'],
            ['name' => 'name', 'location' => 'Body', 'type' => 'string', 'required' => 'Ya', 'rules' => 'max:255', 'example' => 'Paket 1 Sesi'],
            ['name' => 'description', 'location' => 'Body', 'type' => 'string|null', 'required' => 'Tidak', 'rules' => 'nullable', 'example' => 'Satu kali pertemuan'],
            ['name' => 'price', 'location' => 'Body', 'type' => 'number', 'required' => 'Ya', 'rules' => 'min:0', 'example' => '150000'],
        ],
        'payload' => <<<'JSON'
{
  "category_id": 1,
  "name": "Paket 1 Sesi",
  "description": "Satu kali pertemuan",
  "price": 150000
}
JSON,
        'examples' => [
            'curl' => <<<'TEXT'
curl -X POST {{ $apiBaseUrl }}/manage/prices \
  -H "Accept: application/json" \
  -H "Content-Type: application/json" \
  -H "Authorization: Bearer YOUR_TOKEN" \
  -d "{\"category_id\":1,\"name\":\"Paket 1 Sesi\",\"description\":\"Satu kali pertemuan\",\"price\":150000}"
TEXT,
            'php' => <<<'TEXT'
$payload = json_encode([
  'category_id' => 1,
  'name' => 'Paket 1 Sesi',
  'description' => 'Satu kali pertemuan',
  'price' => 150000,
]);

$ch = curl_init('{{ $apiBaseUrl }}/manage/prices');
curl_setopt_array($ch, [
  CURLOPT_RETURNTRANSFER => true,
  CURLOPT_CUSTOMREQUEST => 'POST',
  CURLOPT_POSTFIELDS => $payload,
  CURLOPT_HTTPHEADER => [
    'Accept: application/json',
    'Content-Type: application/json',
    'Authorization: Bearer YOUR_TOKEN',
  ],
]);
echo curl_exec($ch);
curl_close($ch);
TEXT,
            'js' => <<<'TEXT'
fetch('{{ $apiBaseUrl }}/manage/prices', {
  method: 'POST',
  headers: {
    'Accept': 'application/json',
    'Content-Type': 'application/json',
    'Authorization': 'Bearer YOUR_TOKEN'
  },
  body: JSON.stringify({
    category_id: 1,
    name: 'Paket 1 Sesi',
    description: 'Satu kali pertemuan',
    price: 150000
  })
})
  .then(async (res) => ({ status: res.status, body: await res.json() }))
  .then(console.log);
TEXT,
        ],
        'responses' => [
            ['label' => 'Success (201)', 'body' => <<<'JSON'
{
  "message": "Price created.",
  "data": {
    "id": 1,
    "category_id": 1,
    "name": "Paket 1 Sesi",
    "description": "Satu kali pertemuan",
    "price": "150000.00",
    "created_at": "2026-07-19T10:00:00.000000Z",
    "updated_at": "2026-07-19T10:00:00.000000Z",
    "category": {
      "id": 1,
      "name": "Private Class",
      "description": "Kategori untuk kelas privat"
    }
  }
}
JSON],
            ['label' => 'Error (422)', 'body' => <<<'JSON'
{
  "message": "The selected category id is invalid.",
  "errors": {
    "category_id": [
      "The selected category id is invalid."
    ]
  }
}
JSON],
        ],
    ],
    [
        'id' => 'prices-show',
        'section' => 'Pricing',
        'title' => 'Get Price Detail',
        'method' => 'GET',
        'path' => '/api/manage/prices/{price}',
        'auth' => 'Bearer token',
        'description' => 'Mengambil detail satu pricing berdasarkan ID.',
        'headers' => [
            ['name' => 'Accept', 'required' => 'Ya', 'value' => 'application/json', 'description' => 'Meminta response JSON.'],
            ['name' => 'Authorization', 'required' => 'Ya', 'value' => 'Bearer <token>', 'description' => 'Token akses API.'],
        ],
        'fields' => [
            ['name' => 'price', 'location' => 'Path', 'type' => 'integer', 'required' => 'Ya', 'rules' => 'id pricing', 'example' => '1'],
        ],
        'payload' => null,
        'examples' => [
            'curl' => 'curl -H "Accept: application/json" -H "Authorization: Bearer YOUR_TOKEN" {{ $apiBaseUrl }}/manage/prices/1',
            'php' => <<<'TEXT'
$priceId = 1;
$ch = curl_init("{{ $apiBaseUrl }}/manage/prices/$priceId");
curl_setopt_array($ch, [
  CURLOPT_RETURNTRANSFER => true,
  CURLOPT_HTTPHEADER => [
    'Accept: application/json',
    'Authorization: Bearer YOUR_TOKEN',
  ],
]);
echo curl_exec($ch);
curl_close($ch);
TEXT,
            'js' => <<<'TEXT'
const priceId = 1;
fetch(`{{ $apiBaseUrl }}/manage/prices/${priceId}`, {
  headers: {
    'Accept': 'application/json',
    'Authorization': 'Bearer YOUR_TOKEN'
  }
})
  .then(async (res) => ({ status: res.status, body: await res.json() }))
  .then(console.log);
TEXT,
        ],
        'responses' => [
            ['label' => 'Success (200)', 'body' => <<<'JSON'
{
  "data": {
    "id": 1,
    "category_id": 1,
    "name": "Paket 1 Sesi",
    "description": "Satu kali pertemuan",
    "price": "150000.00",
    "created_at": "2026-07-19T10:00:00.000000Z",
    "updated_at": "2026-07-19T10:00:00.000000Z",
    "category": {
      "id": 1,
      "name": "Private Class",
      "description": "Kategori untuk kelas privat"
    }
  }
}
JSON],
            ['label' => 'Error (404)', 'body' => <<<'JSON'
{
  "message": "Not Found"
}
JSON],
        ],
    ],
    [
        'id' => 'prices-update',
        'section' => 'Pricing',
        'title' => 'Update Price',
        'method' => 'PUT / PATCH',
        'path' => '/api/manage/prices/{price}',
        'auth' => 'Bearer token',
        'description' => 'Mengubah data pricing seperti kategori, nama, deskripsi, atau harga.',
        'headers' => [
            ['name' => 'Accept', 'required' => 'Ya', 'value' => 'application/json', 'description' => 'Meminta response JSON.'],
            ['name' => 'Content-Type', 'required' => 'Ya', 'value' => 'application/json', 'description' => 'Format body request.'],
            ['name' => 'Authorization', 'required' => 'Ya', 'value' => 'Bearer <token>', 'description' => 'Token akses API.'],
        ],
        'fields' => [
            ['name' => 'price', 'location' => 'Path', 'type' => 'integer', 'required' => 'Ya', 'rules' => 'id pricing', 'example' => '1'],
            ['name' => 'category_id', 'location' => 'Body', 'type' => 'integer', 'required' => 'Tidak', 'rules' => 'exists jika ada', 'example' => '1'],
            ['name' => 'name', 'location' => 'Body', 'type' => 'string', 'required' => 'Tidak', 'rules' => 'max:255 jika ada', 'example' => 'Paket 4 Sesi'],
            ['name' => 'description', 'location' => 'Body', 'type' => 'string|null', 'required' => 'Tidak', 'rules' => 'nullable', 'example' => 'Empat kali pertemuan'],
            ['name' => 'price', 'location' => 'Body', 'type' => 'number', 'required' => 'Tidak', 'rules' => 'min:0 jika ada', 'example' => '500000'],
        ],
        'payload' => <<<'JSON'
{
  "name": "Paket 4 Sesi",
  "price": 500000
}
JSON,
        'examples' => [
            'curl' => <<<'TEXT'
curl -X PATCH {{ $apiBaseUrl }}/manage/prices/1 \
  -H "Accept: application/json" \
  -H "Content-Type: application/json" \
  -H "Authorization: Bearer YOUR_TOKEN" \
  -d "{\"name\":\"Paket 4 Sesi\",\"price\":500000}"
TEXT,
            'php' => <<<'TEXT'
$priceId = 1;
$payload = json_encode([
  'name' => 'Paket 4 Sesi',
  'price' => 500000,
]);

$ch = curl_init("{{ $apiBaseUrl }}/manage/prices/$priceId");
curl_setopt_array($ch, [
  CURLOPT_RETURNTRANSFER => true,
  CURLOPT_CUSTOMREQUEST => 'PATCH',
  CURLOPT_POSTFIELDS => $payload,
  CURLOPT_HTTPHEADER => [
    'Accept: application/json',
    'Content-Type: application/json',
    'Authorization: Bearer YOUR_TOKEN',
  ],
]);
echo curl_exec($ch);
curl_close($ch);
TEXT,
            'js' => <<<'TEXT'
const priceId = 1;
fetch(`{{ $apiBaseUrl }}/manage/prices/${priceId}`, {
  method: 'PATCH',
  headers: {
    'Accept': 'application/json',
    'Content-Type': 'application/json',
    'Authorization': 'Bearer YOUR_TOKEN'
  },
  body: JSON.stringify({
    name: 'Paket 4 Sesi',
    price: 500000
  })
})
  .then(async (res) => ({ status: res.status, body: await res.json() }))
  .then(console.log);
TEXT,
        ],
        'responses' => [
            ['label' => 'Success (200)', 'body' => <<<'JSON'
{
  "message": "Price updated.",
  "data": {
    "id": 1,
    "category_id": 1,
    "name": "Paket 4 Sesi",
    "description": "Satu kali pertemuan",
    "price": "500000.00",
    "created_at": "2026-07-19T10:00:00.000000Z",
    "updated_at": "2026-07-19T10:10:00.000000Z",
    "category": {
      "id": 1,
      "name": "Private Class",
      "description": "Kategori untuk kelas privat"
    }
  }
}
JSON],
            ['label' => 'Error (422)', 'body' => <<<'JSON'
{
  "message": "The price field must be at least 0.",
  "errors": {
    "price": [
      "The price field must be at least 0."
    ]
  }
}
JSON],
        ],
    ],
    [
        'id' => 'prices-delete',
        'section' => 'Pricing',
        'title' => 'Delete Price',
        'method' => 'DELETE',
        'path' => '/api/manage/prices/{price}',
        'auth' => 'Bearer token',
        'description' => 'Menghapus satu data pricing berdasarkan ID.',
        'headers' => [
            ['name' => 'Accept', 'required' => 'Ya', 'value' => 'application/json', 'description' => 'Meminta response JSON.'],
            ['name' => 'Authorization', 'required' => 'Ya', 'value' => 'Bearer <token>', 'description' => 'Token akses API.'],
        ],
        'fields' => [
            ['name' => 'price', 'location' => 'Path', 'type' => 'integer', 'required' => 'Ya', 'rules' => 'id pricing', 'example' => '1'],
        ],
        'payload' => null,
        'examples' => [
            'curl' => 'curl -X DELETE {{ $apiBaseUrl }}/manage/prices/1 -H "Accept: application/json" -H "Authorization: Bearer YOUR_TOKEN"',
            'php' => <<<'TEXT'
$priceId = 1;
$ch = curl_init("{{ $apiBaseUrl }}/manage/prices/$priceId");
curl_setopt_array($ch, [
  CURLOPT_RETURNTRANSFER => true,
  CURLOPT_CUSTOMREQUEST => 'DELETE',
  CURLOPT_HTTPHEADER => [
    'Accept: application/json',
    'Authorization: Bearer YOUR_TOKEN',
  ],
]);
echo curl_exec($ch);
curl_close($ch);
TEXT,
            'js' => <<<'TEXT'
const priceId = 1;
fetch(`{{ $apiBaseUrl }}/manage/prices/${priceId}`, {
  method: 'DELETE',
  headers: {
    'Accept': 'application/json',
    'Authorization': 'Bearer YOUR_TOKEN'
  }
})
  .then(async (res) => ({ status: res.status, body: await res.json() }))
  .then(console.log);
TEXT,
        ],
        'responses' => [
            ['label' => 'Success (200)', 'body' => <<<'JSON'
{
  "message": "Price deleted."
}
JSON],
            ['label' => 'Error (404)', 'body' => <<<'JSON'
{
  "message": "Not Found"
}
JSON],
        ],
    ],
];
@endphp
<!doctype html>
<html lang="id">
<head>
  <meta charset="utf-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1" />
  <title>Dokumentasi API</title>
  <style>
    :root {
      color-scheme: light;
      --bg: #f4f7fb;
      --surface: #ffffff;
      --surface-soft: #f8fafc;
      --text: #0f172a;
      --muted: #64748b;
      --border: #dbe3f0;
      --primary: #2563eb;
      --primary-soft: #dbeafe;
      --success: #166534;
      --success-soft: #dcfce7;
      --warning: #92400e;
      --warning-soft: #fef3c7;
      --danger: #991b1b;
      --danger-soft: #fee2e2;
      --code-bg: #0f172a;
      --code-text: #e2e8f0;
      --shadow: 0 20px 45px rgba(15, 23, 42, 0.08);
    }

    * { box-sizing: border-box; }
    html { scroll-behavior: smooth; }
    body {
      margin: 0;
      background: var(--bg);
      color: var(--text);
      font-family: Inter, ui-sans-serif, system-ui, -apple-system, BlinkMacSystemFont, "Segoe UI", sans-serif;
      line-height: 1.6;
    }
    a { color: inherit; }
    code, pre {
      font-family: ui-monospace, SFMono-Regular, Menlo, Monaco, Consolas, "Liberation Mono", "Courier New", monospace;
    }
    pre {
      margin: 0;
      padding: 16px;
      border-radius: 16px;
      background: var(--code-bg);
      color: var(--code-text);
      overflow-x: auto;
      font-size: 13px;
      line-height: 1.6;
    }
    table {
      width: 100%;
      border-collapse: collapse;
      background: var(--surface);
      border-radius: 16px;
      overflow: hidden;
      border: 1px solid var(--border);
    }
    th, td {
      padding: 12px 14px;
      border-bottom: 1px solid var(--border);
      text-align: left;
      vertical-align: top;
      font-size: 14px;
    }
    th {
      background: var(--surface-soft);
      color: var(--muted);
      font-weight: 700;
    }
    tr:last-child td { border-bottom: 0; }

    .layout {
      display: grid;
      grid-template-columns: 300px minmax(0, 1fr);
      gap: 28px;
      max-width: 1480px;
      margin: 0 auto;
      padding: 24px;
    }
    .sidebar {
      position: sticky;
      top: 24px;
      align-self: start;
      max-height: calc(100vh - 48px);
      overflow: auto;
      padding: 24px;
      border: 1px solid var(--border);
      border-radius: 24px;
      background: rgba(255, 255, 255, 0.88);
      backdrop-filter: blur(10px);
      box-shadow: var(--shadow);
    }
    .brand {
      margin-bottom: 20px;
      padding-bottom: 18px;
      border-bottom: 1px solid var(--border);
    }
    .brand h1 {
      margin: 0 0 8px;
      font-size: 24px;
      line-height: 1.2;
    }
    .brand p {
      margin: 0;
      color: var(--muted);
      font-size: 14px;
    }
    .base-url {
      margin-top: 12px;
      padding: 12px;
      border-radius: 14px;
      background: var(--surface-soft);
      border: 1px solid var(--border);
      font-size: 13px;
      color: var(--muted);
    }
    .nav-section + .nav-section {
      margin-top: 18px;
      padding-top: 18px;
      border-top: 1px solid var(--border);
    }
    .nav-section-title {
      margin: 0 0 6px;
      font-size: 12px;
      text-transform: uppercase;
      letter-spacing: 0.08em;
      color: var(--muted);
      font-weight: 800;
    }
    .nav-section-desc {
      margin: 0 0 10px;
      color: var(--muted);
      font-size: 13px;
    }
    .nav-link {
      display: block;
      padding: 10px 12px;
      border-radius: 12px;
      text-decoration: none;
      color: #1e293b;
      font-size: 14px;
      transition: background .2s ease, color .2s ease, transform .2s ease;
    }
    .nav-link:hover {
      background: var(--primary-soft);
      color: var(--primary);
      transform: translateX(2px);
    }
    .nav-link.active {
      background: var(--primary);
      color: #fff;
    }

    .content {
      min-width: 0;
    }
    .hero {
      padding: 32px;
      border: 1px solid var(--border);
      border-radius: 28px;
      background: linear-gradient(135deg, #eff6ff 0%, #ffffff 45%, #f8fafc 100%);
      box-shadow: var(--shadow);
    }
    .hero h2 {
      margin: 0 0 10px;
      font-size: 38px;
      line-height: 1.15;
    }
    .hero p {
      margin: 0;
      max-width: 820px;
      color: var(--muted);
      font-size: 16px;
    }
    .hero-grid {
      display: grid;
      grid-template-columns: repeat(3, minmax(0, 1fr));
      gap: 14px;
      margin-top: 24px;
    }
    .hero-stat {
      padding: 16px 18px;
      border-radius: 18px;
      background: rgba(255, 255, 255, 0.75);
      border: 1px solid var(--border);
    }
    .hero-stat strong {
      display: block;
      margin-bottom: 4px;
      font-size: 20px;
    }
    .hero-stat span {
      color: var(--muted);
      font-size: 14px;
    }

    .section {
      margin-top: 28px;
      scroll-margin-top: 24px;
    }
    .section-head {
      margin-bottom: 16px;
      padding: 0 4px;
    }
    .section-head h2 {
      margin: 0 0 6px;
      font-size: 28px;
    }
    .section-head p {
      margin: 0;
      color: var(--muted);
    }
    .card {
      margin-top: 16px;
      padding: 22px;
      border-radius: 24px;
      background: var(--surface);
      border: 1px solid var(--border);
      box-shadow: var(--shadow);
      scroll-margin-top: 24px;
    }
    .card h3 {
      margin: 0;
      font-size: 22px;
    }
    .card h4 {
      margin: 24px 0 10px;
      font-size: 16px;
    }
    .meta {
      display: grid;
      grid-template-columns: repeat(2, minmax(0, 1fr));
      gap: 12px;
      margin-top: 18px;
    }
    .meta-item {
      padding: 14px 16px;
      border-radius: 16px;
      background: var(--surface-soft);
      border: 1px solid var(--border);
    }
    .meta-item span {
      display: block;
      font-size: 12px;
      font-weight: 700;
      text-transform: uppercase;
      letter-spacing: 0.08em;
      color: var(--muted);
      margin-bottom: 6px;
    }
    .badge {
      display: inline-flex;
      align-items: center;
      gap: 8px;
      padding: 6px 10px;
      border-radius: 999px;
      font-size: 12px;
      font-weight: 800;
      letter-spacing: 0.04em;
      text-transform: uppercase;
    }
    .method-get { background: #dcfce7; color: #166534; }
    .method-post { background: #dbeafe; color: #1d4ed8; }
    .method-put { background: #fef3c7; color: #92400e; }
    .method-patch { background: #ede9fe; color: #6d28d9; }
    .method-delete { background: #fee2e2; color: #b91c1c; }
    .method-default { background: #e2e8f0; color: #334155; }
    .inline-code {
      display: inline-block;
      padding: 2px 8px;
      border-radius: 999px;
      background: var(--surface-soft);
      border: 1px solid var(--border);
    }
    .note {
      padding: 16px 18px;
      border-radius: 18px;
      border: 1px solid var(--border);
      background: var(--surface-soft);
      color: var(--muted);
    }
    .note strong { color: var(--text); }
    .note.warning {
      background: var(--warning-soft);
      border-color: #fcd34d;
      color: var(--warning);
    }
    .note.success {
      background: var(--success-soft);
      border-color: #86efac;
      color: var(--success);
    }
    .note.danger {
      background: var(--danger-soft);
      border-color: #fca5a5;
      color: var(--danger);
    }
    .overview-grid {
      display: grid;
      grid-template-columns: repeat(2, minmax(0, 1fr));
      gap: 16px;
      margin-top: 16px;
    }
    .list {
      margin: 0;
      padding-left: 18px;
      color: var(--muted);
    }
    .list li + li { margin-top: 6px; }

    .tabs {
      margin-top: 10px;
    }
    .tablist {
      display: inline-flex;
      flex-wrap: wrap;
      gap: 8px;
      padding: 6px;
      border: 1px solid var(--border);
      border-radius: 16px;
      background: var(--surface-soft);
    }
    .tab {
      appearance: none;
      border: 0;
      background: transparent;
      border-radius: 12px;
      padding: 10px 14px;
      font-weight: 700;
      color: var(--muted);
      cursor: pointer;
      transition: background .2s ease, color .2s ease;
    }
    .tab[aria-selected="true"] {
      background: var(--text);
      color: #fff;
    }
    .tabpanel {
      margin-top: 12px;
    }
    .tabpanel[hidden] {
      display: none;
    }

    @media (max-width: 1100px) {
      .layout {
        grid-template-columns: 1fr;
      }
      .sidebar {
        position: static;
        max-height: none;
      }
    }

    @media (max-width: 768px) {
      .layout {
        padding: 16px;
        gap: 16px;
      }
      .hero {
        padding: 22px;
      }
      .hero h2 {
        font-size: 30px;
      }
      .hero-grid,
      .overview-grid,
      .meta {
        grid-template-columns: 1fr;
      }
      .card {
        padding: 18px;
      }
      th, td {
        font-size: 13px;
      }
    }
  </style>
</head>
<body>
  <div class="layout">
    <aside class="sidebar">
      <div class="brand">
        <h1>API Docs</h1>
        <p>Single-page documentation dengan sidebar untuk navigasi cepat ke setiap endpoint.</p>
        <div class="base-url">
          Base URL<br />
          <code>/api</code><br />
          <code>{{ $apiBaseUrl }}</code>
        </div>
      </div>

      @foreach ($sections as $section)
        <div class="nav-section">
          <p class="nav-section-title">{{ $section['title'] }}</p>
          <p class="nav-section-desc">{{ $section['description'] }}</p>
          @foreach ($section['items'] as $item)
            <a class="nav-link" href="#{{ $item['id'] }}">{{ $item['label'] }}</a>
          @endforeach
        </div>
      @endforeach
    </aside>

    <main class="content">
      <section class="hero">
        <h2>Dokumentasi API Monolith</h2>
        <p>Halaman ini merangkum seluruh endpoint untuk modul account, pricing categories, dan pricing dalam satu halaman. Gunakan sidebar di kiri untuk lompat ke endpoint tertentu dengan cepat.</p>
        <div class="hero-grid">
          <div class="hero-stat">
            <strong>12 Endpoint</strong>
            <span>Account, pricing categories, dan pricing.</span>
          </div>
          <div class="hero-stat">
            <strong>Bearer Token</strong>
            <span>Endpoint prefix <code>/manage</code> wajib memakai token dari login/register.</span>
          </div>
          <div class="hero-stat">
            <strong>1 Halaman</strong>
            <span>Sidebar sticky memudahkan navigasi tanpa pindah page.</span>
          </div>
        </div>
      </section>

      <section class="section" id="overview">
        <div class="section-head">
          <h2>Overview</h2>
          <p>Bagian ini menjelaskan konvensi umum, alur autentikasi, model data, dan format error.</p>
        </div>

        <article class="card" id="overview-general">
          <h3>Informasi Umum</h3>
          <div class="overview-grid">
            <div class="note">
              <strong>Headers standar</strong>
              <ul class="list">
                <li><code>Accept: application/json</code> untuk semua request agar error juga tetap JSON.</li>
                <li><code>Content-Type: application/json</code> untuk request dengan body JSON.</li>
                <li><code>Authorization: Bearer &lt;token&gt;</code> untuk semua endpoint di bawah prefix <code>/manage</code>.</li>
              </ul>
            </div>
            <div class="note">
              <strong>Status error umum</strong>
              <ul class="list">
                <li><code>401</code> token tidak ada atau tidak valid.</li>
                <li><code>404</code> resource tidak ditemukan.</li>
                <li><code>405</code> method tidak didukung.</li>
                <li><code>422</code> validasi gagal.</li>
              </ul>
            </div>
          </div>
        </article>

        <article class="card" id="overview-auth-flow">
          <h3>Alur Auth</h3>
          <div class="note success">
            <strong>Langkah penggunaan token</strong><br />
            1. Panggil <code>POST /api/account/register</code> atau <code>POST /api/account/login</code>.<br />
            2. Ambil nilai <code>data.token</code> dari response.<br />
            3. Gunakan token tersebut pada header <code>Authorization: Bearer YOUR_TOKEN</code> untuk endpoint <code>/api/manage/*</code>.
          </div>
        </article>

        <article class="card" id="overview-models">
          <h3>Model Data</h3>
          <h4>User Register/Login Response</h4>
          <pre>{
  "data": {
    "user": {
      "id": 1,
      "name": "Budi Santoso",
      "email": "budi@example.com"
    },
    "token": "plain-text-api-token"
  }
}</pre>
          <h4>Price Category</h4>
          <pre>{
  "id": 1,
  "name": "Private Class",
  "description": "Kategori untuk kelas privat",
  "prices_count": 2,
  "created_at": "2026-07-19T10:00:00.000000Z",
  "updated_at": "2026-07-19T10:00:00.000000Z"
}</pre>
          <h4>Price</h4>
          <pre>{
  "id": 1,
  "category_id": 1,
  "name": "Paket 1 Sesi",
  "description": "Satu kali pertemuan",
  "price": "150000.00",
  "category": {
    "id": 1,
    "name": "Private Class",
    "description": "Kategori untuk kelas privat"
  }
}</pre>
        </article>

        <article class="card" id="overview-errors">
          <h3>Format Error</h3>
          <div class="overview-grid">
            <div class="note warning">
              <strong>Validasi gagal (422)</strong>
              <pre>{
  "message": "The title field is required.",
  "errors": {
    "title": [
      "The title field is required."
    ]
  }
}</pre>
            </div>
            <div class="note danger">
              <strong>Unauthorized (401)</strong>
              <pre>{
  "message": "Unauthorized."
}</pre>
            </div>
          </div>
        </article>
      </section>

      @foreach ($sections as $section)
        @if ($section['id'] !== 'overview')
          <section class="section" id="{{ $section['id'] }}">
            <div class="section-head">
              <h2>{{ $section['title'] }}</h2>
              <p>{{ $section['description'] }}</p>
            </div>

            @foreach ($endpoints as $endpoint)
              @if ($endpoint['section'] === $section['title'])
                @php
                  $methodClass = 'method-default';
                  if (str_starts_with($endpoint['method'], 'GET')) $methodClass = 'method-get';
                  elseif (str_starts_with($endpoint['method'], 'POST')) $methodClass = 'method-post';
                  elseif (str_starts_with($endpoint['method'], 'PUT')) $methodClass = 'method-put';
                  elseif (str_starts_with($endpoint['method'], 'PATCH')) $methodClass = 'method-patch';
                  elseif (str_starts_with($endpoint['method'], 'DELETE')) $methodClass = 'method-delete';
                @endphp
                <article class="card" id="{{ $endpoint['id'] }}">
                  <h3>{{ $endpoint['title'] }}</h3>
                  <div class="meta">
                    <div class="meta-item">
                      <span>Method</span>
                      <div class="badge {{ $methodClass }}">{{ $endpoint['method'] }}</div>
                    </div>
                    <div class="meta-item">
                      <span>Path</span>
                      <code>{{ $endpoint['path'] }}</code>
                    </div>
                    <div class="meta-item">
                      <span>Auth</span>
                      <div>{{ $endpoint['auth'] }}</div>
                    </div>
                    <div class="meta-item">
                      <span>Deskripsi</span>
                      <div>{{ $endpoint['description'] }}</div>
                    </div>
                  </div>

                  <h4>Headers</h4>
                  <table>
                    <thead>
                      <tr>
                        <th>Header</th>
                        <th>Wajib</th>
                        <th>Contoh</th>
                        <th>Deskripsi</th>
                      </tr>
                    </thead>
                    <tbody>
                      @foreach ($endpoint['headers'] as $header)
                        <tr>
                          <td><code>{{ $header['name'] }}</code></td>
                          <td>{{ $header['required'] }}</td>
                          <td><code>{{ $header['value'] }}</code></td>
                          <td>{{ $header['description'] }}</td>
                        </tr>
                      @endforeach
                    </tbody>
                  </table>

                  <h4>Parameter / Payload</h4>
                  @if (count($endpoint['fields']) > 0)
                    <table>
                      <thead>
                        <tr>
                          <th>Nama</th>
                          <th>Lokasi</th>
                          <th>Tipe</th>
                          <th>Wajib</th>
                          <th>Rules</th>
                          <th>Contoh</th>
                        </tr>
                      </thead>
                      <tbody>
                        @foreach ($endpoint['fields'] as $field)
                          <tr>
                            <td><code>{{ $field['name'] }}</code></td>
                            <td>{{ $field['location'] }}</td>
                            <td>{{ $field['type'] }}</td>
                            <td>{{ $field['required'] }}</td>
                            <td>{{ $field['rules'] }}</td>
                            <td><code>{{ $field['example'] }}</code></td>
                          </tr>
                        @endforeach
                      </tbody>
                    </table>
                  @else
                    <div class="note">Endpoint ini tidak memerlukan payload body maupun parameter tambahan selain header.</div>
                  @endif

                  @if ($endpoint['payload'])
                    <h4>Contoh Payload JSON</h4>
                    <pre>{{ $endpoint['payload'] }}</pre>
                  @endif

                  <h4>Contoh Request</h4>
                  <div class="tabs" data-tabs>
                    <div class="tablist" role="tablist" aria-label="Contoh request {{ $endpoint['title'] }}">
                      <button class="tab" type="button" role="tab" aria-selected="true" data-tab="curl">CURL</button>
                      <button class="tab" type="button" role="tab" aria-selected="false" data-tab="php">PHP</button>
                      <button class="tab" type="button" role="tab" aria-selected="false" data-tab="js">JS</button>
                    </div>
                    <div class="tabpanel" role="tabpanel" data-panel="curl">
                      <pre>{{ $endpoint['examples']['curl'] }}</pre>
                    </div>
                    <div class="tabpanel" role="tabpanel" data-panel="php" hidden>
                      <pre>{{ $endpoint['examples']['php'] }}</pre>
                    </div>
                    <div class="tabpanel" role="tabpanel" data-panel="js" hidden>
                      <pre>{{ $endpoint['examples']['js'] }}</pre>
                    </div>
                  </div>

                  <h4>Contoh Response</h4>
                  @foreach ($endpoint['responses'] as $response)
                    <p class="inline-code">{{ $response['label'] }}</p>
                    <pre>{{ $response['body'] }}</pre>
                  @endforeach
                </article>
              @endif
            @endforeach
          </section>
        @endif
      @endforeach
    </main>
  </div>

  <script>
    (function () {
      function initTabs(container) {
        const tabs = Array.from(container.querySelectorAll('[role="tab"]'));
        const panels = Array.from(container.querySelectorAll('[role="tabpanel"]'));

        function activate(name) {
          tabs.forEach((tab) => {
            tab.setAttribute('aria-selected', tab.dataset.tab === name ? 'true' : 'false');
          });

          panels.forEach((panel) => {
            const isActive = panel.dataset.panel === name;
            if (isActive) {
              panel.removeAttribute('hidden');
            } else {
              panel.setAttribute('hidden', 'hidden');
            }
          });
        }

        tabs.forEach((tab) => {
          tab.addEventListener('click', () => activate(tab.dataset.tab));
        });

        activate('curl');
      }

      document.querySelectorAll('[data-tabs]').forEach(initTabs);

      const links = Array.from(document.querySelectorAll('.nav-link'));
      const sections = links
        .map((link) => document.querySelector(link.getAttribute('href')))
        .filter(Boolean);

      const observer = new IntersectionObserver((entries) => {
        entries.forEach((entry) => {
          if (!entry.isIntersecting) return;

          links.forEach((link) => {
            const isActive = link.getAttribute('href') === `#${entry.target.id}`;
            link.classList.toggle('active', isActive);
          });
        });
      }, {
        rootMargin: '-20% 0px -65% 0px',
        threshold: 0.1
      });

      sections.forEach((section) => observer.observe(section));
    })();
  </script>
</body>
</html>
