<?php

namespace App\Http\Controllers;

use App\Aspects\Addmembers;
use App\Aspects\Transaction;
use App\Models\Group;
use App\Models\User;
use App\Models\UserGroup;
use App\Repositories\group\IGroupRepository;
use App\Traits\ResponseTrait;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;

class GroupController extends Controller
{
    use ResponseTrait;

    public $group;

    public function __construct(IGroupRepository $groupRepository)
    {
        $this->GroupRepository = $groupRepository;
    }

    public function index(): JsonResponse
    {
        $groups = $this->GroupRepository->index();
        return $this->returnData('groups', $groups, "", "");
    }

    public function myGroups(): JsonResponse
    {
        $groups = $this->GroupRepository->my_groups();
        return $this->returnData('groups', $groups, "", "");
    }

    public function membershipGroups(): JsonResponse
    {
        $groups = $this->GroupRepository->membership_groups();
        return $this->returnData('groups', $groups, "", "");
    }

    public function groupUsers(Request $request): JsonResponse{
        $user = auth()->user();
        $validator = Validator::make($request->all(), [
            'group' => 'required|exists:groups,id',
        ]);
        if ($validator->fails()) {
            return $this->returnError("V00", $validator->errors());
        }
        $group = Group::find($request->group);
        if ($group->user_id!=$user->id){
            return $this->returnError("P01","You Do Not Have Permission..");
        }
        $users = $this->GroupRepository->groupUsers($request);
        return $this->returnData("users",$users,"","");
    }

    public function store(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required',
        ]);
        if ($validator->fails()) {
            return $this->returnError("V00", $validator->errors());
        }
        $this->GroupRepository->store($request);

        return $this->returnSuccess("D00", "Groupe created successfully..");
    }

    public function update(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'group' => 'required|exists:groups,id',
            'name' => 'required',
        ]);
        if ($validator->fails()) {
            return $this->returnError("V00", $validator->errors());
        }
        $this->GroupRepository->update($request);

        return $this->returnSuccess("D00", "Group updated successfully..");
    }

    #[Transaction]
    public function addMembers(Request $request): JsonResponse
    {
        $user = auth()->user();
        $validator = Validator::make($request->all(), [
            'group' => 'required|exists:groups,id',
            'users' => 'required',
        ]);
        if ($validator->fails()) {
            return $this->returnError("V00", $validator->errors());
        }
        $group = Group::find($request->group);
        if ($group->user_id!=$user->id){
            return $this->returnError("P01","You Do Not Have Permission..");
        }
        $this->GroupRepository->addMembers($request);
        return $this->returnSuccess("D00", "Members Added successfully..");
    }

    #[Transaction]
    public function removeMember(Request $request): JsonResponse{
        $user = auth()->user();
        $validator = Validator::make($request->all(), [
            'group' => 'required|exists:groups,id',
            'user' => 'required|exists:users,id',
        ]);
        if ($validator->fails()) {
            return $this->returnError("V00", $validator->errors());
        }
        $group = Group::find($request->group);
        if ($group->user_id != $user->id){
            return $this->returnError("P01","You do not have permission");
        }
        $member = UserGroup::where('group_id',$request->group)->where('user_id',$request->user)->first();
        if (!$member){
            return $this->returnError("D01","Member not found");
        }else{
            $this->GroupRepository->removeMember($request);
            return $this->returnSuccess("D00", "Member Removed successfully..");
        }

    }

    #[Transaction]
    public function deleteGroup(Request $request): JsonResponse{
        $user = auth()->user();
        $validator = Validator::make($request->all(), [
            'group' => 'required|exists:groups,id',
        ]);
        if ($validator->fails()) {
            return $this->returnError("V00", $validator->errors());
        }
        $this->GroupRepository->deleteGroup($request);
        return $this->returnSuccess("V00","Group Deleted Successfully..");
    }

}
