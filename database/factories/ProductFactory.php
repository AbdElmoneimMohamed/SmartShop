<?php

declare(strict_types=1);

namespace Database\Factories;

use App\Modules\Product\Models\Product;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;
use Smpita\TypeAs\TypeAs;

/**
 * @extends Factory<Product>
 */
class ProductFactory extends Factory
{
    /**
     * Real e-commerce products (clothing, jewelry, electronics) with real photos,
     * sourced from a public fake-store demo catalog.
     *
     * @var array<int, array{name: string, description: string, price: float, image: string}>
     */
    private const array CATALOG = [
        ['name' => 'Fjallraven Foldsack No. 1 Backpack', 'description' => 'Your perfect pack for everyday use and walks in the forest. Stash your laptop (up to 15 inches) in the padded sleeve.', 'price' => 109.95, 'image' => 'https://fakestoreapi.com/img/81fPKd-2AYL._AC_SL1500_t.png'],
        ['name' => 'Mens Casual Premium Slim Fit T-Shirt', 'description' => 'Slim-fitting style, contrast raglan long sleeve, three-button henley placket, light weight & soft fabric for breathable and comfortable wearing.', 'price' => 22.30, 'image' => 'https://fakestoreapi.com/img/71-3HjGNDUL._AC_SY879._SX._UX._SY._UY_t.png'],
        ['name' => 'Mens Cotton Jacket', 'description' => 'Great outerwear jacket for Spring/Autumn/Winter, suitable for working, hiking, camping, or traveling.', 'price' => 55.99, 'image' => 'https://fakestoreapi.com/img/71li-ujtlUL._AC_UX679_t.png'],
        ['name' => 'Mens Casual Slim Fit Shirt', 'description' => 'The color could be slightly different between on the screen and in practice. Body builds vary by person, so review the size chart.', 'price' => 15.99, 'image' => 'https://fakestoreapi.com/img/71YXzeOuslL._AC_UY879_t.png'],
        ['name' => "John Hardy Women's Legends Naga Chain Bracelet", 'description' => "From our Legends Collection, the Naga was inspired by the mythical water dragon that protects the ocean's pearl.", 'price' => 695.00, 'image' => 'https://fakestoreapi.com/img/71pWzhdJNwL._AC_UL640_QL65_ML3_t.png'],
        ['name' => 'Solid Gold Petite Micropave Ring', 'description' => 'Satisfaction guaranteed. Return or exchange any order within 30 days. Designed and sold by Hafeez Center.', 'price' => 168.00, 'image' => 'https://fakestoreapi.com/img/61sbMiUnoGL._AC_UL640_QL65_ML3_t.png'],
        ['name' => 'White Gold Plated Princess Ring', 'description' => 'Classic created wedding engagement solitaire diamond promise ring for her.', 'price' => 9.99, 'image' => 'https://fakestoreapi.com/img/71YAIFU48IL._AC_UL640_QL65_ML3_t.png'],
        ['name' => 'Pierced Owl Rose Gold Plated Earrings', 'description' => 'Rose gold plated double flared tunnel plug earrings, made of 316L stainless steel.', 'price' => 10.99, 'image' => 'https://fakestoreapi.com/img/51UDEzMJVpL._AC_UL640_QL65_ML3_t.png'],
        ['name' => 'WD 2TB Elements Portable External Hard Drive', 'description' => 'USB 3.0 and USB 2.0 compatibility with fast data transfers to improve PC performance.', 'price' => 64.00, 'image' => 'https://fakestoreapi.com/img/61IBBVJvSDL._AC_SY879_t.png'],
        ['name' => 'SanDisk SSD PLUS 1TB Internal SSD', 'description' => 'Easy upgrade for faster boot up, shutdown, application load and response times.', 'price' => 109.00, 'image' => 'https://fakestoreapi.com/img/61U7T1koQqL._AC_SX679_t.png'],
        ['name' => 'Silicon Power 256GB SSD 3D NAND', 'description' => '3D NAND flash delivers high transfer speeds for faster bootup and improved system performance.', 'price' => 109.00, 'image' => 'https://fakestoreapi.com/img/71kWymZ+c+L._AC_SX679_t.png'],
        ['name' => 'WD 4TB Gaming Drive for PlayStation 4', 'description' => 'Expand your PS4 gaming experience and play anywhere with fast, easy setup.', 'price' => 114.00, 'image' => 'https://fakestoreapi.com/img/61mtL65D4cL._AC_SX679_t.png'],
        ['name' => 'Acer 21.5" Full HD IPS Monitor', 'description' => 'Full HD (1920 x 1080) widescreen IPS display with zero-frame design and ultra-thin profile.', 'price' => 599.00, 'image' => 'https://fakestoreapi.com/img/81QpkIctqPL._AC_SX679_t.png'],
        ['name' => 'Samsung 49" Curved Gaming Monitor', 'description' => '49 inch super ultrawide curved gaming monitor with Quantum Dot technology and 144Hz refresh rate.', 'price' => 999.99, 'image' => 'https://fakestoreapi.com/img/81Zt42ioCgL._AC_SX679_t.png'],
        ['name' => "Women's 3-in-1 Snowboard Jacket", 'description' => 'Detachable liner fleece jacket with adjustable and detachable hood, warm in cold weather.', 'price' => 56.99, 'image' => 'https://fakestoreapi.com/img/51Y5NI-I5jL._AC_UX679_t.png'],
        ['name' => "Women's Faux Leather Moto Biker Jacket", 'description' => 'Faux leather material for style and comfort, with a removable hood and detail stitching at the sides.', 'price' => 29.95, 'image' => 'https://fakestoreapi.com/img/81XH0e8fefL._AC_UY879_t.png'],
        ['name' => "Women's Windbreaker Rain Jacket", 'description' => 'Lightweight, perfect for trips or casual wear, with an adjustable drawstring waist and hood.', 'price' => 39.99, 'image' => 'https://fakestoreapi.com/img/71HblAHs5xL._AC_UY879_-2t.png'],
        ['name' => "Women's Solid Short Sleeve Boat Neck Top", 'description' => 'Lightweight fabric with great stretch for comfort, ribbed on sleeves and neckline.', 'price' => 9.85, 'image' => 'https://fakestoreapi.com/img/71z3kpMAYsL._AC_UY879_t.png'],
        ['name' => "Women's Short Sleeve Moisture Wicking Tee", 'description' => 'Lightweight, roomy and highly breathable with moisture wicking fabric that helps keep you dry.', 'price' => 7.95, 'image' => 'https://fakestoreapi.com/img/51eg55uWmdL._AC_UX679_t.png'],
        ['name' => "Women's Casual Cotton T-Shirt", 'description' => 'Soft fabric with some stretch, casual short sleeve fit for everyday wear.', 'price' => 12.99, 'image' => 'https://fakestoreapi.com/img/61pHAEJ4NML._AC_UX679_t.png'],
    ];

    protected $model = Product::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $item = self::CATALOG[array_rand(self::CATALOG)];

        return [
            'uuid' => (string) Str::uuid7(),
            'name' => TypeAs::string($item['name']),
            'description' => TypeAs::string($item['description']),
            'price' => TypeAs::float($item['price']),
            'image' => TypeAs::string($item['image']),
        ];
    }
}
