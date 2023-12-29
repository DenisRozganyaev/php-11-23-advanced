<?php

namespace App\Controllers\Api;

use App\Models\Folder;
use App\Models\Note;
use App\Models\SharedNote;
use App\Validators\Folders\CreateFolderValidator;
use Enums\Folders;
use Enums\SQL;
use Enums\SqlOrder;

class FoldersController extends BaseApiController
{
    public function index()
    {
        return $this->response(
            body: Folder::where('user_id', '=', authId())
                ->orWhere('user_id', 'IS', SQL::NULL->value)
                ->orderBy([
                    'user_id' => SqlOrder::ASC,
                    'title' => SqlOrder::ASC
                ])
                ->get()
        );
    }

    public function show(int $id)
    {
        $folder = Folder::find($id);

        if ($folder && !is_null($folder->user_id) && $folder->user_id !== authId()) {
            return $this->response(403, [], [
                'message' => 'This resource is forbidden for you'
            ]);
        }

        return $this->response(body: $folder->toArray());
    }

    public function notes(int $id)
    {
        $folder = Folder::find($id);

        $notes = match ($folder->title) {
            Folders::GENERAL->value => Note::where('folder_id', '=', $id)->andWhere('user_id', '=', authId())->get(),
            Folders::SHARED->value => Note::select(['notes.*'])
                ->join(
                    SharedNote::$tableName,
                    [
                        [
                            'left' => 'notes.id',
                            'operator' => '=',
                            'right' => SharedNote::$tableName . '.note_id' # тут була помилка, на лекції було .id а має бути .note_id
                        ],
                        [
                            'left' => authId(),
                            'operator' => '=',
                            'right' => SharedNote::$tableName . '.user_id'
                        ]
                    ],
                    'RIGHT'
                )->get(),
            default => Note::where('folder_id', '=', $id),
        };

        return $this->response(body: $notes);
    }

    public function store()
    {
        $data = array_merge(
            requestBody(),
            ['user_id' => authId()]
        );
        $validator = new CreateFolderValidator();

        if ($validator->validate($data) && $folder = Folder::create($data)) {
            return $this->response(body: $folder->toArray());
        }

        return $this->response(errors: $validator->getErrors());
    }

    public function update(int $id)
    {
        $folder = Folder::find($id);

        if ($folder && is_null($folder->user_id) && $folder->user_id !== authId()) {
            return $this->response(403, errors: [
                'message' => 'This resource is forbidden for you'
            ]);
        }

        $data = [
            ...requestBody(),
            'updated_at' => date('Y-m-d H:i:s')
        ];

        $validator = new CreateFolderValidator();

        if ($validator->validate($data) && $folder = $folder->update($data)) {
            return $this->response(body: $folder->toArray());
        }

        return $this->response(errors: $validator->getErrors());
    }

    public function destroy(int $id)
    {
        $folder = Folder::find($id);

        if ($folder && is_null($folder->user_id) && $folder->user_id !== authId()) {
            return $this->response(403, [], [
                'message' => 'This resource is forbidden for you'
            ]);
        }

        $result = Folder::destroy($id);

        if (!$result) {
            return $this->response(422, [], ['message' => 'Oops smth went wrong']);
        }

        return $this->response();
    }
}
