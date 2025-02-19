<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use GuzzleHttp\Client;
use Illuminate\Support\Facades\Storage;
use App\Models\Exam; // Model for the exams table
use App\Models\SingleProduct;
use App\Models\ExamPrice;
use App\Models\TeImage;
use App\Models\QuestionType;
use App\Models\ExamTopic;
use App\Models\ExamFaq;

class UpdateExams extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'product:update';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Fetch exam details from external API for each exam in the exams table and update single_products; save missing API exam_perma in local storage in real time';

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        // Initialize the error array.
        $errorExams = [];

        // Get all exams from the exams table.
        $exams = Exam::all();

        if ($exams->isEmpty()) {
            $this->info('No exams found in the exams table.');
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
                        'Accept'    => 'application/json',
                    ],
                    'timeout' => 20,
                ]);

                $data = json_decode($response->getBody()->getContents(), true);

                if (!$data) {
                    $this->error("Invalid JSON response for exam_perma: {$exam_perma}");
                    continue;
                }

                // If the API response doesn't provide a valid exam_perma, record the error and update local storage immediately.
                if (!isset($data['exam_perma']) || empty($data['exam_perma'])) {
                    $errorExams[] = $exam->exam_perma;
                    Storage::disk('local')->put('missing_exam_permas.json', json_encode($errorExams));
                    $this->error("API returned null exam_perma for exam '{$exam->exam_perma}'. Error array updated.");
                    continue;
                }

                $apiExamPerma = $data['exam_perma'];

                // Update or create the main record in single_products.
                $singleProduct = SingleProduct::updateOrCreate(
                    ['exam_perma' => $apiExamPerma],
                    [
                        'exam_id'                      => $data['exam_id'],
                        'exam_code'                    => $data['exam_code'],
                        'exam_title'                   => $data['exam_title'],
                        'exam_questions'               => $data['exam_questions'],
                        'exam_update_date'             => $data['exam_update_date'],
                        'exam_pdf'                     => $data['exam_pdf'],
                        'exam_te'                      => $data['exam_te'],
                        'exam_sg'                      => $data['exam_sg'],
                        'exam_vendor_id'               => $data['exam_vendor_id'],
                        'exam_vendor_title'            => $data['exam_vendor_title'],
                        'exam_vendor_perma'            => $data['exam_vendor_perma'],
                        'exam_article'                 => $data['exam_article'],
                        'exam_pdf_price'               => $data['exam_pdf_price'],
                        'exam_ete_price'               => $data['exam_ete_price'],
                        'exam_sg_price'                => $data['exam_sg_price'],
                        'exam_sc_price'                => $data['exam_sc_price'],
                        'is_disabled'                  => $data['is_disabled'],
                        'index_tag'                    => $data['index_tag'],
                        'exam_preorder'                => $data['exam_preorder'],
                        'exam_last_week_passed'        => $data['exam_last_week_passed'],
                        'exam_last_week_average_score' => $data['exam_last_week_average_score'],
                        'exam_last_week_word_to_word'  => $data['exam_last_week_word_to_word'],
                        'exam_certs'                   => isset($data['exam_certs']) ? json_encode($data['exam_certs']) : null,
                        'exam_training_course'         => isset($data['exam_training_course']) ? json_encode($data['exam_training_course']) : null,
                        'exam_redirect'                => isset($data['exam_redirect']) ? json_encode($data['exam_redirect']) : null,
                        'exam_alternate'               => isset($data['exam_alternate']) ? json_encode($data['exam_alternate']) : null,
                        'exam_retired'                 => $data['exam_retired'],
                    ]
                );

                $this->info("Updated single product for exam '{$apiExamPerma}'.");

                // Use the API's exam_id as the foreign key for child tables.
                $foreignKey = $singleProduct->exam_id;

                // Update exam prices.
                if (isset($data['exam_prices']) && is_array($data['exam_prices'])) {
                    foreach ($data['exam_prices'] as $priceData) {
                        ExamPrice::updateOrCreate(
                            [
                                'exam_id' => $foreignKey,
                                'type'    => $priceData['type']
                            ],
                            [
                                'title'      => $priceData['title'],
                                'cart'       => $priceData['cart'],
                                'price'      => $priceData['price'],
                                'full_price' => $priceData['full_price'],
                                'off'        => $priceData['off']
                            ]
                        );
                    }
                    $this->info("Updated exam prices for '{$apiExamPerma}'.");
                }

                if (isset($data['te_images']) && is_array($data['te_images'])) {
                    foreach ($data['te_images'] as $imgData) {
                        TeImage::updateOrCreate(
                            [
                                'exam_id'   => $foreignKey,
                                'te_img_id' => $imgData['te_img_id']
                            ],
                            [
                                'te_img_src' => $imgData['te_img_src']
                            ]
                        );
                    }
                    $this->info("Updated TE images for '{$apiExamPerma}'.");
                }

                if (isset($data['question_types']) && is_array($data['question_types'])) {
                    foreach ($data['question_types'] as $qtypeData) {
                        QuestionType::updateOrCreate(
                            [
                                'exam_id'       => $foreignKey,
                                'question_type' => $qtypeData['question_type']
                            ],
                            [
                                'question_type_count' => $qtypeData['question_type_count']
                            ]
                        );
                    }
                    $this->info("Updated question types for '{$apiExamPerma}'.");
                }

                if (isset($data['exam_topics']) && is_array($data['exam_topics'])) {
                    foreach ($data['exam_topics'] as $topicData) {
                        ExamTopic::updateOrCreate(
                            [
                                'exam_id' => $foreignKey,
                                'topic'   => $topicData['topic']
                            ],
                            [
                                'topic_questions' => $topicData['topic_questions']
                            ]
                        );
                    }
                    $this->info("Updated exam topics for '{$apiExamPerma}'.");
                }

                if (isset($data['exam_faqs']) && is_array($data['exam_faqs'])) {
                    foreach ($data['exam_faqs'] as $faqData) {
                        ExamFaq::updateOrCreate(
                            [
                                'exam_id' => $foreignKey,
                                'faq_q'   => $faqData['faq_q']
                            ],
                            [
                                'faq_a' => $faqData['faq_a']
                            ]
                        );
                    }
                    $this->info("Updated exam FAQs for '{$apiExamPerma}'.");
                }
            } catch (\Exception $e) {
                $this->error("Error updating exam '{$exam_perma}': " . $e->getMessage());
            }
        }

        // Save the errorExams array to local storage as a JSON file.
        Storage::disk('local')->put('missing_exam_permas.json', json_encode($errorExams));
        $this->info("Exams with missing API exam_perma saved to storage/app/missing_exam_permas.json");
        $this->info("All exams processed.");
        return 0;
    }
}
