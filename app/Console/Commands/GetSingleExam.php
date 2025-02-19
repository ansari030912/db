<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use GuzzleHttp\Client;
use Illuminate\Support\Facades\Storage;
use App\Models\Exam;
use App\Models\SingleExam;
use App\Models\ExamPrice;
use App\Models\TeImage;
use App\Models\QuestionType;
use App\Models\ExamTopic;
use App\Models\ExamFaq;

class GetSingleExam extends Command
{
    protected $signature = 'product:update';
    protected $description = 'Fetch exam details from external API for each exam in the exams table and update single_exam; save missing API exam_perma in local storage in real time';

    public function handle()
    {
        $errorExams = [];
        $exams = Exam::all();

        if ($exams->isEmpty()) {
            $this->info('No exams found in the database.');
            return 0;
        }

        $client = new Client();
        $apiKey = 'b46279cb-13bb-4445-a6f9-6f252b61ae79';

        foreach ($exams as $exam) {
            $exam_perma = $exam->exam_perma;
            $apiUrl = "https://certsgang.com/v1/exam/{$exam_perma}";

            try {
                $response = $client->get($apiUrl, [
                    'headers' => [
                        'x-api-key' => $apiKey,
                        'Accept' => 'application/json',
                    ],
                    'timeout' => 20,
                ]);

                $data = json_decode($response->getBody()->getContents(), true);

                if (!$data || !isset($data['exam_perma'])) {
                    $errorExams[] = $exam_perma;
                    Storage::disk('local')->put('missing_exam_permas.json', json_encode($errorExams));
                    $this->error("API returned null or missing exam_perma for '{$exam_perma}'");
                    continue;
                }

                $singleExam = SingleExam::updateOrCreate(
                    ['exam_perma' => $data['exam_perma']],
                    [
                        'exam_id' => $data['exam_id'],
                        'exam_code' => $data['exam_code'],
                        'exam_title' => $data['exam_title'],
                        'exam_questions' => $data['exam_questions'],
                        'exam_update_date' => $data['exam_update_date'],
                        'exam_pdf' => $data['exam_pdf'],
                        'exam_te' => $data['exam_te'],
                        'exam_sg' => $data['exam_sg'],
                        'vendor_id' => $data['exam_vendor_id'],
                        'vendor_title' => $data['exam_vendor_title'],
                        'vendor_perma' => $data['exam_vendor_perma'],
                        'exam_article' => $data['exam_article'],
                        'exam_pdf_price' => $data['exam_pdf_price'],
                        'exam_ete_price' => $data['exam_ete_price'],
                        'exam_sg_price' => $data['exam_sg_price'],
                        'exam_sc_price' => $data['exam_sc_price'],
                        'is_disabled' => $data['is_disabled'],
                        'index_tag' => $data['index_tag'],
                        'exam_preorder' => $data['exam_preorder'],
                        'exam_last_week_passed' => $data['exam_last_week_passed'],
                        'exam_last_week_average_score' => $data['exam_last_week_average_score'],
                        'exam_last_week_word_to_word' => $data['exam_last_week_word_to_word'],
                        'exam_certs' => json_encode($data['exam_certs'] ?? []),
                        'exam_training_course' => json_encode($data['exam_training_course'] ?? []),
                        'exam_redirect' => json_encode($data['exam_redirect'] ?? []),
                        'exam_alternate' => json_encode($data['exam_alternate'] ?? []),
                        'exam_retired' => $data['exam_retired'],
                    ]
                );

                $foreignKey = $singleExam->exam_id;

                foreach ($data['exam_prices'] ?? [] as $priceData) {
                    ExamPrice::updateOrCreate(
                        ['exam_id' => $foreignKey, 'type' => $priceData['type']],
                        [
                            'title' => $priceData['title'],
                            'cart' => $priceData['cart'],
                            'price' => $priceData['price'],
                            'full_price' => $priceData['full_price'],
                            'off' => $priceData['off'],
                        ]
                    );
                }

                foreach ($data['te_images'] ?? [] as $imgData) {
                    TeImage::updateOrCreate(
                        ['exam_id' => $foreignKey, 'te_img_id' => $imgData['te_img_id']],
                        ['te_img_src' => $imgData['te_img_src']]
                    );
                }

                foreach ($data['question_types'] ?? [] as $qtypeData) {
                    QuestionType::updateOrCreate(
                        ['exam_id' => $foreignKey, 'question_type' => $qtypeData['question_type']],
                        ['question_type_count' => $qtypeData['question_type_count']]
                    );
                }

                foreach ($data['exam_topics'] ?? [] as $topicData) {
                    ExamTopic::updateOrCreate(
                        ['exam_id' => $foreignKey, 'topic' => $topicData['topic']],
                        ['topic_questions' => $topicData['topic_questions']]
                    );
                }

                foreach ($data['exam_faqs'] ?? [] as $faqData) {
                    ExamFaq::updateOrCreate(
                        ['exam_id' => $foreignKey, 'faq_q' => $faqData['faq_q']],
                        ['faq_a' => $faqData['faq_a']]
                    );
                }

            } catch (\Exception $e) {
                $this->error("Error updating '{$exam_perma}': " . $e->getMessage());
                $errorExams[] = ['exam_perma' => $exam_perma, 'error' => $e->getMessage()];
                Storage::disk('local')->put('missing_exam_permas.json', json_encode($errorExams));
            }
        }

        Storage::disk('local')->put('missing_exam_permas.json', json_encode($errorExams));
        $this->info("Processing complete. Missing exam_permas saved to missing_exam_permas.json");
        return 0;
    }
}
