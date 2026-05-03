<?php namespace Tests\Feature;
use App\Models\User;
use App\Models\Exam;
use Tests\TestCase;
class ExamApiTest extends TestCase {
    protected $professor;
    protected function setUp(): void {
        parent::setUp();
        $this->professor = User::factory()->create(['role' => 'professor']);
    }
    public function test_can_list_exams() {
        $response = $this->getJson('/api/v1/exams');
        $response->assertStatus(200);
        $response->assertIsArray();
    }
    public function test_can_create_exam() {
        $data = [
            'professor_id' => $this->professor->id,
            'title' => 'Test Exam',
            'description' => 'Test Description',
            'total_questions' => 5,
            'passing_grade' => 60
        ];
        $response = $this->postJson('/api/v1/exams', $data);
        $response->assertStatus(201);
        $this->assertDatabaseHas('exams', ['title' => 'Test Exam']);
    }
    public function test_can_get_exam() {
        $exam = Exam::factory()->create();
        $response = $this->getJson("/api/v1/exams/{$exam->id}");
        $response->assertStatus(200);
        $response->assertJsonFragment(['title' => $exam->title]);
    }
    public function test_can_update_exam() {
        $exam = Exam::factory()->create();
        $data = ['title' => 'Updated Title'];
        $response = $this->putJson("/api/v1/exams/{$exam->id}", $data);
        $response->assertStatus(200);
        $this->assertDatabaseHas('exams', ['title' => 'Updated Title']);
    }
    public function test_can_delete_exam() {
        $exam = Exam::factory()->create();
        $response = $this->deleteJson("/api/v1/exams/{$exam->id}");
        $response->assertStatus(204);
        $this->assertSoftDeleted('exams', ['id' => $exam->id]);
    }
}
