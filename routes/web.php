<?php
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\Auth\RegisterController;
use App\Http\Controllers\Auth\ForgotPasswordController;
use App\Http\Controllers\Auth\GoogleController;
use App\Http\Controllers\Api\LiveKitController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\CourseController;
use App\Http\Controllers\ModuleController;
use App\Http\Controllers\LessonController;
use App\Http\Controllers\AssignmentController;
use App\Http\Controllers\SubmissionController;
use App\Http\Controllers\GradingController;
use App\Http\Controllers\QuizController;
use App\Http\Controllers\Admin\ModuleController as AdminModuleController;
use App\Http\Controllers\RubricController;
use App\Http\Controllers\CourseFileController;
use App\Http\Controllers\ModuleFileController;
use App\Http\Controllers\DiscussionController;
use App\Http\Controllers\TopicController;
use App\Http\Controllers\LiveSessionController;
use App\Http\Controllers\PortfolioController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\NotificationController;
use App\Http\Controllers\ViewController;
use App\Http\Controllers\Admin\UserController;
use App\Http\Controllers\RosterController;
use App\Http\Controllers\GradeRuleController;
use App\Http\Controllers\Admin\AnalyticsController;
use App\Http\Controllers\Admin\AdminCourseController;
use App\Http\Controllers\Admin\EnrollmentController as AdminEnrollmentController;
use App\Http\Controllers\Admin\GradeController as AdminGradeController;
use App\Http\Controllers\Admin\ProgramController;
use App\Http\Controllers\Admin\LiveSessionController as AdminLiveSessionController;
use App\Http\Controllers\LandingController;

Route::get('/', [LandingController::class, 'index'])->name('landing');

Route::middleware('guest')->group(function () {
    Route::get('/login', [LoginController::class, 'showForm'])->name('login');
    Route::post('/login', [LoginController::class, 'login']);
    Route::get('/signup', [RegisterController::class, 'choose'])->name('signup');
    Route::get('/signup/student', [RegisterController::class, 'studentForm'])->name('signup.student');
    Route::post('/signup/student', [RegisterController::class, 'registerStudent'])->name('signup.student.store');
    Route::get('/signup/instructor', [RegisterController::class, 'instructorForm'])->name('signup.instructor');
    Route::post('/signup/instructor', [RegisterController::class, 'registerInstructor'])->name('signup.instructor.store');
    Route::get('/forgot-password', [ForgotPasswordController::class, 'showForm'])->name('password.request');
    Route::post('/forgot-password', [ForgotPasswordController::class, 'send'])->name('password.email');
    Route::get('/reset-password/{token}', [ForgotPasswordController::class, 'showResetForm'])->name('password.reset');
    Route::post('/reset-password', [ForgotPasswordController::class, 'reset'])->name('password.update');

    Route::get('/auth/google', [GoogleController::class, 'redirect'])->name('auth.google');
    Route::get('/auth/google/callback', [GoogleController::class, 'callback'])->name('auth.google.callback');
});

Route::post('/logout', [LoginController::class, 'logout'])->name('logout')->middleware('auth');
Route::post('/locale', function(\Illuminate\Http\Request $req) {
    $req->validate(['locale' => 'required|in:en,ar']);
    session(['locale' => $req->locale]);
    if (auth()->check()) {
        auth()->user()->update(['locale' => $req->locale]);
    }
    app()->setLocale($req->locale);
    return back();
})->name('locale.switch');

Route::post('/api/livekit/public-token', [LiveKitController::class, 'getPublicToken'])->name('api.livekit.public-token');

Route::get('/live/{session}', [LiveSessionController::class, 'showStandalone'])->name('live.show');

Route::middleware('auth')->group(function () {
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::put('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::put('/profile/password', [ProfileController::class, 'updatePassword'])->name('profile.password');
    Route::post('/api/livekit-token', [LiveKitController::class, 'token'])->name('api.livekit.token');
    Route::get('/notifications', [NotificationController::class, 'index'])->name('notifications.index');
    Route::post('/notifications/read-all', [NotificationController::class, 'markAllRead'])->name('notifications.read-all');
    Route::post('/notifications/{notification}/read', [NotificationController::class, 'markRead'])->name('notifications.read');
    Route::get('/portfolio', [PortfolioController::class, 'edit'])->name('portfolio.edit');
    Route::post('/portfolio', [PortfolioController::class, 'store'])->name('portfolio.store');
    Route::delete('/portfolio/{item}', [PortfolioController::class, 'destroy'])->name('portfolio.destroy');
    Route::get('/portfolio/{user}', [PortfolioController::class, 'show'])->name('portfolio.show');
    Route::get('/assignments', [AssignmentController::class, 'studentIndex'])->name('assignments.index');
    Route::get('/live', [LiveSessionController::class, 'index'])->name('live.index');
    Route::post('/live', [LiveSessionController::class, 'storeStandalone'])->name('live.store')->middleware('role:instructor,admin');
    Route::get('/live/{session}/edit', [LiveSessionController::class, 'editStandalone'])->name('live.edit')->middleware('role:instructor,admin');
    Route::put('/live/{session}', [LiveSessionController::class, 'updateStandalone'])->name('live.update')->middleware('role:instructor,admin');
    Route::delete('/live/{session}', [LiveSessionController::class, 'destroy'])->name('live.destroy')->middleware('role:instructor,admin');

    Route::post('/view/student', [ViewController::class, 'student'])->name('view.student');
    Route::post('/view/instructor', [ViewController::class, 'instructor'])->name('view.instructor');
    Route::post('/view/exit', [ViewController::class, 'exit'])->name('view.exit');

Route::post('/theme', [ViewController::class, 'theme'])->name('theme.switch');

    Route::middleware('role:instructor,admin')->group(function () {
    Route::get('/grading', [GradingController::class, 'index'])->name('grading.index');
    Route::get('/grading/courses/{course}/assignments', [GradingController::class, 'assignments'])->name('grading.assignments');
    Route::get('/grading/courses/{course}/assignments/{assignment}/students', [GradingController::class, 'students'])->name('grading.students');
    Route::get('/grading/courses/{course}/assignments/export', [GradingController::class, 'exportCourse'])->name('grading.export-course');
    Route::get('/grading/courses/{course}/assignments/{assignment}/export', [GradingController::class, 'exportAssignment'])->name('grading.export-assignment');
    Route::get('/grading/{submission}', [GradingController::class, 'show'])->name('grading.show');
    Route::post('/grading/{submission}', [GradingController::class, 'store'])->name('grading.store');
        Route::post('/plagiarism/check/{submission}', [SubmissionController::class, 'checkPlagiarism'])->name('plagiarism.check');
        Route::get('/question-bank', [\App\Http\Controllers\QuestionBankController::class, 'globalIndex'])->name('question-bank.index');
        Route::post('/question-bank/{questionBank}/items', [\App\Http\Controllers\QuestionBankController::class, 'addItem'])->name('question-bank.add-item');
        Route::post('/question-bank/{questionBank}/import', [\App\Http\Controllers\QuestionBankController::class, 'importQuestions'])->name('question-bank.import');
        Route::get('/question-bank/import-example', [\App\Http\Controllers\QuestionBankController::class, 'downloadImportExample'])->name('question-bank.import-example');
        Route::post('/question-bank/bulk-import', [\App\Http\Controllers\QuestionBankController::class, 'bulkImportBanks'])->name('question-bank.bulk-import');
        Route::get('/question-bank/bulk-import-example', [\App\Http\Controllers\QuestionBankController::class, 'downloadBulkImportExample'])->name('question-bank.bulk-import-example');
    });

    Route::get('/courses', [CourseController::class, 'index'])->name('courses.index');
    Route::get('/courses/catalog', [CourseController::class, 'catalog'])->name('courses.catalog');
    Route::middleware('role:admin,instructor')->group(function () {
        Route::get('/courses/create', [CourseController::class, 'create'])->name('courses.create');
        Route::post('/courses', [CourseController::class, 'store'])->name('courses.store');
    });

    Route::prefix('/courses/{course}')->name('courses.')->group(function () {
        Route::get('/', [CourseController::class, 'show'])->name('show');
        Route::post('/enroll', [CourseController::class, 'enroll'])->name('enroll');
        Route::delete('/enroll', [CourseController::class, 'unenroll'])->name('unenroll');
        Route::get('/progress', [CourseController::class, 'progress'])->name('progress');
        Route::get('/roster', [RosterController::class, 'index'])->name('roster')->middleware('role:instructor,admin');
        Route::middleware('role:instructor,admin')->group(function () {
            Route::get('/grade-rules', [GradeRuleController::class, 'index'])->name('grade-rules.index');
            Route::post('/grade-rules', [GradeRuleController::class, 'update'])->name('grade-rules.update');
            Route::get('/grade-rules/export', [GradeRuleController::class, 'export'])->name('grade-rules.export');
            Route::get('/grade-rules/export-example', [GradeRuleController::class, 'downloadExample'])->name('grade-rules.export-example');
            Route::get('/edit', [CourseController::class, 'edit'])->name('edit');
            Route::put('/', [CourseController::class, 'update'])->name('update');
            Route::delete('/', [CourseController::class, 'destroy'])->name('destroy');
            Route::post('/duplicate', [CourseController::class, 'duplicate'])->name('duplicate');
        });

        Route::middleware('role:admin')->group(function () {
            Route::post('/roster/add-student', [CourseController::class, 'addStudent'])->name('roster.add');
            Route::post('/roster/bulk', [CourseController::class, 'bulkEnrollCSV'])->name('roster.bulk');
            Route::delete('/roster/{student}', [CourseController::class, 'removeStudent'])->name('roster.remove');
        });
        Route::prefix('content')->name('content.')->group(function () {
            Route::get('/', [ModuleController::class, 'index'])->name('index');
            Route::middleware('role:instructor,admin')->group(function () {
                Route::get('/create', [ModuleController::class, 'create'])->name('create');
                Route::post('/', [ModuleController::class, 'store'])->name('store');
                Route::get('/{module}/edit', [ModuleController::class, 'edit'])->name('edit');
                Route::put('/{module}', [ModuleController::class, 'update'])->name('update');
                Route::delete('/{module}', [ModuleController::class, 'destroy'])->name('destroy');
                Route::get('/lesson/create', [LessonController::class, 'create'])->name('lesson.create');
                Route::post('/lesson', [LessonController::class, 'store'])->name('lesson.store');
                Route::get('/lesson/{lesson}/edit', [LessonController::class, 'edit'])->name('lesson.edit');
                Route::put('/lesson/{lesson}', [LessonController::class, 'update'])->name('lesson.update');
                Route::delete('/lesson/{lesson}', [LessonController::class, 'destroy'])->name('lesson.destroy');
                Route::prefix('/lesson/{lesson}/topics')->name('topics.')->group(function () {
                    Route::get('/create', [TopicController::class, 'create'])->name('create');
                    Route::post('/', [TopicController::class, 'store'])->name('store');
                    Route::get('/{topic}/edit', [TopicController::class, 'edit'])->name('edit');
                    Route::put('/{topic}', [TopicController::class, 'update'])->name('update');
                    Route::delete('/{topic}', [TopicController::class, 'destroy'])->name('destroy');
                });
            });
            Route::get('/lesson/{lesson}', [LessonController::class, 'show'])->name('lesson.show');
            Route::post('/lesson/{lesson}/complete', [LessonController::class, 'complete'])->name('lesson.complete')->middleware('role:student');
        });
        Route::prefix('assignments')->name('assignments.')->group(function () {
            Route::get('/', [AssignmentController::class, 'index'])->name('index');
            Route::middleware('role:instructor,admin')->group(function () {
                Route::get('/create', [AssignmentController::class, 'create'])->name('create');
                Route::post('/', [AssignmentController::class, 'store'])->name('store');
            });
            Route::get('/{assignment}', [AssignmentController::class, 'show'])->name('show');
            Route::post('/{assignment}/submit', [SubmissionController::class, 'store'])->name('submit');
            Route::middleware('role:instructor,admin')->group(function () {
                Route::get('/{assignment}/edit', [AssignmentController::class, 'edit'])->name('edit');
                Route::put('/{assignment}', [AssignmentController::class, 'update'])->name('update');
                Route::delete('/{assignment}', [AssignmentController::class, 'destroy'])->name('destroy');
                Route::get('/{assignment}/gradebook', [GradingController::class, 'gradebook'])->name('gradebook');
                Route::put('/{assignment}/gradebook/{submission}', [AssignmentController::class, 'updateGrade'])->name('gradebook.update');
                Route::post('/{assignment}/grade/{student}', [GradingController::class, 'directGrade'])->name('direct-grade');
            });
        });
        Route::prefix('question-bank')->name('question-bank.')->middleware('role:instructor,admin')->group(function () {
            Route::get('/', [\App\Http\Controllers\QuestionBankController::class, 'index'])->name('index');
            Route::get('/create', [\App\Http\Controllers\QuestionBankController::class, 'create'])->name('create');
            Route::post('/', [\App\Http\Controllers\QuestionBankController::class, 'store'])->name('store');
            Route::get('/{questionBankItem}/edit', [\App\Http\Controllers\QuestionBankController::class, 'edit'])->name('edit');
            Route::put('/{questionBankItem}', [\App\Http\Controllers\QuestionBankController::class, 'update'])->name('update');
            Route::delete('/{questionBankItem}', [\App\Http\Controllers\QuestionBankController::class, 'destroy'])->name('destroy');
        });
        Route::prefix('quizzes')->name('quizzes.')->group(function () {
            Route::get('/', [QuizController::class, 'index'])->name('index');
            Route::middleware('role:instructor,admin')->group(function () {
                Route::get('/create', [QuizController::class, 'create'])->name('create');
                Route::post('/', [QuizController::class, 'store'])->name('store');
            });
            Route::get('/{quiz}', [QuizController::class, 'show'])->name('show');
            Route::get('/{quiz}/take', [QuizController::class, 'take'])->name('take');
            Route::post('/{quiz}/attempt', [QuizController::class, 'attempt'])->name('attempt');
            Route::get('/{quiz}/results', [QuizController::class, 'results'])->name('results');
            Route::middleware('role:instructor,admin')->group(function () {
                Route::get('/{quiz}/edit', [QuizController::class, 'edit'])->name('edit');
                Route::put('/{quiz}', [QuizController::class, 'update'])->name('update');
                Route::delete('/{quiz}', [QuizController::class, 'destroy'])->name('destroy');
                Route::get('/{quiz}/review', [QuizController::class, 'review'])->name('review');
                Route::post('/{quiz}/release/{attempt}', [QuizController::class, 'releaseGrade'])->name('release');
            });
        });
        Route::prefix('discussions')->name('discussions.')->group(function () {
            Route::get('/', [DiscussionController::class, 'index'])->name('index');
            Route::get('/create', [DiscussionController::class, 'create'])->name('create');
            Route::post('/', [DiscussionController::class, 'store'])->name('store');
            Route::get('/{discussion}', [DiscussionController::class, 'show'])->name('show');
            Route::post('/{discussion}/reply', [DiscussionController::class, 'reply'])->name('reply');
            Route::middleware('role:instructor,admin')->group(function () {
                Route::post('/{discussion}/pin', [DiscussionController::class, 'pin'])->name('pin');
                Route::post('/{discussion}/lock', [DiscussionController::class, 'lock'])->name('lock');
            });
        });
        Route::prefix('live')->name('live.')->group(function () {
            Route::get('/', [LiveSessionController::class, 'courseIndex'])->name('index');
            Route::middleware('role:instructor,admin')->group(function () {
                Route::get('/create', [LiveSessionController::class, 'create'])->name('create');
                Route::post('/', [LiveSessionController::class, 'store'])->name('store');
            });
            Route::get('/{session}', [LiveSessionController::class, 'show'])->name('show');
        });
        Route::middleware('role:instructor,admin')->prefix('modules/{module}/files')->name('module-files.')->group(function () {
            Route::post('/', [ModuleFileController::class, 'store'])->name('store');
            Route::delete('/{file}', [ModuleFileController::class, 'destroy'])->name('destroy');
        });

        Route::prefix('attendance')->name('attendance.')->group(function () {
            Route::get('/my', [\App\Http\Controllers\AttendanceController::class, 'myAttendance'])->name('my');
            Route::middleware('role:instructor,admin')->group(function () {
                Route::get('/', [\App\Http\Controllers\AttendanceController::class, 'index'])->name('index');
                Route::post('/', [\App\Http\Controllers\AttendanceController::class, 'store'])->name('store');
                Route::post('/bulk', [\App\Http\Controllers\AttendanceController::class, 'bulkStore'])->name('bulk');
                Route::get('/report', [\App\Http\Controllers\AttendanceController::class, 'report'])->name('report');
                Route::post('/generate-warnings', [\App\Http\Controllers\AttendanceController::class, 'generateWarnings'])->name('warnings');
                Route::get('/export', [\App\Http\Controllers\AttendanceController::class, 'export'])->name('export');
                Route::post('/import', [\App\Http\Controllers\AttendanceController::class, 'import'])->name('import');
                Route::get('/export-example', [\App\Http\Controllers\AttendanceController::class, 'downloadExample'])->name('export-example');
            });
        });

        Route::middleware('role:instructor,admin')->prefix('rubrics')->name('rubrics.')->group(function () {
            Route::get('/', [RubricController::class, 'index'])->name('index');
            Route::get('/create', [RubricController::class, 'create'])->name('create');
            Route::post('/', [RubricController::class, 'store'])->name('store');
            Route::get('/{rubric}/edit', [RubricController::class, 'edit'])->name('edit');
            Route::put('/{rubric}', [RubricController::class, 'update'])->name('update');
            Route::post('/import', [RubricController::class, 'importXml'])->name('import');
            Route::delete('/{rubric}', [RubricController::class, 'destroy'])->name('destroy');
        });

        Route::middleware('role:instructor,admin')->prefix('files')->name('files.')->group(function () {
            Route::get('/', [CourseFileController::class, 'index'])->name('index');
            Route::post('/', [CourseFileController::class, 'store'])->name('store');
            Route::delete('/{file}', [CourseFileController::class, 'destroy'])->name('destroy');
        });
    });

    Route::get('/roster', [RosterController::class, 'globalIndex'])->name('roster.index')->middleware('role:instructor,admin');

    Route::middleware('role:admin')->prefix('admin')->name('admin.')->group(function () {

        // ── Users ─────────────────────────────────────────────────────────────
        Route::get('/users',                                [UserController::class, 'index'])->name('users.index');
        Route::get('/users/{user}',                         [UserController::class, 'show'])->name('users.show');
        Route::post('/users',                               [UserController::class, 'store'])->name('users.store');
        Route::put('/users/{user}',                         [UserController::class, 'update'])->name('users.update');
        Route::put('/users/{user}/role',                    [UserController::class, 'updateRole'])->name('users.role');
        Route::post('/users/{user}/password',               [UserController::class, 'updatePassword'])->name('users.password');
        Route::post('/users/{user}/verify',                 [UserController::class, 'verifyInstructor'])->name('users.verify');
        Route::post('/users/{user}/revoke',                 [UserController::class, 'revokeVerification'])->name('users.revoke');
        Route::post('/users/invite',                        [UserController::class, 'invite'])->name('users.invite');
        Route::post('/users/bulk-create',                   [UserController::class, 'bulkCreate'])->name('users.bulk-create');
        Route::post('/users/{user}/enroll',                 [UserController::class, 'enrollInCourse'])->name('users.enroll');
        Route::delete('/users/{user}/enroll/{course}',      [UserController::class, 'unenrollFromCourse'])->name('users.unenroll');
        Route::delete('/users/{user}',                      [UserController::class, 'destroy'])->name('users.destroy');
        Route::get('/users-export',                         [UserController::class, 'export'])->name('users.export');
        Route::get('/users-export-example',                  [UserController::class, 'downloadExample'])->name('users.export-example');

        // ── Courses ───────────────────────────────────────────────────────────
        Route::get('/courses',                              [AdminCourseController::class, 'index'])->name('courses.index');
        Route::get('/courses/create',                       [AdminCourseController::class, 'create'])->name('courses.create');
        Route::post('/courses',                             [AdminCourseController::class, 'store'])->name('courses.store');
        Route::get('/courses/{course}',                     [AdminCourseController::class, 'show'])->name('courses.show');
        Route::get('/courses/{course}/edit',                [AdminCourseController::class, 'edit'])->name('courses.edit');
        Route::put('/courses/{course}',                     [AdminCourseController::class, 'update'])->name('courses.update');
        Route::post('/courses/{course}/toggle-publish',     [AdminCourseController::class, 'togglePublish'])->name('courses.toggle-publish');
        Route::put('/courses/{course}/instructor',          [AdminCourseController::class, 'reassignInstructor'])->name('courses.reassign');
        Route::post('/courses/bulk',                        [AdminCourseController::class, 'bulk'])->name('courses.bulk');
        Route::post('/courses/bulk-create',                 [AdminCourseController::class, 'bulkCreate'])->name('courses.bulk-create');
        Route::get('/courses/bulk-create-example',          [AdminCourseController::class, 'downloadBulkExample'])->name('courses.bulk-create-example');
        Route::delete('/courses/{course}',                  [AdminCourseController::class, 'destroy'])->name('courses.destroy');

        // ── Question Bank ─────────────────────────────────────────────────────
        Route::get('/question-bank',                            [\App\Http\Controllers\Admin\QuestionBankController::class, 'index'])->name('question-bank.index');
        Route::get('/question-bank/create',                     [\App\Http\Controllers\Admin\QuestionBankController::class, 'create'])->name('question-bank.create');
        Route::post('/question-bank',                           [\App\Http\Controllers\Admin\QuestionBankController::class, 'store'])->name('question-bank.store');
        Route::get('/question-bank/bulk-import-example', [\App\Http\Controllers\Admin\QuestionBankController::class, 'downloadBulkImportExample'])->name('question-bank.bulk-import-example');
        Route::get('/question-bank-import-example',          [\App\Http\Controllers\Admin\QuestionBankController::class, 'downloadImportExample'])->name('question-bank.import-example');
        Route::get('/question-bank/{questionBank}',             [\App\Http\Controllers\Admin\QuestionBankController::class, 'show'])->name('question-bank.show');
        Route::get('/question-bank/{questionBank}/edit',        [\App\Http\Controllers\Admin\QuestionBankController::class, 'edit'])->name('question-bank.edit');
        Route::put('/question-bank/{questionBank}',             [\App\Http\Controllers\Admin\QuestionBankController::class, 'update'])->name('question-bank.update');
        Route::delete('/question-bank/{questionBank}',          [\App\Http\Controllers\Admin\QuestionBankController::class, 'destroy'])->name('question-bank.destroy');
        Route::post('/question-bank/{questionBank}/import', [\App\Http\Controllers\Admin\QuestionBankController::class, 'importQuestions'])->name('question-bank.import');
        Route::post('/question-bank/bulk-import', [\App\Http\Controllers\Admin\QuestionBankController::class, 'bulkImportBanks'])->name('question-bank.bulk-import');

        // ── Modules ───────────────────────────────────────────────────────────
        Route::get('/modules',                                [AdminModuleController::class, 'index'])->name('modules.index');
        Route::get('/modules/create',                         [AdminModuleController::class, 'create'])->name('modules.create');
        Route::post('/modules',                               [AdminModuleController::class, 'store'])->name('modules.store');
        Route::get('/modules/{module}',                       [AdminModuleController::class, 'show'])->name('modules.show');
        Route::get('/modules/{module}/edit',                  [AdminModuleController::class, 'edit'])->name('modules.edit');
        Route::put('/modules/{module}',                       [AdminModuleController::class, 'update'])->name('modules.update');
        Route::post('/modules/reorder',                       [AdminModuleController::class, 'reorder'])->name('modules.reorder');
        Route::delete('/modules/{module}',                    [AdminModuleController::class, 'destroy'])->name('modules.destroy');

        // ── Programs ──────────────────────────────────────────────────────────
        Route::get('/programs',                             [ProgramController::class, 'index'])->name('programs.index');
        Route::post('/programs',                            [ProgramController::class, 'store'])->name('programs.store');
        Route::get('/programs/{program}',                   [ProgramController::class, 'show'])->name('programs.show');
        Route::get('/programs/{program}/edit',              [ProgramController::class, 'edit'])->name('programs.edit');
        Route::put('/programs/{program}',                   [ProgramController::class, 'update'])->name('programs.update');
        Route::delete('/programs/{program}',                [ProgramController::class, 'destroy'])->name('programs.destroy');
        Route::post('/programs/{program}/courses',          [ProgramController::class, 'assignCourse'])->name('programs.courses.assign');
        Route::delete('/programs/{program}/courses/{course}',[ProgramController::class, 'unassignCourse'])->name('programs.courses.unassign');

        // ── Grades ────────────────────────────────────────────────────────────
        Route::get('/grades',                               [AdminGradeController::class, 'index'])->name('grades.index');
        Route::get('/grades/{grade}',                       [AdminGradeController::class, 'show'])->name('grades.show');
        Route::put('/grades/{grade}',                       [AdminGradeController::class, 'update'])->name('grades.update');
        Route::delete('/grades/{grade}',                    [AdminGradeController::class, 'destroy'])->name('grades.destroy');
        Route::get('/grades-export',                        [AdminGradeController::class, 'export'])->name('grades.export');
        Route::post('/grades-import',                       [AdminGradeController::class, 'import'])->name('grades.import');
        Route::get('/grades-import-example',                 [AdminGradeController::class, 'downloadExample'])->name('grades.import-example');

        // ── Enrollments ───────────────────────────────────────────────────────
        Route::get('/enrollments',                              [AdminEnrollmentController::class, 'index'])->name('enrollments.index');
        Route::post('/enrollments',                             [AdminEnrollmentController::class, 'store'])->name('enrollments.store');
        Route::post('/enrollments/bulk',                        [AdminEnrollmentController::class, 'bulkEnroll'])->name('enrollments.bulk');
        Route::get('/enrollments/bulk-example',                 [AdminEnrollmentController::class, 'downloadBulkExample'])->name('enrollments.bulk-example');
        Route::delete('/enrollments/{enrollment}',              [AdminEnrollmentController::class, 'destroy'])->name('enrollments.destroy');
        Route::get('/enrollments-export',                       [AdminEnrollmentController::class, 'export'])->name('enrollments.export');
        Route::get('/enrollments-export-example',                [AdminEnrollmentController::class, 'downloadExample'])->name('enrollments.export-example');

        // ── Announcements ──────────────────────────────────────────────────────
        Route::get('/announcements',                            [\App\Http\Controllers\Admin\AnnouncementController::class, 'index'])->name('announcements.index');
        Route::post('/announcements',                           [\App\Http\Controllers\Admin\AnnouncementController::class, 'store'])->name('announcements.store');
        Route::get('/announcements/{announcement}',             [\App\Http\Controllers\Admin\AnnouncementController::class, 'show'])->name('announcements.show');
        Route::delete('/announcements/{announcement}',          [\App\Http\Controllers\Admin\AnnouncementController::class, 'destroy'])->name('announcements.destroy');
        Route::get('/announcements-export',                     [\App\Http\Controllers\Admin\AnnouncementController::class, 'export'])->name('announcements.export');
        Route::get('/announcements-export-example',              [\App\Http\Controllers\Admin\AnnouncementController::class, 'downloadExample'])->name('announcements.export-example');

        // ── Grading ────────────────────────────────────────────────────────────
        Route::get('/grading',                                  [\App\Http\Controllers\Admin\GradingController::class, 'index'])->name('grading.index');
        Route::get('/grading/{course}',                         [\App\Http\Controllers\Admin\GradingController::class, 'assignments'])->name('grading.assignments');
        Route::get('/grading/{course}/{assignment}',             [\App\Http\Controllers\Admin\GradingController::class, 'submissions'])->name('grading.submissions');

        // ── Submissions ────────────────────────────────────────────────────────
        Route::get('/submissions',                              [\App\Http\Controllers\Admin\SubmissionController::class, 'index'])->name('submissions.index');
        Route::get('/submissions/{submission}',                 [\App\Http\Controllers\Admin\SubmissionController::class, 'show'])->name('submissions.show');
        Route::delete('/submissions/{submission}',              [\App\Http\Controllers\Admin\SubmissionController::class, 'destroy'])->name('submissions.destroy');
        Route::get('/submissions-export',                       [\App\Http\Controllers\Admin\SubmissionController::class, 'export'])->name('submissions.export');
        Route::get('/submissions-export-example',                [\App\Http\Controllers\Admin\SubmissionController::class, 'downloadExample'])->name('submissions.export-example');

        // ── Quizzes ────────────────────────────────────────────────────────────
        Route::get('/quizzes',                                  [\App\Http\Controllers\Admin\QuizController::class, 'index'])->name('quizzes.index');
        Route::get('/quizzes/{quiz}',                           [\App\Http\Controllers\Admin\QuizController::class, 'show'])->name('quizzes.show');
        Route::delete('/quizzes/{quiz}',                        [\App\Http\Controllers\Admin\QuizController::class, 'destroy'])->name('quizzes.destroy');
        Route::get('/quizzes/{quiz}/attempts-export',           [\App\Http\Controllers\Admin\QuizController::class, 'exportAttempts'])->name('quizzes.attempts-export');
        Route::get('/quizzes-attempts-export-example',          [\App\Http\Controllers\Admin\QuizController::class, 'downloadAttemptsExample'])->name('quizzes.attempts-export-example');

        // ── Analytics ─────────────────────────────────────────────────────────
        Route::get('/analytics',                            [AnalyticsController::class, 'index'])->name('analytics.index');

        // ── Live Sessions ──────────────────────────────────────────────────────
        Route::get('/live-sessions',                            [AdminLiveSessionController::class, 'index'])->name('live-sessions.index');
        Route::get('/live-sessions/{liveSession}',              [AdminLiveSessionController::class, 'show'])->name('live-sessions.show');
        Route::post('/live-sessions',                           [AdminLiveSessionController::class, 'store'])->name('live-sessions.store');
        Route::post('/live-sessions/{liveSession}',             [AdminLiveSessionController::class, 'update'])->name('live-sessions.update');
        Route::delete('/live-sessions/{liveSession}',           [AdminLiveSessionController::class, 'destroy'])->name('live-sessions.destroy');
    });
});
