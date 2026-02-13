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
       $questions = [
            [
                'question_ar' => 'كيف يمكنني حجز رحلة؟',
                'question_en' => 'How can I book a trip?',
                'answer_ar' => 'يمكنك حجز رحلة عن طريق اختيار الرحلة المناسبة من صفحة العروض والضغط على زر "احجز الآن" وإكمال خطوات الدفع.',
                'answer_en' => 'You can book a trip by selecting the appropriate trip from the offers page, clicking the "Book Now" button, and completing the payment steps.',
            ],
            [
                'question_ar' => 'هل يمكنني إلغاء الحجز واسترداد المبلغ؟',
                'question_en' => 'Can I cancel the booking and get a refund?',
                'answer_ar' => 'نعم، يمكنك إلغاء الحجز قبل موعد الرحلة بـ 48 ساعة واسترداد المبلغ كاملاً، تطبق الشروط والأحكام.',
                'answer_en' => 'Yes, you can cancel your booking up to 48 hours before your flight and receive a full refund, terms and conditions apply.',
            ],
            [
                'question_ar' => 'ما هي طرق الدفع المتاحة؟',
                'question_en' => 'What payment methods are available?',
                'answer_ar' => 'نقبل الدفع عبر البطاقات الائتمانية (فيزا، ماستركارد) ومدى، بالإضافة إلى التحويل البنكي.',
                'answer_en' => 'We accept payment via credit cards (Visa, Mastercard) and Mada, in addition to bank transfer.',
            ],
            [
                'question_ar' => 'هل توجد خصومات للمجموعات؟',
                'question_en' => 'Are there group discounts?',
                'answer_ar' => 'نعم، نوفر خصومات خاصة للمجموعات التي تزيد عن 5 أشخاص. يرجى التواصل معنا للحصول على عرض سعر.',
                'answer_en' => 'Yes, we offer special discounts for groups of 5 or more. Please contact us for a quote.',
            ],
        ];

        foreach ($questions as $data) {
            Question::updateOrCreate(
                // 1. Search criteria (check if this question already exists)
                ['question_ar' => $data['question_ar']], 
                
                // 2. Data to insert or update (all other fields go here)
                [
                    'question_en' => $data['question_en'],
                    'answer_ar'   => $data['answer_ar'],   
                    'answer_en'   => $data['answer_en'],
                ]
            );
        }
    }
}
