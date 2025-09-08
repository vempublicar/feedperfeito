<?php
require_once 'config/database.php';
require_once 'models/User.php';
require_once 'models/AdminUser.php';
require_once 'models/ContentTemplate.php';
require_once 'models/CreditPackage.php';
require_once 'models/Service.php';
require_once 'models/Promotion.php';

echo "Seeding database...\n";

// Create admin user
$adminUser = new AdminUser();
$admin = $adminUser->createAdminUser('Admin User', 'admin@feedperfeito.com', 'admin123', 'admin');
if ($admin) {
    echo "Admin user created successfully\n";
} else {
    echo "Failed to create admin user\n";
}

// Create sample users
$userModel = new User();
$users = [
    ['name' => 'André M.', 'email' => 'andre@example.com', 'password' => 'user123', 'credits' => 120],
    ['name' => 'Maria S.', 'email' => 'maria@example.com', 'password' => 'user123', 'credits' => 85],
    ['name' => 'João P.', 'email' => 'joao@example.com', 'password' => 'user123', 'credits' => 210],
];

foreach ($users as $userData) {
    $user = $userModel->createUser($userData['name'], $userData['email'], $userData['password']);
    if ($user) {
        // Update credits
        $userModel->updateCredits($user[0]['id'], $userData['credits']);
        echo "User {$userData['name']} created successfully\n";
    } else {
        echo "Failed to create user {$userData['name']}\n";
    }
}

// Create content templates
$templateModel = new ContentTemplate();
$templates = [
    [
        'title' => 'Carrossel "Oferta da Semana"',
        'category' => 'Carrossel',
        'credits_required' => 6,
        'preview_url' => 'https://images.unsplash.com/photo-1516245834210-c4c142787335?q=80&w=1200&auto=format&fit=crop',
        'thumbnail_url' => 'https://images.unsplash.com/photo-1516245834210-c4c142787335?q=80&w=480&auto=format&fit=crop',
        'is_featured' => true,
        'is_active' => true
    ],
    [
        'title' => 'Post "Antes & Depois"',
        'category' => 'Post',
        'credits_required' => 3,
        'preview_url' => 'https://images.unsplash.com/photo-1548075413-9f0e8c2b3f4b?q=80&w=1200&auto=format&fit=crop',
        'thumbnail_url' => 'https://images.unsplash.com/photo-1548075413-9f0e8c2b3f4b?q=80&w=480&auto=format&fit=crop',
        'is_featured' => false,
        'is_active' => true
    ],
    [
        'title' => 'Reels "Truques em 15s"',
        'category' => 'Reels',
        'credits_required' => 8,
        'preview_url' => 'https://images.unsplash.com/photo-1512446816042-444d641267ee?q=80&w=1200&auto=format&fit=crop',
        'thumbnail_url' => 'https://images.unsplash.com/photo-1512446816042-444d641267ee?q=80&w=480&auto=format&fit=crop',
        'is_featured' => true,
        'is_active' => true
    ],
    [
        'title' => 'Stories "Enquete Interativa"',
        'category' => 'Stories',
        'credits_required' => 2,
        'preview_url' => 'https://images.unsplash.com/photo-1483478550801-ceba5fe50e8e?q=80&w=1200&auto=format&fit=crop',
        'thumbnail_url' => 'https://images.unsplash.com/photo-1483478550801-ceba5fe50e8e?q=80&w=480&auto=format&fit=crop',
        'is_featured' => false,
        'is_active' => true
    ],
    [
        'title' => 'Post "Depoimento Cliente"',
        'category' => 'Post',
        'credits_required' => 4,
        'preview_url' => 'https://images.unsplash.com/photo-1507091249565-093fcca5f0c3?q=80&w=1200&auto=format&fit=crop',
        'thumbnail_url' => 'https://images.unsplash.com/photo-1507091249565-093fcca5f0c3?q=80&w=480&auto=format&fit=crop',
        'is_featured' => false,
        'is_active' => true
    ]
];

foreach ($templates as $templateData) {
    $template = $templateModel->create($templateData);
    if ($template) {
        echo "Template {$templateData['title']} created successfully\n";
    } else {
        echo "Failed to create template {$templateData['title']}\n";
    }
}

// Create credit packages
$packageModel = new CreditPackage();
$packages = [
    [
        'title' => 'Starter',
        'credits' => 10,
        'bonus_credits' => 0,
        'price' => 49.90,
        'tag' => null,
        'is_active' => true
    ],
    [
        'title' => 'Essencial',
        'credits' => 25,
        'bonus_credits' => 3,
        'price' => 109.90,
        'tag' => 'Mais Popular',
        'is_active' => true
    ],
    [
        'title' => 'Profissional',
        'credits' => 50,
        'bonus_credits' => 8,
        'price' => 199.90,
        'tag' => 'Melhor Custo/Benefício',
        'is_active' => true
    ],
    [
        'title' => 'Agência',
        'credits' => 100,
        'bonus_credits' => 20,
        'price' => 349.90,
        'tag' => null,
        'is_active' => true
    ]
];

foreach ($packages as $packageData) {
    $package = $packageModel->create($packageData);
    if ($package) {
        echo "Package {$packageData['title']} created successfully\n";
    } else {
        echo "Failed to create package {$packageData['title']}\n";
    }
}

// Create services
$serviceModel = new Service();
$services = [
    [
        'title' => 'Cartão NFC',
        'description' => 'Cartão físico com NFC apontando para sua página de links e contato.',
        'thumbnail_url' => 'https://images.unsplash.com/photo-1517649763962-0c623066013b?q=80&w=1200&auto=format&fit=crop',
        'delivery_time' => '3–5 dias úteis',
        'price_type' => 'BRL',
        'price' => 149.90,
        'credits_required' => null,
        'tag' => 'Popular',
        'is_active' => true
    ],
    [
        'title' => 'Landing Page',
        'description' => 'Página de alta conversão para capturar leads ou vendas.',
        'thumbnail_url' => 'https://images.unsplash.com/photo-1522071820081-009f0129c71c?q=80&w=1200&auto=format&fit=crop',
        'delivery_time' => '7–10 dias úteis',
        'price_type' => 'BRL',
        'price' => 1190.00,
        'credits_required' => null,
        'tag' => 'Mais pedido',
        'is_active' => true
    ],
    [
        'title' => 'Edição de Vídeos',
        'description' => 'Pacote de 4 vídeos curtos com cortes dinâmicos e legendas.',
        'thumbnail_url' => 'https://images.unsplash.com/photo-1518779578993-ec3579fee39f?q=80&w=1200&auto=format&fit=crop',
        'delivery_time' => '5–7 dias úteis',
        'price_type' => 'CREDITS',
        'price' => null,
        'credits_required' => 24,
        'tag' => null,
        'is_active' => true
    ],
    [
        'title' => 'Criação de Loja Virtual',
        'description' => 'Setup completo com tema profissional e integrações essenciais.',
        'thumbnail_url' => 'https://images.unsplash.com/photo-1557825835-a526494be845?q=80&w=1200&auto=format&fit=crop',
        'delivery_time' => '10–20 dias úteis',
        'price_type' => 'QUOTE',
        'price' => null,
        'credits_required' => null,
        'tag' => 'Projeto',
        'is_active' => true
    ]
];

foreach ($services as $serviceData) {
    $service = $serviceModel->create($serviceData);
    if ($service) {
        echo "Service {$serviceData['title']} created successfully\n";
    } else {
        echo "Failed to create service {$serviceData['title']}\n";
    }
}

// Create promotions
$promotionModel = new Promotion();
$promotions = [
    [
        'title' => 'Combo Carrossel + Reels — Lançamento',
        'description' => 'Pacote de 5 artes + 1 Reels otimizado para conversão.',
        'template_id' => 1,
        'original_credits' => 18,
        'discounted_credits' => 12,
        'preview_url' => 'https://images.unsplash.com/photo-1551554781-2f27f0d57a6a?q=80&w=1200&auto=format&fit=crop',
        'thumbnail_url' => 'https://images.unsplash.com/photo-1551554781-2f27f0d57a6a?q=80&w=480&auto=format&fit=crop',
        'tag' => 'Lançamento',
        'expires_at' => date('Y-m-d H:i:s', strtotime('+30 days')),
        'is_active' => true
    ],
    [
        'title' => 'Stories Interativos (3 peças)',
        'description' => 'Enquetes e CTAs prontos para aumentar o engajamento.',
        'template_id' => 4,
        'original_credits' => 9,
        'discounted_credits' => 6,
        'preview_url' => 'https://images.unsplash.com/photo-1511765224389-37f0e77cf0eb?q=80&w=1200&auto=format&fit=crop',
        'thumbnail_url' => 'https://images.unsplash.com/photo-1511765224389-37f0e77cf0eb?q=80&w=480&auto=format&fit=crop',
        'tag' => 'Em alta',
        'expires_at' => date('Y-m-d H:i:s', strtotime('+14 days')),
        'is_active' => true
    ]
];

foreach ($promotions as $promotionData) {
    $promotion = $promotionModel->create($promotionData);
    if ($promotion) {
        echo "Promotion {$promotionData['title']} created successfully\n";
    } else {
        echo "Failed to create promotion {$promotionData['title']}\n";
    }
}

echo "Database seeding completed!\n";
echo "Admin login: admin@feedperfeito.com / admin123\n";
echo "User login: andre@example.com / user123\n";
?>