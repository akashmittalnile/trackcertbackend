<?php

namespace App\Http\Controllers\API;

use DB;
use App\Models\Like;
use App\Models\User;
use App\Models\Course;
use App\Models\Review;
use App\Models\Product;
use App\Models\Category;
use App\Models\Wishlist;
use App\Models\Certificate;
use App\Models\CardDetail;
use App\Models\Notification;
use App\Models\AddToCart;
use App\Models\Coupon;
use App\Models\Order;
use App\Models\OrderDetail;
use App\Models\ProductAttibutes;
use App\Models\Transaction;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\Address;
use App\Models\ChapterQuiz;
use App\Models\ChapterQuizOption;
use App\Models\CourseChapter;
use App\Models\CourseChapterStep;
use App\Models\Notify;
use App\Models\Setting;
use App\Models\Tag;
use App\Models\TempData;
use App\Models\UserChapterStatus;
use App\Models\UserCourse;
use App\Models\UserQuizAnswer;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\File;
use PDF;

class ApiController extends Controller
{
    public function home()
    {
        try {
            $user_id = Auth::user()->id;
            $datas = array();

            $trending_courses = Course::leftJoin('users as u', function($join) {
                $join->on('course.admin_id', '=', 'u.id');
            })->leftJoin('category as cat', 'course.category_id', '=', 'cat.id')
            ->where('course.status', 1)->orderBy('course.id', 'DESC')->select('course.title', 'course.description', 'course.id', 'course.course_fee', 'course.tags', 'course.valid_upto', 'course.certificates', 'course.introduction_image', 'u.first_name', 'u.last_name', 'u.category_name', 'course.category_id', 'cat.name as catname', 'u.profile_image', 'u.status as cc_status')->get(); /*Get data of Treanding Course*/
            $b1 = array();
            $TrendingCourses = array();
            foreach ($trending_courses as $k => $data) {
                if($data->cc_status != 1) continue;
                $purchasedCourse = UserCourse::where('user_id', auth()->user()->id)->where('course_id', $data->id)->where('is_expire', 0)->orderByDesc('id')->first();
                if(isset($purchasedCourse->id)) continue;
                $b1['id'] = isset($data->id) ? $data->id : '';
                $b1['title'] = isset($data->title) ? $data->title : '';
                $b1['content_creator_name'] = $data->first_name.' '.$data->last_name;
                $b1['content_creator_category'] = isset($data->category_name) ? $data->category_name : '';
                $b1['content_creator_id'] = isset($data->admin_id) ? $data->admin_id : '';
                if ($data->profile_image) {
                    $profile_image = uploadAssets('upload/profile-image/'.$data->profile_image);
                } else {
                    $profile_image = '';
                }
                $b1['category_id'] = $data->category_id ?? null;
                $b1['category_name'] = $data->catname ?? "NA";
                $b1['content_creator_image'] = $profile_image;
                $b1['description'] = isset($data->description) ? $data->description : '';
                $b1['admin_id'] = isset($data->admin_id) ? $data->admin_id : '';
                $b1['created_at'] = date('d/m/y,H:i', strtotime($data->created_at));
                $b1['course_fee'] = $data->course_fee;
                $b1['status'] = $data->status;

                $tags = [];
                if(isset($data->tags)){
                    foreach(unserialize($data->tags) as $value){
                        $name = Tag::where('id', $value)->first();
                        if(isset($name->id)){
                            $temparory['name'] = $name->tag_name ?? null;
                            $temparory['id'] = $name->id ?? null;
                            $tags[] = $temparory;
                        }
                    }
                }

                $b1['tags'] = $tags;
                $b1['valid_upto'] = $data->valid_upto;
                if (!empty($data->certificates)) {
                    $b1['certificates_image'] = uploadAssets('upload/course-certificates/' . $data->certificates);
                } else {
                    $b1['certificates_image'] = '';
                }
                if (!empty($data->introduction_image)) {
                    $b1['introduction_video'] = uploadAssets('upload/disclaimers-introduction/' . $data->introduction_image);
                } else {
                    $b1['introduction_video'] = '';
                }
                $exists = Like::where('reaction_by', '=', $user_id)->where('object_id', '=', $data->id)->where('object_type', '=', 1)->first();
                if (isset($exists)) {
                    $b1['isLike'] = 1;
                } else {
                    $b1['isLike'] = 0;
                }
                $wishlist = Wishlist::where('userid', '=', $user_id)->where('object_id', '=', $data->id)->where('object_type', '=', 1)->where('status', 1)->first();
                if (isset($wishlist)) {
                    $b1['isWishlist'] = 1;
                } else {
                    $b1['isWishlist'] = 0;
                }

                $avgRating = DB::table('user_review as ur')->where('object_id', $data->id)->where('object_type', 1)->avg('rating');
                $b1['avg_rating'] = number_format($avgRating, 1, '.', '');

                $TrendingCourses[] = $b1;
            }
            $TrendingCourses = collect($TrendingCourses);
            $TrendingCourses = $TrendingCourses->sortByDesc('avg_rating')->values();
            $TrendingCourses = $TrendingCourses->all();
            
            $top_category = Category::where('status', 1)->orderBy('id', 'DESC')->get(); /*Get data of category*/
            $b2 = array();
            $TopCategory = array();
            if(count($top_category) > 0)
            {
                foreach ($top_category as $k => $data) {
                    $b2['id'] = isset($data->id) ? $data->id : '';
                    $b2['category_name'] = isset($data->name) ? $data->name : '';
                    if (!empty($data->icon)) {
                        $b2['category_image'] = uploadAssets('upload/category-image/' . $data->icon);
                    } else {
                        $b2['category_image'] = '';
                    }
                    $b2['cat_status'] = isset($data->status) ? $data->status : '';
                    $b2['created_at'] = date('d/m/y,H:i', strtotime($data->created_date));
                    $TopCategory[] = $b2;
                }
            }

            $course_category = Category::where('status', 1)->where('type', 1)->orderBy('id', 'DESC')->get(); /*Get data of category*/
            $b8 = array();
            $CourseCategory = array();
            if(count($course_category) > 0)
            {
                foreach ($course_category as $k => $data) {
                    $b8['id'] = isset($data->id) ? $data->id : '';
                    $b8['category_name'] = isset($data->name) ? $data->name : '';
                    if (!empty($data->icon)) {
                        $b8['category_image'] = uploadAssets('upload/category-image/' . $data->icon);
                    } else {
                        $b8['category_image'] = '';
                    }
                    $b8['cat_status'] = isset($data->status) ? $data->status : '';
                    $b8['created_at'] = date('d/m/y,H:i', strtotime($data->created_date));
                    $CourseCategory[] = $b8;
                }
            }
            

            $suggested_courses = Course::leftJoin('users as u', function($join) {
                $join->on('course.admin_id', '=', 'u.id');
            })->leftJoin('category as cat', 'course.category_id', '=', 'cat.id')
            ->where('course.status', 1)->orderBy('course.id', 'DESC')->select('course.title', 'course.description', 'course.id', 'course.course_fee', 'course.tags', 'course.valid_upto', 'course.certificates', 'course.introduction_image', 'u.first_name', 'u.last_name', 'u.category_name', 'course.category_id', 'cat.name as catname', 'u.profile_image', 'u.status as cc_status')->get(); /*Get data of Suggested Course*/
            $b3 = array();
            $SuggestedCourses = array();
            foreach ($suggested_courses as $k => $data) {
                if($data->cc_status != 1) continue;
                $purchasedCourse = UserCourse::where('user_id', auth()->user()->id)->where('course_id', $data->id)->where('is_expire', 0)->orderByDesc('id')->first();
                if(isset($purchasedCourse->id)) continue;
                $b3['id'] = isset($data->id) ? $data->id : '';
                $b3['title'] = isset($data->title) ? $data->title : '';
                
                $b3['content_creator_category'] = isset($data->category_name) ? $data->category_name : '';
                $b3['content_creator_id'] = isset($data->admin_id) ? $data->admin_id : '';
                if ($data->profile_image) {
                    $profile_image = uploadAssets('upload/profile-image/'.$data->profile_image);
                } else {
                    $profile_image = '';
                }
                $b3['category_id'] = $data->category_id ?? null;
                $b3['category_name'] = $data->catname ?? "NA";
                $b3['content_creator_image'] = $profile_image;
                $b3['content_creator_name'] = $data->first_name.' '.$data->last_name;
                $b3['description'] = isset($data->description) ? $data->description : '';
                $b3['admin_id'] = isset($data->admin_id) ? $data->admin_id : '';
                $b3['created_at'] = date('d/m/y,H:i', strtotime($data->created_at));
                $b3['course_fee'] = $data->course_fee;
                $b3['status'] = $data->status;
                $tags = [];
                if(isset($data->tags)){
                    foreach(unserialize($data->tags) as $value){
                        $name = Tag::where('id', $value)->first();
                        if(isset($name->id)){
                            $temparory['name'] = $name->tag_name ?? null;
                            $temparory['id'] = $name->id ?? null;
                            $tags[] = $temparory;
                        }
                    }
                }

                $b3['tags'] = $tags;
            
                $b3['valid_upto'] = $data->valid_upto;
                if (!empty($data->certificates)) {
                    $b3['certificates_image'] = uploadAssets('upload/course-certificates/' . $data->certificates);
                } else {
                    $b3['certificates_image'] = '';
                }
                if (!empty($data->introduction_image)) {
                    $b3['introduction_video'] = uploadAssets('upload/disclaimers-introduction/' . $data->introduction_image);
                } else {
                    $b3['introduction_video'] = '';
                }
                $exists = Like::where('reaction_by', '=', $user_id)->where('object_id', '=', $data->id)->where('object_type', '=', 1)->first();
                if (isset($exists)) {
                    $b3['isLike'] = 1;
                } else {
                    $b3['isLike'] = 0;
                }
                $wishlist = Wishlist::where('userid', '=', $user_id)->where('object_id', '=', $data->id)->where('object_type', '=', 1)->where('status', 1)->first();
                if (isset($wishlist)) {
                    $b3['isWishlist'] = 1;
                } else {
                    $b3['isWishlist'] = 0;
                }

                $avgRating = DB::table('user_review as ur')->where('object_id', $data->id)->where('object_type', 1)->avg('rating');
                $b3['avg_rating'] = number_format($avgRating, 1, '.', '');

                $SuggestedCourses[] = $b3;
            }
            $SuggestedCourses = collect($SuggestedCourses);
            $SuggestedCourses = $SuggestedCourses->sortByDesc('avg_rating')->values();
            $SuggestedCourses = $SuggestedCourses->all();
            
            $Sug_category = Category::where('status', 1)->orderBy('id', 'DESC')->get(); /*Get data of Suggested category*/
            $b6 = array();
            $SugCategory = array();
            if(count($Sug_category)>0){
                foreach ($Sug_category as $k => $data) {
                    $b6['id'] = isset($data->id) ? $data->id : '';
                    $b6['category_name'] = isset($data->name) ? $data->name : '';
                    if (!empty($data->icon)) {
                        $b6['category_image'] = uploadAssets('upload/category-image/' . $data->icon);
                    } else {
                        $b6['category_image'] = '';
                    }
                    $b6['cat_status'] = isset($data->status) ? $data->status : '';
                    $b6['created_at'] = date('d/m/y,H:i', strtotime($data->created_date));
                    $SugCategory[] = $b6;
                }
            }
            
            $allTags = [];
            $tagsQuery = Tag::all();
            if(isset($tagsQuery)){
                foreach($tagsQuery as $value){
                    $temparory['name'] = $value->tag_name;
                    $temparory['id'] = $value->id;
                    $temparory['type'] = $value->type;
                    $temparory['type_name'] = ($value->type == 1) ? "Course" : "NA";
                    $allTags[] = $temparory;
                }
            }

            $users = User::where('role', 3)->pluck('id');
            $courses = Course::leftJoin('users', function($join) {
                $join->on('course.admin_id', '=', 'users.id');
            })->where('course.status', 1)->whereIn('course.admin_id', $users)->orderBy('course.id', 'DESC');
            $courses = $courses->select('course.*', 'users.first_name', 'users.last_name','users.profile_image','users.category_name')->get();
            $special_course = [];
            foreach($courses as $value){
                $purchasedCourse = UserCourse::where('user_id', auth()->user()->id)->where('course_id', $value->id)->where('is_expire', 0)->orderByDesc('id')->first();
                if(isset($purchasedCourse->id)) continue;
                $temp['course_fee'] = $value->course_fee;
                $temp['valid_upto'] = $value->valid_upto;
                if (!empty($value->certificates)) {
                    $temp['certificates_image'] = uploadAssets('upload/course-certificates/' . $value->certificates);
                } else {
                    $temp['certificates_image'] = '';
                }
                if (!empty($value->introduction_image)) {
                    $temp['introduction_video'] = uploadAssets('upload/disclaimers-introduction/' . $value->introduction_image);
                } else {
                    $temp['introduction_video'] = '';
                }
                $exists = Like::where('reaction_by', '=', auth()->user()->id)->where('object_id', '=', $value->id)->where('object_type', '=', 1)->first();
                if (isset($exists)) {
                    $temp['isLike'] = 1;
                } else {
                    $temp['isLike'] = 0;
                }
                $wishlist = Wishlist::where('userid', '=', auth()->user()->id)->where('object_id', '=', $value->id)->where('object_type', '=', 1)->where('status', 1)->first();
                if (isset($wishlist)) {
                    $temp['isWishlist'] = 1;
                } else {
                    $temp['isWishlist'] = 0;
                }
                $temp['title'] = $value->title;
                if ($value->profile_image) {
                    $profile_image = uploadAssets('upload/profile-image/'.$value->profile_image);
                } else {
                    $profile_image = '';
                }
                $temp['content_creator_image'] = $profile_image;
                $temp['content_creator_name'] = $value->first_name.' '.$value->last_name;
                $temp['content_creator_category'] = isset($value->category_name) ? $value->category_name : '';
                $temp['content_creator_id'] = isset($value->admin_id) ? $value->admin_id : '';
                $temp['id'] = $value->id;
                $temp['description'] = $value->description;
                $temp['status'] = $value->status;
                $avgRating = DB::table('user_review as ur')->where('object_id', $value->id)->where('object_type', 2)->avg('rating');
                $temp['avg_rating'] = number_format($avgRating, 1, '.', '');
                $temp['created_date'] = date('d/m/y,H:i', strtotime($value->created_date));
                $tags = [];
                if(isset($value->tags)){
                    foreach(unserialize($value->tags) as $val){
                        $name = Tag::where('id', $val)->first();
                        $temparory['name'] = $name->tag_name;
                        $temparory['id'] = $name->id;
                        $tags[] = $temparory;
                    }
                }
                $temp['tags'] = $tags;
                $special_course[] = $temp;
            }

            $datas['trending_course'] = $TrendingCourses;
            $datas['top_category'] = $TopCategory;
            $datas['course_category'] = $CourseCategory;
            $datas['suggested_course'] = $SuggestedCourses;
            $datas['suggested_category'] = $SugCategory;
            $datas['all_tags'] = $allTags;
            $datas['special_course'] = $special_course;


            return response()->json(['status' => true, 'message' => 'Home Page Listing', 'data' => $datas]);
        } catch (\Exception $e) {
            return errorMsg("Exception -> " . $e->getMessage());
        }
    }

    public function wishlist_listing(Request $request)
    {
        try {
            $user_id = Auth::user()->id;
            if($user_id)
            {
                $validator = Validator::make($request->all(), [
                    'type' => 'required',
                ]);
                if ($validator->fails()) {
                    return response()->json(['status' => false, 'message' => $validator->errors()->first()]);
                }
                
                $type = $request->type;
                if ($type == 1) { /* 1 stand for course */
                    $datas = Wishlist::where('userid', $user_id)->where('status', 1)->where('object_type', 1)->orderBy('id', 'DESC')->get();
                } else {
                    $datas = Wishlist::where('userid', $user_id)->where('status', 1)->where('object_type', 2)->orderBy('id', 'DESC')->get();
                }
    
                $response = array();
                if (isset($datas)) {
                    foreach ($datas as $keys => $item) {
                        if ($type == 1) { /* 1 stand for course */
                            $value = Course::leftJoin('users', function($join) {
                                $join->on('course.admin_id', '=', 'users.id');
                            })->leftJoin('category as cat', 'cat.id', '=', 'course.category_id');

                            if($request->filled('title')) $value->where('course.title', 'like', '%'.$request->title.'%');
                            if($request->filled('category')) $value->whereIntegerInRaw('course.category_id', $request->category);
                            if($request->filled('price')){
                                if($request->price == 1) $value->orderByDesc('course.course_fee');
                                else $value->orderBy('course.course_fee');
                            } else $value->orderBy('course.id', 'DESC');
                            
                            $value = $value->where('course.status', 1)->where('course.id', $item->object_id)->select('users.first_name', 'users.last_name', 'users.profile_image', 'users.category_name', 'course.*', 'cat.id as catid', 'cat.name as catname')->orderBy('course.id', 'DESC')->first();

                            if(isset($value->id)){
                                $temp['course_fee'] = $value->course_fee ?? null;
                                $temp['valid_upto'] = $value->valid_upto ?? null;
                                if (!empty($value->introduction_image)) {
                                    $temp['introduction_video'] = uploadAssets('upload/disclaimers-introduction/' . $value->introduction_image);
                                } else {
                                    $temp['introduction_video'] = '';
                                }
                                $exists = Like::where('reaction_by', '=', $user_id)->where('object_id', '=', $value->id)->where('object_type', '=', 1)->first();
                                if (isset($exists)) {
                                    $temp['isLike'] = 1;
                                } else {
                                    $temp['isLike'] = 0;
                                }
                                $temp['title'] = $value->title;
                                if ($value->profile_image) {
                                    $profile_image = uploadAssets('upload/profile-image/'.$value->profile_image);
                                } else {
                                    $profile_image = '';
                                }
                                $temp['content_creator_image'] = $profile_image;
                                $temp['content_creator_name'] = $value->first_name.' '.$value->last_name;
                                $temp['category_id'] = isset($value->catid) ? $value->catid : '';
                                $temp['category_name'] = isset($value->catname) ? $value->catname : '';
                                $temp['content_creator_id'] = isset($value->admin_id) ? $value->admin_id : '';
                            }
                            
                        }
                        if(isset($value->id)){
                            $temp['id'] = $value->id;
                            $temp['description'] = $value->description;
                            $tags = [];
                            if(isset($value->tags)){
                                foreach(unserialize($value->tags) as $valu){
                                    $name = Tag::where('id', $valu)->first();
                                    if(isset($name->id)){
                                        $temparory['name'] = $name->tag_name ?? null;
                                        $temparory['id'] = $name->id ?? null;
                                        $tags[] = $temparory;
                                    }
                                }
                            }
                            $temp['tags'] = $tags;
                            $temp['status'] = $value->status;
                            $reviewAvg = DB::table('user_review as ur')->where('object_id', $item->object_id)->where('object_type', $type)->avg('rating');
                            $temp['avg_rating'] = number_format($reviewAvg, 1, '.', '');
                            if($request->filled('rating'))
                                if($reviewAvg < min($request->rating)) continue;
                            $temp['rating'] = number_format((float)$reviewAvg, 1, '.', '');
                            $temp['created_date'] = date('d/m/y,H:i', strtotime($value->created_date));
                            $response[] = $temp;
                        }
                    }
                }
                $category = Category::where('status', 1)->orderBy('id', 'DESC')->get(); /*Get data of category*/
                $b2 = array();
                $TopCategory = array();
                if(count($category) > 0)
                {
                    foreach ($category as $k => $data) {
                        $b2['id'] = isset($data->id) ? $data->id : '';
                        $b2['category_name'] = isset($data->name) ? $data->name : '';
                        if (!empty($data->icon)) {
                            $b2['category_image'] = uploadAssets('upload/category-image/' . $data->icon);
                        } else {
                            $b2['category_image'] = '';
                        }
                        $b2['type'] = isset($data->type) ? $data->type : '';
                        $b2['status'] = isset($data->status) ? $data->status : '';
                        $b2['created_at'] = date('d/m/y,H:i', strtotime($data->created_date));
                        $TopCategory[] = $b2;
                    }
                }
                if ($type == 1) {
                    return response()->json(['status' => true, 'message' => 'Course Listing', 'data' => $response, 'category' => $category]);
                }
            }else{
                return response()->json(['status' => false, 'Message' => 'Please login']);
            }
        } catch (\Exception $e) {
            return errorMsg("Exception -> " . $e->getMessage());
        }
    }

    public function trending_course(Request $request)
    {
        try {
            $validator = Validator::make($request->all(), [
                'limit' => 'required',
            ]);
            if ($validator->fails()) {
                return response()->json(['status' => false, 'message' => $validator->errors()->first()]);
            }
            $user_id = Auth::user()->id;
            $limit = $request->limit;

            $course = Course::leftJoin('users as u', function($join) {
                $join->on('course.admin_id', '=', 'u.id');
            })->leftJoin('category as c', 'c.id', '=', 'course.category_id')->where('course.status', 1);
            if($request->filled('title')){
                $course->where('course.title', 'like' , '%' . $request->title . '%');
            }
            if($request->filled('category')){
                $course->whereIntegerInRaw('course.category_id', $request->category);
            }
            if($request->filled('price')){
                if($request->price == 1) $course->orderByDesc('course.course_fee');
                else $course->orderBy('course.course_fee');
            } else{
                $course->orderBy('course.id', 'DESC');
            }
            if ($limit == 0) {
                $course->limit(2);
            }
            $course = $course->select('course.id', 'course.admin_id','course.title', 'course.description', 'course.course_fee', 'course.tags', 'course.valid_upto', 'course.certificates', 'course.introduction_image', 'course.created_date', 'u.first_name', 'u.last_name', 'u.category_name', 'c.name as catname', 'c.id as catid', 'u.profile_image', 'u.status as cc_status')->paginate(4);

            $response = array();
            if (isset($course)) {
                foreach ($course as $keys => $item) {
                    if($item->cc_status != 1){
                        $purchasedCourse = UserCourse::where('user_id', auth()->user()->id)->where('course_id', $item->id)->where('is_expire', 0)->orderByDesc('id')->first();
                        if(!isset($purchasedCourse->id)) continue;
                    } 
                    $temp['id'] = $item->id;
                    $temp['admin_id'] = $item->admin_id;
                    $temp['title'] = $item->title;
                    $temp['description'] = $item->description;
                    $temp['course_fee'] = $item->course_fee;
                    $temp['category_id'] = $item->catid ?? null;
                    $temp['category_name'] = $item->catname ?? 'NA';
                    $tags = [];
                    if(isset($item->tags)){
                        foreach(unserialize($item->tags) as $val){
                            $name = Tag::where('id', $val)->first();
                            $temparory['name'] = $name->tag_name;
                            $temparory['id'] = $name->id;
                            $tags[] = $temparory;
                        }
                    }
                    $temp['tags'] = $tags;
                    $temp['valid_upto'] = $item->valid_upto;
                    if (!empty($item->certificates)) {
                        $temp['certificates_image'] = uploadAssets('upload/course-certificates/' . $item->certificates);
                    } else {
                        $temp['certificates_image'] = '';
                    }
                    if (!empty($item->introduction_image)) {
                        $temp['introduction_video'] = uploadAssets('upload/disclaimers-introduction/' . $item->introduction_image);
                    } else {
                        $temp['introduction_video'] = '';
                    }
                    $temp['status'] = $item->status;
                    $exists = Like::where('reaction_by', '=', $user_id)->where('object_id', '=', $item->id)->where('object_type', '=', 1)->first();
                    if (isset($exists)) {
                        $temp['isLike'] = 1;
                    } else {
                        $temp['isLike'] = 0;
                    }

                    $wishlist = Wishlist::where('userid', '=', $user_id)->where('object_id', '=', $item->id)->where('object_type', '=', 1)->where('status', 1)->first();
                    if (isset($wishlist)) {
                        $temp['isWishlist'] = 1;
                    } else {
                        $temp['isWishlist'] = 0;
                    }

                    if ($item->profile_image) {
                        $profile_image = uploadAssets('upload/profile-image/'.$item->profile_image);
                    } else {
                        $profile_image = '';
                    }
                    $temp['content_creator_image'] = $profile_image;
                    $temp['content_creator_name'] = isset($item->first_name) ? $item->first_name . ' ' . $item->last_name ?? '' : '';
                    $temp['content_creator_category'] = isset($item->category_name) ? $item->category_name : '';
                    $temp['content_creator_id'] = isset($item->admin_id) ? $item->admin_id : '';
                    $temp['created_date'] = date('d/m/y,H:i', strtotime($item->created_date));
                    $avgRating = DB::table('user_review as ur')->where('object_id', $item->id)->where('object_type', 1)->avg('rating');
                    $temp['avg_rating'] = number_format($avgRating, 1, '.', '');
                    if($request->filled('rating'))
                        if($avgRating < min($request->rating)) continue;
                    $response[] = $temp;
                }
            }

            if(!$request->filled('price')){
                $response = collect($response);
                $response = $response->sortByDesc('avg_rating')->values();
                $response = $response->all();
            }

            $top_category = Category::where('status', 1)->where('type', 1)->orderBy('id', 'DESC')->get(); /*Get data of category*/
            $b2 = array();
            $TopCategory = array();
            if(count($top_category) > 0)
            {
                foreach ($top_category as $k => $data) {
                    $b2['id'] = isset($data->id) ? $data->id : '';
                    $b2['category_name'] = isset($data->name) ? $data->name : '';
                    if (!empty($data->icon)) {
                        $b2['category_image'] = uploadAssets('upload/category-image/' . $data->icon);
                    } else {
                        $b2['category_image'] = '';
                    }
                    $b2['status'] = isset($data->status) ? $data->status : '';
                    $b2['created_at'] = date('d/m/y,H:i', strtotime($data->created_date));
                    $TopCategory[] = $b2;
                }
            }
            return response()->json(['status' => true, 'message' => 'Trending Couse Listing', 'data' => $response, 'category' => $TopCategory, 'last_page_no' => $course->lastPage()]);
        } catch (\Exception $e) {
            return errorMsg("Exception -> " . $e->getMessage());
        }
    }

    public function course_listing()
    {
        try {
            $user_id = Auth::user()->id;
            $course = Course::leftJoin('users', function($join) {
                $join->on('course.admin_id', '=', 'users.id');
            })
            ->where('course.status', 1)->orderBy('course.id', 'DESC')->get();
            $response = array();
            if (isset($course)) {
                foreach ($course as $keys => $item) {
                    $temp['id'] = $item->id;
                    $temp['admin_id'] = $item->admin_id;
                    $temp['title'] = $item->title;
                    $temp['description'] = $item->description;
                    $temp['course_fee'] = $item->course_fee;
                    $temp['tags'] = $item->tags;
                    $temp['valid_upto'] = $item->valid_upto;
                    $temp['certificates_image'] = $item->certificates;
                    $temp['introduction_video'] = $item->introduction_image;
                    $temp['status'] = $item->status;
                    $temp['rating'] = 4.6;
                    $temp['is_like'] = 1;
                    if ($item->profile_image) {
                        $profile_image = uploadAssets('upload/profile-image/'.$item->profile_image);
                    } else {
                        $profile_image = '';
                    }
                    $temp['content_creator_image'] = $profile_image;
                    $temp['content_creator_name'] = $item->first_name.' '.$item->last_name;
                    $temp['content_creator_category'] = isset($item->category_name) ? $item->category_name : '';
                    $temp['content_creator_id'] = isset($item->admin_id) ? $item->admin_id : '';
                    $temp['created_date'] = date('d/m/y,H:i', strtotime($item->created_date));
                    $response[] = $temp;
                }
            }
            return response()->json(['status' => true, 'message' => 'Couse Listing', 'data' => $response]);
        } catch (\Exception $e) {
            return errorMsg("Exception -> " . $e->getMessage());
        }
    }

    public function all_category(Request $request)
    {
        try {
            $user_id = Auth::user();
            if ($user_id) {
                $category = Category::where('status', 1);
                if($request->filled('type')){
                    $category->where('type', $request->type);
                }
                $category = $category->orderBy('id', 'DESC')->get(); /*Get data of category*/
                $response = array();
                if (isset($category)) {
                    foreach ($category as $keys => $item) {
                        $temp['id'] = $item->id;
                        $temp['category_name'] = $item->name;
                        $temp['category_image'] = uploadAssets('upload/category-image/' .$item->icon);
                        $temp['status'] = $item->status;
                        $temp['type'] = $item->type;
                        $temp['type_name'] = ($item->type==1) ? "Course" : "NA";
                        $temp['created_date'] = date('d/m/y,H:i', strtotime($item->created_date));
                        $response[] = $temp;
                    }
                }
                return response()->json(['status' => true, 'message' => 'Category Listing', 'data' => $response]);
            } else {
                return response()->json(['status' => false, 'Message' => 'Please login']);
            }
        } catch (\Exception $e) {
            return errorMsg("Exception -> " . $e->getMessage());
        }
    }

    public function search_category(Request $request)
    {
        try {
            $user_id = Auth::user();
            $data = array();
            if ($user_id) {
                $datas = array();
                if ($request->has('query')) {
                    $search = $request->q;
                    $datas = Category::where('name', 'LIKE', "%$search%")
                        ->where('cat_status', 1)
                        ->get();
                } else {
                    $datas = Category::where('status', 1)->get();
                }
                return response()->json(['status' => true, 'message' => 'Category Listing', 'data' => $datas]);
            } else {
                $data['status'] = false;
                $data['message'] = "Please Login";
                return response()->json($data);
            }
        } catch (\Exception $e) {
            return errorMsg("Exception -> " . $e->getMessage());
        }
    }

    public function search_object_type_all(Request $request)
    {
        try {
            $user_id = Auth::user();
            $data = array();
            if ($user_id) {
                $validator = Validator::make($request->all(), [
                    'type' => 'required', /*1:course*/
                ]);
                if ($validator->fails()) {
                    return response()->json(['status' => false, 'message' => $validator->errors()->first()]);
                }
                $datas = array();
                if ($request->has('query')) {
                    $search = $request->q;
                    if ($request->type == 1) {
                        $datas = Course::leftJoin('users', function($join) {
                            $join->on('course.admin_id', '=', 'users.id');
                        })
                        ->where('course.status', 1)->where('course.title', 'LIKE', "%$search%")
                        ->orWhere('course.admin_name', 'LIKE', "%$search%")->orderBy('course.id', 'DESC')->get();
                    }


                } else {
                    if ($request->type == 1) {
                        $datas = Course::leftJoin('users', function($join) {
                            $join->on('course.admin_id', '=', 'users.id');
                        })
                        ->where('course.status', 1)->orderBy('course.id', 'DESC')->get();
                    }
                }
                if ($request->type == 1) {
                    return response()->json(['status' => true, 'message' => 'Course Listing', 'data' => $datas]);
                }


            } else {
                $data['status'] = false;
                $data['message'] = "Please Login";
                return response()->json($data);
            }
        } catch (\Exception $e) {
            return errorMsg("Exception -> " . $e->getMessage());
        }
    }

    public function search_object_type_trending(Request $request)
    {
        try {
            $user_id = Auth::user();
            $data = array();
            if ($user_id) {
                $validator = Validator::make($request->all(), [
                    'type' => 'required', /*1:course*/
                ]);
                if ($validator->fails()) {
                    return response()->json(['status' => false, 'message' => $validator->errors()->first()]);
                }
                $datas = array();
                if ($request->has('query')) {
                    $search = $request->q;
                    if ($request->type == 1) {
                        $datas = Course::leftJoin('users', function($join) {
                            $join->on('course.admin_id', '=', 'users.id');
                        })
                        ->where('course.status', 1)->where('course.title', 'LIKE', "%$search%")
                            ->orWhere('course.admin_name', 'LIKE', "%$search%")->orderBy('course.id', 'DESC')->get();
                    }


                } else {
                    if ($request->type == 1) {
                        $datas = Course::leftJoin('users', function($join) {
                            $join->on('course.admin_id', '=', 'users.id');
                        })
                        ->where('course.status', 1)->orderBy('course.id', 'DESC')->get();
                    }
                }
                if ($request->type == 1) {
                    return response()->json(['status' => true, 'message' => 'Course Listing', 'data' => $datas]);
                }


            } else {
                $data['status'] = false;
                $data['message'] = "Please Login";
                return response()->json($data);
            }
        } catch (\Exception $e) {
            return errorMsg("Exception -> " . $e->getMessage());
        }
    }

    public function search_object_type_suggest(Request $request)
    {
        try {
            $user_id = Auth::user();
            $data = array();
            if ($user_id) {
                $validator = Validator::make($request->all(), [
                    'type' => 'required', /*1:course*/
                ]);
                if ($validator->fails()) {
                    return response()->json(['status' => false, 'message' => $validator->errors()->first()]);
                }
                $datas = array();
                if ($request->has('query')) {
                    $search = $request->q;
                    if ($request->type == 1) {
                        $datas = Course::leftJoin('users', function($join) {
                            $join->on('course.admin_id', '=', 'users.id');
                        })
                        ->where('course.status', 1)->where('course.title', 'LIKE', "%$search%")
                            ->orWhere('course.admin_name', 'LIKE', "%$search%")->orderBy('course.id', 'DESC')->get();
                    }


                } else {
                    if ($request->type == 1) {
                        $datas = Course::leftJoin('users', function($join) {
                            $join->on('course.admin_id', '=', 'users.id');
                        })
                        ->where('course.status', 1)->orderBy('course.id', 'DESC')->get();
                    }
                }
                if ($request->type == 1) {
                    return response()->json(['status' => true, 'message' => 'Course Listing', 'data' => $datas]);
                }


            } else {
                $data['status'] = false;
                $data['message'] = "Please Login";
                return response()->json($data);
            }
        } catch (\Exception $e) {
            return errorMsg("Exception -> " . $e->getMessage());
        }
    }

    public function suggested_list(Request $request)
    {
        try {
            $validator = Validator::make($request->all(), [
                'type' => 'required',
            ]);
            if ($validator->fails()) {
                return response()->json(['status' => false, 'message' => $validator->errors()->first()]);
            }
            $user_id = Auth::user()->id;
            $type = $request->type;
            if ($type == 1) { /* 1 stand for course */
                $datas = Course::leftJoin('users', function($join) {
                    $join->on('course.admin_id', '=', 'users.id');
                })->where('course.status', 1);
                if($request->filled('title')){
                    $datas->where('course.title', 'like' , '%' . $request->title . '%');
                }
                if($request->filled('category')){
                    $datas->whereIntegerInRaw('course.category_id', $request->category);
                }
                if($request->filled('price')){
                    if($request->price == 1) $datas->orderByDesc('course.course_fee');
                    else $datas->orderBy('course.course_fee');
                } else{
                    $datas->orderBy('course.id', 'DESC');
                }
                $datas = $datas->select('course.*', 'users.first_name', 'users.last_name','users.profile_image','users.category_name')->get();
            }
      
            $response = array();
            if (isset($datas)) {
                foreach ($datas as $keys => $value) {
                    if ($type == 1) { /* 1 stand for course */
                        $temp['course_fee'] = $value->course_fee;
                        $temp['valid_upto'] = $value->valid_upto;
                        if (!empty($value->certificates)) {
                            $temp['certificates_image'] = uploadAssets('upload/course-certificates/' . $value->certificates);
                        } else {
                            $temp['certificates_image'] = '';
                        }
                        if (!empty($value->introduction_image)) {
                            $temp['introduction_video'] = uploadAssets('upload/disclaimers-introduction/' . $value->introduction_image);
                        } else {
                            $temp['introduction_video'] = '';
                        }
                        $exists = Like::where('reaction_by', '=', $user_id)->where('object_id', '=', $value->id)->where('object_type', '=', 1)->first();
                        if (isset($exists)) {
                            $temp['isLike'] = 1;
                        } else {
                            $temp['isLike'] = 0;
                        }
                        $wishlist = Wishlist::where('userid', '=', $user_id)->where('object_id', '=', $value->id)->where('object_type', '=', 1)->where('status', 1)->first();
                        if (isset($wishlist)) {
                            $temp['isWishlist'] = 1;
                        } else {
                            $temp['isWishlist'] = 0;
                        }
                        $temp['title'] = $value->title;
                        if ($value->profile_image) {
                            $profile_image = uploadAssets('upload/profile-image/'.$value->profile_image);
                        } else {
                            $profile_image = '';
                        }
                        $temp['content_creator_image'] = $profile_image;
                        $temp['content_creator_name'] = $value->first_name.' '.$value->last_name;
                        $temp['content_creator_category'] = isset($value->category_name) ? $value->category_name : '';
                        $temp['content_creator_id'] = isset($value->admin_id) ? $value->admin_id : '';
                        $avgRating = DB::table('user_review as ur')->where('object_id', $value->id)->where('object_type', 1)->avg('rating');
                        $temp['avg_rating'] = number_format($avgRating, 1, '.', '');
                        if($request->filled('rating'))
                            if($avgRating < min($request->rating)) continue;
                    }
                    $temp['id'] = $value->id;
                    $temp['description'] = $value->description;
                    $tags = [];
                    if(isset($value->tags)){
                        foreach(unserialize($value->tags) as $val){
                            $name = Tag::where('id', $val)->first();
                            if(isset($name->id)){
                                $temparory['name'] = $name->tag_name ?? null;
                                $temparory['id'] = $name->id ?? null;
                                $tags[] = $temparory;
                            }
                        }
                    }
                    $temp['tags'] = $tags;
                    $temp['status'] = $value->status;
                    $temp['created_date'] = date('d/m/y,H:i', strtotime($value->created_date));
                    $response[] = $temp;
                }
                if($request->filled('price')){
                    $response = collect($response);
                    $response = $response->sortByDesc('avg_rating')->values();
                    $response = $response->all();
                }
                
            }
            $top_category = Category::where('status', 1)->orderBy('id', 'DESC')->get(); /*Get data of category*/
            $b2 = array();
            $TopCategory = array();
            if(count($top_category) > 0)
            {
                foreach ($top_category as $k => $data) {
                    $b2['id'] = isset($data->id) ? $data->id : '';
                    $b2['category_name'] = isset($data->name) ? $data->name : '';
                    if (!empty($data->icon)) {
                        $b2['category_image'] = uploadAssets('upload/category-image/' . $data->icon);
                    } else {
                        $b2['category_image'] = '';
                    }
                    $b2['status'] = isset($data->status) ? $data->status : '';
                    $b2['type_name'] = ($data->type == 1) ? 'Course' : 'NA';
                    $b2['type'] = $data->type;
                    $b2['created_at'] = date('d/m/y,H:i', strtotime($data->created_date));
                    $TopCategory[] = $b2;
                }
            }
            if ($type == 1) {
                return response()->json(['status' => true, 'message' => 'Suggested Course Listing', 'data' => $response,'category' => $TopCategory]);
            }


        } catch (\Exception $e) {
            return errorMsg("Exception -> " . $e->getMessage());
        }
    }

    /*Listing of Course*/
    public function all_type_listing(Request $request)
    {
        try {
            $validator = Validator::make($request->all(), [
                'type' => 'required',
            ]);
            if ($validator->fails()) {
                return response()->json(['status' => false, 'message' => $validator->errors()->first()]);
            }
            $user_id = Auth::user()->id;
            $type = $request->type;
            if ($type == 1) {
                $datas = Course::leftJoin('users', function($join) {
                    $join->on('course.admin_id', '=', 'users.id');
                })->where('course.status', 1);
                if($request->filled('title')){
                    $datas->where('course.title', 'like' , '%' . $request->title . '%');
                }
                if($request->filled('category')){
                    $datas->whereIntegerInRaw('course.category_id', $request->category);
                }
                if($request->filled('price')){
                    if($request->price == 1) $datas->orderByDesc('course.course_fee');
                    else $datas->orderBy('course.course_fee');
                } else{
                    $datas->orderBy('course.id', 'DESC');
                }
                $datas = $datas->select('course.*', 'users.first_name', 'users.last_name','users.profile_image','users.category_name','users.status as cc_status')->get();
            }

            $response = array();
            if (isset($datas)) {
                foreach ($datas as $keys => $value) {
                    if($request->filled('tag'))
                        if(!in_array($request->tag, unserialize($value->tags))) continue;
                    if($type == 1 && $value->cc_status != 1){
                        $purchasedCourse = UserCourse::where('user_id', auth()->user()->id)->where('course_id', $value->id)->where('is_expire', 0)->orderByDesc('id')->first();
                        if(!isset($purchasedCourse->id)) continue;
                    } 
                    if ($type == 1) { /* 1 stand for course */
                            $temp['course_fee'] = $value->course_fee;
                            $temp['valid_upto'] = $value->valid_upto;
                            if (!empty($value->certificates)) {
                                $temp['certificates_image'] = uploadAssets('upload/course-certificates/' . $value->certificates);
                            } else {
                                $temp['certificates_image'] = '';
                            }
                            if (!empty($value->introduction_image)) {
                                $temp['introduction_video'] = uploadAssets('upload/disclaimers-introduction/' . $value->introduction_image);
                            } else {
                                $temp['introduction_video'] = '';
                            }
                            $exists = Like::where('reaction_by', '=', $user_id)->where('object_id', '=', $value->id)->where('object_type', '=', 1)->first();
                            if (isset($exists)) {
                                $temp['isLike'] = 1;
                            } else {
                                $temp['isLike'] = 0;
                            }
                            $wishlist = Wishlist::where('userid', '=', $user_id)->where('object_id', '=', $value->id)->where('object_type', '=', 1)->where('status', 1)->first();
                            if (isset($wishlist)) {
                                $temp['isWishlist'] = 1;
                            } else {
                                $temp['isWishlist'] = 0;
                            }
                            $temp['title'] = $value->title;
                            if ($value->profile_image) {
                                $profile_image = uploadAssets('upload/profile-image/'.$value->profile_image);
                            } else {
                                $profile_image = '';
                            }
                            $temp['content_creator_image'] = $profile_image;
                            $temp['content_creator_name'] = $value->first_name.' '.$value->last_name;
                            $temp['content_creator_category'] = isset($value->category_name) ? $value->category_name : '';
                            $temp['content_creator_id'] = isset($value->admin_id) ? $value->admin_id : '';
                            $avgRating = DB::table('user_review as ur')->where('object_id', $value->id)->where('object_type', 1)->avg('rating');
                            $temp['avg_rating'] = number_format($avgRating, 1, '.', '');
                            if($request->filled('rating'))
                                if($avgRating < min($request->rating)) continue;
                    }
                    $temp['id'] = $value->id;
                    $temp['description'] = $value->description;
                    $temp['status'] = $value->status;
                    $temp['created_date'] = date('d/m/y,H:i', strtotime($value->created_date));
                    $tags = [];
                    if(isset($value->tags)){
                        foreach(unserialize($value->tags) as $val){
                            $name = Tag::where('id', $val)->first();
                            if(isset($name->id)){
                                $temparory['name'] = $name->tag_name ?? null;
                                $temparory['id'] = $name->id ?? null;
                                $tags[] = $temparory;
                            }
                        }
                    }
                    $temp['tags'] = $tags;
                    $response[] = $temp;
                    
                }
            }
            $category = Category::select('id', 'name','description','icon','type')->where('status', 1)->orderByDesc('id')->get();
            if ($type == 1) {
                return response()->json(['status' => true, 'message' => ' Course Listing', 'data' => $response, 'category' => $category]);
            }


        } catch (\Exception $e) {
            return errorMsg("Exception -> " . $e->getMessage());
        }
    }

    /*Course details*/
    public function object_type_details(Request $request)
    {
        try {
            $user_id = Auth::user()->id;
            if ($user_id) {
                $validator = Validator::make($request->all(), [
                    'type' => 'required',
                    'id' => 'required'
                ]);
                if ($validator->fails()) {
                    return response()->json(['status' => false, 'message' => $validator->errors()->first()]);
                }
                $type = $request->type;
                $id = $request->id;
                if ($type == 1) { /* 1 stand for course */
                    $item = Course::leftJoin('users as u', function($join) {
                        $join->on('course.admin_id', '=', 'u.id');
                    })->leftJoin('category as cat', 'cat.id', '=', 'course.category_id')
                    ->where('course.status', 1)->where('course.id', $id)->select('u.first_name', 'u.last_name', 'u.profile_image', 'u.category_name', 'course.*', 'cat.id as catid', 'cat.name as catname')->orderBy('course.id', 'DESC')->first();
                }

                if (!empty($item)) {
                    if ($type == 1) { /* 1 stand for course */
                        $temp['course_fee'] = $item->course_fee;
                        $temp['valid_upto'] = $item->valid_upto;
                        if (!empty($item->certificates)) {
                            $temp['certificates_image'] = uploadAssets('upload/course-certificates/' . $item->certificates);
                        } else {
                            $temp['certificates_image'] = '';
                        }
                        if (!empty($item->introduction_image)) {
                            $temp['introduction_video'] = uploadAssets('upload/disclaimers-introduction/' . $item->introduction_image);
                        } else {
                            $temp['introduction_video'] = '';
                        }
                        $exists = Like::where('reaction_by', '=', $user_id)->where('object_id', '=', $item->id)->where('object_type', '=', 1)->first();
                        if (isset($exists)) {
                            $temp['isLike'] = 1;
                        } else {
                            $temp['isLike'] = 0;
                        }
                        $wishlist = Wishlist::where('userid', '=', $user_id)->where('object_id', '=', $item->id)->where('object_type', '=', 1)->where('status', 1)->first();
                        if (isset($wishlist)) {
                            $temp['isWishlist'] = 1;
                        } else {
                            $temp['isWishlist'] = 0;
                        }
                        $temp['title'] = $item->title;
                        if ($item->profile_image) {
                            $profile_image = uploadAssets('upload/profile-image/'.$item->profile_image);
                        } else {
                            $profile_image = '';
                        }
                        $temp['content_creator_image'] = $profile_image;
                        $temp['content_creator_name'] = $item->first_name.' '.$item->last_name;
                        $temp['content_creator_category'] = isset($item->category_name) ? $item->category_name : '';
                        $temp['content_creator_id'] = isset($item->admin_id) ? $item->admin_id : '';

                        $temp['category_id'] = $item->catid ?? null;
                        $temp['category_name'] = $item->catname ?? null;

                        $course_chapter = DB::table('course_chapter as cc')->where('cc.course_id', $id)->get();
                        $chapters = [];
                        $chapter_count = 0;
                        $chapter_quiz_count = 0;

                        $isPurchase = UserCourse::where('course_id', $id)->where('user_id', $user_id)->orderByDesc('id')->first();
                        $isPurchased = (isset($isPurchase->id)) ? true : false;
                        $temp['isPurchased'] = $isPurchased;
                        if(isset($isPurchase->id)){
                            $temp['courseCompleted'] = $isPurchase->status;
                            $course_purchase = Setting::where('attribute_code','course_purchase_validity')->first();
                            if(isset($course_purchase->id) && $course_purchase->attribute_value != '' && $course_purchase->attribute_value != 0){
                                $valid = date('d M, Y', strtotime($isPurchase->created_date . '+' . $course_purchase->attribute_value . 'days'));
                            }  
                            $temp['course_status'] = courseExpire(date('d M, Y', strtotime($isPurchase->created_date)), $valid) ? 2 : 1;
                            $temp['course_status_name'] = courseExpire(date('d M, Y', strtotime($isPurchase->created_date)), $valid) ? 'Course Validity Expired' : 'Course Validity is Active';
                            $temp['course_purchase_date'] = date('d M, Y', strtotime($isPurchase->created_date));
                            $temp['course_expire_date'] = $valid;
                            $temp['certificate'] = ($isPurchase->status==1) ? url('/')."/api/download-pdf/".encrypt_decrypt('encrypt',$id)."/".encrypt_decrypt('encrypt',$user_id) : null;
                        } else {
                            $temp['courseCompleted'] = 0;
                            $temp['course_status'] = 0;
                            $temp['course_status_name'] = 'Not Purchased Yet';
                            $temp['course_purchase_date'] = null;
                            $temp['course_expire_date'] = null;
                            $temp['certificate'] = null;
                        }
                        

                        foreach($course_chapter as $keyc => $valc){
                            $arr['id'] = $valc->id;
                            $arr['chapter_name'] = $valc->chapter ?? "NA";
                            $steps = CourseChapterStep::where('course_chapter_id', $valc->id)->get();
                            if(isset($steps) && count($steps)) $chapter_count++;
                            $chapter_steps = [];
                            foreach($steps as $vals){
                                $arr1['id'] = $vals->id;
                                $arr1['type'] = $vals->type;
                                $arr1['passing_percentage'] = $vals->passing;
                                if($vals->type == 'quiz') $chapter_quiz_count++;

                                $isComplete = UserChapterStatus::where('userid', $user_id)->where('course_id', $id)->where('chapter_id', $valc->id)->where('step_id', $vals->id)->first();
                                $isCompleted = isset($isComplete->id) ? $isComplete->status : 0;
                                
                                if($isPurchased){
                                    if($isCompleted == 1 || $isCompleted == 2){
                                        $arr1['complete_date'] = date('d M, Y H:iA', strtotime($isComplete->created_date));
                                        $total = ChapterQuiz::where('step_id', $vals->id)->whereIn('type', ['quiz', 'survey'])->sum('marks');
                                        $obtained = UserQuizAnswer::where('quiz_id', $vals->id)->where('userid', auth()->user()->id)->sum('marks_obtained');
                                        $totalQuestion = ChapterQuiz::where('step_id', $vals->id)->whereIn('type', ['quiz', 'survey'])->count();
                                        $totalCorrect = UserQuizAnswer::where('quiz_id', $vals->id)->where('userid',$user_id)->where('status', 1)->count();

                                        $arr1['quiz_url'] = (($isCompleted == 1) ? null : (($vals->type == 'quiz') ? url('/').'/api/contest/'.encrypt_decrypt('encrypt',$valc->id).'/'.encrypt_decrypt('encrypt',$vals->id) . '/' . encrypt_decrypt('encrypt', $user_id) : null));
                                        $arr1['survey_url'] = null;
                                        $arr1['marks_obtained'] = $obtained ?? 0;
                                        $arr1['marks_out_of'] = $total ?? 0;
                                        $arr1['total_question'] = $totalQuestion ?? 0;
                                        $arr1['total_correct'] = $totalCorrect ?? 0;
                                        if($total != 0 && $total != null){
                                            $arr1['percentage_obtained'] = number_format((float)(($obtained * 100) / $total), 1, '.', '');
                                            $arr1['pass_status'] = (number_format((float)(($obtained * 100) / $total), 1, '.', '') >= $vals->passing) ? "Pass" : "Fail! Please retake test.";
                                        } else {
                                            $arr1['percentage_obtained'] = 0;
                                            $arr1['pass_status'] = null;
                                        } 
                                        
                                    } else {
                                        $arr1['complete_date'] = null;
                                        $arr1['quiz_url'] = ($vals->type == 'quiz') ? url('/').'/api/contest/'.encrypt_decrypt('encrypt',$valc->id).'/'.encrypt_decrypt('encrypt',$vals->id) . '/' . encrypt_decrypt('encrypt', $user_id) : null;
                                        $arr1['survey_url'] = ($vals->type == 'survey') ? url('/').'/api/survey/'.encrypt_decrypt('encrypt',$valc->id).'/'.encrypt_decrypt('encrypt',$vals->id) . '/' . encrypt_decrypt('encrypt', $user_id) : null;
                                        $arr1['marks_obtained'] = null;
                                        $arr1['marks_out_of'] = null;
                                        $arr1['percentage_obtained'] = null;
                                        $arr1['pass_status'] = null;
                                        $arr1['total_question'] = null;
                                        $arr1['total_correct'] = null;
                                    } 
                                    $arr1['is_completed'] = $isCompleted;
                                }else{
                                    $arr1['complete_date'] = null;
                                    $arr1['quiz_url'] = null;
                                    $arr1['survey_url'] = null;
                                    $arr1['marks_obtained'] = null;
                                    $arr1['marks_out_of'] = null;
                                    $arr1['is_completed'] = null;
                                    $arr1['percentage_obtained'] = null;
                                    $arr1['pass_status'] = null;
                                    $arr1['total_question'] = null;
                                    $arr1['total_correct'] = null;
                                }
                                
                                $arr1['title'] = $vals->title;
                                $arr1['description'] = $vals->description;
                                if($vals->type == 'assignment'){
                                    $arr1['file'] = (isset($isComplete->id) ? (isset($isComplete->file) ? uploadAssets('upload/course/' . $isComplete->file) : null) : null);
                                    $arr1['filename'] = $isComplete->file ?? null;
                                }else{
                                    $arr1['file'] = ($vals->details == null || $vals->details == "") ? null : uploadAssets('upload/course/' . $vals->details);
                                    $arr1['filename'] = $vals->details ?? null;
                                }
                                
                                
                                $arr1['prerequisite'] = $vals->prerequisite;
                                $arr1['sort_order'] = $vals->sort_order;
                                $question = ChapterQuiz::where('step_id', $vals->id)->get();
                                $chapter_question = [];
                                foreach($question as $valq){
                                    $arr2['id'] = $valq->id;
                                    $arr2['title'] = $valq->title;
                                    $arr2['marks'] = $valq->marks;
                                    $arr2['type'] = $valq->type;

                                    $option = ChapterQuizOption::where('quiz_id', $valq->id)->get();
                                    $chapter_option = [];
                                    foreach($option as $valo){
                                        $arr3['id'] = $valo->id;
                                        $arr3['value'] = $valo->answer_option_key;
                                        $arr3['correct'] = $valo->is_correct;
                                        $arr3['correct_status'] = ($valo->is_correct == 1) ? "Correct" : "Wrong";
                                        $chapter_option[] = $arr3;
                                    }
                                    $arr2['chapter_option'] = $chapter_option;
                                    $chapter_question[] = $arr2;
                                }
                                $arr1['chapter_question'] = $chapter_question;
                                $chapter_steps[] = $arr1;
                            }
                            $arr['chapter_steps'] = $chapter_steps;
                            $chapters[] = $arr;
                        }

                        $temp['chapters'] = $chapters;
                        $temp['chapter_count'] = $chapter_count;
                        $temp['chapter_quiz_count'] = $chapter_quiz_count;
                        $reviewAvg = DB::table('user_review as ur')->where('object_id', $item->id)->where('object_type', 1)->avg('rating');
                    }
                    $temp['id'] = $item->id;
                    
                    $tags = [];
                    if(isset($item->tags)){
                        foreach(unserialize($item->tags) as $value){
                            $name = Tag::where('id', $value)->first();
                            if(isset($name->id)){
                                $temparory['name'] = $name->tag_name ?? null;
                                $temparory['id'] = $name->id ?? null;
                                $tags[] = $temparory;
                            }
                        }
                    }
                    

                    // $chapters = DB::table('course_chapter as cc')->join('course_chapter_steps as ccs', 'cc.id', '=', 'ccs.course_chapter_id')->join('chapter_quiz as cq', 'ccs.id', '=', 'cq.step_id')->join('chapter_quiz_options as cqo', 'cq.id', '=', 'quiz_id')->where('cc.course_id', $id)->get();

                    $reviewCount = Review::where('object_id', $item->id)->where('object_type', $type)->count();
                    
                    $review = DB::table('user_review as ur')->join('users as u', 'u.id', '=', 'ur.userid')->select('u.first_name', 'u.last_name', 'ur.rating', 'ur.review', 'ur.created_date', 'u.profile_image')->where('ur.object_id', $item->id)->where('object_type', $type)->limit(2)->get();

                    $reviewArr = [];
                    foreach($review as $valReview){
                        $tempReview['first_name'] = $valReview->first_name;
                        $tempReview['last_name'] = $valReview->last_name;
                        $tempReview['rating'] = $valReview->rating;
                        $tempReview['review'] = $valReview->review;
                        if(isset($valReview->profile_image) && $valReview->profile_image != ""){
                            $valReview->profile_image = uploadAssets('upload/profile-image/' . $valReview->profile_image);
                        } else $valReview->profile_image = null;
                        $tempReview['profile_image'] = $valReview->profile_image;
                        $tempReview['created_date'] = $valReview->created_date;
                        $reviewArr[] = $tempReview;
                    }
                    
                    $temp['description'] = $item->description;
                    $temp['tags'] = $tags;
                    $temp['status'] = $item->status;
                    $temp['avg_rating'] = number_format($reviewAvg, 1, '.', '');
                    $temp['review_count'] = $reviewCount;
                    $temp['review'] = $reviewArr;
                    $temp['created_date'] = date('d/m/y,H:i', strtotime($item->created_date));
                    if ($type == 1) {
                        return response()->json(['status' => true, 'message' => ' Course Listing', 'data' => $temp]);
                    }
                } else {
                    if ($type == 1) {
                        return response()->json(['status' => true, 'message' => ' Course Listing', 'data' => '']);
                    }
                }
            } else {
                return response()->json(['status' => false, 'Message' => 'Please login']);
            }

        } catch (\Exception $e) {
            return errorMsg("Exception -> " . $e->getMessage());
        }
    }

    public function add_wishlist(Request $request)
    {
        try {
            $user = Auth::user();
            if ($user->id) {
                $validator = Validator::make($request->all(), [
                    'type' => 'required',
                    /* Type for 1 = Course (Object Type)*/
                    'id' => 'required',
                    /* Id of Course (Object ID)*/
                ]);
                if ($validator->fails()) {
                    return response()->json(['status' => false, 'message' => $validator->errors()->first()]);
                }
                $u_id = $user->id;
                $item_id = $request->id;
                $item_type = $request->type;
                $status = 1;
                $exist = Wishlist::where('userid', $u_id)->where('object_type', $item_type)->where('object_id', $item_id)->first();
                /* Status check for liked post 1 = Already liked , 2 = Create new liked post */
                if ($exist) {
                    return response()->json(['status' => false, 'message' => 'Already favourites',]);
                } else {
                    $data = DB::table('user_wishlist')->insert([
                        'object_id' => (int) $item_id,
                        'object_type' => (int) $item_type,
                        'userid' => (int) $u_id,
                        'status' => $status,
                        'created_date' => date('Y-m-d H:i:s')
                    ]);
                    $type = ($item_type==1) ? 'Course' : 'NA';
                    return response()->json(['status' => true, 'message' => $type.' has been added to wishlist',]);
                }
            } else {
                return response()->json(['status' => false, 'message' => 'Please login']);
            }
        } catch (\Exception $e) {
            return errorMsg("Exception -> " . $e->getMessage());
        }
    }

    public function remove_wishlist(Request $request)
    {
        try {
            $user = Auth::user();
            if ($user->id) {
                $validator = Validator::make($request->all(), [
                    'type' => 'required',
                    /* Type for 1 = Course (Object Type)*/
                    'id' => 'required',
                    /* Id of Course (Object ID)*/
                ]);
                if ($validator->fails()) {
                    return response()->json(['status' => false, 'message' => $validator->errors()->first()]);
                }
                $u_id = $user->id;
                $item_id = $request->id;
                $item_type = $request->type;
                $status = 0;
                $exist = Wishlist::where('userid', $u_id)->where('object_type', $item_type)->where('object_id', $item_id)->first();
                /* Status check for liked post 1 = Already liked , 2 = Create new liked post */
                if ($exist) {
                    Wishlist::where('userid', $u_id)->where('object_type', $item_type)->where('object_id', $item_id)->delete();
                    $type = ($item_type==1) ? 'Course' : 'NA';
                    return response()->json(['status' => true, 'message' => $type.' has been removed from wishlist',]);
                } else {
                    return response()->json(['status' => false, 'message' => 'Something went wrong.',]);
                }
            } else {
                return response()->json(['status' => false, 'message' => 'Please login']);
            }
        } catch (\Exception $e) {
            return errorMsg("Exception -> " . $e->getMessage());
        }
    }

    public function submit_review(Request $request)
    {
        try {
            $user = Auth::user();
            if ($user->id) {
                $validator = Validator::make($request->all(), [
                    'type' => 'required',
                    /* Type for 1 = Course (Object Type)*/
                    'id' => 'required',
                    /* Id of Course (Object ID)*/
                    'rating' => 'required',
                    'comment' => 'required'
                ]);
                if ($validator->fails()) {
                    return response()->json(['status' => false, 'message' => $validator->errors()->first()]);
                }
                $isPurchase = OrderDetail::join('orders as o', 'o.id', '=', 'order_product_detail.order_id')->where('o.user_id', auth()->user()->id)->where('order_product_detail.product_id', $request->id)->where('order_product_detail.product_type', $request->type)->select('o.id')->first();
                if(!isset($isPurchase->id)){
                    return response()->json(['status' => false, 'Message' => "Can't review for this ".($request->type==1 ? 'course' : 'NA')]);
                }
                $user_id = $user->id;
                $object_id = $request->id;
                $object_type = $request->type;
                $comment = $request->comment;
                $rating = $request->rating;
                $exist = Review::where('userid', $user_id)->where('object_id', $object_id)->where('object_type', $object_type)->first();
                if (isset($exist)) {
                    Review::where('id', $exist->id)->update(['rating' => $rating, 'review' => $comment]);
                    return response()->json(['status' => true, 'Message' => 'Review updated successfully']);
                } else {
                    $save = Review::create([
                        'userid' => $user_id,
                        'object_id' => $object_id,
                        'object_type' => $object_type,
                        'rating' => $rating,
                        'review' => $comment,
                        'created_date' => date('Y-m-d H:i:s'),
                        'status' => 1,
                    ]);
                    if ($save) {
                        return response()->json(['status' => true, 'message' => 'Review added successfully']);
                    } else {
                        return response()->json(['status' => false, 'message' => 'Already reviewed']);
                    }
                }
            } else {
                return response()->json(['status' => false, 'Message' => 'Please login']);
            }
        } catch (\Exception $e) {
            return errorMsg("Exception -> " . $e->getMessage());
        }
    }

    public function review_list(Request $request)
    {
        try {
            $user = Auth::user();
            if ($user->id) {
                $validator = Validator::make($request->all(), [
                    'id' => 'required',
                    'type' => 'required',
                ]);
                if ($validator->fails()) {
                    return response()->json(['status' => false, 'message' => $validator->errors()->first()]);
                }
                $user_id = $user->id;
                $object_id = $request->id;
                $object_type = $request->type;
                $review = Review::where('object_id', $object_id)->where('object_type', $object_type)->get();
                if (count($review) > 0) {
                    $data = [];
                    foreach ($review as $key => $c) {
                        $data[$key] = $c->toArray();
                        $data[$key]['review'] = $c->review;
                        $user_name = User::where('id', $c->userid)->first();
                        $data[$key]['user_name'] = $user_name->first_name . ' ' . $user_name->last_name;
                        $data[$key]['profile_image'] = (isset($user_name->profile_image) && ($user_name->profile_image!="")) ? uploadAssets('upload/profile-image/' . $user_name->profile_image) : null;
                    }
                    return response()->json([
                        "status" => true,
                        "message" => "Review List",
                        "review_list" => $data
                    ]);
                } else {
                    return response()->json(['status' => false, 'message' => 'No data found', 'review_list' => []]);
                }
            } else {
                return response()->json(['status' => false, 'message' => 'Please login']);
            }
        } catch (\Exception $e) {
            return errorMsg("Exception -> " . $e->getMessage());
        }
    }

    public function profile()
    {
        try {
            $user_id = Auth::user()->id;
            if ($user_id) {
                $user = User::where('id', $user_id)->first(); /*Get data of category*/
                if (isset($user)) {
                    $temp['id'] = $user->id;
                    $temp['email'] = $user->email;
                    $temp['first_name'] = ucfirst($user->first_name);
                    $temp['last_name'] = ucfirst($user->last_name);
                    $temp['phone'] = $user->phone;
                    if (isset($user->profile_image) && $user->profile_image != "") {
                        $temp['profile_image'] = uploadAssets('upload/profile-image/' . $user->profile_image);
                    } else {
                        $temp['profile_image'] = assets('assets/superadmin-images/no-image.svg');
                    }
                    $temp['company'] = $user->company;
                    $temp['professional_title'] = 'Tatto Artist';
                    $temp['timezone'] = 'Track Cert';
                    $temp['created_date'] = date('d/m/y', strtotime($user->created_date));
                }
                return response()->json(['status' => true, 'message' => 'User Details', 'data' => $temp]);
            } else {
                return response()->json(['status' => false, 'Message' => 'Please login']);
            }
        } catch (\Exception $e) {
            return errorMsg("Exception -> " . $e->getMessage());
        }
    }

    public function update_profile(Request $request)
    {
        try {
            $validator = Validator::make($request->all(), [
                'first_name' => 'required|string|max:255',
                'last_name' => 'required|string|max:255',
                'phone' => 'required',
                'profile_image' => 'mimes:jpeg,png,jpg|image',
            ]);
            if ($validator->fails()) {
                return response()->json(['status' => false, 'message' => $validator->errors()->first()]);
            } else {

                $img = null;
                if($request->profile_image){
                    $img = fileUpload($request->profile_image, 'upload/profile-image');
                    removeFile("upload/profile-image/".auth()->user()->profile_image);
                }else $img = auth()->user()->profile_image;

                User::where('id', auth()->user()->id)->update([
                    'first_name' => $request->first_name,
                    'last_name' => $request->last_name,
                    'phone' => $request->phone,
                    'profile_image' => $img
                ]);

                $user = User::where('id', auth()->user()->id)->first();
                if(isset($user->profile_image) && $user->profile_image != ""){
                    $user->profile_image = uploadAssets('upload/profile-image/'.$user->profile_image);
                }
                return response()->json([
                    'status' => true,
                    'message' => 'Profile updated successfully',
                    'user' => $user
                ]);
            }
        } catch (\Exception $e) {
            return errorMsg("Exception -> " . $e->getMessage());
        }
    }

    public function change_password(Request $request)
    {
        try {
            $user = Auth::user();
            $u_id = $user->id;
            $data = array();
            if ($u_id) {
                $old_password = $request->current_password;
                $new_password = $request->new_password;
                $datas = User::where('id', $u_id)->first();
                $u_password = $datas->password;
                if (Hash::check($old_password, $u_password)) {
                    $updatedata = array('password' => bcrypt($new_password));
                    $id = User::where('id', $u_id)->update($updatedata);
                    if ($id) {
                        $notify = new Notify;
                        $notify->added_by = auth()->user()->id;
                        $notify->user_id = auth()->user()->id;
                        $notify->module_name = 'password';
                        $notify->title = 'Password Changed Successfully';
                        $notify->message = 'Hello, ' . auth()->user()->first_name . "\nYour password has been changed successfully.";
                        if(auth()->user()->profile_image == "" || auth()->user()->profile_image == null){
                            $profile_image = null;
                        } else $profile_image = uploadAssets('upload/profile-image/'.auth()->user()->profile_image);
                        $notify->image = $profile_image;
                        $notify->is_seen = '0';
                        $notify->created_at = date('Y-m-d H:i:s');
                        $notify->updated_at = date('Y-m-d H:i:s');
                        $notify->save();

                        $data = array(
                            'msg' => 'Hello, ' . auth()->user()->first_name . "\nYour password has been changed successfully.",
                            'title' => 'Password Changed Successfully'
                        );
                        sendNotification(auth()->user()->fcm_token ?? "", $data);

                        $data['status'] = true;
                        $data['message'] = "Password Changed Successfully";
                        return response()->json($data);
                    } else {
                        $data['status'] = false;
                        $data['message'] = "Something went wrong";
                        return response()->json($data);
                    }
                } else {
                    $data['status'] = true;
                    $data['message'] = "Old Password Doesn't Matched";
                    return response()->json($data);
                }
            } else {
                $data['status'] = false;
                $data['message'] = "Please Login";
                return response()->json($data);
            }
        } catch (\Exception $e) {
            return errorMsg("Exception -> " . $e->getMessage());
        }
    }

    // public function certificates()
    // {
    //     try {
    //         $user_id = Auth::user()->id;
    //         if ($user_id) {
    //             $datas = Certificate::where('user_id', $user_id)->get(); /*Get data of category*/
    //             $response = array();
    //             if (isset($datas)) {
    //                 foreach ($datas as $keys => $item) {
    //                     $temp['id'] = $item->id;
    //                     $temp['user_id'] = $item->user_id;
    //                     if ($item->certificate_image) {
    //                         $temp['certificate_image'] = uploadAssets('upload/certificate-image/' . $item->certificate_image);
    //                     } else {
    //                         $temp['certificate_image'] = '';
    //                     }
    //                     $temp['rating'] = 4.9;
    //                     $temp['name'] = 'Max bryant';
    //                     $response[] = $temp;
    //                 }
    //             }
    //             return response()->json(['status' => true, 'message' => 'Certificate Listing', 'data' => $response]);
    //         } else {
    //             return response()->json(['status' => false, 'Message' => 'Please login']);
    //         }
    //     } catch (\Exception $e) {
    //         return errorMsg("Exception -> " . $e->getMessage());
    //     }
    // }

    public function notifications()
    {
        try {
            $user_id = Auth::user()->id;
            if ($user_id) {
                $datas = Notification::where('user_id', $user_id)->get(); /*Get data of category*/
                $response = array();
                if (isset($datas)) {
                    foreach ($datas as $keys => $item) {
                        $temp['id'] = $item->id;
                        $temp['user_id'] = $item->user_id;
                        $temp['title'] = $item->title;
                        $temp['message'] = $item->message;
                        $temp['type'] = $item->type;
                        $temp['is_read'] = $item->is_read;
                        if ($item->image) {
                            $temp['image'] = uploadAssets('upload/notification-image/' . $item->image);
                        } else {
                            $temp['image'] = '';
                        }
                        $temp['created_date'] = date('d/m/y,H:i', strtotime($item->created_at));
                        $response[] = $temp;
                    }
                }
                return response()->json(['status' => true, 'message' => 'Notifications Listing', 'data' => $response]);
            } else {
                return response()->json(['status' => false, 'Message' => 'Please login']);
            }
        } catch (\Exception $e) {
            return errorMsg("Exception -> " . $e->getMessage());
        }
    }

    public function save_card_listing(Request $request)
    {
        try {
            $user_id = Auth::user()->id;
            if ($user_id) {
                $card = CardDetail::where('userid', $user_id)->get();
                if (count($card) > 0) {
                    $response = [];
                    foreach ($card as $key => $value) {
                        $temp['card_id'] = $value->id;
                        $temp['card_number'] = "XXXX XXXX XXXX ".substr(encrypt_decrypt('decrypt', $value->card_no),12);
                        $temp['card_holder_name'] = $value->name_on_card;
                        $temp['cvv'] = encrypt_decrypt('decrypt', $value->CVV);
                        $temp['valid_upto'] = encrypt_decrypt('decrypt', $value->expiry);
                        $temp['card_type'] = $value->card_type;

                        $card_type = $value->card_type;
                        if ($card_type == 'VISA') {
                            $temp['card_image'] = uploadAssets('upload/notification-image/visa.png');
                        } else {
                            $temp['card_image'] = uploadAssets('upload/notification-image/m-card.png');
                        }
                        $response[] = $temp;
                    }
                    return response()->json(['status' => true, 'message' => 'Card list found.', 'data' => $response]);
                } else
                    return response()->json(['status' => true, 'message' => 'You have no card.']);
            } else {
                return response()->json(['status' => false, 'Message' => 'Please login']);
            }
        } catch (\Exception $e) {
            return response()->json(['status' => false, 'message' => 'Exception => ' . $e->getMessage()]);
        }
    }

    public function add_card(Request $request)
    {
        try {
            $user_id = Auth::user()->id;
            if ($user_id) {
                $validator = Validator::make($request->all(), [
                    'card_number' => 'required|numeric',
                    'valid_upto' => 'required',
                    'cvv' => 'required',
                    'card_holder_name' => 'required'
                ]);
                if ($validator->fails()) {
                    return response()->json(['status' => false, 'message' => $validator->errors()->first()]);
                } else {
                    $card = new CardDetail;
                    $card->userid = $user_id;
                    $card->card_no = encrypt_decrypt('encrypt', $request->card_number);
                    $card->expiry = encrypt_decrypt('encrypt', $request->valid_upto);
                    $card->CVV = encrypt_decrypt('encrypt', $request->cvv);
                    $card->name_on_card = $request->card_holder_name;
                    $card->card_type = 'VISA';
                    $card->created_date = date('Y-m-d H:i:s');
                    $card->save();
                    return response()->json(['status' => true, 'message' => 'Card Added']);
                }
            } else {
                return response()->json(['status' => false, 'Message' => 'Please login']);
            }
        } catch (\Exception $e) {
            return errorMsg("Exception -> " . $e->getMessage());
        }
    }

    public function delete_card(Request $request)
    {
        try {
            $user_id = Auth::user()->id;
            if ($user_id) {
                $validator = Validator::make($request->all(), [
                    'id' => 'required|numeric',
                ]);
                if ($validator->fails()) {
                    return response()->json(['status' => false, 'message' => $validator->errors()->first()]);
                }
                $card = CardDetail::where('id', $request->id)->delete();
                return response()->json(['status' => true, 'message' => 'Card deleted']);
            } else {
                return response()->json(['status' => false, 'Message' => 'Please login']);
            }
        } catch (\Exception $e) {
            return errorMsg("Exception -> " . $e->getMessage());
        }
    }

    public function cart_details_payment_page()
    {
        try {
            $user_id = Auth::user()->id;
            if ($user_id) {
                $tax = Setting::where('attribute_code','tax')->first();
                $shopping_cart = AddToCart::where('userid', auth()->user()->id)->where('object_type', 1)->get();
                if(count($shopping_cart)>0){
                    $cart_value = AddToCart::where('userid', $user_id)->where('object_type', 1)->sum(\DB::raw('cart_value * quantity'));
                    $cart_count = cartCount();
                    $discount = 0;
                    foreach($shopping_cart as $cart){
                        $coupon = Coupon::where('object_type', 1)->where('object_id', $cart->object_id)->where('id', $cart->coupon_id)->first();
                        if(isset($coupon->id)){
                            $discount += (($cart->cart_value*$coupon->coupon_discount_amount)/100); 
                        }
                    }
                    $total_amount = ($cart_value);
                    if(isset($tax->id) && $tax->attribute_value != '' && $tax->attribute_value != 0)
                        $tax_amount = ($total_amount*$tax->attribute_value)/100;
                    else $tax_amount = 0;
                    $ship_price = 0;
                    $cart_value = $cart_value+$discount;
                    $type = 1;
                }else{
                    $cart = TempData::where('user_id', auth()->user()->id)->where('type', 'cart')->first();
                    if (isset($cart->id)) {
                        $old = unserialize($cart->data);
                        $cart_value = $old['subTotal'];
                        $cart_count = cartCount();
                        $discount = $old['appliedCouponPrice'] ?? 0;
                        if(isset($tax->id) && $tax->attribute_value != '' && $tax->attribute_value != 0)
                            $tax_amount = $old['tax'];
                        else $tax_amount = 0;
                        $total_amount = $old['subTotal'] - $old['appliedCouponPrice'] + $old['shippingPrice'];
                        $ship_price = $old['shippingPrice'] ?? 0;
                        $type = 2;
                    } else return response()->json(['status' => false, 'message' => 'Cart empty!']);
                }
                return response()->json([
                    'status' => true,
                    'message' => 'Cart details.',
                    'sub_total' => number_format((float) $cart_value, 2, '.', ''),
                    'order_count' => $cart_count,
                    'discount' => $discount,
                    'tax' => number_format((float) $tax_amount, 2, '.', ''),
                    'shipping_cost' => $ship_price,
                    'total' => number_format((float) ($total_amount+$tax_amount),2, '.', ''),
                    'type' => $type,
                ]);
            } else {
                return response()->json(['status' => false, 'Message' => 'Please login']);
            }
        } catch (\Exception $e) {
            return errorMsg("Exception -> " . $e->getMessage());
        }
    }

    public function coupon_list()
    {
        $data = array();
        $user_id = Auth::user()->id;
        if ($user_id) {
            $val = Coupon::where('status', 1)->get();
            if (!empty($val)) {
                $data['status'] = 1;
                $data['message'] = 'Coupon data';
                $data['data'] = $val;
                return response()->json($data);
            } else {
                $data['status'] = 0;
                $data['message'] = 'No Records';
                $data['data'] = '';
                return response()->json($data);
            }
        } else {
            $data['status'] = 0;
            $data['message'] = 'Please login';
            $data['data'] = '';
            return response()->json($data);
        }
    }

    public function my_order(Request $request)
    {
        try {
            $user_id = Auth::user()->id;
            if ($user_id) {
                $validator = Validator::make($request->all(), [
                    'type' => 'required',
                ]);
                if ($validator->fails()) {
                    return response()->json(['status' => false, 'message' => $validator->errors()->first()]);
                }
                $orders = OrderDetail::leftJoin('orders as o', 'o.id', '=', 'order_product_detail.order_id')->where('order_product_detail.product_type', $request->type)->where('o.user_id', $user_id);
                if($request->type == 1){
                    $orders->leftJoin('course as c', 'c.id', '=', 'order_product_detail.product_id')->leftJoin('category as cat', 'cat.id', '=', 'c.category_id')->select('o.id as order_id', 'o.order_number', 'o.total_amount_paid', 'o.status as order_status', 'o.created_date as order_date', 'c.title as title', 'c.description as desc', 'c.course_fee as price', 'c.admin_id as added_by', 'c.valid_upto', 'c.id as id', 'c.introduction_image', 'cat.name as catname', 'c.category_id as catid', 'order_product_detail.id as itemid', 'o.taxes', 'order_product_detail.order_status as order_pro_status');
                    if($request->filled('title')){
                        $orders->where('c.title', 'like', '%' . $request->title . '%');
                    }
                    if($request->filled('category')){
                        $orders->whereIntegerInRaw('c.category_id', $request->category);
                    }
                }
                if($request->filled('start_date')){ 
                    $orders->whereDate('o.created_date','>=',$request->start_date);   
                }
                if($request->filled('end_date')){
                    $orders->whereDate('o.created_date','<=',$request->end_date);   
                }
                $orders = $orders->orderByDesc('o.id')->get();
                $response = [];
                if (count($orders) > 0) {
                    foreach($orders as $value){
                        
                        $temp['order_id'] = $value->order_id;
                        $temp['order_date'] = date('d M, Y H:iA', strtotime($value->order_date));
                        $temp['order_number'] = $value->order_number;
                        $temp['id'] = $value->id ?? 0;
                        if($request->type==1){
                            $course_purchase = Setting::where('attribute_code','course_purchase_validity')->first();
                            if(isset($course_purchase->id) && $course_purchase->attribute_value != '' && $course_purchase->attribute_value != 0){
                                $valid = date('d M, Y', strtotime($value->order_date . '+' . $course_purchase->attribute_value . 'days'));
                            }
                            $temp['course_valid_date'] = $valid;
                            $temp['introduction_video'] = uploadAssets('upload/disclaimers-introduction/'.$value->introduction_image);
                            $isCourseComplete = UserCourse::where('course_id', $value->id)->where('user_id', $user_id)->where('is_expire', 0)->where('status', 1)->orderByDesc('id')->first();
                            if(isset($isCourseComplete->id)){
                                $temp['course_completed'] = 1;
                                $temp['certificate'] = url('/')."/api/download-pdf/".encrypt_decrypt('encrypt',$isCourseComplete->course_id)."/".encrypt_decrypt('encrypt',$user_id);
                            } 
                            else{
                                $temp['course_completed'] = 0;
                                $temp['certificate'] = null;
                            }
                        }
                        $temp['item_id'] = $value->itemid ?? null;
                        $temp['title'] = $value->title ?? "NA";
                        $temp['description'] = $value->desc ?? "NA";
                        $temp['total_amount_paid'] = $value->total_amount_paid ?? 0;
                        $temp['tax'] = $value->taxes ?? 0;
                        $temp['price'] = $value->price ?? 0;
                        $temp['sale_price'] = $value->sale_price ?? 0;
                        $temp['category_id'] = $value->catid ?? null;
                        $temp['category_name'] = $value->catname ?? null;
                        $avgRating = DB::table('user_review as ur')->where('object_id', $value->id)->where('object_type', $request->type)->avg('rating');
                        $temp['avg_rating'] = number_format($avgRating, 1, '.', '');
                        $temp['order_status'] = ($value->order_status == 1) ? 'Paid' : 'Payment Pending';
                        
                        $ContentCreator = User::where('id', $value->added_by)->first();
                        if (isset($ContentCreator->profile_image) && $ContentCreator->profile_image != '' && $ContentCreator->profile_image != null) {
                            $profile_image = uploadAssets('upload/profile-image/' . $ContentCreator->profile_image);
                        } else {
                            $profile_image = '';
                        }
                        $temp['content_creator_image'] = $profile_image;
                        $temp['content_creator_name'] = $ContentCreator->first_name .' '.$ContentCreator->last_name ?? '';
                        $temp['content_creator_id'] = isset($ContentCreator->id) ? $ContentCreator->id : '';

                        $review = Review::where('object_id', $value->id)->where('object_type', $request->type)->where('userid', $user_id)->first();
                        if(isset($review->id)) $temp['isReviewed'] = 1;
                        else $temp['isReviewed'] = 0;
                        $response[] = $temp;

                    }
                    $top_category = Category::where('status', 1)->orderBy('id', 'DESC')->get(); /*Get data of category*/
                    $b2 = array();
                    $TopCategory = array();
                    if(count($top_category) > 0)
                    {
                        foreach ($top_category as $k => $data) {
                            $b2['id'] = isset($data->id) ? $data->id : '';
                            $b2['category_name'] = isset($data->name) ? $data->name : '';
                            if (!empty($data->icon)) {
                                $b2['category_image'] = uploadAssets('upload/category-image/' . $data->icon);
                            } else {
                                $b2['category_image'] = '';
                            }
                            $b2['cat_status'] = isset($data->status) ? $data->status : '';
                            $b2['created_at'] = date('d/m/y,H:i', strtotime($data->created_date));
                            $TopCategory[] = $b2;
                        }
                    }

                    return response()->json([
                        'status' => true,
                        'message' => 'My Order',
                        'data' => $response,
                        'category' => $top_category
                    ]);
                } else {
                    return response()->json([
                        'status' => true,
                        'message' => 'No Order found',
                        'data' => $response
                    ]);
                }
            } else {
                return response()->json(['status' => false, 'Message' => 'Please login']);
            }
        } catch (\Exception $e) {
            return errorMsg("Exception -> " . $e->getMessage());
        }
    }

    // public function remove_coupon(Request $request)
    // {
    //     $data = array();
    //     if ($this->hasUser)
    //     {
    //         $validator = Validator::make($request->all() , [
    //             //'cart_id' => 'required|integer|min:1',
    //             'coupon_id' =>  'required',
    //         ]);
    //         if ($validator->fails())
    //         {
    //             return response()->json($validator->errors() , 202);
    //         }
    //         $coupons = ApplyCoupon::where('id','=',$request->coupon_id)->where('user_id','=',$this->hasUser->id)->first();
    //         if(!empty($coupons))
    //         {
    //             $coupons = ApplyCoupon::where('id','=',$request->coupon_id)->where('user_id','=',$this->hasUser->id)->delete();
    //             $data['status']=1;
    //             $data['message']='Coupon remove sucessfully';
    //             return response()->json($data);
    //         }else{
    //             $data['status']=1;
    //             $data['message']='No available coupon cart';
    //             return response()->json($data);
    //         }
    //     }else
    //     {
    //         $data['status'] = 0;
    //         $data['message'] = 'Please login';
    //         return response()->json($data);
    //     }
    // }

    public function cart_count(Request $request){
        try{
            $cart = cartCount();
            $notification = Notify::where('user_id', auth()->user()->id)->count();
            return response()->json([
                'status' => true,
                'message' => 'Cart count',
                'data' => $cart,
                'notification' => ($notification > 0) ? true : false,
                'notification_count' => $notification,
            ]);
        } catch (\Exception $e) {
            return errorMsg("Exception -> " . $e->getMessage());
        }
    }

    public function assignment_upload_file(Request $request){
        try{
            $validator = Validator::make($request->all(), [
                'file' => 'required|max:2048',
                'chapter_step_id' => 'required'
            ]);
            if ($validator->fails()) {
                return response()->json(['status' => false, 'message' => $validator->errors()->first()]);
            } else{
                $fileName = fileUpload($request->file, 'upload/course');  

                $step = CourseChapterStep::where('id', $request->chapter_step_id)->where('type', 'assignment')->first();
                if(isset($step->course_chapter_id)){
                    $courseChapter = CourseChapter::where('id', $step->course_chapter_id)->first();
                    if(isset($courseChapter->course_id)){
                        $check = UserCourse::where('course_id', $courseChapter->course_id)->where('user_id', auth()->user()->id)->where('is_expire', 0)->orderByDesc('id')->first();
                        if(!isset($check->id)) return response()->json(['status' => false, 'message' => 'Please purchase this course first']);
                        $userChapterStatus = new UserChapterStatus;
                        $userChapterStatus->userid = auth()->user()->id;
                        $userChapterStatus->course_id = $courseChapter->course_id;
                        $userChapterStatus->chapter_id = $step->course_chapter_id;
                        $userChapterStatus->step_id = $request->chapter_step_id;
                        $userChapterStatus->step_type = $step->type;
                        $userChapterStatus->file = $fileName;
                        $userChapterStatus->status = 1;
                        $userChapterStatus->created_date = date('Y-m-d H:i:s');
                        $userChapterStatus->save();

                        $this->course_complete(auth()->user()->id, $courseChapter->course_id);
                        return response()->json(['status' => true, 'message' => 'File uploaded successfully', 'data' => $fileName]);
                    } else return response()->json(['status' => false, 'message' => 'Something went wrong']);
                } else return response()->json(['status' => false, 'message' => 'Incorrect step id']);
            }
        } catch (\Exception $e) {
            return errorMsg("Exception -> " . $e->getMessage());
        }
    }

    public function mark_complete(Request $request){
        try{
            $validator = Validator::make($request->all(), [
                'chapter_step_id' => 'required'
            ]);
            if ($validator->fails()) {
                return response()->json(['status' => false, 'message' => $validator->errors()->first()]);
            } else{
                $courseStep = CourseChapterStep::where('id', $request->chapter_step_id)->first();
                if(isset($courseStep->course_chapter_id)){
                    $courseChapter = CourseChapter::where('id', $courseStep->course_chapter_id)->first();
                    if(isset($courseChapter->course_id)){
                        $check = UserCourse::where('course_id', $courseChapter->course_id)->where('user_id', auth()->user()->id)->where('is_expire', 0)->orderByDesc('id')->first();
                        if(!isset($check->id)) return response()->json(['status' => false, 'message' => 'Please purchase this course first']);
                        $userChapterStatus = new UserChapterStatus;
                        $userChapterStatus->userid = auth()->user()->id;
                        $userChapterStatus->course_id = $courseChapter->course_id;
                        $userChapterStatus->chapter_id = $courseStep->course_chapter_id;
                        $userChapterStatus->step_id = $request->chapter_step_id;
                        $userChapterStatus->step_type = $courseStep->type;
                        $userChapterStatus->file = null;
                        $userChapterStatus->status = 1;
                        $userChapterStatus->created_date = date('Y-m-d H:i:s');
                        $userChapterStatus->save();

                        $this->course_complete(auth()->user()->id, $courseChapter->course_id);
                        return response()->json(['status' => true, 'message' => ucwords($courseStep->type).' has been marked as completed.']);
                    } else return response()->json(['status' => false, 'message' => 'Something went wrong']);
                }else return response()->json(['status' => false, 'message' => 'Incorrect step id']);
            }
        } catch (\Exception $e) {
            return errorMsg("Exception -> " . $e->getMessage());
        }
    }

    public function contestQuizSurvey(Request $request, $chapterId, $quizId, $userId){
        try{
            $chapterId = encrypt_decrypt('decrypt',$chapterId);
            $quizId = encrypt_decrypt('decrypt',$quizId);
            $userId = encrypt_decrypt('decrypt',$userId);
            $course = CourseChapterStep::where('course_chapter_id', $chapterId)->where('id', $quizId)->whereIn('type', ['quiz'])->first();
            if(isset($course)){
                $data = [];
                $quiz = ChapterQuiz::where('step_id', $course->id)->whereIn('type', ['quiz', 'survey'])->get();
                foreach($quiz as $val1){
                    $temp['step_id'] = $course->id;
                    $temp['question_id'] = $val1->id;
                    $temp['type'] = $val1->type;
                    $temp['title'] = $val1->title;
                    $temp['marks'] = $val1->marks;
                    $optionCount = ChapterQuizOption::where('quiz_id', $val1->id)->where('is_correct', '1')->count();
                    $temp['optionCount'] = $optionCount;
                    $options = ChapterQuizOption::where('quiz_id', $val1->id)->get();
                    $temp['option'] = [];
                    $quizAnswer = UserQuizAnswer::where('quiz_id', $course->id)->where('question_id', $val1->id)->first();
                    $temp['quiz_answer'] = isset($quizAnswer) ? $quizAnswer->answer_option_key : null;
                    foreach($options as $val2){
                        $temp2['id'] = $val2->id;
                        $temp2['answer'] = $val2->answer_option_key;
                        $temp2['correct'] = $val2->is_correct;
                        $temp['option'][] = $temp2;
                    }
                    $data[] = $temp;
                }
                // dd($data);
                $questionCount = ChapterQuiz::where('step_id', $course->id)->whereIn('type', ['quiz', 'survey'])->count();
                return view('home.contest-page')->with(compact('data', 'questionCount', 'userId'));
            } else return response()->json(['status'=> false, 'message'=> 'Invalid URL']);
        } catch (\Exception $e) {
            return errorMsg("Exception -> " . $e->getMessage());
        }
    }

    public function surveyFormUrl(Request $request, $chapterId, $surveyId, $userId){
        try{
            $chapterId = encrypt_decrypt('decrypt',$chapterId);
            $surveyId = encrypt_decrypt('decrypt',$surveyId);
            $userId = encrypt_decrypt('decrypt',$userId);
            $course = CourseChapterStep::where('course_chapter_id', $chapterId)->where('id', $surveyId)->whereIn('type', ['survey'])->first();
            if(isset($course)){
                // dd($course);
                $data = [];
                $quiz = ChapterQuiz::where('step_id', $course->id)->whereIn('type', ['survey'])->get();
                // dd($quiz);
                foreach($quiz as $val1){
                    $temp['step_id'] = $course->id;
                    $temp['question_id'] = $val1->id;
                    $temp['type'] = $val1->type;
                    $temp['title'] = $val1->title;
                    $options = ChapterQuizOption::where('quiz_id', $val1->id)->get();
                    $temp['option'] = [];
                    $quizAnswer = UserQuizAnswer::where('quiz_id', $course->id)->where('question_id', $val1->id)->first();
                    $temp['quiz_answer'] = isset($quizAnswer) ? $quizAnswer->answer_option_key : null;
                    foreach($options as $val2){
                        $temp2['id'] = $val2->id;
                        $temp2['answer'] = $val2->answer_option_key;
                        $temp2['correct'] = $val2->is_correct;
                        $temp['option'][] = $temp2;
                    }
                    $data[] = $temp;
                }
                // dd($data);
                $questionCount = ChapterQuiz::where('step_id', $course->id)->whereIn('type', ['survey'])->count();
                return view('home.survey-page')->with(compact('data', 'questionCount', 'userId'));
            } else return response()->json(['status'=> false, 'message'=> 'Invalid URL']);
        } catch (\Exception $e) {
            return errorMsg("Exception -> " . $e->getMessage());
        }
    }

    public function contestForm(Request $request){
        try{
            $validator = Validator::make($request->all(), [
                'quiz_id' => 'required',
                'question_id' => 'required',
                'option' => 'required',
                'user_id' => 'required',
            ]);
            if ($validator->fails()) {
                return response()->json(['status' => false, 'message' => $validator->errors()->first()]);
            } else{
                $answer = ChapterQuizOption::where('quiz_id', $request->question_id)->where('id', $request->option)->where('is_correct', '1')->first();
                if(isset($answer->id)){
                    $marks = ChapterQuiz::where('step_id', $request->quiz_id)->where('id', $request->question_id)->first();
                } else $marks = null;
                $isRetake = UserQuizAnswer::where('userid', $request->user_id)->where('quiz_id', $request->quiz_id)->where('question_id', $request->question_id)->first();
                if(isset($isRetake->id)){
                    UserQuizAnswer::where('userid', $request->user_id)->where('quiz_id', $request->quiz_id)->where('question_id', $request->question_id)->update([
                        'marks_obtained' => isset($marks) ? $marks->marks : 0,
                        'status' => isset($answer->id) ? 1 : 0,
                        'answer_option_key' => $request->option
                    ]);
                }else {
                    $option = new UserQuizAnswer;
                    $option->userid = $request->user_id;
                    $option->quiz_id = $request->quiz_id;
                    $option->question_id = $request->question_id;
                    $option->marks_obtained = isset($marks) ? $marks->marks : 0;
                    $option->answer_option_key = $request->option;
                    $option->created_date = date('Y-m-d H:i:s');
                    $option->status = isset($answer->id) ? 1 : 0;
                    $option->save();
                }
                return response()->json(['status'=> true, 'message'=> 'Answer is save successfully.', 'request'=> $request->all(), 'answer_status' => isset($answer->id) ? 1 : 0]);
            }
        } catch (\Exception $e) {
            return errorMsg("Exception -> " . $e->getMessage());
        }
    }

    public function surveyForm(Request $request){
        try{
            $validator = Validator::make($request->all(), [
                'quiz_id' => 'required',
                'question_id' => 'required',
                'option' => 'required',
                'user_id' => 'required',
            ]);
            if ($validator->fails()) {
                return response()->json(['status' => false, 'message' => $validator->errors()->first()]);
            } else{
                    $option = new UserQuizAnswer;
                    $option->userid = $request->user_id;
                    $option->quiz_id = $request->quiz_id;
                    $option->question_id = $request->question_id;
                    $option->marks_obtained = null;
                    $option->answer_option_key = $request->option;
                    $option->created_date = date('Y-m-d H:i:s');
                    $option->status = 1;
                    $option->save();
                
                return response()->json(['status'=> true, 'message'=> 'Feedback is save successfully.', 'request'=> $request->all()]);
            }
        } catch (\Exception $e) {
            return errorMsg("Exception -> " . $e->getMessage());
        }
    }

    public function resultQuizSurvey(Request $request, $quizId, $userId){
        try{
            $quizId = encrypt_decrypt('decrypt',$quizId);
            $userId = encrypt_decrypt('decrypt',$userId);
            $courseStep = CourseChapterStep::where('id', $quizId)->whereIn('type', ['quiz'])->first();
            $total = ChapterQuiz::where('step_id', $quizId)->whereIn('type', ['quiz', 'survey'])->sum('marks');
            $obtained = UserQuizAnswer::where('quiz_id', $quizId)->where('userid',$userId)->sum('marks_obtained');
            $totalQuestion = ChapterQuiz::where('step_id', $quizId)->whereIn('type', ['quiz', 'survey'])->count();
            $totalCorrect = UserQuizAnswer::where('quiz_id', $quizId)->where('userid',$userId)->where('status', 1)->count();
            $totalWrong = UserQuizAnswer::where('quiz_id', $quizId)->where('userid',$userId)->where('status', 0)->count();
            $passingPercentage = $courseStep->passing ?? 33;
            if(isset($courseStep->course_chapter_id)){
                $chapterId = $courseStep->course_chapter_id;
                $courseChapter = CourseChapter::where('id', $courseStep->course_chapter_id)->first();
                if( number_format((float)(($obtained * 100) / $total), 1, '.', '') >= $passingPercentage ){
                    $status = 1;
                }else{
                    $status = 2;
                }
                if(isset($courseChapter->course_id)){
                    $isExist = UserChapterStatus::where('userid', $userId)->where('course_id', $courseChapter->course_id)->where('chapter_id', $courseStep->course_chapter_id)->where('step_id', $courseStep->id)->where('step_type', $courseStep->type)->first();
                    if(isset($isExist->id)){
                        UserChapterStatus::where('id', $isExist->id)->update([
                            'status' => $status ?? 0,
                        ]);
                    }else{
                        $userChapterStatus = new UserChapterStatus;
                        $userChapterStatus->userid = $userId;
                        $userChapterStatus->course_id = $courseChapter->course_id;
                        $userChapterStatus->chapter_id = $courseStep->course_chapter_id;
                        $userChapterStatus->step_id = $courseStep->id;
                        $userChapterStatus->step_type = $courseStep->type;
                        $userChapterStatus->file = null;
                        $userChapterStatus->status = $status ?? 0;
                        $userChapterStatus->created_date = date('Y-m-d H:i:s');
                        $userChapterStatus->save(); 
                    }
                    $this->course_complete($userId, $courseChapter->course_id);
                }
            }
            return view('home.result-page')->with(compact('obtained', 'total', 'totalWrong', 'totalCorrect', 'totalQuestion', 'passingPercentage', 'quizId', 'userId', 'chapterId'));
        } catch (\Exception $e) {
            return errorMsg("Exception -> " . $e->getMessage());
        }
    }

    public function resultSurvey(Request $request, $quizId, $userId){
        try{
            $quizId = encrypt_decrypt('decrypt',$quizId);
            $userId = encrypt_decrypt('decrypt',$userId);
            $courseStep = CourseChapterStep::where('id', $quizId)->whereIn('type', ['survey'])->first();
            if(isset($courseStep->course_chapter_id)){
                $chapterId = $courseStep->course_chapter_id;
                $courseChapter = CourseChapter::where('id', $courseStep->course_chapter_id)->first();
                if(isset($courseChapter->course_id)){
                    $isExist = UserChapterStatus::where('userid', $userId)->where('course_id', $courseChapter->course_id)->where('chapter_id', $courseStep->course_chapter_id)->where('step_id', $courseStep->id)->where('step_type', $courseStep->type)->first();
                    if(isset($isExist->id)){
                        UserChapterStatus::where('id', $isExist->id)->update([
                            'status' => $status ?? 0,
                        ]);
                    }else{
                        $userChapterStatus = new UserChapterStatus;
                        $userChapterStatus->userid = $userId;
                        $userChapterStatus->course_id = $courseChapter->course_id;
                        $userChapterStatus->chapter_id = $courseStep->course_chapter_id;
                        $userChapterStatus->step_id = $courseStep->id;
                        $userChapterStatus->step_type = $courseStep->type;
                        $userChapterStatus->file = null;
                        $userChapterStatus->status = 1;
                        $userChapterStatus->created_date = date('Y-m-d H:i:s');
                        $userChapterStatus->save(); 
                    }
                    $this->course_complete($userId, $courseChapter->course_id);
                }
            }
            return view('home.survey-result')->with(compact('quizId', 'userId', 'chapterId'));
        } catch (\Exception $e) {
            return errorMsg("Exception -> " . $e->getMessage());
        }
    }

    public function generate_pdf($id, $uid) {
        $id = encrypt_decrypt('decrypt', $id);
        $uid = encrypt_decrypt('decrypt', $uid);
        $user = User::where('id', $uid)->first();
        $admin = User::where('role', 3)->first();
        $course = Course::join('users as u', 'u.id', '=', 'course.admin_id')->where('course.id', $id)->select('course.title', 'u.first_name', 'u.last_name', 'u.company_name', 'u.professional_title', 'u.signature', 'u.business_logo')->first();
        // dd($course);
        $date = UserCourse::where('user_id', $uid)->where('course_id', $id)->where('is_expire', 0)->orderByDesc('id')->first();

        $html = view('home.certificates', compact('course', 'date', 'user', 'admin'))->render();

        $mpdf = new \Mpdf\Mpdf([
            'format'=> 'A4',
            'orientation' => 'L',
            'mode' => 'utf-8'
        ]);
        $mpdf->img_dpi = 6;
        $mpdf->imageVars['myvariable'] = '';
        // $mpdf->showImageErrors = true;
        $mpdf->SetDisplayMode('fullpage');
        $mpdf->list_indent_first_level = 0; 
        $mpdf->WriteHTML($html);
        $mpdf->Output();
    }

    public function certificates(Request $request){
        try{
            $data = UserCourse::join('course as c', 'c.id', '=', 'user_courses.course_id')->leftJoin('users as u', 'c.admin_id', '=', 'u.id')->where('user_courses.user_id', auth()->user()->id)->where('user_courses.status', 1)->where('user_courses.is_expire', 0)->select('c.id as course_id', 'user_courses.user_id as user_id', 'c.title', 'user_courses.status', 'u.first_name', 'u.last_name', 'u.profile_image', 'c.introduction_image')->orderByDesc('user_courses.id')->get();
            $res = [];
            foreach($data as $val){
                $temp['course_id'] = $val->course_id;
                $temp['user_id'] = $val->user_id;
                $temp['title'] = $val->title;
                $temp['status'] = $val->status;

                if(isset($val->profile_image)) $temp['creator_image'] = uploadAssets('upload/profile-image/'.$val->profile_image);
                else $temp['creator_image'] = null;
                
                if(isset($val->introduction_image)) $temp['video'] = uploadAssets('upload/disclaimers-introduction/'.$val->introduction_image);
                else $temp['video'] = null;

                $temp['creator_name'] = $val->first_name . ' ' . $val->last_name;
                $avgRating = DB::table('user_review as ur')->where('object_id', $val->course_id)->where('object_type', 1)->avg('rating');
                $temp['avg_rating'] = number_format($avgRating, 1, '.', '');
                $temp['download_pdf'] = url('/')."/api/download-pdf/".encrypt_decrypt('encrypt',$val->course_id)."/".encrypt_decrypt('encrypt',$val->user_id);
                $res[] = $temp;
            }
            return response()->json(['status' => true, 'message' => 'Suggested Course Listing', 'data' => $res]);
        } catch (\Exception $e) {
            return errorMsg("Exception -> " . $e->getMessage());
        }
    }

    public function special_courses(Request $request){
        try{
            $users = User::where('role', 3)->pluck('id');
            $courses = Course::leftJoin('users', function($join) {
                $join->on('course.admin_id', '=', 'users.id');
            })->where('course.status', 1)->whereIn('course.admin_id', $users);
            if($request->filled('title')){
                $courses->where('course.title', 'like' , '%' . $request->title . '%');
            }
            if($request->filled('category')){
                $courses->whereIntegerInRaw('course.category_id', $request->category);
            }
            if($request->filled('price')){
                if($request->price == 1) $courses->orderByDesc('course.course_fee');
                else $courses->orderBy('course.course_fee');
            } else{
                $courses->orderBy('course.id', 'DESC');
            }
            $courses = $courses->select('course.*', 'users.first_name', 'users.last_name','users.profile_image','users.category_name')->get();
            $data = [];
            foreach($courses as $value){
                if($request->filled('tag'))
                    if(!in_array($request->tag, unserialize($value->tags))) continue;
                $temp['course_fee'] = $value->course_fee;
                $temp['valid_upto'] = $value->valid_upto;
                if (!empty($value->certificates)) {
                    $temp['certificates_image'] = uploadAssets('upload/course-certificates/' . $value->certificates);
                } else {
                    $temp['certificates_image'] = '';
                }
                if (!empty($value->introduction_image)) {
                    $temp['introduction_video'] = uploadAssets('upload/disclaimers-introduction/' . $value->introduction_image);
                } else {
                    $temp['introduction_video'] = '';
                }
                $exists = Like::where('reaction_by', '=', auth()->user()->id)->where('object_id', '=', $value->id)->where('object_type', '=', 1)->first();
                if (isset($exists)) {
                    $temp['isLike'] = 1;
                } else {
                    $temp['isLike'] = 0;
                }
                $wishlist = Wishlist::where('userid', '=', auth()->user()->id)->where('object_id', '=', $value->id)->where('object_type', '=', 1)->where('status', 1)->first();
                if (isset($wishlist)) {
                    $temp['isWishlist'] = 1;
                } else {
                    $temp['isWishlist'] = 0;
                }
                $temp['title'] = $value->title;
                if ($value->profile_image) {
                    $profile_image = uploadAssets('upload/profile-image/'.$value->profile_image);
                } else {
                    $profile_image = '';
                }
                $temp['content_creator_image'] = $profile_image;
                $temp['content_creator_name'] = $value->first_name.' '.$value->last_name;
                $temp['content_creator_category'] = isset($value->category_name) ? $value->category_name : '';
                $temp['content_creator_id'] = isset($value->admin_id) ? $value->admin_id : '';
                $temp['id'] = $value->id;
                $temp['description'] = $value->description;
                $temp['status'] = $value->status;
                $avgRating = DB::table('user_review as ur')->where('object_id', $value->id)->where('object_type', 2)->avg('rating');
                $temp['avg_rating'] = number_format($avgRating, 1, '.', '');
                if($request->filled('rating'))
                    if($avgRating < min($request->rating)) continue;
                $temp['created_date'] = date('d/m/y,H:i', strtotime($value->created_date));
                $tags = [];
                if(isset($value->tags)){
                    foreach(unserialize($value->tags) as $val){
                        $name = Tag::where('id', $val)->first();
                        $temparory['name'] = $name->tag_name;
                        $temparory['id'] = $name->id;
                        $tags[] = $temparory;
                    }
                }
                $temp['tags'] = $tags;
                $data[] = $temp;
            }
            return response()->json(['status' => true, 'message' => 'Special Courses', 'data' => $data]);
        } catch (\Exception $e) {
            return errorMsg("Exception -> " . $e->getMessage());
        }
    }

    public function course_complete($uid, $courseid){
        try{
            $check = UserCourse::where('course_id', $courseid)->where('user_id', $uid)->where('status', 1)->where('is_expire', 0)->orderByDesc('id')->first();
            if(isset($check->id)) return;
            $isComplete = true;
            $chapters = CourseChapter::where('course_id', $courseid)->pluck('id');
            foreach($chapters as $val){
                $steps = CourseChapterStep::where('course_chapter_id', $val)->pluck('id');
                foreach($steps as $item){
                    $myCompleteChapters = UserChapterStatus::where('userid', $uid)->where('course_id', $courseid)->where('chapter_id', $val)->where('step_id', $item)->where('status', 1)->first();
                    if(!isset($myCompleteChapters->id)) {
                        $isComplete = false;
                        break;
                    }
                }
            }
            UserCourse::where('course_id', $courseid)->where('user_id', $uid)->where('is_expire', 0)->orderByDesc('id')->update([
                'status' => $isComplete ? 1 : 0,
                'updated_date' => date('Y-m-d H:i:s'),
            ]);
        } catch (\Exception $e) {
            return errorMsg("Exception -> " . $e->getMessage());
        }
    }

    public function order_detail(Request $request){
        try{
            $validator = Validator::make($request->all(), [
                'order_id' => 'required',
                'item_id' => 'required',
            ]);
            if ($validator->fails()) {
                return response()->json(['status' => false, 'message' => $validator->errors()->first()]);
            }else{
                $id = $request->order_id;
                $order = Order::where('orders.id', $id)->leftJoin('users as u', 'u.id', '=', 'orders.user_id')->select('orders.total_amount_paid', 'u.first_name', 'u.last_name', 'u.email', 'u.profile_image', 'u.phone', 'u.role', 'u.status as ustatus', 'orders.id', 'orders.order_number', 'orders.created_date', 'orders.status', 'orders.taxes', 'orders.delivery_charges', 'orders.coupon_discount_price', 'orders.amount', DB::raw("orders.amount - orders.coupon_discount_price + orders.taxes as total_amount"))->first();

                $order->total_amount = number_format((float) $order->total_amount, 2, '.', '');
                $order->total_amount_paid = number_format((float) $order->total_amount_paid, 2, '.', '');
                $order->sub_total = number_format((float) $order->amount ?? 0, 2, '.', '');
                $order->taxes = number_format((float) $order->taxes, 2, '.', '');
                $order->coupon_discount_price = number_format((float) $order->coupon_discount_price ?? 0, 2, '.', '');
                $order->created_date = date('d M, Y H:iA', strtotime($order->created_date));
                $order->shipping_cost = number_format((float) $order->delivery_charges ?? 0, 2, '.', '');

                $avgRating = DB::table('order_product_detail as opd')->leftJoin('user_review as ur', 'ur.object_id', '=', DB::raw('opd.product_id AND ur.object_type = opd.product_type'))->where('opd.id', $request->item_id)->avg('ur.rating');
                $order->avg_rating = number_format($avgRating, 1, '.', '');

                $item = OrderDetail::where('id', $request->item_id)->first();
                if(isset($item->id)){
                    if($item->product_type == 1){
                        $data = DB::table('course as c')->leftJoin('users as u', 'u.id', '=', 'c.admin_id')->select('u.first_name', 'u.last_name', 'u.profile_image', 'c.title')->where('c.id', $item->product_id)->first();
                        $data->profile_image = ($data->profile_image!="" && isset($data->profile_image)) ? uploadAssets('upload/profile-image/'.$data->profile_image) : null;
                    }
                    $order->creator_name = $data;
                }

                $orderDetails = DB::table('orders')->select(DB::raw("ifnull(c.admin_id, p.added_by) as added_by, ifnull(c.title,p.name) title, order_product_detail.id as itemid, order_product_detail.quantity, order_product_detail.product_id, order_product_detail.product_type, ifnull(c.status,p.status) status, order_product_detail.amount, order_product_detail.admin_amount, ifnull(c.introduction_image,(select attribute_value from product_details pd where p.id = pd.product_id and attribute_code = 'cover_image' limit 1))  as image"))->join('users as u', 'orders.user_id', '=', 'u.id')->join('order_product_detail', 'orders.id', '=', 'order_product_detail.order_id')->leftjoin('course as c', 'c.id','=', DB::raw('order_product_detail.product_id AND order_product_detail.product_type = 1'))->leftjoin('product as p', 'p.id','=', DB::raw('order_product_detail.product_id AND order_product_detail.product_type = 2'))->where('orders.id', $id)->get();

                $other_detail = [];
                foreach($orderDetails as $val){
                    $temp['id'] = $val->product_id;
                    $temp['item_id'] = $val->itemid;
                    $temp['is_primary'] = ($val->itemid==$request->item_id) ? true : false;
                    $temp['type'] = $val->product_type;
                    $temp['type_name'] = ($val->product_type==1) ? "Course" : "Product";
                    $temp['title'] = $val->title ?? "NA";
                    $temp['status'] = $val->status;
                    $temp['total_amount_paid'] = $val->amount*$val->quantity;
                    $temp['quantity'] = $val->quantity;
                    $temp['admin_fee'] = $val->admin_amount;
                    $temp['video'] = ($val->product_type==1) ? uploadAssets('upload/disclaimers-introduction/'.$val->image) : null;
                    $temp['image'] = ($val->product_type==2) ? uploadAssets('upload/products/'.$val->image) : null;
                    $avgRating = DB::table('user_review as ur')->where('ur.object_id', $val->product_id)->where('ur.object_type', $val->product_type)->avg('ur.rating');
                    $temp['avg_rating'] = number_format((float)$avgRating, 1, '.', '');
                    $user = User::where('id', $val->added_by)->first();
                    $temp['creator_name'] = $user->first_name . ' ' . $user->last_name;
                    $temp['creator_image'] = ($user->profile_image!="" && isset($user->profile_image)) ? uploadAssets('upload/profile-image/'.$user->profile_image) : null;
                    $other_detail[] = $temp;
                }

                $transaction = Order::where('orders.id', $id)->leftJoin('payment_detail as pd', 'pd.id', '=', 'orders.payment_id')->leftJoin('payment_methods as pm', 'pm.id', '=', 'pd.card_id')->select('pm.card_no', 'pm.card_type', 'pm.method_type', 'pm.expiry')->first();
                $transaction->card_type = $transaction->card_type ?? "Mastercard";
                $transaction->method_type = $transaction->method_type ?? "Card";
                $transaction->card_no = $transaction->card_no ?? "7878";
                $transaction->expiry = $transaction->expiry ?? "12/2026";

                $order->transaction = $transaction;

                $invoice = url('/')."/api/download-invoice/".encrypt_decrypt('encrypt', $order->id);

                return response()->json(['status' => true, 'message' => 'Order details', 'data' => $order, 'items' => $other_detail, 'invoice' => $invoice]);
            }
        } catch (\Exception $e) {
            return errorMsg("Exception -> " . $e->getMessage());
        }
    }

    public function download_invoice($id){
        try{
            $id = encrypt_decrypt('decrypt', $id);
            $order = Order::where('orders.id', $id)->leftJoin('users as u', 'u.id', '=', 'orders.user_id')->select('u.first_name', 'u.last_name', 'u.email', 'u.profile_image', 'u.phone', 'u.role', 'u.status as ustatus', 'orders.id', 'orders.order_number', 'orders.created_date', 'orders.status')->first();

            $orderDetails = DB::table('orders')->select(DB::raw("ifnull(c.title,p.name) title, order_product_detail.product_id, order_product_detail.product_type, ifnull(c.status,p.status) status, order_product_detail.amount, order_product_detail.admin_amount, ifnull(c.introduction_image,(select attribute_value from product_details pd where p.id = pd.product_id and attribute_code = 'cover_image' limit 1))  as image"))->join('users as u', 'orders.user_id', '=', 'u.id')->join('order_product_detail', 'orders.id', '=', 'order_product_detail.order_id')->leftjoin('course as c', 'c.id','=', DB::raw('order_product_detail.product_id AND order_product_detail.product_type = 1'))->leftjoin('product as p', 'p.id','=', DB::raw('order_product_detail.product_id AND order_product_detail.product_type = 2'))->where('orders.id', $id)->get();
            
            $pdf = PDF::loadView('home.pdf-invoice', compact('order', 'orderDetails'), [], [ 
                'mode' => 'utf-8',
                'title' => 'Order Invoice',
                'format' => 'Legal',
            ]);
            return $pdf->stream($order->order_number.'-invoice.pdf');
        } catch (\Exception $e) {
            return errorMsg("Exception -> " . $e->getMessage());
        }
    }

    public function getNotification(){
        try{
            $notify = Notify::where('user_id', auth()->user()->id)->get();
            return response()->json(['status' => true, 'message' => 'Notifications', 'data'=> $notify]);
        } catch (\Exception $e) {
            return errorMsg("Exception -> " . $e->getMessage());
        }
    }

    public function clearNotification(){
        try{
            $notify = Notify::where('user_id', auth()->user()->id)->delete();
            return response()->json(['status' => true, 'message' => 'All Notifications Cleared']);
        } catch (\Exception $e) {
            return errorMsg("Exception -> " . $e->getMessage());
        }
    }

    public function storeAddress(Request $request){
        try{
            $rules = [
                'first_name' => 'required',
                'last_name' => 'required',
                'email' => 'required|email',
                'phone' => 'required',
                'address_line_1' => 'required',
                'latitude' => 'required',
                'longitude' => 'required',
                'city' => 'required',
                'state' => 'required',
                'country' => 'required',
                'zip_code' => 'required',
                'address_type' => 'required',
                'billing_checkbox' => 'required',
            ];
            if($request->billing_checkbox==0){
                $rules['billing_first_name'] = 'required';
                $rules['billing_last_name'] = 'required';
                $rules['billing_email'] = 'required';
                $rules['billing_phone'] = 'required';
                $rules['billing_address_line_1'] = 'required';
                $rules['billing_latitude'] = 'required';
                $rules['billing_longitude'] = 'required';
                $rules['billing_city'] = 'required';
                $rules['billing_state'] = 'required';
                $rules['billing_country'] = 'required';
                $rules['billing_zip_code'] = 'required';
            }
            $validator = Validator::make($request->all(), $rules);
            if ($validator->fails()) {
                return response()->json(['status' => false, 'message' => $validator->errors()->first()]);
            } else {
                if(isset($request->is_default) && $request->is_default==1){
                    Address::where('user_id', auth()->user()->id)->update(['default_address'=> 0]);
                }
                $valid = $this->validateAddress($request);
                if($valid[0]['status']){
                    $address = new Address;
                    $address->user_id = auth()->user()->id;
                    $address->first_name = $request->first_name;
                    $address->middle_name = $request->middle_name ?? null;
                    $address->last_name = $request->last_name;
                    $address->email = $request->email;
                    $address->phone = $request->phone;
                    $address->company_name = $request->company_name ?? null;
                    $address->address_line_1 = $request->address_line_1;
                    $address->address_line_2 = $request->address_line_2 ?? null;
                    $address->latitude = $request->latitude;
                    $address->longitude = $request->longitude;
                    $address->city = $request->city;
                    $address->state = $request->state;
                    $address->country = $request->country;
                    $address->zip_code = $request->zip_code;
                    $address->address_type = $request->address_type ?? 'residential';
                    $address->shipping_type = 'shipping';
                    $address->default_address = $request->is_default ?? 0;
                    $address->created_at = date('Y-m-d H:i:s');
                    $address->updated_at = date('Y-m-d H:i:s');
                    $address->save();

                    $billingAddressExist = Address::where('user_id', auth()->user()->id)->where('shipping_type', 'billing')->first();
                    if(isset($billingAddressExist->id)){
                        $billingAddress = Address::where('user_id', auth()->user()->id)->where('shipping_type', 'billing')->first();
                    }else{
                        $billingAddress = new Address;
                    }
                    if($request->billing_checkbox==1){
                        $billingAddress->user_id = auth()->user()->id;
                        $billingAddress->first_name = $request->first_name;
                        $billingAddress->middle_name = $request->middle_name ?? null;
                        $billingAddress->last_name = $request->last_name;
                        $billingAddress->email = $request->email;
                        $billingAddress->phone = $request->phone;
                        $billingAddress->company_name = $request->company_name ?? null;
                        $billingAddress->address_line_1 = $request->address_line_1;
                        $billingAddress->address_line_2 = $request->address_line_2 ?? null;
                        $billingAddress->latitude = $request->latitude;
                        $billingAddress->longitude = $request->longitude;
                        $billingAddress->city = $request->city;
                        $billingAddress->state = $request->state;
                        $billingAddress->country = $request->country;
                        $billingAddress->zip_code = $request->zip_code;
                        $billingAddress->address_type = null;
                        $billingAddress->shipping_type = 'billing';
                        $billingAddress->default_address = 0;
                        $billingAddress->created_at = date('Y-m-d H:i:s');
                        $billingAddress->updated_at = date('Y-m-d H:i:s');
                    }else if($request->billing_checkbox==0){
                        $validBilling = $this->validateBillingAddress($request);
                        if($validBilling[0]['status']){
                            $billingAddress = new Address;
                            $billingAddress->user_id = auth()->user()->id;
                            $billingAddress->first_name = $request->billing_first_name;
                            $billingAddress->middle_name = $request->billing_middle_name ?? null;
                            $billingAddress->last_name = $request->billing_last_name;
                            $billingAddress->email = $request->billing_email;
                            $billingAddress->phone = $request->billing_phone;
                            $billingAddress->company_name = $request->billing_company_name ?? null;
                            $billingAddress->address_line_1 = $request->billing_address_line_1;
                            $billingAddress->address_line_2 = $request->billing_address_line_2 ?? null;
                            $billingAddress->latitude = $request->billing_latitude;
                            $billingAddress->longitude = $request->billing_longitude;
                            $billingAddress->city = $request->billing_city;
                            $billingAddress->state = $request->billing_state;
                            $billingAddress->country = $request->billing_country;
                            $billingAddress->zip_code = $request->billing_zip_code;
                            $billingAddress->address_type = null;
                            $billingAddress->shipping_type = 'billing';
                            $billingAddress->default_address = 0;
                            $billingAddress->created_at = date('Y-m-d H:i:s');
                            $billingAddress->updated_at = date('Y-m-d H:i:s');
                        } else {
                            return response()->json($validBilling[0]);
                        }
                    }
                    if($billingAddress->save()){
                        return response()->json(['status' => true, 'message'=> 'New Address Added Successfully.']);
                    }else{
                        return response()->json(['status' => false, 'message'=> 'Something went wrong.']);
                    }
                } else {
                    return response()->json($valid[0]);
                }
            }
        } catch (\Exception $e) {
            return errorMsg("Exception -> " . $e->getMessage());
        }
    }

    public function validateAddress($data){
        $curl = curl_init();
        curl_setopt_array($curl, array(
            CURLOPT_URL => 'https://api.shipengine.com/v1/addresses/validate',
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => '',
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 0,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => 'POST',
            CURLOPT_POSTFIELDS => "[
            {
                'address_line1': '$data->address_line_1',
                'city_locality': '$data->city',
                'state_province': '$data->state',
                'postal_code': '$data->zip_code',
                'country_code': 'US'
            }
        ]",
            CURLOPT_HTTPHEADER => array(
                'Content-Type: application/json',
                'API-Key: ' . env('SHIP_ENGINE_KEY')
            ),
        ));
        $response = curl_exec($curl);
        curl_close($curl);
        $res = json_decode($response);

        if($res[0]->status != 'verified') {
            foreach ($res[0]->messages as $key => $item) {
                if($item->message == 'Invalid postal_code.') {
                    $error_msg = 'Please enter a valid Zip Code!';
                } else {
                    $error_msg = $item->message;
                }
                return array(["status" => false, "message" => $error_msg]);
            }
        } elseif ($res[0]->status == 'verified') {
            $zip_code_response = explode('-',$res[0]->matched_address->postal_code);
            $city_response = explode('-',$res[0]->matched_address->city_locality);
            $state_response = explode('-',$res[0]->matched_address->state_province);
            if($zip_code_response[0] != $data->zip_code)
            {
                return array([ "status" => false, "message" => 'The correct zip code is '.$zip_code_response[0], "data" => $zip_code_response[0]]);
            } else if(strtoupper($city_response[0]) != strtoupper($data->city)){
                return array([ "status" => false, "message" => 'The correct city is '.$city_response[0], "data" => $city_response[0]]);
            }  else if(strtoupper($state_response[0]) != strtoupper($data->state)){
                return array([ "status" => false, "message" => 'The correct state code is '.$state_response[0], "data" => $state_response[0]]);
            }  else {
                return array(["status" => true]);
            }
        }
    }

    public function validateBillingAddress($data){
        $curl = curl_init();
        curl_setopt_array($curl, array(
            CURLOPT_URL => 'https://api.shipengine.com/v1/addresses/validate',
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => '',
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 0,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => 'POST',
            CURLOPT_POSTFIELDS => "[
            {
                'address_line1': '$data->billing_address_line_1',
                'city_locality': '$data->billing_city',
                'state_province': '$data->billing_state',
                'postal_code': '$data->billing_zip_code',
                'country_code': 'US'
            }
        ]",
            CURLOPT_HTTPHEADER => array(
                'Content-Type: application/json',
                'API-Key: ' . env('SHIP_ENGINE_KEY')
            ),
        ));
        $response = curl_exec($curl);
        curl_close($curl);
        $res = json_decode($response);

        if($res[0]->status != 'verified') {
            foreach ($res[0]->messages as $key => $item) {
                if($item->message == 'Invalid postal_code.') {
                    $error_msg = 'Please enter a valid billing Zip Code!';
                } else {
                    $error_msg = $item->message;
                }
                return array(["status" => false, "message" => $error_msg]);
            }
        } elseif ($res[0]->status == 'verified') {
            $zip_code_response = explode('-',$res[0]->matched_address->postal_code);
            $city_response = explode('-',$res[0]->matched_address->city_locality);
            $state_response = explode('-',$res[0]->matched_address->state_province);
            if($zip_code_response[0] != $data->billing_zip_code)
            {
                return array([ "status" => false, "message" => 'The correct billing zip code is '.$zip_code_response[0], "data" => $zip_code_response[0]]);
            } else if(strtoupper($city_response[0]) != strtoupper($data->billing_city)){
                return array([ "status" => false, "message" => 'The correct billing city is '.$city_response[0], "data" => $city_response[0]]);
            }  else if(strtoupper($state_response[0]) != strtoupper($data->billing_state)){
                return array([ "status" => false, "message" => 'The correct billing state code is '.$state_response[0], "data" => $state_response[0]]);
            }  else {
                return array(["status" => true]);
            }
        }
    }

    public function getAddress(){
        try{
            $address = Address::where('user_id', auth()->user()->id)->where('shipping_type', 'shipping')->get();
            $billing = Address::where('user_id', auth()->user()->id)->where('shipping_type', 'billing')->first();
            if(count($address) > 0){
                return response()->json(['status' => true, 'message'=> 'Addresses', 'data'=> $address, 'billing' => isset($billing->id) ? $billing : null]);
            }else {
                return response()->json(['status' => false, 'message'=> 'No address found']);
            }
        } catch (\Exception $e) {
            return errorMsg("Exception -> " . $e->getMessage());
        }
    }

    public function getAddressDetails($id){
        try{
            $address = Address::where('user_id', auth()->user()->id)->where('id', $id)->where('shipping_type', 'shipping')->first();
            $billing = Address::where('user_id', auth()->user()->id)->where('shipping_type', 'billing')->first();
            if(isset($address)){
                return response()->json(['status' => true, 'message'=> 'Address', 'data'=> $address, 'billing' => isset($billing->id) ? $billing : null]);
            }else {
                return response()->json(['status' => false, 'message'=> 'No address found']);
            }
        } catch (\Exception $e) {
            return errorMsg("Exception -> " . $e->getMessage());
        }
    }

    public function deleteAddress($id){
        try{
            $address = Address::where('user_id', auth()->user()->id)->where('id', $id)->delete();
            return response()->json(['status' => true, 'message'=> 'Address Deleted Successfully']);
        } catch (\Exception $e) {
            return errorMsg("Exception -> " . $e->getMessage());
        }
    }

    public function updateAddress(Request $request){
        try{
            $rules = [
                'id' => 'required',
                'first_name' => 'required',
                'last_name' => 'required',
                'email' => 'required|email',
                'phone' => 'required',
                'address_line_1' => 'required',
                'latitude' => 'required',
                'longitude' => 'required',
                'city' => 'required',
                'state' => 'required',
                'country' => 'required',
                'zip_code' => 'required',
                'address_type' => 'required',
                'billing_checkbox' => 'required'
            ];
            if($request->billing_checkbox==0){
                $rules['billing_first_name'] = 'required';
                $rules['billing_last_name'] = 'required';
                $rules['billing_email'] = 'required';
                $rules['billing_phone'] = 'required';
                $rules['billing_address_line_1'] = 'required';
                $rules['billing_latitude'] = 'required';
                $rules['billing_longitude'] = 'required';
                $rules['billing_city'] = 'required';
                $rules['billing_state'] = 'required';
                $rules['billing_country'] = 'required';
                $rules['billing_zip_code'] = 'required';
            }
            $validator = Validator::make($request->all(), $rules);
            if ($validator->fails()) {
                return response()->json(['status' => false, 'message' => $validator->errors()->first()]);
            } else {
                if(isset($request->is_default) && $request->is_default==1){
                    Address::where('user_id', auth()->user()->id)->update(['default_address'=> 0]);
                }
                $valid = $this->validateAddress($request);
                if($valid[0]['status']){
                    Address::where('user_id', auth()->user()->id)->where('id', $request->id)->update([
                        'first_name' => $request->first_name,
                        'middle_name' => $request->middle_name ?? null,
                        'last_name' => $request->last_name,
                        'email' => $request->email,
                        'phone' => $request->phone,
                        'company_name' => $request->company_name ?? null,
                        'address_line_1' => $request->address_line_1,
                        'address_line_2' => $request->address_line_2 ?? null,
                        'latitude' => $request->latitude,
                        'longitude' => $request->longitude,
                        'city' => $request->city,
                        'state' => $request->state,
                        'country' => $request->country,
                        'zip_code' => $request->zip_code,
                        'address_type' => $request->address_type ?? 'residential',
                        'shipping_type' => 'shipping',
                        'default_address' => $request->is_default ?? 0,
                        'updated_at' => date('Y-m-d H:i:s'),
                    ]);

                    $billingAddressExist = Address::where('user_id', auth()->user()->id)->where('shipping_type', 'billing')->first();
                    if(isset($billingAddressExist->id)){
                        $billingAddress = Address::where('user_id', auth()->user()->id)->where('shipping_type', 'billing')->first();
                    }else{
                        $billingAddress = new Address;
                    }
                    if($request->billing_checkbox==1){
                        $billingAddress->user_id = auth()->user()->id;
                        $billingAddress->first_name = $request->first_name;
                        $billingAddress->middle_name = $request->middle_name ?? null;
                        $billingAddress->last_name = $request->last_name;
                        $billingAddress->email = $request->email;
                        $billingAddress->phone = $request->phone;
                        $billingAddress->company_name = $request->company_name ?? null;
                        $billingAddress->address_line_1 = $request->address_line_1;
                        $billingAddress->address_line_2 = $request->address_line_2 ?? null;
                        $billingAddress->latitude = $request->latitude;
                        $billingAddress->longitude = $request->longitude;
                        $billingAddress->city = $request->city;
                        $billingAddress->state = $request->state;
                        $billingAddress->country = $request->country;
                        $billingAddress->zip_code = $request->zip_code;
                        $billingAddress->address_type = null;
                        $billingAddress->shipping_type = 'billing';
                        $billingAddress->default_address = 0;
                        $billingAddress->created_at = date('Y-m-d H:i:s');
                        $billingAddress->updated_at = date('Y-m-d H:i:s');
                    }else if($request->billing_checkbox==0){
                        $validBilling = $this->validateBillingAddress($request);
                        if($validBilling[0]['status']){
                            $billingAddress = new Address;
                            $billingAddress->user_id = auth()->user()->id;
                            $billingAddress->first_name = $request->billing_first_name;
                            $billingAddress->middle_name = $request->billing_middle_name ?? null;
                            $billingAddress->last_name = $request->billing_last_name;
                            $billingAddress->email = $request->billing_email;
                            $billingAddress->phone = $request->billing_phone;
                            $billingAddress->company_name = $request->billing_company_name ?? null;
                            $billingAddress->address_line_1 = $request->billing_address_line_1;
                            $billingAddress->address_line_2 = $request->billing_address_line_2 ?? null;
                            $billingAddress->latitude = $request->billing_latitude;
                            $billingAddress->longitude = $request->billing_longitude;
                            $billingAddress->city = $request->billing_city;
                            $billingAddress->state = $request->billing_state;
                            $billingAddress->country = $request->billing_country;
                            $billingAddress->zip_code = $request->billing_zip_code;
                            $billingAddress->address_type = null;
                            $billingAddress->shipping_type = 'billing';
                            $billingAddress->default_address = 0;
                            $billingAddress->created_at = date('Y-m-d H:i:s');
                            $billingAddress->updated_at = date('Y-m-d H:i:s');
                        } else {
                            return response()->json($validBilling[0]);
                        }
                    }
                    $billingAddress->save();
                    return response()->json(['status' => true, 'message'=> 'Address Updated Successfully.']);
                } else {
                    return response()->json($valid[0]);
                }
            }
        } catch (\Exception $e) {
            return errorMsg("Exception -> " . $e->getMessage());
        }
    }

    public function sendNotificationApi(Request $request)
    {
        $url = 'https://fcm.googleapis.com/fcm/send';
        $serverKey = 'AAAArLOz8H4:APA91bEFEqNkNlnmUsegFRwkU2nlX5FZ9z7G7LzLzuolkmqwLTIR0jijjmTMAKg1Ik4thMroyPU82NYsxzEVH4OXvhiZQLTgxjMamiIpPXSUy7N71A1OtcjXtVJlLHn3-nMkVNqHVpcV'; // ADD SERVER KEY HERE PROVIDED BY FCM
        $msg = array(
            'body'  => "Testing",
            'title' => "Track Cert",
            'sound' => 'default'
        );
        $arr = array(
            'to' => $request->token,
            'notification' => $msg,
            'data' => array(
                'title' => 'Testing',
                'msg' => "Only for testing purpose",
            ),
            "priority" => "high"
        );
        $encodedData = json_encode($arr);
        $headers = [
            'Authorization:key=' . $serverKey,
            'Content-Type: application/json',
        ];

        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);
        curl_setopt($ch, CURLOPT_HTTP_VERSION, CURL_HTTP_VERSION_1_1);
        // Disabling SSL Certificate support temporarly
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $encodedData);
        // Execute post
        $result = curl_exec($ch);
        die($result);
        if ($result === FALSE) {
            die('Curl failed: ' . curl_error($ch));
        }
        // Close connection
        curl_close($ch);
    }
}