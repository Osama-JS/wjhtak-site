<?php

namespace Database\Seeders;

use App\Models\Question;
use Illuminate\Database\Seeder;

class QuestionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Question::create([
            'question' => 'كيف يمكنني حجز رحلة؟',
            'answer' => 'يمكنك حجز رحلة عن طريق اختيار الرحلة المناسبة من صفحة العروض والضغط على زر "احجز الآن" وإكمال خطوات الدفع.',
        ]);

        Question::create([
            'question' => 'هل يمكنني إلغاء الحجز واسترداد المبلغ؟',
            'answer' => 'نعم، يمكنك إلغاء الحجز قبل موعد الرحلة بـ 48 ساعة واسترداد المبلغ كاملاً، تطبق الشروط والأحكام.',
        ]);

        Question::create([
            'question' => 'ما هي طرق الدفع المتاحة؟',
            'answer' => 'نقبل الدفع عبر البطاقات الائتمانية (فيزا، ماستركارد) ومدى، بالإضافة إلى التحويل البنكي.',
        ]);

        Question::create([
            'question' => 'هل توجد خصومات للمجموعات؟',
            'answer' => 'نعم، نوفر خصومات خاصة للمجموعات التي تزيد عن 5 أشخاص. يرجى التواصل معنا للحصول على عرض سعر.',
        ]);
    }
}
