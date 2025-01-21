<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\TwilioController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\RentalController;
use App\Http\Controllers\JobPositionController;
use App\Http\Controllers\HouseGallaryController;
use App\Http\Controllers\BedGalleryController;
use App\Http\Controllers\ReviewController;
use App\Http\Controllers\UploadImageController;
use App\Http\Controllers\CategoryController;
use App\Http\Controllers\HouseOfferController;
use App\Http\Controllers\JobCategoryController;
use App\Http\Controllers\ResponsiblityController;
use App\Http\Controllers\QualificationController;
use App\Http\Controllers\FavoriteController;
use App\Http\Controllers\ApplicationsReservationController;
/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});


Route::group(['middleware'=>['auth:sanctum']],function(){
    Route::post('/auth/logout', [UserController::class, 'logout'])->name('logout');
    Route::get('/auth/getuser/{id}', [UserController::class, 'getUser'])->name('getuser');
    Route::post('/rental/add', [RentalController::class, 'addRental'])->name('addRental');
    Route::post('/jobs/add', [JobPositionController::class, 'addJob'])->name('addiobs');

    Route::post('/category/add', [CategoryController::class, 'addCategory'])->name('addcategory');
    Route::post('/houseoffer/add', [HouseOfferController::class, 'addHouseOffer'])->name('addhouseoffer');

    Route::post('/housegallery/add', [HouseGallaryController::class, 'addHouseGallery'])->name('addhousegallery');

    Route::post('/bedgallery/add', [BedGalleryController::class, 'addBedGallery'])->name('addbedgallery');

    Route::post('/review/add', [ReviewController::class, 'addReview'])->name('addreview');

    Route::post('/responsiblity/add', [ResponsiblityController::class, 'addResponsiblity'])->name('addresponsiblity');
    Route::post('/qualification/add', [QualificationController::class, 'addQualification'])->name('addqualification');

    Route::post('/rental/update/{id}', [RentalController::class, 'updateRental'])->name('updaterental');
    Route::post('/jobs/update/{id}', [JobPositionController::class, 'updateJob'])->name('updatejob');

    Route::delete('/rental/delete/{id}', [RentalController::class, 'deleteHouse'])->name('deletehouse');
    Route::delete('/jobs/delete/{id}', [JobPositionController::class, 'deleteJob'])->name('deletejob');

    Route::post('/houseoffer/update/{id}', [HouseOfferController::class, 'updateHouseOffer'])->name('updatehouseOffer');
    Route::delete('houseoffer/delete/{id}', [HouseOfferController::class, 'deleteHouseOffer'])->name('deleteHouseOffer');

    Route::post('/responsibility/update/{id}', [ResponsiblityController::class, 'updateResponsibility'])->name('updateresponsibility');
    Route::delete('/responsibility/delete/{id}', [ResponsiblityController::class, 'deleteResponsibility'])->name('deleteresponsibility');

    Route::post('/qualification/update/{id}', [QualificationController::class, 'updateQualification'])->name('updatequalification');
    Route::delete('/qualification/delete/{id}', [QualificationController::class, 'deleteQualification'])->name('deletequalification');

    Route::post('/favorites/add', [FavoriteController::class, 'addFavorite'])->name('add_favorite');
    Route::post('/favorites/remove', [FavoriteController::class, 'removeFavorite'])->name('remove_favorite');
    Route::get('/favorites', [FavoriteController::class, 'getFavorites'])->name('get_favorite');
    Route::get('/jobfavorites', [FavoriteController::class, 'getJobFavorites'])->name('get_jobfavorite');
    Route::post('/check-rental-favorite', [FavoriteController::class, 'checkRentalFavoriteStatus']);
    Route::post('/check-job-favorite', [FavoriteController::class, 'checkJobFavoriteStatus']);
    Route::delete('/remove-rental-favorite', [FavoriteController::class, 'removeRentalFavorite']);
    Route::delete('/remove-job-favorite', [FavoriteController::class, 'removeJobFavorite']);

    Route::post('/applications', [ApplicationsReservationController::class, 'create']);
    Route::get('/applications', [ApplicationsReservationController::class, 'index']); 
    Route::get('/applications/forme', [ApplicationsReservationController::class, 'appForMe']);
});

Route::controller(UserController::class)->group(function () {
    Route::post('/auth/register', 'register')->name('register');
    Route::post('/auth/login', 'login')->name('login');
    Route::get('/user/{id}', 'getUser')->name('getuser');
    Route::post('/auth/phone_auth', 'phoneAuth')->name('phone_auth');
    Route::post('/auth/google_auth', 'googleAuth')->name('google_auth');
    Route::post('/auth/apple_auth', 'appleAuth')->name('apple_auth');
    Route::post('/auth/facebook_auth', 'facebookAuth')->name('facebook_auth');
});

Route::controller(RentalController::class)->group(function () {
    Route::get('/rental', 'getRental')->name('rental');
});

Route::controller(UploadImageController::class)->group(function () {
    Route::post('upload/single', 'uploadSingleImage')->name('singleimage');
    Route::post('upload/multiple', 'uploadMultipleImage')->name('multipleimage');

    Route::get('getimages/{fileName}', 'getImage')->name('getsingleimage');
    Route::get('getimages/multiple', 'getMultipleImage')->name('getmultipleimage');
});

Route::controller(HouseGallaryController::class)->group(function () {
    Route::get('/housegallery', 'getHouseGallery')->name('housegallery');
    Route::post('housegallery/upload','uploadImage')->name('image');
});

Route::controller(BedGalleryController::class)->group(function () {
    Route::get('/bedgallery', 'getBedGallery')->name('bedgallery');
});

Route::controller(ReviewController::class)->group(function () {
    Route::get('/review', 'getReview')->name('review');
});
Route::controller(CategoryController::class)->group(function () {
    Route::get('/category', 'getCategory')->name('category');
    Route::get('/jobcategory', 'getJobCategory')->name('jobcategory');
});


Route::controller(HouseRuleController::class)->group(function () {
    Route::get('/houserule', 'getHouseRules')->name('houserules');
});

Route::controller(HouseOfferController::class)->group(function () {
    Route::get('/houseoffer', 'getHouseOffer')->name('houseoffer');
});

Route::controller(QualificationController::class)->group(function () {
    Route::get('/qualification/{id}', 'getQualification')->name('qualification');
});

Route::controller(ResponsiblityController::class)->group(function () {
    Route::get('/responsiblity/{id}', 'getResponsiblity')->name('responsiblity');
});

Route::controller(JobPositionController::class)->group(function () {
    Route::get('/jobs', [JobPositionController::class, 'getJobs'])->name('jobs');
});
Route::post('sms/send', [TwilioController::class, 'sendVerificationCode']);
Route::post('sms/verify', [TwilioController::class, 'verifyCode']);
