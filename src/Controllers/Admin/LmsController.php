<?php

namespace App\Controllers\Admin;

use App\DB;
use App\Models\Course;
use App\Models\Certificate;
use PDO;

class LmsController
{
    private Course $courseModel;
    private Certificate $certModel;
    private PDO $db;

    public function __construct()
    {
        $this->courseModel = new Course();
        $this->certModel = new Certificate();
        $this->db = DB::connection();
    }

    // ========================================================
    // 1. DASHBOARD & THỐNG KÊ DOANH THU, HỌC VIÊN
    // ========================================================
    public function dashboard(): void
    {
        // Thống kê tổng số
        $totalStudents = (int) $this->db->query("SELECT COUNT(*) FROM users WHERE role = 'student' OR role = 'customer'")->fetchColumn();
        $totalInstructors = (int) $this->db->query("SELECT COUNT(*) FROM users WHERE role = 'instructor'")->fetchColumn();
        $totalCourses = (int) $this->db->query("SELECT COUNT(*) FROM courses")->fetchColumn();
        $totalCertificates = (int) $this->db->query("SELECT COUNT(*) FROM certificates WHERE status = 'valid'")->fetchColumn();

        // Doanh thu từ khóa học
        $revenueStmt = $this->db->query("SELECT COALESCE(SUM(total_amount), 0) FROM orders WHERE payment_status IN ('completed', 'paid') AND order_code LIKE 'LMS-%'");
        $totalRevenue = (float) $revenueStmt->fetchColumn();

        // Khóa học có nhiều học viên nhất
        $topCourses = $this->db->query("SELECT c.id, c.title, c.price, c.sale_price,
                       COUNT(e.id) AS student_count,
                       ROUND(COALESCE(AVG(e.progress_percent), 0), 1) AS avg_progress
                FROM courses c
                LEFT JOIN enrollments e ON e.course_id = c.id
                GROUP BY c.id, c.title, c.price, c.sale_price
                ORDER BY student_count DESC LIMIT 6")->fetchAll(PDO::FETCH_ASSOC);

        // Lượt nộp bài quiz gần đây
        $recentAttempts = $this->db->query("SELECT qa.*, u.name AS student_name, q.title AS quiz_title
                FROM quiz_attempts qa
                INNER JOIN users u ON u.id = qa.user_id
                INNER JOIN quizzes q ON q.id = qa.quiz_id
                ORDER BY qa.id DESC LIMIT 6")->fetchAll(PDO::FETCH_ASSOC);

        // Dữ liệu biểu đồ doanh thu theo khóa học (Top 5)
        $chartCourseLabels = [];
        $chartCourseData = [];
        foreach ($topCourses as $tc) {
            $chartCourseLabels[] = mb_substr($tc['title'], 0, 25) . '...';
            $chartCourseData[] = (int) $tc['student_count'];
        }

        view('admin.lms.dashboard', [
            'totalStudents' => $totalStudents,
            'totalInstructors' => $totalInstructors,
            'totalCourses' => $totalCourses,
            'totalCertificates' => $totalCertificates,
            'totalRevenue' => $totalRevenue,
            'topCourses' => $topCourses,
            'recentAttempts' => $recentAttempts,
            'chartLabels' => json_encode($chartCourseLabels, JSON_UNESCAPED_UNICODE),
            'chartData' => json_encode($chartCourseData),
            'title' => 'Quản trị LMS — Tổng quan Báo cáo & Doanh thu',
        ], 'layouts.admin');
    }

    // ========================================================
    // 2. QUẢN LÝ KHÓA HỌC (CRUD)
    // ========================================================
    public function courses(): void
    {
        $courses = $this->courseModel->all();

        view('admin.lms.courses.index', [
            'courses' => $courses,
            'title' => 'Quản lý Khóa học',
        ], 'layouts.admin');
    }

    public function createCourse(): void
    {
        $instructors = $this->db->query("SELECT id, name, email, role FROM users WHERE role = 'instructor' OR role = 'admin' ORDER BY CASE WHEN role = 'instructor' THEN 0 ELSE 1 END, name ASC")->fetchAll(PDO::FETCH_ASSOC);
        $categories = $this->db->query("SELECT id, name FROM categories WHERE status = 'active' OR status IS NULL ORDER BY name ASC")->fetchAll(PDO::FETCH_ASSOC);

        view('admin.lms.courses.create', [
            'instructors' => $instructors,
            'categories' => $categories,
            'title' => 'Thêm Khóa học mới',
        ], 'layouts.admin');
    }

    public function storeCourse(): void
    {
        try {
            $title = trim($_POST['title'] ?? '');
            if ($title === '') {
                flash('error', 'Vui lòng nhập tiêu đề khóa học.');
                redirect('/admin/lms/courses/create');
            }

            $rawSlug = trim($_POST['slug'] ?? '');
            $slug = $this->generateSlug($rawSlug !== '' ? $rawSlug : $title);
            $slug = $this->ensureUniqueSlug($slug);

            $instructorId = (int) ($_POST['instructor_id'] ?? authUser()['id']);
            $categoryId = !empty($_POST['category_id']) ? (int) $_POST['category_id'] : null;

            $price = max(0, (float) ($_POST['price'] ?? 0));
            $salePrice = (isset($_POST['sale_price']) && trim((string)$_POST['sale_price']) !== '') ? max(0, (float) $_POST['sale_price']) : null;
            if ($salePrice !== null && $price > 0 && $salePrice > $price) {
                $salePrice = $price;
            }

            $id = $this->courseModel->create([
                'instructor_id' => $instructorId,
                'category_id' => $categoryId,
                'title' => $title,
                'slug' => $slug,
                'summary' => trim($_POST['summary'] ?? ''),
                'description' => trim($_POST['description'] ?? ''),
                'thumbnail' => trim($_POST['thumbnail'] ?? '') ?: '/image/banner1.png',
                'price' => $price,
                'sale_price' => $salePrice,
                'level' => in_array($_POST['level'] ?? '', ['all', 'beginner', 'intermediate', 'advanced']) ? $_POST['level'] : 'all',
                'status' => in_array($_POST['status'] ?? '', ['published', 'draft', 'archived']) ? $_POST['status'] : 'published',
            ]);

            flash('success', 'Đã tạo khóa học thành công! Bạn có thể tiếp tục bổ sung chương học & bài giảng.');
            redirect('/admin/lms/courses/' . $id . '/curriculum');
        } catch (\Throwable $e) {
            flash('error', 'Lỗi khi tạo khóa học: ' . $e->getMessage());
            redirect('/admin/lms/courses/create');
        }
    }

    public function editCourse(int $id): void
    {
        $course = $this->courseModel->find($id);
        if (!$course) {
            flash('error', 'Khóa học không tồn tại.');
            redirect('/admin/lms/courses');
        }

        $instructors = $this->db->query("SELECT id, name, email, role FROM users WHERE role = 'instructor' OR role = 'admin' ORDER BY CASE WHEN role = 'instructor' THEN 0 ELSE 1 END, name ASC")->fetchAll(PDO::FETCH_ASSOC);
        $categories = $this->db->query("SELECT id, name FROM categories WHERE status = 'active' OR status IS NULL ORDER BY name ASC")->fetchAll(PDO::FETCH_ASSOC);

        view('admin.lms.courses.edit', [
            'course' => $course,
            'instructors' => $instructors,
            'categories' => $categories,
            'title' => 'Chỉnh sửa Khóa học: ' . $course['title'],
        ], 'layouts.admin');
    }

    public function updateCourse(int $id): void
    {
        try {
            $course = $this->courseModel->find($id);
            if (!$course) {
                flash('error', 'Khóa học không tồn tại.');
                redirect('/admin/lms/courses');
            }

            $title = trim($_POST['title'] ?? '');
            if ($title === '') {
                flash('error', 'Vui lòng nhập tiêu đề khóa học.');
                redirect('/admin/lms/courses/' . $id . '/edit');
            }

            $rawSlug = trim($_POST['slug'] ?? '');
            $slug = $this->generateSlug($rawSlug !== '' ? $rawSlug : ($course['slug'] ?: $title));
            $slug = $this->ensureUniqueSlug($slug, $id);

            $instructorId = (int) ($_POST['instructor_id'] ?? $course['instructor_id']);
            $categoryId = !empty($_POST['category_id']) ? (int) $_POST['category_id'] : null;

            $price = max(0, (float) ($_POST['price'] ?? 0));
            $salePrice = (isset($_POST['sale_price']) && trim((string)$_POST['sale_price']) !== '') ? max(0, (float) $_POST['sale_price']) : null;
            if ($salePrice !== null && $price > 0 && $salePrice > $price) {
                $salePrice = $price;
            }

            $this->courseModel->update($id, [
                'instructor_id' => $instructorId,
                'category_id' => $categoryId,
                'title' => $title,
                'slug' => $slug,
                'summary' => trim($_POST['summary'] ?? ''),
                'description' => trim($_POST['description'] ?? ''),
                'thumbnail' => trim($_POST['thumbnail'] ?? '') ?: ($course['thumbnail'] ?: '/image/banner1.png'),
                'price' => $price,
                'sale_price' => $salePrice,
                'level' => in_array($_POST['level'] ?? '', ['all', 'beginner', 'intermediate', 'advanced']) ? $_POST['level'] : 'all',
                'status' => in_array($_POST['status'] ?? '', ['published', 'draft', 'archived']) ? $_POST['status'] : 'published',
            ]);

            flash('success', 'Đã cập nhật thông tin khóa học thành công!');
            redirect('/admin/lms/courses');
        } catch (\Throwable $e) {
            flash('error', 'Lỗi khi cập nhật khóa học: ' . $e->getMessage());
            redirect('/admin/lms/courses/' . $id . '/edit');
        }
    }

    private function generateSlug(string $text): string
    {
        $unicode = [
            'a' => 'á|à|ả|ã|ạ|ă|ắ|ặ|ằ|ẳ|ẵ|â|ấ|ầ|ẩ|ẫ|ậ',
            'd' => 'đ',
            'e' => 'é|è|ẻ|ẽ|ẹ|ê|ế|ề|ể|ễ|ệ',
            'i' => 'í|ì|ỉ|ĩ|ị',
            'o' => 'ó|ò|ỏ|õ|ọ|ô|ố|ồ|ổ|ỗ|ộ|ơ|ớ|ờ|ở|ỡ|ợ',
            'u' => 'ú|ù|ủ|ũ|ụ|ư|ứ|ừ|ử|ữ|ự',
            'y' => 'ý|ỳ|ỷ|ỹ|ỵ',
            'A' => 'Á|À|Ả|Ã|Ạ|Ă|Ắ|Ặ|Ằ|Ẳ|Ẵ|Â|Ấ|Ầ|Ẩ|Ẫ|Ậ',
            'D' => 'Đ',
            'E' => 'É|È|Ả|Ẽ|Ẹ|Ê|Ế|Ề|Ể|Ễ|Ệ',
            'I' => 'Í|Ỉ|Ĩ|Ị',
            'O' => 'Ó|Ò|Ỏ|Õ|Ọ|Ô|Ố|Ồ|Ổ|Ỗ|Ộ|Ơ|Ớ|Ờ|Ở|Ỡ|Ợ',
            'U' => 'Ú|Ù|Ủ|Ũ|Ụ|Ư|Ứ|Ừ|Ử|Ữ|Ự',
            'Y' => 'Ý|Ỳ|Ỷ|Ỹ|Ỵ',
        ];
        foreach ($unicode as $nonAccent => $pattern) {
            $text = preg_replace("/($pattern)/u", $nonAccent, $text);
        }
        $text = strtolower(trim($text));
        $text = preg_replace('/[^a-z0-9-]+/', '-', $text);
        $text = preg_replace('/-+/', '-', $text);
        $text = trim($text, '-');
        return $text !== '' ? $text : 'khoa-hoc-' . time();
    }

    private function ensureUniqueSlug(string $slug, ?int $excludeId = null): string
    {
        $base = $slug;
        $counter = 1;
        while (true) {
            if ($excludeId !== null) {
                $stmt = $this->db->prepare("SELECT id FROM courses WHERE slug = ? AND id != ? LIMIT 1");
                $stmt->execute([$slug, $excludeId]);
            } else {
                $stmt = $this->db->prepare("SELECT id FROM courses WHERE slug = ? LIMIT 1");
                $stmt->execute([$slug]);
            }

            if (!$stmt->fetchColumn()) {
                return $slug;
            }

            $slug = $base . '-' . $counter++;
        }
    }

    public function deleteCourse(int $id): void
    {
        $this->courseModel->delete($id);
        flash('success', 'Đã xóa khóa học thành công.');
        redirect('/admin/lms/courses');
    }

    // ========================================================
    // 3. QUẢN LÝ GIÁO TRÌNH (CHƯƠNG, BÀI HỌC, VIDEO, TÀI LIỆU)
    // ========================================================
    public function curriculum(int $courseId): void
    {
        $course = $this->courseModel->find($courseId);
        if (!$course) {
            flash('error', 'Không tìm thấy khóa học.');
            redirect('/admin/lms/courses');
        }

        $curriculum = $this->courseModel->getCurriculum($courseId);

        view('admin.lms.courses.curriculum', [
            'course' => $course,
            'curriculum' => $curriculum,
            'title' => 'Quản lý Giáo trình: ' . $course['title'],
        ], 'layouts.admin');
    }

    public function storeSection(int $courseId): void
    {
        $title = trim($_POST['title'] ?? '');
        if ($title !== '') {
            $stmt = $this->db->prepare("INSERT INTO sections (course_id, title, sort_order) VALUES (?, ?, ?)");
            $sortOrder = (int) $this->db->query("SELECT COALESCE(MAX(sort_order), 0) + 1 FROM sections WHERE course_id = {$courseId}")->fetchColumn();
            $stmt->execute([$courseId, $title, $sortOrder]);
            flash('success', 'Đã thêm chương học mới!');
        }

        redirect('/admin/lms/courses/' . $courseId . '/curriculum');
    }

    public function deleteSection(int $courseId, int $sectionId): void
    {
        $this->db->prepare("DELETE FROM sections WHERE id = ? AND course_id = ?")->execute([$sectionId, $courseId]);
        $this->courseModel->updateCounters($courseId);
        flash('success', 'Đã xóa chương học.');
        redirect('/admin/lms/courses/' . $courseId . '/curriculum');
    }

    public function storeLesson(int $courseId): void
    {
        $sectionId = (int) ($_POST['section_id'] ?? 0);
        $title = trim($_POST['title'] ?? '');
        $lessonType = $_POST['lesson_type'] ?? 'video';
        $videoUrl = trim($_POST['video_url'] ?? '');
        $durationSeconds = (int) ($_POST['duration_seconds'] ?? 600);
        $content = trim($_POST['content'] ?? '');
        $isPreview = !empty($_POST['is_preview']) ? 1 : 0;

        if ($sectionId && $title !== '') {
            $sortOrder = (int) $this->db->query("SELECT COALESCE(MAX(sort_order), 0) + 1 FROM lessons WHERE section_id = {$sectionId}")->fetchColumn();
            $stmt = $this->db->prepare("INSERT INTO lessons (section_id, title, slug, lesson_type, content, is_preview, sort_order) VALUES (?, ?, ?, ?, ?, ?, ?)");
            $slug = strtolower(trim(preg_replace('/[^A-Za-z0-9-]+/', '-', $title)));
            $stmt->execute([$sectionId, $title, $slug, $lessonType, $content, $isPreview, $sortOrder]);
            $lessonId = (int) $this->db->lastInsertId();

            if ($lessonType === 'video' && $videoUrl !== '') {
                $vStmt = $this->db->prepare("INSERT INTO lesson_videos (lesson_id, provider, video_url, duration_seconds) VALUES (?, 'youtube', ?, ?)");
                $vStmt->execute([$lessonId, $videoUrl, $durationSeconds]);
            }

            // Nếu là quiz bài học
            if ($lessonType === 'quiz') {
                $qStmt = $this->db->prepare("INSERT INTO quizzes (course_id, lesson_id, title, description, duration_minutes, passing_score, max_attempts) VALUES (?, ?, ?, ?, 15, 80.00, 3)");
                $qStmt->execute([$courseId, $lessonId, 'Kiểm tra: ' . $title, 'Bài kiểm tra đánh giá kiến thức bài học.']);
            }

            $this->courseModel->updateCounters($courseId);
            flash('success', 'Đã thêm bài học mới thành công!');
        }

        redirect('/admin/lms/courses/' . $courseId . '/curriculum');
    }

    public function deleteLesson(int $courseId, int $lessonId): void
    {
        $this->db->prepare("DELETE FROM lessons WHERE id = ?")->execute([$lessonId]);
        $this->courseModel->updateCounters($courseId);
        flash('success', 'Đã xóa bài học.');
        redirect('/admin/lms/courses/' . $courseId . '/curriculum');
    }

    public function storeMaterial(int $courseId, int $lessonId): void
    {
        $title = trim($_POST['title'] ?? '');
        $fileType = trim($_POST['file_type'] ?? 'pdf');

        if ($title !== '') {
            $stmt = $this->db->prepare("INSERT INTO lesson_materials (lesson_id, title, file_path, file_type, file_size_bytes) VALUES (?, ?, ?, ?, ?)");
            $stmt->execute([$lessonId, $title, '/storage/materials/' . $title, $fileType, 1048576]);
            flash('success', 'Đã đính kèm tài liệu vào bài học!');
        }

        redirect('/admin/lms/courses/' . $courseId . '/curriculum');
    }

    // ========================================================
    // 4. QUẢN LÝ BÀI KIỂM TRA & CÂU HỎI / ĐÁP ÁN (QUIZ ENGINE)
    // ========================================================
    public function quizzes(): void
    {
        $quizzes = $this->db->query("SELECT q.*, c.title AS course_title, l.title AS lesson_title,
                       (SELECT COUNT(*) FROM quiz_questions qq WHERE qq.quiz_id = q.id) AS question_count,
                       (SELECT COUNT(*) FROM quiz_attempts qa WHERE qa.quiz_id = q.id) AS attempt_count
                FROM quizzes q
                INNER JOIN courses c ON c.id = q.course_id
                LEFT JOIN lessons l ON l.id = q.lesson_id
                ORDER BY q.id DESC")->fetchAll(PDO::FETCH_ASSOC);

        view('admin.lms.quizzes.index', [
            'quizzes' => $quizzes,
            'title' => 'Quản lý Bài Kiểm Tra & Câu Hỏi',
        ], 'layouts.admin');
    }

    public function quizQuestions(int $quizId): void
    {
        $stmt = $this->db->prepare("SELECT q.*, c.title AS course_title FROM quizzes q INNER JOIN courses c ON c.id = q.course_id WHERE q.id = ?");
        $stmt->execute([$quizId]);
        $quiz = $stmt->fetch(PDO::FETCH_ASSOC);

        if (!$quiz) {
            flash('error', 'Bài kiểm tra không tồn tại.');
            redirect('/admin/lms/quizzes');
        }

        // Lấy câu hỏi và đáp án
        $qModel = new \App\Models\Quiz();
        $questions = $qModel->getQuestions($quizId, true);

        view('admin.lms.quizzes.questions', [
            'quiz' => $quiz,
            'questions' => $questions,
            'title' => 'Ngân hàng Câu hỏi: ' . $quiz['title'],
        ], 'layouts.admin');
    }

    public function storeQuestion(int $quizId): void
    {
        $questionText = trim($_POST['question_text'] ?? '');
        $points = (float) ($_POST['points'] ?? 1.0);
        $explanation = trim($_POST['explanation'] ?? '');
        $answers = $_POST['answers'] ?? [];
        $correctIndex = (int) ($_POST['correct_index'] ?? 0);

        if ($questionText !== '' && !empty($answers)) {
            $sortOrder = (int) $this->db->query("SELECT COALESCE(MAX(sort_order), 0) + 1 FROM quiz_questions WHERE quiz_id = {$quizId}")->fetchColumn();

            $stmt = $this->db->prepare("INSERT INTO quiz_questions (quiz_id, question_text, question_type, points, explanation, sort_order) VALUES (?, ?, 'single_choice', ?, ?, ?)");
            $stmt->execute([$quizId, $questionText, $points, $explanation, $sortOrder]);
            $questionId = (int) $this->db->lastInsertId();

            $ansStmt = $this->db->prepare("INSERT INTO quiz_answers (question_id, answer_text, is_correct) VALUES (?, ?, ?)");
            foreach ($answers as $idx => $ansText) {
                $text = trim($ansText);
                if ($text !== '') {
                    $isCorrect = ($idx === $correctIndex) ? 1 : 0;
                    $ansStmt->execute([$questionId, $text, $isCorrect]);
                }
            }

            flash('success', 'Đã thêm câu hỏi và đáp án mới vào bài kiểm tra!');
        }

        redirect('/admin/lms/quizzes/' . $quizId . '/questions');
    }

    public function deleteQuestion(int $quizId, int $questionId): void
    {
        $this->db->prepare("DELETE FROM quiz_questions WHERE id = ? AND quiz_id = ?")->execute([$questionId, $quizId]);
        flash('success', 'Đã xóa câu hỏi.');
        redirect('/admin/lms/quizzes/' . $quizId . '/questions');
    }

    // ========================================================
    // 5. QUẢN LÝ KẾT QUẢ THI CỦA HỌC VIÊN
    // ========================================================
    public function quizResults(): void
    {
        $results = $this->db->query("SELECT qa.*, u.name AS student_name, u.email AS student_email,
                       q.title AS quiz_title, q.passing_score, c.title AS course_title
                FROM quiz_attempts qa
                INNER JOIN users u ON u.id = qa.user_id
                INNER JOIN quizzes q ON q.id = qa.quiz_id
                INNER JOIN courses c ON c.id = q.course_id
                ORDER BY qa.id DESC")->fetchAll(PDO::FETCH_ASSOC);

        view('admin.lms.quizzes.results', [
            'results' => $results,
            'title' => 'Quản lý Kết quả Thi của Học viên',
        ], 'layouts.admin');
    }

    public function quizResultDetail(int $attemptId): void
    {
        $stmt = $this->db->prepare("SELECT qa.*, u.name AS student_name, u.email AS student_email,
                       q.title AS quiz_title, q.passing_score, c.title AS course_title
                FROM quiz_attempts qa
                INNER JOIN users u ON u.id = qa.user_id
                INNER JOIN quizzes q ON q.id = qa.quiz_id
                INNER JOIN courses c ON c.id = q.course_id
                WHERE qa.id = ?");
        $stmt->execute([$attemptId]);
        $attempt = $stmt->fetch(PDO::FETCH_ASSOC);

        if (!$attempt) {
            flash('error', 'Lượt làm bài không tồn tại.');
            redirect('/admin/lms/quiz-results');
        }

        // Lấy danh sách câu trả lời của bài làm này
        $ansStmt = $this->db->prepare("SELECT qaa.*, qq.question_text, qq.explanation, qa.answer_text AS chosen_answer,
                       (SELECT qa2.answer_text FROM quiz_answers qa2 WHERE qa2.question_id = qq.id AND qa2.is_correct = 1 LIMIT 1) AS correct_answer
                FROM quiz_attempt_answers qaa
                INNER JOIN quiz_questions qq ON qq.id = qaa.question_id
                INNER JOIN quiz_answers qa ON qa.id = qaa.answer_id
                WHERE qaa.attempt_id = ?");
        $ansStmt->execute([$attemptId]);
        $details = $ansStmt->fetchAll(PDO::FETCH_ASSOC);

        view('admin.lms.quizzes.result_detail', [
            'attempt' => $attempt,
            'details' => $details,
            'title' => 'Chi tiết Bài thi #' . $attemptId . ' — ' . $attempt['student_name'],
        ], 'layouts.admin');
    }

    // ========================================================
    // 6. QUẢN LÝ HỌC VIÊN & GIẢNG VIÊN
    // ========================================================
    public function students(): void
    {
        $students = $this->db->query("SELECT u.*,
                       COUNT(e.id) AS enrolled_courses_count,
                       COUNT(cert.id) AS certificates_count
                FROM users u
                LEFT JOIN enrollments e ON e.user_id = u.id
                LEFT JOIN certificates cert ON cert.user_id = u.id
                WHERE u.role = 'student' OR u.role = 'customer'
                GROUP BY u.id
                ORDER BY u.id DESC")->fetchAll(PDO::FETCH_ASSOC);

        view('admin.lms.students.index', [
            'students' => $students,
            'title' => 'Quản lý Học viên',
        ], 'layouts.admin');
    }

    public function instructors(): void
    {
        $instructors = $this->db->query("SELECT u.*, ip.headline, ip.qualification, ip.experience_years, ip.bio,
                       COUNT(c.id) AS total_courses
                FROM users u
                LEFT JOIN instructor_profiles ip ON ip.user_id = u.id
                LEFT JOIN courses c ON c.instructor_id = u.id
                WHERE u.role = 'instructor'
                GROUP BY u.id, ip.headline, ip.qualification, ip.experience_years, ip.bio
                ORDER BY u.id DESC")->fetchAll(PDO::FETCH_ASSOC);

        view('admin.lms.instructors.index', [
            'instructors' => $instructors,
            'title' => 'Quản lý Giảng viên',
        ], 'layouts.admin');
    }

    public function storeInstructor(): void
    {
        try {
            $name = trim($_POST['name'] ?? '');
            $email = trim($_POST['email'] ?? '');
            $phone = trim($_POST['phone'] ?? '');
            $password = trim($_POST['password'] ?? '');
            $headline = trim($_POST['headline'] ?? '');
            $qualification = trim($_POST['qualification'] ?? '');
            $experienceYears = max(0, (int) ($_POST['experience_years'] ?? 1));
            $bio = trim($_POST['bio'] ?? '');

            if ($name === '' || $email === '') {
                flash('error', 'Họ tên và email không được để trống.');
                redirect('/admin/lms/instructors');
            }

            if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
                flash('error', 'Email không đúng định dạng.');
                redirect('/admin/lms/instructors');
            }

            // Check email uniqueness
            $checkStmt = $this->db->prepare("SELECT id FROM users WHERE email = ? LIMIT 1");
            $checkStmt->execute([$email]);
            if ($checkStmt->fetchColumn()) {
                flash('error', 'Email này đã tồn tại trên hệ thống. Vui lòng chọn email khác.');
                redirect('/admin/lms/instructors');
            }

            $passHash = password_hash($password !== '' ? $password : '123456', PASSWORD_BCRYPT);
            $uStmt = $this->db->prepare("INSERT INTO users (name, email, phone, password, role, status) VALUES (?, ?, ?, ?, 'instructor', 'active')");
            $uStmt->execute([$name, $email, $phone ?: '0900000000', $passHash]);
            $userId = (int) $this->db->lastInsertId();

            $pStmt = $this->db->prepare("INSERT INTO instructor_profiles (user_id, headline, qualification, experience_years, bio) VALUES (?, ?, ?, ?, ?)");
            $pStmt->execute([
                $userId,
                $headline ?: 'Chuyên gia & Giảng viên Đào tạo',
                $qualification ?: 'Cử nhân / Kỹ sư Công nghệ',
                $experienceYears,
                $bio ?: 'Giảng viên giàu kinh nghiệm thực chiến trong ngành.'
            ]);

            flash('success', "Đã thêm giảng viên '{$name}' thành công!");
            redirect('/admin/lms/instructors');
        } catch (\Throwable $e) {
            flash('error', 'Lỗi khi thêm giảng viên: ' . $e->getMessage());
            redirect('/admin/lms/instructors');
        }
    }

    public function updateInstructor(int $id): void
    {
        try {
            $name = trim($_POST['name'] ?? '');
            $phone = trim($_POST['phone'] ?? '');
            $status = in_array($_POST['status'] ?? '', ['active', 'inactive', 'banned']) ? $_POST['status'] : 'active';
            $headline = trim($_POST['headline'] ?? '');
            $qualification = trim($_POST['qualification'] ?? '');
            $experienceYears = max(0, (int) ($_POST['experience_years'] ?? 0));
            $bio = trim($_POST['bio'] ?? '');

            if ($name === '') {
                flash('error', 'Họ tên giảng viên không được để trống.');
                redirect('/admin/lms/instructors');
            }

            $stmt = $this->db->prepare("UPDATE users SET name = ?, phone = ?, status = ? WHERE id = ? AND role = 'instructor'");
            $stmt->execute([$name, $phone, $status, $id]);

            $pStmt = $this->db->prepare("INSERT INTO instructor_profiles (user_id, headline, qualification, experience_years, bio)
                VALUES (?, ?, ?, ?, ?)
                ON DUPLICATE KEY UPDATE headline = VALUES(headline), qualification = VALUES(qualification), experience_years = VALUES(experience_years), bio = VALUES(bio)");
            $pStmt->execute([$id, $headline, $qualification, $experienceYears, $bio]);

            flash('success', "Đã cập nhật thông tin giảng viên thành công!");
            redirect('/admin/lms/instructors');
        } catch (\Throwable $e) {
            flash('error', 'Lỗi cập nhật giảng viên: ' . $e->getMessage());
            redirect('/admin/lms/instructors');
        }
    }

    public function deleteInstructor(int $id): void
    {
        try {
            $courseCount = (int) $this->db->query("SELECT COUNT(*) FROM courses WHERE instructor_id = {$id}")->fetchColumn();
            if ($courseCount > 0) {
                flash('error', "Không thể xóa giảng viên này vì đang phụ trách {$courseCount} khóa học. Vui lòng chuyển giao khóa học trước!");
                redirect('/admin/lms/instructors');
            }

            $this->db->prepare("DELETE FROM instructor_profiles WHERE user_id = ?")->execute([$id]);
            $this->db->prepare("DELETE FROM users WHERE id = ? AND role = 'instructor'")->execute([$id]);

            flash('success', 'Đã xóa giảng viên thành công!');
            redirect('/admin/lms/instructors');
        } catch (\Throwable $e) {
            flash('error', 'Lỗi khi xóa giảng viên: ' . $e->getMessage());
            redirect('/admin/lms/instructors');
        }
    }

    // ========================================================
    // 7. QUẢN LÝ CHỨNG CHỈ (CẤP PHÁT / THU HỒI)
    // ========================================================
    public function certificates(): void
    {
        $certs = $this->certModel->all();
        $students = $this->db->query("SELECT id, name, email FROM users WHERE role IN ('student', 'customer') ORDER BY name ASC")->fetchAll(PDO::FETCH_ASSOC);
        $courses = $this->db->query("SELECT id, title FROM courses ORDER BY title ASC")->fetchAll(PDO::FETCH_ASSOC);

        view('admin.lms.certificates.index', [
            'certificates' => $certs,
            'students' => $students,
            'courses' => $courses,
            'title' => 'Quản lý Chứng chỉ Cấp phát',
        ], 'layouts.admin');
    }

    public function toggleCertificate(int $id): void
    {
        $stmt = $this->db->prepare("SELECT status FROM certificates WHERE id = ?");
        $stmt->execute([$id]);
        $current = $stmt->fetchColumn();

        if ($current) {
            $newStatus = ($current === 'valid') ? 'revoked' : 'valid';
            $upd = $this->db->prepare("UPDATE certificates SET status = ? WHERE id = ?");
            $upd->execute([$newStatus, $id]);
            flash('success', "Đã đổi trạng thái chứng chỉ sang: " . ($newStatus === 'valid' ? 'Hợp lệ' : 'Đã thu hồi'));
        }

        redirect('/admin/lms/certificates');
    }

    public function issueCertificateManual(): void
    {
        $userId = (int) ($_POST['user_id'] ?? 0);
        $courseId = (int) ($_POST['course_id'] ?? 0);

        if ($userId && $courseId) {
            $check = $this->db->prepare("SELECT id FROM certificates WHERE user_id = ? AND course_id = ?");
            $check->execute([$userId, $courseId]);
            if ($check->fetchColumn()) {
                flash('error', 'Học viên này đã có chứng chỉ cho khóa học đã chọn.');
            } else {
                $code = 'CERT-' . date('Y') . '-' . strtoupper(substr(md5($userId . '-' . $courseId . '-' . time()), 0, 8));
                $stmt = $this->db->prepare("INSERT INTO certificates (certificate_code, user_id, course_id, status) VALUES (?, ?, ?, 'valid')");
                $stmt->execute([$code, $userId, $courseId]);

                // Đảm bảo có enrollment 100%
                $this->db->prepare("INSERT INTO enrollments (user_id, course_id, status, progress_percent, completed_at) VALUES (?, ?, 'active', 100.00, NOW()) ON DUPLICATE KEY UPDATE progress_percent = 100.00")->execute([$userId, $courseId]);

                flash('success', "Đã cấp chứng chỉ thành công! Mã: {$code}");
            }
        }

        redirect('/admin/lms/certificates');
    }
}
