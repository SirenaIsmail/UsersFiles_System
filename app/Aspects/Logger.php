<?php

namespace App\Aspects;

use AhmadVoid\SimpleAOP\Aspect;
use App\Models\File;
use App\Models\FileHistory;
use Carbon\Carbon;

#[\Attribute(\Attribute::TARGET_CLASS | \Attribute::TARGET_METHOD)]
class Logger implements Aspect
{

    // The constructor can accept parameters for the attribute
    public function __construct()
    {

    }

    public function executeBefore($request, $controller, $method)
    {
        // TODO: Implement executeBefore() method.
    }

    public function executeAfter($request, $controller, $method, $response)
    {
        $user = auth()->user();
        if ($response->getStatusCode() == 200) {
            if ($method == 'upload') {
                if ($response->getData()->file) {
                    $action = 0;
                    FileHistory::create([
                        "file_name" => $response->getData()->file->name,
                        "user_name" => $user->name,
                        "action" => $action,
                        "file_id" => $response->getData()->file->id,
                        "user_id" => $user->id,
                        "created_at" => Carbon::now()->format('d-m-Y h:i A')
                    ]);
                }
            }elseif ($method == 'update') {
                    $action = 4;
                    FileHistory::create([
                        "file_name" => $response->getData()->file->name,
                        "user_name" => $user->name,
                        "action" => $action,
                        "file_id" => $response->getData()->file->id,
                        "user_id" => $user->id,
                        "created_at" => Carbon::now()->format('d-m-Y h:i A')
                    ]);

            } elseif ($method == 'download') {
                $action = 2;
                $file = File::find($request->id);
                FileHistory::create([
                    "file_name" => $file->name,
                    "user_name" => $user->name,
                    "action" => $action,
                    "file_id" => $file->id,
                    "user_id" => $user->id,
                    "created_at" => Carbon::now()->format('d-m-Y h:i A')
                ]);
            } elseif ($method == 'bulkCheckIn') {
                $action = 1;
                $ids = $request->input('ids', []);
                if (!is_array($ids)) {
                    $ids = explode(',', $ids);
                }
                $files = File::whereIn('id', $ids)->get();
                foreach ($files as $file) {
                    FileHistory::create([
                        "file_name" => $file->name,
                        "user_name" => $user->name,
                        "action" => $action,
                        "file_id" => $file->id,
                        "user_id" => $user->id,
                        "created_at" => Carbon::now()->format('d-m-Y h:i A')
                    ]);
                }
            } elseif ($method == 'checkOut') {
                $action = 3;
                $file = File::find($request->id);
                FileHistory::create([
                    "file_name" => $file->name,
                    "user_name" => $user->name,
                    "action" => $action,
                    "file_id" => $file->id,
                    "user_id" => $user->id,
                    "created_at" => Carbon::now()->format('d-m-Y h:i A')
                ]);
            }
        }

    }

    public function executeException($request, $controller, $method, $exception)
    {

    }
}
