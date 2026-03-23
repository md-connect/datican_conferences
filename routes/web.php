<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\PageController;
use App\Http\Controllers\Admin\AuthController;
use App\Http\Controllers\Admin\ConferenceDashboardController;
use App\Http\Controllers\Admin\RegistrationExportController;
use App\Http\Controllers\ConferenceRegistrationController;
use App\Http\Controllers\PaperController;
use App\Http\Controllers\ReviewController;
use App\Http\Controllers\BidController;
use App\Http\Controllers\AssignmentController;
use App\Http\Controllers\DiscussionController;
use App\Http\Controllers\CameraReadyController;
use App\Http\Controllers\AnalyticsController;
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\Auth\RegisterController;
use App\Http\Controllers\Auth\ProfileController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\ChairController; 

// Authentication Routes
Route::get('/login', [LoginController::class, 'showLoginForm'])->name('login');
Route::post('/login', [LoginController::class, 'login']);
Route::post('/logout', [LoginController::class, 'logout'])->name('logout');
// Password Change Routes 
Route::get('/profile/change-password', [\App\Http\Controllers\Auth\ChangePasswordController::class, 'showChangeForm'])->name('password.change');
Route::post('/profile/change-password', [\App\Http\Controllers\Auth\ChangePasswordController::class, 'change'])->name('password.update');

// Registration Routes
Route::get('/register', [RegisterController::class, 'showRegistrationForm'])->name('register');
Route::post('/register', [RegisterController::class, 'register']);

// Public Pages
Route::get('/', [PageController::class, 'home'])->name('home');
Route::get('/call-for-papers', [PageController::class, 'callForPapers'])->name('call-for-papers');
Route::get('/committees', [PageController::class, 'committees'])->name('committees');
Route::get('/acknowledgement', [PageController::class, 'acknowledgement'])->name('acknowledgement');

// Conference Registration Routes
Route::prefix('conference')->group(function () {
    Route::get('2026-registration', [ConferenceRegistrationController::class, 'showRegistrationForm'])
        ->name('conference.registration');
    Route::get('/conference-registration/view', [ConferenceRegistrationController::class, 'showRegistrationForm'])
        ->name('conference.registration.view');
    
    Route::post('2026-registration', [ConferenceRegistrationController::class, 'register'])
        ->name('conference.register');
    
    Route::get('success', [ConferenceRegistrationController::class, 'success'])
        ->name('conference.registration.success');

    Route::get('stats', [ConferenceRegistrationController::class, 'stats'])
        ->name('conference.registration.stats')
        ->middleware('auth');
});

// ==================== AUTHENTICATED ROUTES ====================
Route::middleware(['auth'])->group(function () {
    // Unified Dashboard
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');
    
    // Profile Routes
    Route::get('/profile', [ProfileController::class, 'show'])->name('profile');
    Route::get('/profile/edit', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::put('/profile', [ProfileController::class, 'update'])->name('profile.update');
    
    // Papers
    Route::resource('papers', PaperController::class);
    Route::post('/papers/{paper}/submit', [PaperController::class, 'submit'])->name('papers.submit');
    Route::get('/papers/{paper}/download', [PaperController::class, 'download'])->name('papers.download');
    Route::get('/papers/{paper}/submit-full', [PaperController::class, 'submitFullForm'])->name('papers.submit-full-form');
    Route::post('/papers/{paper}/submit-full', [PaperController::class, 'submitFull'])->name('papers.submit-full');
    
    // Reviews
    Route::middleware(['auth'])->group(function () {
         // Reviewers expertise
        Route::get('/reviewer/expertise', [App\Http\Controllers\ReviewerExpertiseController::class, 'index'])->name('reviewer.expertise');
        Route::post('/reviewer/expertise', [App\Http\Controllers\ReviewerExpertiseController::class, 'store'])->name('reviewer.expertise.store');
        Route::put('/reviewer/expertise/{id}', [App\Http\Controllers\ReviewerExpertiseController::class, 'update'])->name('reviewer.expertise.update');
        Route::delete('/reviewer/expertise/{id}', [App\Http\Controllers\ReviewerExpertiseController::class, 'destroy'])->name('reviewer.expertise.destroy');
        // Revision routes (add these)
        Route::get('/papers/{paper}/revise', [PaperController::class, 'reviseForm'])->name('papers.revise-form');
        Route::post('/papers/{paper}/revise', [PaperController::class, 'submitRevision'])->name('papers.submit-revision');
        // My Reviews
        Route::get('/reviews/my-reviews', [ReviewController::class, 'index'])->name('reviews.my');
        Route::get('/reviews/create', [ReviewController::class, 'create'])->name('reviews.create');
        Route::post('/reviews', [ReviewController::class, 'store'])->name('reviews.store');
        Route::get('/reviews/{review}', [ReviewController::class, 'show'])->name('reviews.show');
        Route::get('/reviews/{review}/edit', [ReviewController::class, 'edit'])->name('reviews.edit');
        Route::put('/reviews/{review}', [ReviewController::class, 'update'])->name('reviews.update');
        Route::post('/reviews/{review}/accept', [ReviewController::class, 'accept'])->name('reviews.accept');
        Route::post('/reviews/{review}/decline', [ReviewController::class, 'decline'])->name('reviews.decline');
        Route::get('/papers/{paper}/review', [ReviewController::class, 'startReview'])->name('reviews.start');
        Route::get('/reviews/stats', [ReviewController::class, 'stats'])->name('reviews.stats');
    });
    
    // Bidding
    Route::get('/bidding', [BidController::class, 'index'])->name('bidding.index');
    Route::post('/bids', [BidController::class, 'store'])->name('bids.store');
    Route::put('/bids/{bid}', [BidController::class, 'update'])->name('bids.update');
    
    // Discussions
    Route::resource('discussions', DiscussionController::class)->except(['index', 'create']);
    Route::get('/papers/{paper}/discussions', [DiscussionController::class, 'index'])->name('discussions.paper');
    Route::post('/discussions/{discussion}/resolve', [DiscussionController::class, 'resolve'])->name('discussions.resolve');
    
    // Camera Ready
    Route::get('/camera-ready/{paper}', [CameraReadyController::class, 'show'])->name('camera-ready.show');
    Route::post('/camera-ready/{paper}', [CameraReadyController::class, 'store'])->name('camera-ready.store');
    Route::post('/camera-ready/{cameraReady}/approve', [CameraReadyController::class, 'approve'])->name('camera-ready.approve');
    Route::post('/camera-ready/{cameraReady}/reject', [CameraReadyController::class, 'reject'])->name('camera-ready.reject');
    Route::get('/camera-ready/{cameraReady}/download', [CameraReadyController::class, 'download'])->name('camera-ready.download');
    
    // ==================== CHAIR-ONLY ROUTES ====================
    Route::middleware(['chair'])->group(function () {
        // Chair Dashboard
        Route::get('/chair/dashboard', [ChairController::class, 'dashboard'])->name('chair.dashboard');
        
        // Chair Management Pages
        Route::get('/chair/papers', [ChairController::class, 'papers'])->name('chair.papers');
        Route::get('/chair/reviews', [ChairController::class, 'reviews'])->name('chair.reviews');
        Route::get('/chair/reviewers', [ChairController::class, 'reviewers'])->name('chair.reviewers');

        // ========== NEW: Conference Registrations Views ==========
        Route::get('/chair/registrations', [ChairController::class, 'registrations'])->name('chair.registrations');
        
        // ========== NEW: Export Routes ==========
        Route::get('/chair/export/registrations', [ChairController::class, 'exportRegistrations'])->name('chair.export.registrations');
        Route::get('/chair/export/papers', [ChairController::class, 'exportPapers'])->name('chair.export.papers');
        Route::get('/chair/export/reviews', [ChairController::class, 'exportReviews'])->name('chair.export.reviews');
        
        
        // Paper Decisions
        Route::get('/papers/{paper}/decide', [ChairController::class, 'showDecisionForm'])->name('chair.papers.decision.form');
        Route::post('/papers/{paper}/decision', [ChairController::class, 'makeDecision'])->name('chair.papers.decision');
        
        // Review Management
        Route::post('/assignments/reset/{paper}', [AssignmentController::class, 'resetForReassignment'])
            ->name('assignments.reset')
            ->middleware(['auth', 'can:admin,App\Models\Paper']);
        Route::post('/reviews/{review}/reassign', [ChairController::class, 'reassign'])->name('chair.reviews.reassign');
        Route::post('/reviews/{review}/remind', [ChairController::class, 'sendReminder'])->name('chair.reviews.remind');
        
        // User Management (for chairs - limited to reviewer/chair toggling)
        Route::post('/users/{user}/toggle-reviewer', [ChairController::class, 'toggleReviewer'])->name('chair.users.toggle.reviewer');
        Route::post('/users/{user}/toggle-chair', [ChairController::class, 'toggleChair'])->name('chair.users.toggle.chair');
        
        // Assignments (shared with admin)
        Route::get('/assignments/suggest/{paper}', [AssignmentController::class, 'suggest'])->name('assignments.suggest');
        Route::get('/assignments', [AssignmentController::class, 'index'])->name('assignments.index');
        Route::get('/assignments/{paper}/assign', [AssignmentController::class, 'assign'])->name('assignments.assign');
        Route::post('/assignments/manual', [AssignmentController::class, 'manualAssign'])->name('assignments.manual');
        Route::post('/assignments/auto', [AssignmentController::class, 'autoAssign'])->name('assignments.auto');
        Route::get('/assignments/{paper}/suggest', [AssignmentController::class, 'suggest'])->name('assignments.suggest');
        Route::get('/assignments/config', [AssignmentController::class, 'config'])->name('assignments.config');
        Route::delete('/assignments/{assignment}', [AssignmentController::class, 'destroy'])->name('assignments.destroy');
        
        // Paper status management (for chairs)
        Route::post('/papers/{paper}/status', [PaperController::class, 'updateStatus'])->name('papers.update-status');
        
        // Analytics (for chairs)
        Route::get('/analytics', [AnalyticsController::class, 'index'])->name('analytics.index');
        Route::get('/analytics/export/{type}', [AnalyticsController::class, 'export'])->name('analytics.export');
        Route::get('/analytics/reviewers', [AnalyticsController::class, 'reviewerPerformance'])->name('analytics.reviewers');
    });
    
    // ==================== ADMIN-ONLY ROUTES ====================
    // Admin has access to everything chair has PLUS more
    Route::middleware(['can:admin,App\Models\Paper'])->group(function () {
        // User Management (admin only - not for chairs)
        Route::get('/users', [UserController::class, 'index'])->name('users.index');
        Route::get('/users/{user}', [UserController::class, 'show'])->name('users.show');
        Route::put('/users/{user}', [UserController::class, 'update'])->name('users.update');
        Route::delete('/users/{user}', [UserController::class, 'destroy'])->name('users.destroy');
        
        // System settings (admin only)
        Route::get('/settings', [\App\Http\Controllers\SettingsController::class, 'index'])->name('settings.index');
        Route::post('/settings', [\App\Http\Controllers\SettingsController::class, 'update'])->name('settings.update');
        Route::get('/settings/deadlines', [\App\Http\Controllers\SettingsController::class, 'deadlines'])->name('settings.deadlines');
        Route::post('/settings/deadlines', [\App\Http\Controllers\SettingsController::class, 'updateDeadlines'])->name('settings.update.deadlines');
    });
});

// ==================== ADMIN PREFIX ROUTES (Legacy/Conference Admin) ====================
Route::prefix('admin')->group(function () {
    // Authentication (public)
    Route::get('login', [AuthController::class, 'showLoginForm'])->name('admin.login');
    Route::post('login', [AuthController::class, 'login'])->name('admin.login.submit');
    Route::post('logout', [AuthController::class, 'logout'])->name('admin.logout');
    
    // Protected Admin Routes using middleware
    Route::middleware(['web', 'admin'])->group(function () {
        Route::get('conference/dashboard', [ConferenceDashboardController::class, 'dashboard'])
            ->name('admin.conference.dashboard');
        Route::get('conference/registrations', [ConferenceDashboardController::class, 'registrations'])
            ->name('admin.registrations');
        Route::get('conference/registrations/{id}', [ConferenceDashboardController::class, 'showRegistration'])
            ->name('admin.registration.show');
        
        // Export Routes
        Route::get('export/registrations', [RegistrationExportController::class, 'export'])
            ->name('admin.export.registrations');
    });
});