<?php

namespace App\Controllers\Api;

use App\Models\Folder;
use App\Validators\Folders\CreateFolderValidator;

class FoldersController extends BaseApiController
{
    public function index()
    {
        dd(__METHOD__);
    }

    public function show(int $id)
    {
        dd(__METHOD__, $id);
    }

    public function store()
    {
        $data = array_merge(
            requestBody(),
            ['user_id' => authId()]
        );
        $validator = new CreateFolderValidator();

        if ($validator->validate($data)) {
            $folder = Folder::create($data);
            dd($folder);
        }

        return $this->response(200, [], $validator->getErrors());
    }

    public function update(int $id)
    {
        dd(__METHOD__, $id);
    }

    public function destroy(int $id)
    {
        dd(__METHOD__, $id);
    }
}
