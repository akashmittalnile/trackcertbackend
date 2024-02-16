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
use App\Models\CardDetails;
use App\Models\Notification;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;

class ApiController extends Controller
{
    public function home()
    {
        try {
            $user_id = Auth::user()->id;
            $datas = array();
            
            $trending_courses = Course::where('status', 1)->orderBy('id','DESC')->get(); /*Get data of Treanding Course*/
            $b1 = array();
            $TrendingCourses = array();
            foreach ($trending_courses as $k => $data)
            {
                $b1['id']  = isset($data->id) ? $data->id : '';
                $b1['title']  = isset($data->title) ? $data->title : '';
                $b1['description']  = isset($data->description) ? $data->description : '';
                $b1['admin_id']  = isset($data->admin_id) ? $data->admin_id : '';
                $b1['created_at']  = date('d/m/y,H:i', strtotime($data->created_at));
                $b1['rating'] = 4.6;
                $b1['course_fee'] = $data->course_fee;
                $b1['status'] = $data->status;
                $b1['tags'] = isset($data->tags) ? $data->tags : '';
                $b1['valid_upto'] = $data->valid_upto;
                if(!empty($data->product_image))
                {
                    $b1['certificates_image'] = url('assets/upload/course-certificates/'.$data->certificates);
                }else{
                    $b1['certificates_image'] = '';
                }
                if(!empty($data->product_image))
                {
                    $b1['introduction_image'] = url('assets/upload/disclaimers-introduction/'.$data->introduction_image);
                }else{
                    $b1['introduction_image'] = '';
                }
                $exists = Like::where('user_id', '=', $user_id)->where('object_id', '=', $data->id)->where('object_type', '=', 1)->first();
                if(isset($exists))
                {
                    $b1['isLike']  = 1;
                }else{
                    $b1['isLike']  = 0;
                }    
                $TrendingCourses[] = $b1;
            }

            $top_category = Category::where('cat_status', 1)->orderBy('id','DESC')->get(); /*Get data of category*/
            $b2 = array();
            $TopCategory = array();
            foreach ($top_category as $k => $data)
            {
                $b2['id']  = isset($data->id) ? $data->id : '';
                $b2['category_name']  = isset($data->category_name) ? $data->category_name : '';
                if(!empty($data->product_image))
                {
                    $b2['category_image'] = url('assets/upload/category_image/'.$data->product_image);
                }else{
                    $b2['category_image'] = '';
                }
                $b2['cat_status']  = isset($data->cat_status) ? $data->cat_status : '';
                $b2['created_at']  = date('d/m/y,H:i', strtotime($data->created_at));
                $TopCategory[] = $b2;
            }

            $suggested_courses = Course::where('status', 1)->orderBy('id','DESC')->get(); /*Get data of Suggested Course*/
            $b3 = array();
            $SuggestedCourses = array();
            foreach ($suggested_courses as $k => $data)
            {
                $b3['id']  = isset($data->id) ? $data->id : '';
                $b3['title']  = isset($data->title) ? $data->title : '';
                $b3['description']  = isset($data->description) ? $data->description : '';
                $b3['admin_id']  = isset($data->admin_id) ? $data->admin_id : '';
                $b3['created_at']  = date('d/m/y,H:i', strtotime($data->created_at));
                $b3['rating'] = 4.6;
                $b3['course_fee'] = $data->course_fee;
                $b3['status'] = $data->status;
                $b3['tags'] = isset($data->tags) ? $data->tags : '';
                $b3['valid_upto'] = $data->valid_upto;
                if(!empty($data->certificates))
                {
                    $b3['certificates_image'] = url('assets/upload/course-certificates/'.$data->certificates);
                }else{
                    $b3['certificates_image'] = '';
                }
                if(!empty($data->introduction_image))
                {
                    $b3['introduction_image'] = url('assets/upload/disclaimers-introduction/'.$data->introduction_image);
                }else{
                    $b3['introduction_image'] = '';
                }
                $exists = Like::where('user_id', '=', $user_id)->where('object_id', '=', $data->id)->where('object_type', '=', 1)->first();
                if(isset($exists))
                {
                    $b3['isLike']  = 1;
                }else{
                    $b3['isLike']  = 0;
                }    
                $SuggestedCourses[] = $b3;
            }

            $all_products = Product::where('status', 1)->orderBy('id','DESC')->get(); /*Get data of All Product*/
            $b4 = array();
            $AllProducts = array();
            foreach ($all_products as $k => $data)
            {
                $b4['id']  = isset($data->id) ? $data->id : '';
                $b4['title']  = isset($data->title) ? $data->title : '';
                $b4['description']  = isset($data->description) ? $data->description : '';
                $b4['admin_id']  = isset($data->admin_id) ? $data->admin_id : '';
                $b4['created_at']  = date('d/m/y,H:i', strtotime($data->created_at));
                $b4['rating'] = 4.6;
                $b4['price'] = $data->price;
                $b4['status'] = $data->status;
                if(!empty($data->product_image))
                {
                    $b4['Product_image'] = url('assets/upload/products/'.$data->product_image);
                }else{
                    $b4['Product_image'] = '';
                }
                
                $exists = Like::where('user_id', '=', $user_id)->where('object_id', '=', $data->id)->where('object_type', '=', 2)->first();
                if(isset($exists))
                {
                    $b4['isLike']  = 1;
                }else{
                    $b4['isLike']  = 0;
                }    
                $AllProducts[] = $b4;
            }

            $sug_products = Product::where('status', 1)->orderBy('id','DESC')->get(); /*Get data of Suggested Product*/
            $b5 = array();
            $SugProducts = array();
            foreach ($sug_products as $k => $data)
            {
                $b5['id']  = isset($data->id) ? $data->id : '';
                $b5['title']  = isset($data->title) ? $data->title : '';
                $b5['description']  = isset($data->description) ? $data->description : '';
                $b5['admin_id']  = isset($data->admin_id) ? $data->admin_id : '';
                $b5['created_at']  = date('d/m/y,H:i', strtotime($data->created_at));
                $b5['rating'] = 4.6;
                $b5['price'] = $data->price;
                $b5['status'] = $data->status;
                if(!empty($data->product_image))
                {
                    $b5['Product_image'] = url('assets/upload/products/'.$data->product_image);
                }else{
                    $b5['Product_image'] = '';
                }
                $exists = Like::where('user_id', '=', $user_id)->where('object_id', '=', $data->id)->where('object_type', '=', 2)->first();
                if(isset($exists))
                {
                    $b5['isLike']  = 1;
                }else{
                    $b5['isLike']  = 0;
                }    
                $SugProducts[] = $b5;
            }

            $Sug_category = Category::where('cat_status', 1)->orderBy('id','DESC')->get(); /*Get data of Suggested category*/
            $b6 = array();
            $SugCategory = array();
            foreach ($Sug_category as $k => $data)
            {
                $b6['id']  = isset($data->id) ? $data->id : '';
                $b6['category_name']  = isset($data->category_name) ? $data->category_name : '';
                if(!empty($data->product_image))
                {
                    $b6['category_image'] = url('assets/upload/category_image/'.$data->product_image);
                }else{
                    $b6['category_image'] = '';
                }
                $b6['cat_status']  = isset($data->cat_status) ? $data->cat_status : '';
                $b6['created_at']  = date('d/m/y,H:i', strtotime($data->created_at));
                $SugCategory[] = $b6;
            }

            $datas['trending_course'] = $TrendingCourses;
            $datas['top_category'] = $TopCategory;
            $datas['suggested_course'] = $SuggestedCourses;
            $datas['all_product'] = $AllProducts;
            $datas['suggested_product'] = $SugProducts;
            $datas['suggested_category'] = $SugCategory;
            return response()->json(['status' => true, 'message' => 'Home Page Listing', 'data' => $datas]);
        } catch (\Exception $e) {
            return errorMsg("Exception -> " . $e->getMessage());
        }
    }

    public function wishlist_listing(Request $request)
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
            if ($type == 1) {    /* 1 stand for course ,2 for product */
                $datas = Wishlist::where('status', 1)->where('object_type', 1)->orderBy('id','DESC')->get();
            } else {
                $datas = Wishlist::where('status', 1)->where('object_type', 2)->orderBy('id','DESC')->get();
            }
            
            $response = array();
            if (isset($datas)) {
                foreach ($datas as $keys => $item) {
                    if ($type == 1) {    /* 1 stand for course ,2 for product */
                        $value = Course::where('status', 1)->where('id', $item->object_id)->orderBy('id','DESC')->first();
                        $temp['course_fee'] = $value->course_fee;
                        $temp['valid_upto'] = $value->valid_upto;
                        if(!empty($value->certificates))
                        {
                            $temp['certificates_image'] = url('assets/upload/course-certificates/'.$value->certificates);
                        }else{
                            $temp['certificates_image'] = '';
                        }
                        if(!empty($value->introduction_image))
                        {
                            $temp['introduction_image'] = url('assets/upload/disclaimers-introduction/'.$value->introduction_image);
                        }else{
                            $temp['introduction_image'] = '';
                        }
                        $exists = Like::where('user_id', '=', $user_id)->where('object_id', '=', $value->id)->where('object_type', '=', 1)->first();
                        if(isset($exists))
                        {
                            $temp['isLike']  = 1;
                        }else{
                            $temp['isLike']  = 0;
                        }
                    } else {
                        $value = Product::where('status', 1)->where('id', $item->object_id)->orderBy('id','DESC')->first();
                        $temp['price'] = $value->price;
                        if(!empty($value->product_image))
                        {
                            $temp['Product_image'] = url('assets/upload/products/'.$value->product_image);
                        }else{
                            $temp['Product_image'] = '';
                        }
                        $exists = Like::where('user_id', '=', $user_id)->where('object_id', '=', $value->id)->where('object_type', '=', 2)->first();
                        if(isset($exists))
                        {
                            $temp['isLike']  = 1;
                        }else{
                            $temp['isLike']  = 0;
                        }
                    }
                    $temp['id'] = $value->id;
                    $temp['admin_id'] = $value->admin_id;
                    $temp['title'] =  $value->title;
                    $temp['description'] = $value->description;
                    $temp['tags'] = $value->tags;
                    $temp['status'] = $value->status;
                    $temp['rating'] = 4.6;
                    $temp['created_date'] = date('d/m/y,H:i', strtotime($value->created_date));
                    $response[] = $temp;
                }
            }
            if ($type == 1) {
                return response()->json(['status' => true, 'message' => 'Course Listing', 'data' => $response]);
            } else {
                return response()->json(['status' => true, 'message' => 'Product Listing', 'data' => $response]);
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
            if ($limit == 0) {    /* 0 stand for limit ,1 for all */
                $course = Course::where('status', 1)->orderBy('id','DESC')->limit(2)->get();
            } else {
                $course = Course::where('status', 1)->orderBy('id','DESC')->get();
            }
            
            
            $response = array();
            if (isset($course)) {
                foreach ($course as $keys => $item) {
                    $temp['id'] = $item->id;
                    $temp['admin_id'] = $item->admin_id;
                    $temp['title'] =  $item->title;
                    $temp['description'] = $item->description;
                    $temp['course_fee'] = $item->course_fee;
                    $temp['tags'] = $item->tags;
                    $temp['valid_upto'] = $item->valid_upto;
                    if(!empty($item->certificates))
                    {
                        $temp['certificates_image'] = url('assets/upload/course-certificates/'.$item->certificates);
                    }else{
                        $temp['certificates_image'] = '';
                    }
                    if(!empty($value->introduction_image))
                    {
                        $temp['introduction_image'] = url('assets/upload/disclaimers-introduction/'.$item->introduction_image);
                    }else{
                        $temp['introduction_image'] = '';
                    }
                    $temp['status'] = $item->status;
                    $temp['rating'] = 4.6;
                    $exists = Like::where('user_id', '=', $user_id)->where('object_id', '=', $item->id)->where('object_type', '=', 1)->first();
                    if(isset($exists))
                    {
                        $temp['isLike']  = 1;
                    }else{
                        $temp['isLike']  = 0;
                    }
                    $temp['created_date'] = date('d/m/y,H:i', strtotime($item->created_date));
                    $response[] = $temp;
                }
            }
            return response()->json(['status' => true, 'message' => 'Trending Couse Listing', 'data' => $response]);
        } catch (\Exception $e) {
            return errorMsg("Exception -> " . $e->getMessage());
        }
    }

    public function course_listing()
    {
        try {
            $user_id = Auth::user()->id;
            $course = Course::where('status', 1)->orderBy('id','DESC')->get();
            $response = array();
            if (isset($course)) {
                foreach ($course as $keys => $item) {
                    $temp['id'] = $item->id;
                    $temp['admin_id'] = $item->admin_id;
                    $temp['title'] =  $item->title;
                    $temp['description'] = $item->description;
                    $temp['course_fee'] = $item->course_fee;
                    $temp['tags'] = $item->tags;
                    $temp['valid_upto'] = $item->valid_upto;
                    $temp['certificates_image'] = $item->certificates;
                    $temp['introduction_image'] = $item->introduction_image;
                    $temp['status'] = $item->status;
                    $temp['rating'] = 4.6;
                    $temp['is_like'] = 1;
                    $temp['created_date'] = date('d/m/y,H:i', strtotime($item->created_date));
                    $response[] = $temp;
                }
            }
            return response()->json(['status' => true, 'message' => 'Couse Listing', 'data' => $response]);
        } catch (\Exception $e) {
            return errorMsg("Exception -> " . $e->getMessage());
        }
    }

    public function all_category()
    {
        try {
            $user_id = Auth::user();
            $category = Category::where('cat_status', 1)->orderBy('id','DESC')->get(); /*Get data of category*/
            $response = array();
            if (isset($category)) {
                foreach ($category as $keys => $item) {
                    $temp['id'] = $item->id;
                    $temp['admin_id'] = $item->admin_id;
                    $temp['category_name'] =  $item->title;
                    $temp['category_image'] = $item->description;
                    $temp['status'] = $item->cat_status;
                    $temp['created_date'] = date('d/m/y,H:i', strtotime($item->created_date));
                    $response[] = $temp;
                }
            }
            return response()->json(['status' => true, 'message' => 'Category Listing', 'data' => $response]);
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
            if ($type == 1) {    /* 1 stand for course ,2 for product */
                $datas = Wishlist::where('status', 1)->where('object_type', 1)->orderBy('id','DESC')->get();
            } else {
                $datas = Wishlist::where('status', 1)->where('object_type', 2)->orderBy('id','DESC')->get();
            }
            
            $response = array();
            if (isset($datas)) {
                foreach ($datas as $keys => $item) {
                    if ($type == 1) {    /* 1 stand for course ,2 for product */
                        $value = Course::where('status', 1)->where('id', $item->object_id)->orderBy('id','DESC')->first();
                        $temp['course_fee'] = $value->course_fee;
                        $temp['valid_upto'] = $value->valid_upto;
                        if(!empty($value->certificates))
                        {
                            $temp['certificates_image'] = url('assets/upload/course-certificates/'.$value->certificates);
                        }else{
                            $temp['certificates_image'] = '';
                        }
                        if(!empty($value->introduction_image))
                        {
                            $temp['introduction_image'] = url('assets/upload/disclaimers-introduction/'.$value->introduction_image);
                        }else{
                            $temp['introduction_image'] = '';
                        }
                        $exists = Like::where('user_id', '=', $user_id)->where('object_id', '=', $value->id)->where('object_type', '=', 1)->first();
                        if(isset($exists))
                        {
                            $temp['isLike']  = 1;
                        }else{
                            $temp['isLike']  = 0;
                        }
                    } else {
                        $value = Product::where('status', 1)->where('id', $item->object_id)->orderBy('id','DESC')->first();
                        $temp['price'] = $value->price;
                        if(!empty($value->product_image))
                        {
                            $temp['Product_image'] = url('assets/upload/products/'.$value->product_image);
                        }else{
                            $temp['Product_image'] = '';
                        }
                        $exists = Like::where('user_id', '=', $user_id)->where('object_id', '=', $value->id)->where('object_type', '=', 2)->first();
                        if(isset($exists))
                        {
                            $temp['isLike']  = 1;
                        }else{
                            $temp['isLike']  = 0;
                        }
                    }
                    $temp['id'] = $value->id;
                    $temp['admin_id'] = $value->admin_id;
                    $temp['title'] =  $value->title;
                    $temp['description'] = $value->description;
                    $temp['tags'] = $value->tags;
                    $temp['status'] = $value->status;
                    $temp['rating'] = 4.6;
                    $temp['created_date'] = date('d/m/y,H:i', strtotime($value->created_date));
                    $response[] = $temp;
                }
            }
            if ($type == 1) {
                return response()->json(['status' => true, 'message' => 'Suggested Course Listing', 'data' => $response]);
            } else {
                return response()->json(['status' => true, 'message' => 'Suggested Product Listing', 'data' => $response]);
            }
            
            
        } catch (\Exception $e) {
            return errorMsg("Exception -> " . $e->getMessage());
        }
    }

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
            if($type == 1)
            {
                $datas = Course::where('status', 1)->orderBy('id','DESC')->get();
            }else{
                $datas = Product::where('status', 1)->orderBy('id','DESC')->get();
            }
            
            $response = array();
            if (isset($datas)) {
                foreach ($datas as $keys => $value) {
                    if ($type == 1) {    /* 1 stand for course ,2 for product */
                        
                        $temp['course_fee'] = $value->course_fee;
                        $temp['valid_upto'] = $value->valid_upto;
                        if(!empty($value->certificates))
                        {
                            $temp['certificates_image'] = url('assets/upload/course-certificates/'.$value->certificates);
                        }else{
                            $temp['certificates_image'] = '';
                        }
                        if(!empty($value->introduction_image))
                        {
                            $temp['introduction_image'] = url('assets/upload/disclaimers-introduction/'.$value->introduction_image);
                        }else{
                            $temp['introduction_image'] = '';
                        }
                        $exists = Like::where('user_id', '=', $user_id)->where('object_id', '=', $value->id)->where('object_type', '=', 1)->first();
                        if(isset($exists))
                        {
                            $temp['isLike']  = 1;
                        }else{
                            $temp['isLike']  = 0;
                        }
                    } else {
                        $temp['price'] = $value->price;
                        if(!empty($value->product_image))
                        {
                            $temp['Product_image'] = url('assets/upload/products/'.$value->product_image);
                        }else{
                            $temp['Product_image'] = '';
                        }
                        $exists = Like::where('user_id', '=', $user_id)->where('object_id', '=', $value->id)->where('object_type', '=', 2)->first();
                        if(isset($exists))
                        {
                            $temp['isLike']  = 1;
                        }else{
                            $temp['isLike']  = 0;
                        }
                    }
                    $temp['id'] = $value->id;
                    $temp['admin_id'] = $value->admin_id;
                    $temp['title'] =  $value->title;
                    $temp['description'] = $value->description;
                    $temp['tags'] = $value->tags;
                    $temp['status'] = $value->status;
                    $temp['rating'] = 4.6;
                    $temp['created_date'] = date('d/m/y,H:i', strtotime($value->created_date));
                    $response[] = $temp;
                }
            }
            if ($type == 1) {
                return response()->json(['status' => true, 'message' => ' Course Listing', 'data' => $response]);
            } else {
                return response()->json(['status' => true, 'message' => ' Product Listing', 'data' => $response]);
            }
            
            
        } catch (\Exception $e) {
            return errorMsg("Exception -> " . $e->getMessage());
        }
    }

    public function object_type_details(Request $request)
    {
        try {
            $user_id = Auth::user()->id;
            if($user_id)
            {
                $validator = Validator::make($request->all(), [
                    'type' => 'required',
                    'id' => 'required'
                ]);
                if ($validator->fails()) {
                    return response()->json(['status' => false, 'message' => $validator->errors()->first()]);
                }
                $type = $request->type;
                $id = $request->id;
                if ($type == 1) {    /* 1 stand for course ,2 for product */
                    $item = Course::where('status', 1)->where('id', $id)->orderBy('id','DESC')->first();
                } else {
                    $item = Product::where('status', 1)->where('id', $id)->orderBy('id','DESC')->first();
                }
                
                if (!empty($item)) {
                    if ($type == 1) {    /* 1 stand for course ,2 for product */
                        $temp['course_fee'] = $item->course_fee;
                        $temp['valid_upto'] = $item->valid_upto;
                        if(!empty($item->certificates))
                        {
                            $temp['certificates_image'] = url('assets/upload/course-certificates/'.$item->certificates);
                        }else{
                            $temp['certificates_image'] = '';
                        }
                        if(!empty($item->introduction_image))
                        {
                            $temp['introduction_image'] = url('assets/upload/disclaimers-introduction/'.$item->introduction_image);
                        }else{
                            $temp['introduction_image'] = '';
                        }
                        $exists = Like::where('user_id', '=', $user_id)->where('object_id', '=', $item->id)->where('object_type', '=', 1)->first();
                        if(isset($exists))
                        {
                            $temp['isLike']  = 1;
                        }else{
                            $temp['isLike']  = 0;
                        }
                    } else {
                        $temp['price'] = $item->price;
                        if(!empty($item->product_image))
                        {
                            $temp['Product_image'] = url('assets/upload/products/'.$item->product_image);
                        }else{
                            $temp['Product_image'] = '';
                        }
                        $exists = Like::where('user_id', '=', $user_id)->where('object_id', '=', $item->id)->where('object_type', '=', 2)->first();
                        if(isset($exists))
                        {
                            $temp['isLike']  = 1;
                        }else{
                            $temp['isLike']  = 0;
                        }
                    }
                    $temp['id'] = $item->id;
                    $temp['admin_id'] = $item->admin_id;
                    $temp['title'] =  $item->title;
                    $temp['description'] = $item->description;
                    $temp['tags'] = $item->tags;
                    $temp['status'] = $item->status;
                    $temp['rating'] = 4.6;
                    $temp['created_date'] = date('d/m/y,H:i', strtotime($item->created_date));
                    if ($type == 1) {
                        return response()->json(['status' => true, 'message' => ' Course Listing', 'data' => $temp]);
                    } else {
                        return response()->json(['status' => true, 'message' => ' Product Listing', 'data' => $temp]);
                    }
                }else{
                    if ($type == 1) {
                        return response()->json(['status' => true, 'message' => ' Course Listing', 'data' => '']);
                    } else {
                        return response()->json(['status' => true, 'message' => ' Product Listing', 'data' => '']);
                    }
                }
            }else{
                return response()->json(['status'=> false,'Message'=>'Please login']);
            }
            
        } catch (\Exception $e) {
            return errorMsg("Exception -> " . $e->getMessage());
        }
    }

    public function like_object_type(Request $request)
    {
        try {
            $user = Auth::user();
            if($user->id)
            {
                $validator = Validator::make($request->all(),[
                    'type'=>'required', /* Type for 1 = Course, 2:Product (Object Type)*/
                    'id'=>'required', /* Id of Course Or Product (Object ID)*/
                    'status'=>'required'
                ]);
                if ($validator->fails()) {
                    return response()->json(['status' => false, 'message' => $validator->errors()->first()]);
                }
                $u_id = $user->id;
                $item_id = $request->id;
                $item_type = $request->type;
                $status = $request->status;
                $exist = Like::where('user_id', $u_id)->where('object_type', $item_type)->where('object_id', $item_id)->first();
                /* Status check for liked post 1 = Already liked , 2 = Create new liked post */
                if ($exist){
                    if($status == 1)
                    {
                        return response()->json(['status'=> 0,'Message'=>'Already favourites',]);
                    }else{
                        DB::table('likes')->where('user_id', $u_id)->where('object_type', $item_type)->where('object_id', $item_id)->delete();
                        return response()->json(['status'=> 1,'Message'=>'Removed to favourites',]);
                    }
                }else{
                    $data = DB::table('likes')->insert([
                        'object_id' => (int) $item_id,
                        'object_type' => (int) $item_type,
                        'user_id' => (int) $u_id,
                        'status' => $status,
                    ]);
                    return response()->json(['status'=> 1,'Message'=>'Added to favourites',]);
                }
            }else{
                return response()->json(['status'=> 0,'Message'=>'Please login']);
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
                 $validator = Validator::make($request->all(),[
                    'type'=>'required', /* Type for 1 = Course, 2:Product (Object Type)*/
                    'id'=>'required', /* Id of Course Or Product (Object ID)*/
                    'rating'=>'required',
                    'comment'=>'required'
                ]);
                if ($validator->fails()) {
                    return response()->json(['status' => false, 'message' => $validator->errors()->first()]);
                }
                $user_id = $user->id;
                $object_id = $request->id;
                $object_type = $request->type;
                $comment = $request->comment;
                $rating = $request->rating;
                $exist = Review::where('userid', $user_id)->where('object_id', $object_id)->where('object_type', $object_type)->first();
                if (isset($exist)) {
                    Review::where('id', $exist->id)->update(['rating' => $rating,'review'=>$comment]);
                    return response()->json(['status' => true, 'Message' => 'Updated to Reviews']);
                } else {
                    $save = Review::create([
                        'userid' => $user_id,
                        'object_id' => $object_id,
                        'object_type' => $object_type,
                        'rating' => $rating,
                        'review' => $comment,
                        'status' => 1,
                    ]);
                    if ($save) {
                        return response()->json(['status' => true, 'Message' => 'Review added successfully']);
                    } else {
                        return response()->json(['status' => false, 'Message' => 'Already reviewed']);
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
            if($user->id)
            {
                $validator = Validator::make($request->all(),[
                    'id'=>'required',
                    'type'=>'required',
                ]);
                if ($validator->fails()) {
                    return response()->json(['status' => false, 'message' => $validator->errors()->first()]);
                }
                $user_id = $user->id;
                $object_id = $request->id;
                $object_type = $request->type;
                $review = Review::where('object_id', $object_id)->where('object_type', $object_type)->get();
                if (count($review)>0){
                    $data = [];
                    foreach ($review as $key => $c) {
                        $data[$key] = $c->toArray();
                        $data[$key]['review'] = $c->review;
                        $user_name = User::where('id',$c->userid)->first();
                        $data[$key]['user_name'] = $user_name->first_name.' '.$user_name->last_name;
                    }
                    return response()->json([
                        "status" => true,
                        "message" => "Review List",
                        "review_list" => $data
                    ]);
                } else {
                    return response()->json(['status'=> false,'Message'=>'No data','review_list'=>[]]);
                }
            }else{
                return response()->json(['status'=> false,'Message'=>'Please login']);
            }
        } catch (\Exception $e) {
            return errorMsg("Exception -> " . $e->getMessage());
        }
    }

    public function top_category(Request $request)
    {
        try {
            $user_id = Auth::user()->id;
            if($user_id)
            {
                $type = $request->type;
                if ($type == 1) {    /* 1 stand for course ,2 for product */
                    $datas = Course::where('status', 1)->orderBy('id','DESC')->limit(2)->get();
                } else {
                    $datas = Product::where('status', 1)->orderBy('id','DESC')->get();
                }
                
                $response = array();
                if (isset($datas)) {
                    foreach ($datas as $keys => $item) {
                        $temp['id'] = $item->id;
                        $temp['admin_id'] = $item->admin_id;
                        $temp['title'] =  $item->title;
                        $temp['description'] = $item->description;
                        $temp['course_fee'] = $item->course_fee;
                        $temp['tags'] = $item->tags;
                        $temp['valid_upto'] = $item->valid_upto;
                        $temp['certificates_image'] = $item->certificates;
                        $temp['introduction_image'] = $item->introduction_image;
                        $temp['status'] = $item->status;
                        $temp['rating'] = 4.6;
                        $temp['is_like'] = 1;
                        $temp['created_date'] = date('d/m/y,H:i', strtotime($item->created_date));
                        $response[] = $temp;
                    }
                }
                if ($type == 1) {
                    return response()->json(['status' => true, 'message' => 'Course Listing', 'data' => $response]);
                } else {
                    return response()->json(['status' => true, 'message' => 'Product Listing', 'data' => $response]);
                }
            }else{
                return response()->json(['status'=> false,'Message'=>'Please login']);
            }
        } catch (\Exception $e) {
            return errorMsg("Exception -> " . $e->getMessage());
        }
    }

    public function profile()
    {
        try {
            $user_id = Auth::user()->id;
            if($user_id)
            {
                $user = User::where('id', $user_id)->first(); /*Get data of category*/
                if (isset($user)) {
                    $temp['id'] = $user->id;
                    $temp['email'] = $user->email;
                    $temp['first_name'] =  ucfirst($user->first_name);
                    $temp['last_name'] = ucfirst($user->last_name);
                    $temp['phone'] = $user->phone;
                    if ($user->profile_image) {
                        $temp['profile_image'] = url('assets/upload/profile-image/'.$user->profile_image);
                    } else {
                        $temp['profile_image'] = url('assets/superadmin-images/no-image.png');
                    }
                    $temp['company'] = $user->company;
                    $temp['professional_title'] = 'Tatto Artist';
                    $temp['timezone'] = 'Arkansas';
                    $temp['created_date'] = date('d/m/y', strtotime($user->created_date));
                }
                return response()->json(['status' => true, 'message' => 'User Details', 'data' => $temp]);
            }else{
                return response()->json(['status'=> false,'Message'=>'Please login']);
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
            $data=array();
            if($u_id)
            {
                $old_password = $request->current_password;
                $new_password = $request->new_password;
                $datas = User::where('id',$u_id)->first();
                $u_password = $datas->password;
                if (Hash::check($old_password, $u_password))
                {
                    $updatedata = array('password'=>bcrypt($new_password));
                    $id = User::where('id',$u_id)->update($updatedata);
                    if($id)
                    {
                        $data['status'] = true;
                        $data['message'] = "Password change successfully";
                        return response()->json($data);
                    }else{
                        $data['status'] = false;
                        $data['message'] = "Something went wrong";
                        return response()->json($data);
                    }
                }else{
                    $data['status'] = true;
                    $data['message'] = "Password does not match";
                    return response()->json($data);
                }
            }else{
            $data['status'] = false;
            $data['message'] = "Please Login";
            return response()->json($data);
            }
        } catch (\Exception $e) {
            return errorMsg("Exception -> " . $e->getMessage());
        }
    }

    public function certificates()
    {
        try {
            $user_id = Auth::user()->id;
            if($user_id)
            {
                $datas = Certificate::where('user_id', $user_id)->get(); /*Get data of category*/
                $response = array();
                if (isset($datas)) {
                    foreach ($datas as $keys => $item) {
                        $temp['id'] = $item->id;
                        $temp['user_id'] = $item->user_id;
                        if ($item->certificate_image) {
                            $temp['certificate_image'] = url('assets/upload/certificate-image/'.$item->certificate_image);
                        }else{
                            $temp['certificate_image'] = ''; 
                        }
                        $temp['rating'] = 4.9;
                        $temp['name'] = 'Max bryant';
                        $response[] = $temp;
                    }
                }
                return response()->json(['status' => true, 'message' => 'Certificate Listing', 'data' => $response]);
            }else{
                return response()->json(['status'=> false,'Message'=>'Please login']);
            }
        } catch (\Exception $e) {
            return errorMsg("Exception -> " . $e->getMessage());
        }
    }

    public function notifications()
    {
        try {
            $user_id = Auth::user()->id;
            if($user_id)
            {
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
                            $temp['image'] = url('assets/upload/notification-image/'.$item->image);
                        }else{
                            $temp['image'] = ''; 
                        }
                        $temp['created_date'] = date('d/m/y,H:i', strtotime($item->created_at));
                        $response[] = $temp;
                    }
                }
                return response()->json(['status' => true, 'message' => 'Notifications Listing', 'data' => $response]);
            }else{
                return response()->json(['status'=> false,'Message'=>'Please login']);
            }
        } catch (\Exception $e) {
            return errorMsg("Exception -> " . $e->getMessage());
        }
    }

    public function save_card_listing(Request $request)
    {
        try {
            $card = CardDetails::where('user_id', Auth::user()->id)->get();
            if (count($card) > 0) {
                $response = [];
                foreach ($card as $key => $value) {
                    $temp['card_id'] = $value->id;
                    $temp['card_number'] = encrypt_decrypt('decrypt', $value->card_number);
                    $temp['card_holder_name'] = $value->card_holder_name;
                    $temp['cvv'] = encrypt_decrypt('decrypt', $value->cvv);
                    $temp['valid_upto'] = encrypt_decrypt('decrypt', $value->valid_upto);
                    $temp['card_type'] = $value->card_type;

                    $card_type = $value->card_type;
                    if($card_type == 'VISA')
                    {
                        $temp['card_image'] = url('assets/upload/notification-image/visa.png');
                    }else{
                        $temp['card_image'] = url('assets/upload/notification-image/m-card.png');
                    }
                    $response[] = $temp;
                }
                return successMsg('Card list found.', $response);
            } else return errorMsg('You have no card.');
        } catch (\Exception $e) {
            return response()->json(['status' => false, 'message' => 'Exception => ' . $e->getMessage()]);
        }
    }

    public function add_card(Request $request)
    {
        try {
            $validator = Validator::make($request->all(), [
                'card_number' => 'required|numeric',
                'valid_upto' => 'required',
                'cvv' => 'required',
                'card_holder_name' => 'required'
            ]);
            if ($validator->fails()) {
                return response()->json(['status' => false, 'message' => $validator->errors()->first()]);
            }else {
                $card = new CardDetails;
                $card->user_id = auth()->user()->id;
                $card->card_number = encrypt_decrypt('encrypt', $request->card_number);
                $card->valid_upto = encrypt_decrypt('encrypt', $request->valid_upto);
                $card->cvv = encrypt_decrypt('encrypt', $request->cvv);
                $card->card_holder_name = $request->card_holder_name;
                $card->card_type = 'VISA';
                $card->created_at = date('Y-m-d H:i:s');
                $card->save();
                return response()->json(['status' => true, 'message' => 'Card Added']);
            }
        } catch (\Exception $e) {
            return errorMsg("Exception -> " . $e->getMessage());
        }
    }

    public function delete_card(Request $request)
    {
        try {
            $validator = Validator::make($request->all(), [
                'id' => 'required|numeric',
            ]);
            if ($validator->fails()) {
                return response()->json(['status' => false, 'message' => $validator->errors()->first()]);
            }else {
                $card = CardDetails::where('id',$request->id)->delete();
                return response()->json(['status' => true, 'message' => 'Card deleted']);
            }
        } catch (\Exception $e) {
            return errorMsg("Exception -> " . $e->getMessage());
        }
    }

    public function cart_list(Request $request)
    {
        try {
            $type = $request->type;
            if ($type == 1) {    /* 1 stand for course ,2 for product */
                $datas = Course::where('status', 1)->orderBy('id','DESC')->limit(2)->get();
            } else {
                $datas = Product::where('status', 1)->orderBy('id','DESC')->get();
            }
            
            $response = array();
            if (isset($datas)) {
                foreach ($datas as $keys => $item) {
                    $temp['id'] = $item->id;
                    $temp['admin_id'] = $item->admin_id;
                    $temp['title'] =  $item->title;
                    $temp['description'] = $item->description;
                    $temp['course_fee'] = $item->course_fee;
                    $temp['tags'] = $item->tags;
                    $temp['valid_upto'] = $item->valid_upto;
                    $temp['certificates_image'] = $item->certificates;
                    $temp['introduction_image'] = $item->introduction_image;
                    $temp['status'] = $item->status;
                    $temp['rating'] = 4.6;
                    $temp['is_like'] = 1;
                    $temp['created_date'] = date('d/m/y,H:i', strtotime($item->created_date));
                    $response[] = $temp;
                }
            }
            if ($type == 1) {
                return response()->json(['status' => true, 'message' => 'Course Listing', 'data' => $response]);
            } else {
                return response()->json(['status' => true, 'message' => 'Product Listing', 'data' => $response]);
            }
            
            
        } catch (\Exception $e) {
            return errorMsg("Exception -> " . $e->getMessage());
        }
    }

    public function cart_details(Request $request)
    {
        try {
            $type = $request->type;
            if ($type == 1) {    /* 1 stand for course ,2 for product */
                $datas = Course::where('status', 1)->orderBy('id','DESC')->limit(2)->get();
            } else {
                $datas = Product::where('status', 1)->orderBy('id','DESC')->get();
            }
            
            $response = array();
            if (isset($datas)) {
                foreach ($datas as $keys => $item) {
                    $temp['id'] = $item->id;
                    $temp['admin_id'] = $item->admin_id;
                    $temp['title'] =  $item->title;
                    $temp['description'] = $item->description;
                    $temp['course_fee'] = $item->course_fee;
                    $temp['tags'] = $item->tags;
                    $temp['valid_upto'] = $item->valid_upto;
                    $temp['certificates_image'] = $item->certificates;
                    $temp['introduction_image'] = $item->introduction_image;
                    $temp['status'] = $item->status;
                    $temp['rating'] = 4.6;
                    $temp['is_like'] = 1;
                    $temp['created_date'] = date('d/m/y,H:i', strtotime($item->created_date));
                    $response[] = $temp;
                }
            }
            if ($type == 1) {
                return response()->json(['status' => true, 'message' => 'Course Listing', 'data' => $response]);
            } else {
                return response()->json(['status' => true, 'message' => 'Product Listing', 'data' => $response]);
            }
            
            
        } catch (\Exception $e) {
            return errorMsg("Exception -> " . $e->getMessage());
        }
    }
}
