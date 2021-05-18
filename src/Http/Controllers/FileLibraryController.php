<?php

namespace Thotam\ThotamFileLibrary\Http\Controllers;

use Auth;
use Illuminate\Http\Request;
use Thotam\ThotamFileLibrary\Models\FileLibrary;
use Illuminate\Support\Facades\Storage;

class FileLibraryController extends Controller
{
    /**
     * view
     *
     * @return void
     */
    public function view($id)
    {
        $file = FileLibrary::find($id);
        if (!!$file) {
            if ($file->drive == 'google') {
                return redirect("https://drive.google.com/open?id=" . $file->google_id);
            } else {
                return response(Storage::disk('public')->get($file->local_path))->header('Content-Type', Storage::disk('public')->mimeType($file->local_path));
            }
        } else {
            return view('thotam-file-library::errors.dynamic', [
                'error_code' => '404',
                'error_description' => 'Không tìm thấy file này',
                'title' => 'FileLibrary',
            ]);
        }
    }

    /**
     * download
     *
     * @return void
     */
    public function download($id)
    {
        $file = FileLibrary::find($id);
        if (!!$file) {
            if ($file->drive == 'google') {
                return Storage::disk('google')->download($file->google_display_path);
            } else {
                return Storage::disk('public')->download($file->local_path);
            }
        } else {
            return view('thotam-file-library::errors.dynamic', [
                'error_code' => '404',
                'error_description' => 'Không tìm thấy file này',
                'title' => 'FileLibrary',
            ]);
        }
    }
}
