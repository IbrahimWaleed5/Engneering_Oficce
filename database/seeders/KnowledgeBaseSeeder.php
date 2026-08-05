<?php

namespace Database\Seeders;

use App\Models\KnowledgeBaseArticle;
use Illuminate\Database\Seeder;

class KnowledgeBaseSeeder extends Seeder
{
    public function run(): void
    {
        $articles = [
            [
                'question' =>
                    'كيف أرفع إيصال الدفع؟',
                'answer' =>
                    'ادخل إلى صفحة استشاراتي، ثم افتح الاستشارة المطلوبة واضغط على زر رفع إيصال الدفع.',
                'category' => 'payment',
                'keywords' =>
                    'دفع إيصال تحويل فاتورة سداد',
                'is_active' => true,
            ],
            [
                'question' =>
                    'كيف أتابع حالة الاستشارة؟',
                'answer' =>
                    'يمكنك متابعة حالة الاستشارة من صفحة استشاراتي داخل لوحة التحكم.',
                'category' => 'consultation',
                'keywords' =>
                    'استشارة متابعة حالة طلب',
                'is_active' => true,
            ],
            [
                'question' =>
                    'لماذا لم يتم اعتماد الدفع؟',
                'answer' =>
                    'قد يكون إيصال الدفع ما زال تحت المراجعة. تأكد من وضوح الإيصال وصحة قيمة التحويل.',
                'category' => 'payment',
                'keywords' =>
                    'اعتماد دفع معلق مراجعة إيصال',
                'is_active' => true,
            ],
            [
                'question' =>
                    'كيف أغير بيانات حسابي؟',
                'answer' =>
                    'ادخل إلى إعدادات الحساب، ثم عدّل البيانات المطلوبة واضغط حفظ.',
                'category' => 'account',
                'keywords' =>
                    'حساب بيانات تعديل اسم هاتف بريد',
                'is_active' => true,
            ],
            [
                'question' =>
                    'كيف أتواصل مع المهندس؟',
                'answer' =>
                    'بعد تعيين المهندس للاستشارة ستظهر لك المحادثة داخل صفحة الاستشارة.',
                'category' => 'consultation',
                'keywords' =>
                    'مهندس محادثة تواصل رسالة',
                'is_active' => true,
            ],
        ];

        foreach ($articles as $article) {
            KnowledgeBaseArticle::updateOrCreate(
                [
                    'question' => $article['question'],
                ],
                $article
            );
        }
    }
}
