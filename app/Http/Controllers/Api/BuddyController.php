<?php

namespace App\Http\Controllers\Api;
use App\Http\Controllers\Api\BaseController;
use App\Models\Buddy;
use App\Models\User;
use App\Models\Favourite;
use Illuminate\Http\Request;
use DB,Validator,Auth;
use App\Http\Resources\UserResource;
use App\Http\Resources\BuddyResource;
use App\Models\Helpers\CommonHelper;

class BuddyController extends BaseController
{
    use CommonHelper;

    /**
     * Find Buddy.
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */

    public function find_new_buddy(Request $request) {
        try{
            $search = @$request->search;

            $connected_buddy_ids1 = Buddy::where(function($q1) use($request){
                                        $q1->where('user1',Auth::guard('api')->user()->id);
                                    })
                                    ->where('status','!=','rejected')->pluck('user2')->toArray();
            $connected_buddy_ids2 = Buddy::where(function($q1) use($request){
                                        $q1->where('user2',Auth::guard('api')->user()->id);
                                    })
                                    ->where('status','!=','rejected')->pluck('user1')->toArray();
            $connected_buddy_ids = array_merge($connected_buddy_ids1, $connected_buddy_ids2);

            $users = User::where('status','active')->where('user_type','customer')->where('id','!=',Auth::guard('api')->user()->id)->whereNotIn('id',$connected_buddy_ids);

            if($search && $search != '') {
                $users = $users->where(function($que) use($search){
                                $que->where('first_name','like','%'.$search.'%')
                                ->orWhere('company_name','like','%'.$search.'%')
                                ->orWhere('designation','like','%'.$search.'%')
                                ->orWhere('email','like','%'.$search.'%')
                                ->orWhere('mobile_number','like','%'.$search.'%');
                            });
            }

            $users = $users->paginate();

            if(count($users))
            {
                return $this->sendPaginateResponse(UserResource::collection($users), trans('buddy.users_list'));
            }
            else
            {
                return $this->sendResponse([],trans('buddy.users_not_found'));
            }
        }catch(\Exception $e){
            return $this->sendError('',$e->getMessage());           
        }
    }

    /**
     * Add Buddy.
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */

    public function add_buddy(Request $request) {
        DB::beginTransaction();
        try{
            $validator = Validator::make($request->all(), [
                'user_id' => 'required|numeric|exists:users,id',
            ]);
            if($validator->fails()){
                return $this->sendValidationError('', $validator->errors()->first());       
            }

            //check if request already sent by any one of users
            $request_sent = Buddy::where('user1',Auth::guard('api')->user()->id)->where('user2',$request->user_id)->where('status','pending')->first();
            $request_sent1 = Buddy::where('user2',Auth::guard('api')->user()->id)->where('user1',$request->user_id)->where('status','pending')->first();
            if($request_sent || $request_sent1)
            {
                return $this->sendResponse('', trans('buddy.request_already_sent'));
            }
            //check if buddy already exist in buddy list
            $buddy_exist = Buddy::where('user1',Auth::guard('api')->user()->id)->where('user2',$request->user_id)->where('status','accepted')->first();
            $buddy_exist1 = Buddy::where('user2',Auth::guard('api')->user()->id)->where('user1',$request->user_id)->where('status','accepted')->first();
            if($buddy_exist || $buddy_exist1)
            {
                return $this->sendResponse('', trans('buddy.buddy_already_exist'));
            }

            $buddy = new Buddy;
            $buddy->user1 = Auth::guard('api')->user()->id;
            $buddy->user2 = $request->user_id;
            if($buddy->save()){
                // New Buddy Request Notification (System Notification To Reciever)
                $user     = User::where(['id' => $request->user_id])->first();
                $title    = trans('notify.buddy_request_title');
                $body     = trans('notify.buddy_request_body',['user' => $user->first_name,'buddy_name' => Auth::guard('api')->user()->first_name]);
                $slug     = 'buddy_request';
                $buddy_id = Auth::guard('api')->user()->id;

                $this->sendNotification($user,$title,$body,$slug,$buddy_id);
                DB::commit();
                return $this->sendResponse('', trans('buddy.request_sent'));
            }else{
                DB::rollback();
                return $this->sendError('',trans('buddy.request_sent_error'));
            }
        }catch(\Exception $e){
            DB::rollback();
            return $this->sendError('',$e->getMessage());           
        }
    }

    /**
     * Add Buddy.
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */

    public function remove_buddy(Request $request) {
        DB::beginTransaction();
        try{
            $validator = Validator::make($request->all(), [
                'buddy_id' => 'required|numeric|exists:users,id',
            ]);
            if($validator->fails()){
                return $this->sendValidationError('', $validator->errors()->first());       
            }
            $buddy = Buddy::where(function($q1) use($request){
                $q1->where('user1',Auth::guard('api')->user()->id);
                $q1->where('user2',$request->buddy_id);
                $q1->where('status','accepted');
            })
            ->orWhere(function($q2) use($request){
                $q2->where('user1',$request->buddy_id);
                $q2->where('user2',Auth::guard('api')->user()->id);
                $q2->where('status','accepted');
            })
            ->where('status','accepted')->first();

            if(!$buddy)
            {
                return $this->sendValidationError('', trans('buddy.buddy_not_exist'));  
            }

            if($buddy->delete())
            {
                //remove buddy from my fav
                Favourite::where('buddy_id',$request->buddy_id)->where('user_id',Auth::guard('api')->user()->id)->delete();
                //remove me from buddy fav
                Favourite::where('user_id',$request->buddy_id)->where('buddy_id',Auth::guard('api')->user()->id)->delete();
                DB::commit();
                return $this->sendResponse('', trans('buddy.buddy_removed'));
            }
        }catch(\Exception $e){
            DB::rollback();
            return $this->sendError('',$e->getMessage());           
        }
    }

    /**
     * Buddy Requests.
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */

    public function buddy_requests(Request $request) {
        DB::beginTransaction();
        try{
            $buddy_requests = Buddy::where('user2',Auth::guard('api')->user()->id)->where('status','pending')->orderBy('created_at','desc')->paginate();

            if(count($buddy_requests))
            {
                return $this->sendPaginateResponse(BuddyResource::collection($buddy_requests), trans('buddy.your_requests'));
            }
            else
            {
                return $this->sendResponse([],trans('buddy.requests_not_found'));
            }
        }catch(\Exception $e){
            DB::rollback();
            return $this->sendError('',$e->getMessage());           
        }
    }

    /**
     * Accept Buddy Request.
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */

    public function accept_buddy_request(Request $request) {
        DB::beginTransaction();
        try{
            $validator = Validator::make($request->all(), [
                'request_id' => 'required|numeric|exists:buddies,id',
            ]);
            if($validator->fails()){
                return $this->sendValidationError('', $validator->errors()->first());       
            }

            $buddy_request = Buddy::where('id',$request->request_id)->where('status','pending')->first();

            if(!$buddy_request)
            {
                return $this->sendError('',trans('buddy.invalid_request'));
            }

            $buddy_request->status = 'accepted';
            if($buddy_request->save()){

                // Buddy Request Accept Notification (System Notification To sender when receiver accept the request)
                $user     = User::where(['id' => $buddy_request->user1])->first();
                $title    = trans('notify.buddy_request_accepted_title');
                $body     = trans('notify.buddy_request_accepted_body',['user' => $user->first_name,'buddy_name' => Auth::guard('api')->user()->first_name]);
                $slug     = 'buddy_request_accepted';
                $buddy_id = Auth::guard('api')->user()->id;

                $this->sendNotification($user,$title,$body,$slug,$buddy_id);

                DB::commit();
                return $this->sendResponse('', trans('buddy.request_accepted',['buddy_name'=>$buddy_request->user_1->first_name]));
            }else{
                DB::rollback();
                return $this->sendError('',trans('buddy.request_accepted_error'));
            }
        }catch(\Exception $e){
            DB::rollback();
            return $this->sendError('',$e->getMessage());           
        }
    }

    /**
     * Reject Buddy Request.
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */

    public function reject_buddy_request(Request $request) {
        DB::beginTransaction();
        try{
            $validator = Validator::make($request->all(), [
                'request_id' => 'required|numeric|exists:buddies,id',
            ]);
            if($validator->fails()){
                return $this->sendValidationError('', $validator->errors()->first());       
            }

            $buddy_request = Buddy::where('id',$request->request_id)->where('status','pending')->first();

            if(!$buddy_request)
            {
                return $this->sendError('',trans('buddy.invalid_request'));
            }

            $buddy_request->status = 'rejected';
            if($buddy_request->save()){
                DB::commit();
                return $this->sendResponse('', trans('buddy.request_rejected',['buddy_name'=>$buddy_request->user_1->first_name]));
            }else{
                DB::rollback();
                return $this->sendError('',trans('buddy.request_rejected_error'));
            }
        }catch(\Exception $e){
            DB::rollback();
            return $this->sendError('',$e->getMessage());           
        }
    }
    
    /**
     * Buddy List.
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */

    public function buddy_list(Request $request) {
        try{
            $search = @$request->search;

            $buddies = Buddy::where(function($que){
                            $que->where('user1',Auth::guard('api')->user()->id)
                            ->orWhere('user2',Auth::guard('api')->user()->id);
                        })
                        ->where('status','accepted');

            if($search && $search != '') {
                $buddies = $buddies->where(function($qu) use($search){
                                $qu->whereHas('user_1',function($q1) use($search){
                                    $q1->where('first_name','like','%'.$search.'%');
                                    $q1->where('id','!=',Auth::guard('api')->user()->id);

                                });
                                $qu->orWhereHas('user_2',function($q2) use($search){
                                    $q2->where('first_name','like','%'.$search.'%');
                                    $q2->where('id','!=',Auth::guard('api')->user()->id);
                                });
                            });
                
            }

            $buddies = $buddies->paginate(20);

            if(count($buddies))
            {
                return $this->sendPaginateResponse(BuddyResource::collection($buddies), trans('buddy.buddy_list'));
            }
            else
            {
                return $this->sendResponse([],trans('buddy.buddies_not_found'));
            }
        }catch(\Exception $e){
            return $this->sendError('',$e->getMessage());           
        }
    }

    /**
     * Buddy List for filter.
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */

    public function buddy_list_for_filter(Request $request) {
        try{
            $search = @$request->search;

            $buddies = Buddy::where(function($que){
                            $que->where('user1',Auth::guard('api')->user()->id)
                            ->orWhere('user2',Auth::guard('api')->user()->id);
                        })
                        ->where('status','accepted')->get();

            $response = [];
            foreach ($buddies as $bd) {
                $user = $bd->user_1;
                if($bd->user1 == Auth::guard('api')->user()->id)
                {
                    $user = $bd->user_2;
                }
                $buddy['id'] = (string)$user->id; 
                $buddy['name'] = (string)$user->first_name; 
                $response[] = $buddy;
            }
            if(count($buddies))
            {
                return $this->sendResponse($response, trans('buddy.buddy_list'));
            }
            else
            {
                return $this->sendResponse([],trans('buddy.buddies_not_found'));
            }
        }catch(\Exception $e){
            return $this->sendError('',$e->getMessage());           
        }
    }

    /**
     * Buddy Profile.
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */

    public function buddy_profile(Request $request) {
        try{

            $validator=  Validator::make($request->all(),[
                'buddy_id'         => 'required|numeric|exists:users,id',
            ]);

            if($validator->fails()) {
                return $this->sendValidationError('', $validator->errors()->first());
            }

            $buddy = User::find($request->buddy_id);


            if($buddy)
            {
                return $this->sendResponse(new UserResource($buddy), trans('buddy.buddy_profile'));
            }
            else
            {
                return $this->sendResponse([],trans('buddy.buddies_not_found'));
            }
        }catch(\Exception $e){
            return $this->sendError('',$e->getMessage());           
        }
    }

    public function favourite_buddies(Request $request){

        $user = Auth::guard('api')->user();
        
        $favorite_buddy_ids = Favourite::where('user_id',$user->id)->pluck('buddy_id')->toArray();
        
        $fav_buddies = User::whereIn('id', $favorite_buddy_ids)->paginate();

        if(count($fav_buddies)) {
            return $this->sendPaginateResponse(UserResource::collection($fav_buddies),trans('buddy.favourite_buddies'));
        }else {
            return $this->sendResponse('',trans('common.no_data')); 
        }
    }

    public function add_remove_favourite(Request $request, $id) {
        $user = Auth::guard('api')->user();
        $buddy = Buddy::where(function($q1) use($id){
            $q1->where('user1',Auth::guard('api')->user()->id);
            $q1->where('user2',$id);
            $q1->where('status','accepted');
        })
        ->orWhere(function($q2) use($id){
            $q2->where('user1',$id);
            $q2->where('user2',Auth::guard('api')->user()->id);
            $q2->where('status','accepted');
        })
        ->first();

        if(!$buddy)
        {
            return $this->sendValidationError('', trans('buddy.buddy_not_exist'));  
        }
        $fav_data = ['user_id' => $user->id, 'buddy_id' => $id];
            
        $fav_buddy = Favourite::where($fav_data)->get();
        if(count($fav_buddy)) {
            Favourite::where($fav_data)->delete();
        } else {
            Favourite::create($fav_data);
        }
        return $this->sendResponse(new BuddyResource($buddy),trans('buddy.favourite_response'));  
    }

    /**
     * All Buddies (Req, Fav, Connected).
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */

    public function all_buddies(Request $request) {
        try{
            $data = [];

            //buddy requests
            $buddy_requests = Buddy::where('user2',Auth::guard('api')->user()->id)->where('status','pending')->latest()->take(3)->get();
            $data['buddy_requests'] = BuddyResource::collection($buddy_requests);

            // favourite buddies
            $favorite_buddy_ids = Favourite::where('user_id',Auth::guard('api')->user()->id)->pluck('buddy_id')->toArray();
            $fav_buddies = User::whereIn('id', $favorite_buddy_ids)->latest()->take(5)->get();
            $data['favourite_buddies'] = UserResource::collection($fav_buddies);

            //connected buddies excluding favourites
            $buddies = Buddy::where(function($que) use($favorite_buddy_ids){
                            $que->where('user1',Auth::guard('api')->user()->id)
                            ->orWhere('user2',Auth::guard('api')->user()->id);
                        })
                        ->where(function($qu) use($favorite_buddy_ids){
                            $qu->whereNotIn('user1',$favorite_buddy_ids)
                            ->whereNotIn('user2',$favorite_buddy_ids);
                        })
                        ->where('status','accepted')->latest()->take(5)->get();
            $data['buddies'] = BuddyResource::collection($buddies);

            return $this->sendResponse($data, trans('buddy.your_requests'));
            
        }catch(\Exception $e){
            return $this->sendError('',$e->getMessage());           
        }
    }
    
    
}
