<?php

namespace App\Http\Controllers;

use App\Models\File;
use App\Repositories\file\IFileRepository;
use App\Repositories\group\IGroupRepository;
use App\Traits\FileCheckTrait;
use App\Traits\ResponseTrait;

use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
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


    public function upload(Request $request): JsonResponse
    {
        $user = auth()->user();
        $validator = Validator::make($request->all(), [
            'group' => 'required',
            'file' => 'required',
        ]);
        if ($validator->fails()) {
            return $this->returnError("V00", $validator->errors());
        }
        $file = $request->file('file');
        $fileName = $file->getClientOriginalName();
        $file->move(public_path('Files'), $fileName);
        File::create([
            'name' => $fileName,
            'path' => public_path('Files'),
            'user_id' => $user->id,
            'group_id' => $request->group
        ]);
        return $this->returnSuccess("D00", "file uploaded successfully..");
    }


    public function download(Request $request){
        $user=auth()->user();
        $validator = Validator::make($request->all(), [
            'id' => 'required',
        ]);
        if ($validator->fails()) {
            return $this->returnError("V00", $validator->errors());
        }
        $file = File::find($request->id);
        if (!$file){
            return $this->returnError("D01","file not found..");
        }
        if ($user->id!=$file->forID){
            return $this->returnError("P01","You need to checkin this file firstly..");
        }

        $path = public_path('Files/' . $file->name);

        if (!file_exists($path)) {
            return $this->returnError("F01","File not found");
//            abort(404, 'File not found');
        }else{
            return response()->download($path);

        }
//        return $this->returnData("path",$path);
//        return Response::download($path,$request->name,['Content-Type: application/txt'],"inline");

    }


    public function removeFile(Request $request):JsonResponse{

        $user = auth()->user();
        $validator = Validator::make($request->all(), [
            'id' => 'required|exists:files,id,user_id,' . $user->id,
        ]);

        if ($validator->fails()) {
            return $this->returnError("V01", $validator->errors());
        }

        $this->FileRepository->removeFile($request);

        return $this->returnSuccess("D01", "File deleted successfully.");
    }


    public function index(): JsonResponse{
        $files = $this->FileRepository->index();
        return $this->returnData('files',$files,"","");
    }


    public function search($filter): JsonResponse{
        $filterResult = $this->FileRepository->search($filter);
        return $this->returnData('files',$filterResult,"","");
    }

    public function bulkCheckIn(Request $request){
        $user = auth()->user();
        $ids = $request->input('ids', []);
        if (!is_array($ids)) {
            $ids = explode(',', $ids);
        }
        if (!$this->check($ids)) {
            return response()->json([
                "message" => "one or more files are locked.",
            ]);
        }
            DB::table('files')->whereIn('id',$ids)->lockForUpdate()
                ->update(['status' => 1,'forID' => $user->id]);
            DB::commit();

        return response()->json([
            "message" => "You checked in one or more files successfully.",
        ]);

    }


    public function checkOut(Request $request){
        $user= auth()->user();
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
            ]);
        }
    }
}
