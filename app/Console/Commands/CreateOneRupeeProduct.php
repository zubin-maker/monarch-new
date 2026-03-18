<?php

namespace App\Console\Commands;

use App\Models\User\UserCurrency;
use App\Models\User\UserItem;
use App\Models\User\UserItemCategory;
use App\Models\User\UserItemContent;
use App\Models\User\Language as UserLanguage;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class CreateOneRupeeProduct extends Command
{
    protected $signature = 'dev:create-one-rupee-product
                            {user_id : The store owner user_id to create the product for}
                            {--title=One Rupee Test Product : Product title}
                            {--stock=9999 : Stock quantity}
                            {--price=1.00 : Price in INR (stored in current_price)}
                            {--type=physical : physical|digital}';

    protected $description = 'Create a ₹1 dummy product (user_items + user_item_contents) for checkout testing.';

    public function handle(): int
    {
        $userId = (int) $this->argument('user_id');
        $title = (string) $this->option('title');
        $stock = (int) $this->option('stock');
        $price = (float) $this->option('price');
        $type = (string) $this->option('type');

        if (!in_array($type, ['physical', 'digital'], true)) {
            $this->error('Invalid --type. Use physical or digital.');
            return self::FAILURE;
        }

        $currency = UserCurrency::where('user_id', $userId)->where('is_default', 1)->first();
        if (!$currency) {
            $currency = UserCurrency::where('user_id', $userId)->first();
        }

        $languages = UserLanguage::where('user_id', $userId)->get();
        if ($languages->isEmpty()) {
            $this->error("No languages found for user_id={$userId}. This store setup looks incomplete.");
            return self::FAILURE;
        }

        $sku = (string) random_int(1000000, 9999999);
        $baseSlug = Str::slug($title);
        $uniqueSuffix = Str::lower(Str::random(6));

        $item = DB::transaction(function () use ($userId, $stock, $sku, $price, $currency, $type, $languages, $title, $baseSlug, $uniqueSuffix) {
            $item = UserItem::create([
                'user_id' => $userId,
                'stock' => $stock,
                'sku' => $sku,
                'thumbnail' => null,
                'current_price' => $price,
                'previous_price' => null,
                'currency_id' => $currency?->id,
                'is_feature' => 1,
                'rating' => 0.00,
                'type' => $type,
                'download_link' => null,
                'download_file' => null,
                'status' => 1,
                'flash_amount' => null,
                'flash' => null,
                'background_color' => null,
            ]);

            foreach ($languages as $lang) {
                $categoryId = UserItemCategory::where('user_id', $userId)
                    ->where('language_id', $lang->id)
                    ->value('id');

                UserItemContent::create([
                    'user_id' => $userId,
                    'item_id' => $item->id,
                    'label_id' => null,
                    'language_id' => $lang->id,
                    'category_id' => $categoryId,
                    'subcategory_id' => null,
                    'title' => $title,
                    'slug' => $baseSlug . '-' . $uniqueSuffix,
                    'summary' => 'Test product for ₹1 checkout flow verification.',
                    'description' => '<p>Test product for ₹1 checkout flow verification.</p>',
                    'meta_keywords' => 'test, one-rupee, checkout',
                    'meta_description' => 'Test product for ₹1 checkout flow verification.',
                ]);
            }

            return $item;
        });

        $this->info("Created ₹{$price} test product:");
        $this->line(" - user_items.id: {$item->id}");
        $this->line(" - sku: {$item->sku}");
        $this->line('Next: visit your shop, add it to cart, and complete checkout.');

        return self::SUCCESS;
    }
}

