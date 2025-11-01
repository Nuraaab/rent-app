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
use App\Http\Controllers\GroupController;
use App\Http\Controllers\PostController;
use App\Http\Controllers\ServiceController;
use App\Http\Controllers\ItemController;
use App\Http\Controllers\ChatController;
use App\Http\Controllers\UserInteractionController;
use App\Http\Controllers\NetworkingController;
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

// Route::group(['middleware' => ['api.auth.check']], function () {
//     Route::get('/auth/check-auth', [UserController::class, 'checkAuth'])->name('check-auth');
//     Route::post('/auth/logout', [UserController::class, 'logout'])->name('logout');
//     Route::get('/auth/getuser/{id}', [UserController::class, 'getUser'])->name('getuser');
//     Route::post('/rental/add', [RentalController::class, 'addRental'])->name('addRental');
//     Route::post('/rental/post', [RentalController::class, 'postRental'])->name('postRental');
//     Route::post('/jobs/add', [JobPositionController::class, 'addJob'])->name('addjobs');

//     Route::post('/category/add', [CategoryController::class, 'addCategory'])->name('addcategory');
//     Route::post('/houseoffer/add', [HouseOfferController::class, 'addHouseOffer'])->name('addhouseoffer');

//     Route::post('/housegallery/add', [HouseGallaryController::class, 'addHouseGallery'])->name('addhousegallery');
//     Route::post('/bedgallery/add', [BedGalleryController::class, 'addBedGallery'])->name('addbedgallery');

//     Route::post('/review/add', [ReviewController::class, 'addReview'])->name('addreview');

//     Route::post('/responsibility/add', [ResponsibilityController::class, 'addResponsibility'])->name('addresponsibility');
//     Route::post('/qualification/add', [QualificationController::class, 'addQualification'])->name('addqualification');

//     Route::post('/rental/update/{id}', [RentalController::class, 'updateRental'])->name('updaterental');
//     Route::post('/jobs/update/{id}', [JobPositionController::class, 'updateJob'])->name('updatejob');

//     Route::delete('/rental/delete/{id}', [RentalController::class, 'deleteHouse'])->name('deletehouse');
//     Route::delete('/jobs/delete/{id}', [JobPositionController::class, 'deleteJob'])->name('deletejob');

//     Route::post('/houseoffer/update/{id}', [HouseOfferController::class, 'updateHouseOffer'])->name('updatehouseOffer');
//     Route::delete('houseoffer/delete/{id}', [HouseOfferController::class, 'deleteHouseOffer'])->name('deleteHouseOffer');

//     Route::post('/responsibility/update/{id}', [ResponsibilityController::class, 'updateResponsibility'])->name('updateresponsibility');
//     Route::delete('/responsibility/delete/{id}', [ResponsibilityController::class, 'deleteResponsibility'])->name('deleteresponsibility');

//     Route::post('/qualification/update/{id}', [QualificationController::class, 'updateQualification'])->name('updatequalification');
//     Route::delete('/qualification/delete/{id}', [QualificationController::class, 'deleteQualification'])->name('deletequalification');

//     Route::post('/favorites/add', [FavoriteController::class, 'addFavorite'])->name('add_favorite');
//     Route::post('/favorites/remove', [FavoriteController::class, 'removeFavorite'])->name('remove_favorite');
//     Route::get('/favorites', [FavoriteController::class, 'getFavorites'])->name('get_favorite');
//     Route::get('/jobfavorites', [FavoriteController::class, 'getJobFavorites'])->name('get_jobfavorite');
//     Route::post('/check-rental-favorite', [FavoriteController::class, 'checkRentalFavoriteStatus']);
//     Route::post('/check-job-favorite', [FavoriteController::class, 'checkJobFavoriteStatus']);
//     Route::delete('/remove-rental-favorite', [FavoriteController::class, 'removeRentalFavorite']);
//     Route::delete('/remove-job-favorite', [FavoriteController::class, 'removeJobFavorite']);

//     Route::post('/applications', [ApplicationsReservationController::class, 'create']);
//     Route::get('/applications', [ApplicationsReservationController::class, 'index']); 
//     Route::get('/applications/forme', [ApplicationsReservationController::class, 'appForMe']);
// });


Route::group(['middleware'=>['auth:sanctum']],function(){
    // user
    Route::get('/user', [UserController::class, 'getUser']); 
    Route::post('/user/update', [UserController::class, 'updateUser']);
    // user end
    Route::get('/auth/check-auth', [UserController::class, 'checkAuth'])->name('check-auth');
    Route::post('/auth/logout', [UserController::class, 'logout'])->name('logout');
    Route::get('/auth/getuser/{id}', [UserController::class, 'getUser'])->name('getuser');
    
    // Rentals routes (authenticated)
    Route::post('/rental/add', [RentalController::class, 'addRental'])->name('addRental');
    Route::post('/rental/post', [RentalController::class, 'postRental'])->name('postRental');
    Route::post('/rental/{id}/inquiry', [RentalController::class, 'sendInquiry'])->name('sendInquiry');
    Route::get('/rental/my', [RentalController::class, 'myProperties'])->name('my_properties');
    
    // Jobs routes (authenticated)
    Route::post('/jobs/add', [JobPositionController::class, 'addJob'])->name('addiobs');
    Route::post('/jobs/addnew', [JobPositionController::class, 'postJob'])->name('postJob');
    Route::get('/jobs/my', [JobPositionController::class, 'myJobs'])->name('my_jobs');

    Route::post('/rental/gallery/{id}', [RentalController::class, 'updateGallery'])->name('updateGallery');
    Route::delete('/rental/gallery/delete/{id}', [RentalController::class, 'deleteGallery'])->name('deleteGallery');
    Route::delete('/rental/bedGallery/delete/{id}', [RentalController::class, 'deleteBedGallery'])->name('deleteBedGallery');
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

    // Modern favorite endpoints
    Route::post('/favorites/toggle', [FavoriteController::class, 'toggleFavorite'])->name('toggle_favorite');
    Route::get('/favorites/ids', [FavoriteController::class, 'getFavoriteIds'])->name('get_favorite_ids');
    Route::post('/favorites/batch-check', [FavoriteController::class, 'batchCheckStatus'])->name('batch_check_status');
    
    // Legacy support endpoints
    Route::post('/favorites/add', [FavoriteController::class, 'addFavorite'])->name('add_favorite');
    Route::post('/favorites/remove', [FavoriteController::class, 'removeFavorite'])->name('remove_favorite');
   
    Route::post('/check-rental-favorite', [FavoriteController::class, 'checkRentalFavoriteStatus']);
    Route::post('/check-job-favorite', [FavoriteController::class, 'checkJobFavoriteStatus']);
    Route::delete('/remove-rental-favorite', [FavoriteController::class, 'removeRentalFavorite']);
    Route::delete('/remove-job-favorite', [FavoriteController::class, 'removeJobFavorite']);

    Route::post('/applications', [ApplicationsReservationController::class, 'create']);
    Route::get('/applications', [ApplicationsReservationController::class, 'index']); 
    Route::get('/applications/forme', [ApplicationsReservationController::class, 'appForMe']);
    Route::post('/applications/status/{id}', [ApplicationsReservationController::class, 'updateStatus'])->name('updateStatus');
    Route::delete('/applications/cancel/{id}', [ApplicationsReservationController::class, 'cancelStatus'])->name('cancelStatus');
    
    // Group management routes
    Route::get('/groups', [GroupController::class, 'index']);
    Route::get('/groups/joined', [GroupController::class, 'joined']);
    Route::post('/groups', [GroupController::class, 'store']);
    Route::get('/groups/{group}', [GroupController::class, 'show']);
    Route::put('/groups/{group}', [GroupController::class, 'update']);
    Route::delete('/groups/{group}', [GroupController::class, 'destroy']);
    
    // Group membership operations
    Route::post('/groups/{group}/join', [GroupController::class, 'join']);
    Route::post('/groups/{group}/leave', [GroupController::class, 'leave']);
    Route::get('/groups/{group}/members', [GroupController::class, 'members']);
    
    // Search functionality
    Route::get('/groups/search', [GroupController::class, 'search']);
    
    // Chat routes
    Route::get('/chat/conversations', [ChatController::class, 'getConversations']);
    Route::post('/chat/conversations', [ChatController::class, 'getOrCreateConversation']);
    Route::get('/chat/conversations/{conversationId}/messages', [ChatController::class, 'getMessages']);
    Route::post('/chat/conversations/{conversationId}/messages', [ChatController::class, 'sendMessage']);
    Route::post('/chat/send-message', [ChatController::class, 'sendMessageToUser']);
    
    // User interaction routes
    Route::post('/interactions', [UserInteractionController::class, 'sendInteraction']);
    Route::get('/interactions/received', [UserInteractionController::class, 'getReceivedInteractions']);
    Route::get('/interactions/sent', [UserInteractionController::class, 'getSentInteractions']);
    Route::get('/interactions/nudge-usage', [UserInteractionController::class, 'getNudgeUsage']);
    
    // Networking routes
    Route::get('/networking', [NetworkingController::class, 'index']);
    Route::post('/networking', [NetworkingController::class, 'store']);
    Route::get('/networking/{networkingProfile}', [NetworkingController::class, 'show']);
    Route::put('/networking/{networkingProfile}', [NetworkingController::class, 'update']);
    Route::delete('/networking/{networkingProfile}', [NetworkingController::class, 'destroy']);
    Route::post('/networking/{networkingProfile}/connect', [NetworkingController::class, 'connect']);
    
    // Posts routes
    Route::get('/posts', [PostController::class, 'index']);
    Route::post('/posts', [PostController::class, 'store']);
    Route::delete('/posts/{post}', [PostController::class, 'destroy']);
    
    // Post interactions
    Route::post('/posts/{post}/like', [PostController::class, 'toggleLike']);
    Route::post('/posts/{post}/comments', [PostController::class, 'addComment']);
    Route::get('/posts/{post}/comments', [PostController::class, 'getComments']);
    
    // Services routes (authenticated)
    Route::post('/services', [ServiceController::class, 'store']);
    Route::get('/services/my', [ServiceController::class, 'myServices']);
    Route::put('/services/{service}', [ServiceController::class, 'update']);
    Route::delete('/services/{service}', [ServiceController::class, 'destroy']);
    
    // Items routes (authenticated)
    Route::post('/items', [ItemController::class, 'store']);
    Route::get('/items/my', [ItemController::class, 'myItems']);
    Route::put('/items/{item}', [ItemController::class, 'update']);
    Route::delete('/items/{item}', [ItemController::class, 'destroy']);
});

Route::controller(UserController::class)->group(function () {
    Route::post('/auth/register', 'register')->name('register');
    Route::post('/auth/login', 'login')->name('login');
    Route::get('/user/{id}', 'getUser')->name('getuser');
    Route::post('/auth/phone_auth', 'phoneAuth')->name('phone_auth');
    Route::post('/auth/google_auth', 'googleAuth')->name('google_auth');
    Route::post('/auth/firebase_email_auth', 'firebaseEmailAuth')->name('firebase_email_auth');
    Route::post('/auth/apple_auth', 'appleAuth')->name('apple_auth');
    Route::post('/auth/facebook_auth', 'facebookAuth')->name('facebook_auth');
});

Route::controller(RentalController::class)->group(function () {
    Route::get('/rental', 'getRental')->name('rental');
});
// Public favorite endpoints (with auth check internally)
Route::middleware(['auth:sanctum'])->group(function () {
    Route::get('/favorites', [FavoriteController::class, 'getFavorites'])->name('get_favorite');
    Route::get('/jobfavorites', [FavoriteController::class, 'getJobFavorites'])->name('get_jobfavorite');
});

Route::controller(UploadImageController::class)->group(function () {
    Route::post('upload/single', 'uploadSingleImage')->name('singleimage');
    Route::post('upload/multiple', 'uploadMultipleImage')->name('multipleimage');
    Route::post('uploadTest', 'upload')->name('uploadTest');
    Route::get('getimages/{fileName}', 'getImage')->name('getsingleimage');
    Route::get('getimages/multiple', 'getMultipleImage')->name('getmultipleimage');
});

Route::controller(HouseGallaryController::class)->group(function () {
    Route::get('/housegallery', 'getHouseGallery')->name('housegallery');
    Route::post('/housegallery/upload','uploadImage')->name('image');
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

// TODO: Create HouseRuleController if needed
// Route::controller(HouseRuleController::class)->group(function () {
//     Route::get('/houserule', 'getHouseRules')->name('houserules');
// });

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

// Public group routes (no authentication required)
Route::get('/groups/public', [GroupController::class, 'index']);
Route::get('/public-groups', [GroupController::class, 'index']);

// Public networking routes (with optional authentication for is_connected status)
Route::get('/public-networking', [NetworkingController::class, 'index']);

// Public services routes (no authentication required)
Route::get('/services', [ServiceController::class, 'index']);
Route::get('/services/{service}', [ServiceController::class, 'show']);

// Public items routes (no authentication required)
Route::get('/items', [ItemController::class, 'index']);
Route::get('/items/{item}', [ItemController::class, 'show']);

Route::post('sms/send', [TwilioController::class, 'sendVerificationCode']);
Route::post('sms/verify', [TwilioController::class, 'verifyCode']);
