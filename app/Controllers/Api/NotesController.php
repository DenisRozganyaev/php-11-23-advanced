<?php

namespace App\Controllers\Api;

use App\Models\Folder;
use App\Models\Note;
use App\Validators\Notes\CreateNotesValidator;
use App\Validators\Notes\UpdateNoteValidator;
use Enums\SqlOrder;

class NotesController extends BaseApiController
{
    public function index()
    {
        return $this->response(
            body: Note::where('user_id', '=', authId())
                ->orderBy([
                    'updated_at' => SqlOrder::DESC
                ])
                ->get()
        );
    }

    public function show(int $id)
    {
        $note = Note::find($id);

        if ($note && $note->user_id !== authId()) {
            return $this->response(403, [], [
                'message' => 'This resource is forbidden for you'
            ]);
        }

        return $this->response(body: $note->toArray());
    }

    public function store()
    {
        $data = array_merge(
            requestBody(),
            ['user_id' => authId()]
        );
        $validator = new CreateNotesValidator();

        if ($validator->validate($data) && $note = Note::create($data)) {
            return $this->response(body: $note->toArray());
        }

        return $this->response(errors: $validator->getErrors());
    }

    public function update(int $id)
    {
        $note = Note::find($id);

        if ($note && $note->user_id !== authId()) {
            return $this->response(403, errors: [
                'message' => 'This resource is forbidden for you'
            ]);
        }

        $data = [
            ...requestBody(),
            'updated_at' => date('Y-m-d H:i:s')
        ];

        $validator = new UpdateNoteValidator();
        dd($validator->validate($data));
//        if ($validator->validate($data) && $folder = $folder->update($data)) {
//            return $this->response(body: $folder->toArray());
//        }

        return $this->response(errors: $validator->getErrors());
    }

    public function destroy(int $id)
    {
        $note = Note::find($id);

        if ($note && $note->user_id !== authId()) {
            return $this->response(403, [], [
                'message' => 'This resource is forbidden for you'
            ]);
        }

        $result = Note::destroy($id);

        if (!$result) {
            return $this->response(422, [], ['message' => 'Oops smth went wrong']);
        }

        return $this->response();
    }
}
