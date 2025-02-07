<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Service>
 */
class ServiceFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $elements = [
            'Sağlık Destek',
            'Canlı Yaşam Programı',
            'Zinde Yaşam',
            'Hızlı İyileşme',
            'Bütünsel Sağlık',
            'Özel Terapiler',
            'Fiziksel Rehabilitasyon',
            'Yaşam Koçluğu',
            'Kişisel İyileşme',
            'Derin İyileşme',
            'Zihin ve Bedeni Dengele',
            'Güçlü Direnç Programı',
            'Yenilenme Terapisi',
            'Beslenme Danışmanlığı',
            'Hareket ve Aktivite',
            'Sağlık Değerlendirme',
            'Ruhsal Yenilenme',
            'Biyolojik Yaşam Desteği',
            'Esneklik Artırma',
            'İleri Seviye Sağlık Hizmetleri',
        ];

        return [
            'active' => true,
            'name' => $this->faker->randomElement($elements),
            'code' => $this->faker->postcode(),
            'price' => $this->faker->numberBetween(1000, 10000),
        ];
    }
}
