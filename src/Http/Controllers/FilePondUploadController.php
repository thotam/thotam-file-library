<?php

namespace Thotam\ThotamFileLibrary\Http\Controllers;

use Illuminate\Support\Str;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;
use League\Flysystem\WhitespacePathNormalizer;
use Thotam\ThotamFileLibrary\Models\FileLibrary;
use Thotam\ThotamFileLibrary\Models\FilePondUpload;
use Thotam\ThotamFileLibrary\Jobs\GoogleDriveUpload;

class FilePondUploadController extends Controller
{
    /**
     * create_upload
     *
     * @param  mixed $request
     * @return void
     */
    public function create_upload(Request $request)
    {
        $FilePondUpload = FilePondUpload::create([
            'status' => 1,
            'active' => true,
        ]);

        return response($FilePondUpload->id, 200)->header('Content-Type', 'text/plain');
    }

    /**
     * patch_upload
     *
     * @param  mixed $request
     * @return void
     */
    public function patch_upload(Request $request)
    {
        //Check Patch
        if (!is_numeric($request->patch)) {
            $response = array('message' => 'ID Upload không hợp lệ');
            return response(json_encode($response), 400)->header('Content-Type', 'application/json');
        }

        //Get and check File in database
        $FilePondUpload = Cache::remember($request->patch . 'FilePondUpload', 60 * 60, function () use ($request) {
            return FilePondUpload::find($request->patch);
        });

        if (!!!$FilePondUpload) {
            $response = array('message' => 'Không tìm thấy file Upload');
            return response(json_encode($response), 404)->header('Content-Type', 'application/json');
        }

        //Do save chunk
        if ($request->header('upload-offset') == 0) {
            $hash = Str::random(30);
            $meta = Str::replace('/', '_', '-meta' . base64_encode($FilePondUpload->id . '_' . $request->header('upload-name')) . '-');
            $extension = '.tt';

            $name = $hash . $meta . $extension;

            $FilePondUpload->update([
                'status' => 2,
                'livewire_patch' => $name,
                'full_patch' => (new WhitespacePathNormalizer)->normalizePath(config('livewire.temporary_file_upload.directory') ?: 'livewire-tmp') . '/' . $name,
                'name' => $name,
            ]);

            Storage::disk('local')->write($FilePondUpload->full_patch, $request->getContent());
            Cache::forget($request->patch . 'FilePondUpload');
        } else {
            $upload = fopen(Storage::disk('local')->path($FilePondUpload->full_patch), 'a');
            fwrite($upload, $request->getContent());
            fclose($upload);
        }

        //Check if done
        if ($request->header('upload-offset') + $request->header('content-length') == $request->header('upload-length')) {
            $FilePondUpload->update([
                'status' => 3,
            ]);

            $response = array(
                'id' => $FilePondUpload->id,
                'status' => 3,
            );
            return response(json_encode($response), 200)->header('Content-Type', 'application/json');
        }

        //The server is waiting for the next chunk to be sent.
        $response = array(
            'id' => $FilePondUpload->id,
            'status' => 2,
            'uploadted' => $request->header('upload-offset') . " to " . ($request->header('upload-offset') + $request->header('content-length')),
        );
        return response(json_encode($response), 201)->header('Content-Type', 'application/json');
    }

    /**
     * delete_upload
     *
     * @param  mixed $request
     * @return void
     */
    public function delete_upload(Request $request)
    {
        //Check Patch
        if (!is_numeric($request->getContent())) {
            $response = array('message' => 'ID Upload không hợp lệ');
            return response(json_encode($response), 400)->header('Content-Type', 'application/json');
        }

        //Get and check File in database
        $FilePondUpload = FilePondUpload::find($request->getContent());

        if (!!!$FilePondUpload) {
            $response = array('message' => 'Không tìm thấy file Upload');
            return response(json_encode($response), 404)->header('Content-Type', 'application/json');
        }

        //Delete File
        Storage::disk('local')->delete($FilePondUpload->full_patch);

        //Update Database Status
        $FilePondUpload->update([
            'status' => 0,
        ]);

        return response($FilePondUpload->id . " Deleted", 200)->header('Content-Type', 'text/plain');
    }

    /**
     * ckeditor_upload
     *
     * @param  mixed $request
     * @return void
     */
    public function ckeditor_upload(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'upload' => 'required|file|image',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'error' => [
                    'message' => collect($validator->errors()->get('upload'))->implode(', '),
                ]
            ]);
        }

        //action
        try {
            $time = now();

            $file_name = $time->format('Ymd His') . " " . $request->upload->getClientOriginalName();
            $file_path = "CKEditor/" . $time->format('Y') . "/" . $time->format('m') . "/" . $time->format('d');
            $mime_type = $request->upload->getMimeType();

            $local_path = $request->upload->storeAs($file_path, $file_name, ["disk" => "public"]);

            $FileLibrary = FileLibrary::create([
                "drive" => "public",
                "file_name" => $file_name,
                "mime_type" => $mime_type,
                "active" => true,
                "local_path" => $local_path,
            ]);

            GoogleDriveUpload::dispatch($FileLibrary);

            return response()->json([
                'url' => $FileLibrary->image_mail_link,
            ]);
        } catch (\Illuminate\Database\QueryException $e) {
            return response()->json([
                'error' => [
                    'message' => implode(" - ", $e->errorInfo),
                ]
            ]);
        } catch (\Exception $e2) {
            return response()->json([
                'error' => [
                    'message' => $e2->getMessage(),
                ]
            ]);
        }
    }
}
