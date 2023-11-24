<?php

namespace App\Http\Controllers;

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

    public function index(): JsonResponse{
        $groups = $this->GroupRepository->index();
        return $this->returnData('groups',$groups,"","");
    }

    public function user_group():JsonResponse{
        $groups = $this->GroupRepository->user_group();
        return $this->returnData('groups',$groups,"","");
    }

    public function store(Request $request): JsonResponse{
        $validator = Validator::make($request->all(), [
            'name' => 'required',
        ]);
        if ($validator->fails()) {
            return $this->returnError("V00", $validator->errors());
        }
        $this->GroupRepository->store($request);

        return $this->returnSuccess("D00","Groupe created successfully..");
    }

    public function addMembers(Request $request): JsonResponse{
        $validator = Validator::make($request->all(), [
            'group' => 'required',
            'users' => 'required',
        ]);
        if ($validator->fails()) {
            return $this->returnError("V00", $validator->errors());
        }
        $this->GroupRepository->addMembers($request);
        return $this->returnSuccess("D00","Members Added successfully..");
    }


}
