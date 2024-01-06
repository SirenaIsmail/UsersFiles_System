<?php

namespace App\Http\Controllers;

use App\Aspects\Logger;
use App\Aspects\Transaction;
use App\Models\File;
use App\Models\Group;
use App\Models\User;
use App\Models\UserGroup;
use App\Repositories\file\IFileRepository;
use App\Repositories\group\IGroupRepository;
use App\Traits\FileCheckTrait;
use App\Traits\ResponseTrait;

use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;
use Symfony\Component\HttpFoundation\BinaryFileResponse;
use Illuminate\Support\Facades\Response;


class FileController extends Controller
{
    use ResponseTrait;
    use FileCheckTrait;
    public $group;

    public function __construct(IFileRepository $fileRepository)
    {
        $this->FileRepository = $fileRepository;
    }

    #[Logger]
    #[Transaction]
    public function upload(Request $request): JsonResponse
    {
        $user = auth()->user();
        $validator = Validator::make($request->all(), [
            'group' => ['required',Rule::exists('groups','id')->where('user_id',$user->id)],
            'file' => 'required',
        ]);
        if ($validator->fails()) {
            return $this->returnError("V00", $validator->errors());
        }
        $file = $request->file('file');
        $fileName = $file->getClientOriginalName();
        if (\Illuminate\Support\Facades\File::exists(public_path('Files/').$fileName)) {
            return $this->returnError("E01","sory! there are a file with the same name");
        }
        $file->move(public_path('Files'), $fileName);
        $fl = File::create([
            'name' => $fileName,
            'path' => public_path('Files'),
            'user_id' => $user->id,
            'group_id' => $request->group
        ]);
        return $this->returnData("file",$fl, "file uploaded successfully..","D00");
    }

    #[Logger]
    #[Transaction]
    public function update(Request $request): JsonResponse
    {
        $user = auth()->user();
        $validator = Validator::make($request->all(), [
            'id' => 'required|exists:files,id',
            'file' => 'required',
        ]);
        if ($validator->fails()) {
            return $this->returnError("V00", $validator->errors());
        }
        $oldfile = File::find($request->id);
        if ($oldfile->forID!=$user->id){
            return $this->returnError("P01","You Do Not Have Permission..");
        }
        $file = $request->file('file');
        $fileName = $oldfile->name;
        $file->move(public_path('Files'), $fileName);
        $oldfile->update([
            'name' => $fileName,
            'path' => public_path('Files'),
            'user_id' => $user->id,
            'group_id' => $request->group
        ]);
        return $this->returnData("file",$oldfile, "file Updated successfully..","D00");
    }

    #[Logger]
    public function download(Request $request){
        $user=auth()->user();
        $validator = Validator::make($request->all(), [
            'id' => 'required|exists:files,id',
        ]);
        if ($validator->fails()) {
            return $this->returnError("V00", $validator->errors());
        }
        $file = File::find($request->id);
        if ($user->id!=$file->forID){
            return $this->returnError("P01","You need to checkin this file firstly..");
        }

        $path = public_path('Files/' . $file->name);

        if (!file_exists($path)) {
            return $this->returnError("F01","File not found");
        }else{
            return response()->download($path);

        }
    }

    #[Transaction]
    public function removeFile(Request $request):JsonResponse{

        $user = auth()->user();
        $validator = Validator::make($request->all(), [
            'id' => 'required|exists:files,id'
        ]);
        if ($validator->fails()) {
            return $this->returnError("V01", $validator->errors());
        }
        $file = File::find($request->id);
        $group = Group::find($file->group_id);
        if ($group->user_id != $user->id){
            return $this->returnError("P01","You Do Not Have Permission..");
        }
        $this->FileRepository->removeFile($request);
        return $this->returnSuccess("D01", "File deleted successfully.");
    }


    public function index(): JsonResponse{
        $files = $this->FileRepository->index();
        return $this->returnData('files',$files,"","");
    }

    public function myCheckedFiles(): JsonResponse{
        $files = $this->FileRepository->my_checked();
        return $this->returnData('files',$files,"","");
    }


    public function search($filter): JsonResponse{
        $filterResult = $this->FileRepository->search($filter);
        return $this->returnData('files',$filterResult,"","");
    }

    #[Logger]
    public function bulkCheckIn(Request $request){
        $user = auth()->user();
        $ids = $request->input('ids', []);
        if (!is_array($ids)) {
            $ids = explode(',', $ids);
        }
        if (!$this->check($ids)) {
            return response()->json([
                "message" => "one or more files are locked.",
            ],status: 404);
        }
            DB::table('files')->whereIn('id',$ids)->lockForUpdate()
                ->update(['status' => 1,'forID' => $user->id]);
            DB::commit();

        return response()->json([
            "message" => "You checked in one or more files successfully.",
        ]);

    }

    #[Logger]
    public function checkOut(Request $request){
        $user= auth()->user();
        $validator = Validator::make($request->all(), [
            'id' => 'required|exists:files,id'
        ]);

        if ($validator->fails()) {
            return $this->returnError("V01", $validator->errors());
        }
        $file = File::find($request->id);
        if ($user->id!=$file->forID){
            return $this->returnError("P01","you do not have permission");
        }
        if ($file->status == 1) {
            $file->status = 0;
            $file->forID = null;
            $file->save();
            return response()->json([
                "message" => "you checked out successfully.",
            ]);
        } else {
            return response()->json([
                "message" => "check out failed.",
            ],status: 404);
        }
    }

    public function file_history(Request $request): JsonResponse{
        $user = auth()->user();
        $validator = Validator::make($request->all(), [
            'id' => 'required|exists:files,id'
        ]);

        if ($validator->fails()) {
            return $this->returnError("V01", $validator->errors());
        }
        $file = File::find($request->id);
        $group = Group::find($file->group_id);
        if ($group->user_id != $user->id){
            return $this->returnError("P01","You Do Not Have Permission..");
        }

        $his = $this->FileRepository->file_his($request);
        return $this->returnData('history',$his,'',"D00");
    }


    public function user_history(Request $request): JsonResponse{
        $user = auth()->user();
        $validator = Validator::make($request->all(), [
            'id' => 'required|exists:users,id'
        ]);

        if ($validator->fails()) {
            return $this->returnError("V01", $validator->errors());
        }
        $usr = User::find($request->id);
        $user_groups = UserGroup::with('groups')->where('user_id',$usr->id)->get();
        $i=0;
        foreach ($user_groups as $group){
            if ($group->groups->user_id==$user->id){
                $i++;
            }
        }
        if ($i==0){
            return $this->returnError("P01","You Do Not Have Permission..");
        }

        $his = $this->FileRepository->user_his($request);
        return $this->returnData('history',$his,'',"D00");
    }
}
