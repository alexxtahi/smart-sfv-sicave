<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\File;
use Tests\TestCase;

class UserTest extends TestCase
{
    public function test_store_user_from_uploaded_file()
    {
        $this->withExceptionHandling();
        $fileToUpload = File::get(public_path('modele-importation-en-masse-compte-user.xlsx'));
        // $fileToUpload = Storage::get(storage_path('modeles/modele-importation-en-masse-compte-user.xlsx'));
        $response = $this->post('/users/store-from-upload', [
            'fileToUpload' => $fileToUpload
        ]);
        dd($response);
        $response->assertOK();
    }
}
