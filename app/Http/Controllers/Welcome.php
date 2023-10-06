<?php

namespace App\Http\Controllers;

use App\Events\RedisUpdated;
use App\Http\Controllers\Controller;
use App\Jobs\ProcessAfterResponseJob;
use Illuminate\Http\Request;
use App\Models\WelcomeModel;
use Illuminate\Support\Facades\Redis;
use Illuminate\Support\Facades\Storage;

class Welcome extends Controller
{
    //

    public function showData()
    {
        $files = WelcomeModel::all();
        $filesStatus = [];
        foreach ($files as $file) {
            $name = $file->name;
            $time = $file->updated_at;
            $status = $this->getRedis($name);
            $filesStatus[] = [
                'time' => $time,
                'name' => $name,
                'status' => $status,
            ];
        }
        // print_r($filesStatus);
        return view('welcome', ['files' => $filesStatus]);
    }
    protected function getRedis($name)
    {
        // return Redis::get("status:{$name}") ?: 'complete';
        return  Redis::get("status:{$name}");
        // return Redis::set("status",'complete');
    }
    protected function cleanNonUtf8($content)
    {
        // Reject potentially malicious or malformed strings
        if (!mb_check_encoding($content, 'UTF-8')) {
            return '';
        }

        // Remove any non-UTF-8 characters
        $cleanedContent = iconv('UTF-8', 'UTF-8//IGNORE', $content);

        return $cleanedContent;
    }
    protected function findRowIndex($data, $id)
    {
        foreach ($data as $index => $row) {
            if ($row[0] == $id) {
                return $index;
            }
        }

        return null;
    }

    protected function mergeCSVData($original, $new)
    {
        $merged = $original;

        foreach ($new as $newRow) {
            $index = $this->findRowIndex($original, $newRow[0]);

            if ($index !== null) {
                $merged[$index] = $newRow; // Update the existing row with new data
            } else {
                $merged[] = $newRow; // Add the new row to the merged data
            }
        }

        return $merged;
    }

    public function fileUpload(Request $req)
    {
        $req->validate([
            'file' => 'required|mimes:csv,txt'
        ]);

        $fileModel = new WelcomeModel;

        if ($req->file()) {
            $fileName =  $req->file->getClientOriginalName();
            $file = $req->file('file');
            $checkPath = "uploads/{$fileName}";
            $content = file_get_contents($file->getRealPath());
            $cleanContent = $this->cleanNonUtf8($content);
            file_put_contents($file->getRealPath(), $cleanContent);
            Redis::set("status:{$fileName}", 'processing');
            try {
                if (Storage::disk('public')->exists($checkPath)) {
                    $originalContent = array_map('str_getcsv', file(public_path("storage/{$checkPath}")));
                    $newContent = array_map('str_getcsv', file($req->file->getRealPath()));
                    $updatedContent = $this->mergeCSVData($originalContent, $newContent);

                    $csvOutput = fopen(public_path("storage/{$checkPath}"), 'w');
                    foreach ($updatedContent as $row) {
                        fputcsv($csvOutput, $row);
                    }
                    fclose($csvOutput);

                    Redis::set("status:{$fileName}", 'complete');
                    event(new RedisUpdated(['status' => 'complete']));
                } else {
                    $filePath = "uploads". $fileName . 'public';
                    $fileModel->name = $fileName;
                    $fileModel->file_path = '/storage/' . $filePath;
                    $fileModel->status =  Redis::get("status:{$fileName}");
                    $fileModel->save();
                    $filePath = $req->file('file')->storeAs('uploads', $fileName, 'public');
                   
                    event(new RedisUpdated(['status' => 'complete']));
                    Redis::set("status:{$fileName}", 'complete');
                    // dispatch(new ProcessAfterResponseJob( $req->file('file')->getRealPath(),$fileName));
                }
            } catch (\Exception $e) {
                Redis::set("status:{$fileName}", 'failed');
                Redis::set("status:{$fileName}", 'failed');
                return back()->with('failed', 'An error occurred.')->with('file', $fileName);
            }
            return back()->with('success', 'File has been updated.')->with('file', $fileName);

        }
    }
}
