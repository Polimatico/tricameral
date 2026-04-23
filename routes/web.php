<?php

use App\Http\Controllers\DocsController;
use App\Http\Controllers\ForkController;
use App\Http\Controllers\IssueCommentController;
use App\Http\Controllers\IssueController;
use App\Http\Controllers\IssueTagController;
use App\Http\Controllers\LocaleController;
use App\Http\Controllers\MergeController;
use App\Http\Controllers\OpinionController;
use App\Http\Controllers\OpinionReplyController;
use App\Http\Controllers\OpinionVoteController;
use App\Http\Controllers\ProjectController;
use App\Http\Controllers\ProjectMemberController;
use App\Http\Controllers\PullRequestCommentController;
use App\Http\Controllers\PullRequestController;
use App\Http\Controllers\PullRequestTagController;
use App\Http\Controllers\StarController;
use App\Http\Controllers\UserController;
use Illuminate\Support\Facades\Route;

Route::get('/', fn () => view('home'))->name('home');
Route::post('/locale', [LocaleController::class, 'switch'])->name('locale.switch');

Route::get('/users/{user}', [UserController::class, 'publicProfile'])->name('users.show');

Route::middleware('guest')->group(function () {
    Route::get('/login', [UserController::class, 'showLogin'])->name('login');
    Route::post('/login', [UserController::class, 'login'])->middleware('throttle:6,1');

    Route::get('/register', [UserController::class, 'showRegister'])->name('register');
    Route::post('/register', [UserController::class, 'register']);
});

Route::post('/logout', [UserController::class, 'logout'])->name('logout')->middleware('auth');

Route::middleware('auth')->group(function () {
    Route::get('/profile', [UserController::class, 'showEditProfile'])->name('profile.edit');
    Route::patch('/profile', [UserController::class, 'updateProfile'])->name('profile.update');

    Route::get('/users', [UserController::class, 'usersIndex'])->name('users.index');

    Route::get('/my-projects', [ProjectController::class, 'myProjects'])->name('my_projects.index');

    Route::get('/stars', [StarController::class, 'index'])->name('stars.index');
    Route::post('/projects/{project}/star', [StarController::class, 'toggle'])->name('projects.star');

    Route::get('/projects', [ProjectController::class, 'index'])->name('projects.index');
    Route::post('/projects', [ProjectController::class, 'store'])->name('projects.store');
    Route::get('/projects/{project}', [ProjectController::class, 'show'])->name('projects.show');
    Route::get('/projects/{project}/opinions', [ProjectController::class, 'opinions'])->name('projects.opinions');
    Route::get('/projects/{project}/issues', [ProjectController::class, 'issues'])->name('projects.issues');
    Route::post('/projects/{project}/issues', [IssueController::class, 'store'])->name('projects.issues.store');
    Route::get('/projects/{project}/issues/{issue}', [IssueController::class, 'show'])->name('projects.issues.show');
    Route::patch('/projects/{project}/issues/{issue}/status', [IssueController::class, 'updateStatus'])->name('projects.issues.status');
    Route::delete('/projects/{project}/issues/{issue}', [IssueController::class, 'destroy'])->name('projects.issues.destroy');

    Route::post('/projects/{project}/issues/{issue}/comments', [IssueCommentController::class, 'store'])->name('projects.issues.comments.store');
    Route::delete('/projects/{project}/issues/{issue}/comments/{comment}', [IssueCommentController::class, 'destroy'])->name('projects.issues.comments.destroy');

    Route::post('/projects/{project}/issue-tags', [IssueTagController::class, 'store'])->name('projects.issue_tags.store');
    Route::delete('/projects/{project}/issue-tags/{tag}', [IssueTagController::class, 'destroy'])->name('projects.issue_tags.destroy');
    Route::post('/projects/{project}/issues/{issue}/tags', [IssueTagController::class, 'attach'])->name('projects.issues.tags.attach');
    Route::delete('/projects/{project}/issues/{issue}/tags/{tag}', [IssueTagController::class, 'detach'])->name('projects.issues.tags.detach');
    Route::get('/projects/{project}/fork', [ForkController::class, 'create'])->name('projects.fork');
    Route::post('/projects/{project}/fork', [ForkController::class, 'store'])->name('projects.fork.store');
    Route::patch('/projects/{project}/fork-listing', [ForkController::class, 'updateListingMode'])->name('projects.fork.listing');
    Route::patch('/projects/{project}/forks/{fork}/visibility', [ForkController::class, 'updateForkVisibility'])->name('projects.fork.visibility');
    Route::get('/projects/{project}/pull', [ProjectController::class, 'pull'])->name('projects.pull');
    Route::post('/projects/{project}/pull', [PullRequestController::class, 'store'])->name('projects.pull.store');
    Route::get('/projects/{project}/pull/{pullRequest}', [PullRequestController::class, 'show'])->name('projects.pull.show');
    Route::patch('/projects/{project}/pull/{pullRequest}/status', [PullRequestController::class, 'updateStatus'])->name('projects.pull.status');
    Route::patch('/projects/{project}/pull/{pullRequest}/body', [PullRequestController::class, 'updateBody'])->name('projects.pull.body');
    Route::delete('/projects/{project}/pull/{pullRequest}', [PullRequestController::class, 'destroy'])->name('projects.pull.destroy');
    Route::post('/projects/{project}/pull/{pullRequest}/comments', [PullRequestCommentController::class, 'store'])->name('projects.pull.comments.store');
    Route::delete('/projects/{project}/pull/{pullRequest}/comments/{comment}', [PullRequestCommentController::class, 'destroy'])->name('projects.pull.comments.destroy');
    Route::post('/projects/{project}/pull-tags', [PullRequestTagController::class, 'store'])->name('projects.pull_tags.store');
    Route::delete('/projects/{project}/pull-tags/{tag}', [PullRequestTagController::class, 'destroy'])->name('projects.pull_tags.destroy');
    Route::post('/projects/{project}/pull/{pullRequest}/tags', [PullRequestTagController::class, 'attach'])->name('projects.pull.tags.attach');
    Route::delete('/projects/{project}/pull/{pullRequest}/tags/{tag}', [PullRequestTagController::class, 'detach'])->name('projects.pull.tags.detach');
    Route::get('/projects/{project}/merge', [ProjectController::class, 'merge'])->name('projects.merge');
    Route::get('/projects/{project}/merge/{pullRequest}', [ProjectController::class, 'mergeShow'])->name('projects.merge.show');
    Route::get('/projects/{project}/merge/{pullRequest}/diff', [MergeController::class, 'diff'])->name('projects.merge.diff');
    Route::post('/projects/{project}/merge/{pullRequest}/apply', [MergeController::class, 'apply'])->name('projects.merge.apply');
    Route::get('/projects/{project}/team', [ProjectController::class, 'team'])->name('projects.team');
    Route::get('/projects/{project}/settings', [ProjectController::class, 'settings'])->name('projects.settings');
    Route::patch('/projects/{project}/settings', [ProjectController::class, 'updateSettings'])->name('projects.settings.update');
    Route::delete('/projects/{project}', [ProjectController::class, 'destroy'])->name('projects.destroy');

    Route::post('/projects/{project}/opinions', [OpinionController::class, 'store'])->name('projects.opinions.store');
    Route::get('/projects/{project}/opinions/{opinion}', [OpinionController::class, 'show'])->name('projects.opinions.show');
    Route::patch('/projects/{project}/opinions/{opinion}', [OpinionController::class, 'update'])->name('projects.opinions.update');
    Route::delete('/projects/{project}/opinions/{opinion}', [OpinionController::class, 'destroy'])->name('projects.opinions.destroy');
    Route::post('/projects/{project}/opinions/{opinion}/vote', [OpinionVoteController::class, 'store'])->name('projects.opinions.vote');

    Route::post('/projects/{project}/opinions/{opinion}/replies', [OpinionReplyController::class, 'store'])->name('projects.opinions.replies.store');
    Route::patch('/projects/{project}/opinions/{opinion}/replies/{reply}', [OpinionReplyController::class, 'update'])->name('projects.opinions.replies.update');
    Route::delete('/projects/{project}/opinions/{opinion}/replies/{reply}', [OpinionReplyController::class, 'destroy'])->name('projects.opinions.replies.destroy');

    Route::post('/projects/{project}/members', [ProjectMemberController::class, 'store'])->name('projects.members.store');
    Route::patch('/projects/{project}/members/{user}', [ProjectMemberController::class, 'update'])->name('projects.members.update');
    Route::delete('/projects/{project}/members/{user}', [ProjectMemberController::class, 'destroy'])->name('projects.members.destroy');
    Route::get('/projects/{project}/editor/{file}', [DocsController::class, 'show'])->name('docs.show')->whereIn('file', ['readme', 'conduct_code', 'law_text']);
    Route::post('/projects/{project}/editor/{file}', [DocsController::class, 'store'])->name('docs.store')->whereIn('file', ['readme', 'conduct_code', 'law_text']);
});
