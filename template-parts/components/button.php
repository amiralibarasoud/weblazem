<?php

// ورودی‌ها با مقدار پیش‌فرض
$label = $label ?? 'دکمه تستی';
$url = $url ?? '#';
$type = $type ?? 'primary'; // primary, outline, danger, ...
$size = $size ?? 'md'; // sm, md, lg
$extra = $extra ?? ''; // کلاس اضافی

// تعریف کلاس‌های دکمه با Tailwind
$base_class = "inline-flex items-center justify-center rounded-xl font-medium transition-all duration-200 focus:outline-none";

$type_classes = [
    'primary' => 'bg-blue-600 text-white hover:bg-blue-700',
    'outline' => 'border border-blue-600 text-blue-600 hover:bg-blue-50',
    'danger' => 'bg-red-600 text-white hover:bg-red-700',
];

$size_classes = [
    'sm' => 'text-sm px-3 py-1.5',
    'md' => 'text-base px-4 py-2',
    'lg' => 'text-lg px-6 py-3',
];

// ترکیب نهایی کلاس‌ها
$class = "$base_class {$type_classes[$type]} {$size_classes[$size]} $extra";
?>

<a href="<?= esc_url($url); ?>" class="<?= esc_attr($class); ?>">
    <?= esc_html($label); ?>
</a>
