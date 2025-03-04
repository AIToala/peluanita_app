<?php

namespace Database\Seeders;

use App\Models\Servicio; 
use Illuminate\Database\Seeder;

class ServicioSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $servicios = [
            // 💇 Servicios de Corte y Peinado
            [
                'nombre' => 'Corte de cabello para hombres',
                'descripcion' => 'Corte de cabello con técnicas modernas, incluye lavado y estilizado.',
                'costo_base' => 15,
                'estado' => 1,
            ],
            [
                'nombre' => 'Corte de cabello para mujeres',
                'descripcion' => 'Corte personalizado según el tipo de rostro, incluye lavado y secado.',
                'costo_base' => 20,
                'estado' => 1,
            ],
            [
                'nombre' => 'Corte infantil',
                'descripcion' => 'Corte de cabello para niños, con un estilo divertido y práctico.',
                'costo_base' => 10,
                'estado' => 1,
            ],
            [
                'nombre' => 'Peinado y brushing',
                'descripcion' => 'Peinado con secador para dar volumen y forma al cabello.',
                'costo_base' => 20,
                'estado' => 1,
            ],
            [
                'nombre' => 'Alisado con plancha',
                'descripcion' => 'Alisado con plancha profesional para un acabado liso y brillante.',
                'costo_base' => 15,
                'estado' => 1,
            ],
            [
                'nombre' => 'Rizado con tenaza',
                'descripcion' => 'Creación de rizos definidos con herramientas de calor.',
                'costo_base' => 20,
                'estado' => 1,
            ],
            [
                'nombre' => 'Recorte de flequillo',
                'descripcion' => 'Ajuste y estilizado del flequillo para mantener la forma deseada.',
                'costo_base' => 5,
                'estado' => 1,
            ],

            // 🎨 Servicios de Coloración
            [
                'nombre' => 'Tinte completo',
                'descripcion' => 'Coloración permanente en todo el cabello con productos de alta calidad.',
                'costo_base' => 50,
                'estado' => 1,
            ],
            [
                'nombre' => 'Mechas o reflejos',
                'descripcion' => 'Aclarado parcial del cabello para dar dimensión y brillo.',
                'costo_base' => 70,
                'estado' => 1,
            ],
            [
                'nombre' => 'Balayage',
                'descripcion' => 'Técnica de coloración degradada para un look natural y sofisticado.',
                'costo_base' => 90,
                'estado' => 1,
            ],

            // 💆 Tratamientos Capilares
            [
                'nombre' => 'Hidratación profunda',
                'descripcion' => 'Tratamiento intensivo para restaurar la hidratación del cabello seco.',
                'costo_base' => 35,
                'estado' => 1,
            ],
            [
                'nombre' => 'Botox capilar',
                'descripcion' => 'Rejuvenecimiento del cabello con proteínas y vitaminas.',
                'costo_base' => 60,
                'estado' => 1,
            ],
            [
                'nombre' => 'Keratina',
                'descripcion' => 'Alisado semipermanente para reducir el frizz y mejorar la textura.',
                'costo_base' => 120,
                'estado' => 1,
            ],

            // 👰 Servicios Especiales
            [
                'nombre' => 'Peinado para bodas y eventos',
                'descripcion' => 'Peinado elaborado para ocasiones especiales.',
                'costo_base' => 80,
                'estado' => 1,
            ],
            [
                'nombre' => 'Extensiones de cabello',
                'descripcion' => 'Aplicación de extensiones naturales o sintéticas.',
                'costo_base' => 200,
                'estado' => 1,
            ],
            [
                'nombre' => 'Maquillaje profesional',
                'descripcion' => 'Maquillaje personalizado para bodas, fiestas o sesiones fotográficas.',
                'costo_base' => 70,
                'estado' => 1,
            ],

            // 🪒 Servicios para Hombres
            [
                'nombre' => 'Corte de barba',
                'descripcion' => 'Recorte y perfilado de barba para un look definido.',
                'costo_base' => 15,
                'estado' => 1,
            ],
            [
                'nombre' => 'Afeitado clásico con navaja',
                'descripcion' => 'Afeitado tradicional con toalla caliente y productos premium.',
                'costo_base' => 25,
                'estado' => 1,
            ],

            // 💅 Servicios Adicionales
            [
                'nombre' => 'Manicure y pedicure',
                'descripcion' => 'Cuidado de uñas con limpieza, limado y esmaltado.',
                'costo_base' => 35.25,
                'estado' => 1,
            ],
            [
                'nombre' => 'Diseño de cejas',
                'descripcion' => 'Depilación y moldeado de cejas con cera o hilo.',
                'costo_base' => 15.50,
                'estado' => 1,
            ],
        ];

        foreach ($servicios as $servicio) {
            Servicio::create($servicio);
        }
    }
}
