<?php

namespace App\Controllers\Api;

use App\Models\Note;
use App\Models\SharedNote;
use App\Models\User;
use App\Validators\SharedNoteValidator;

class SharedNotesController extends BaseApiController
{
    protected SharedNoteValidator $validator;

    public function __construct()
    {
        $this->validator = new SharedNoteValidator();
    }

    public function add(int $note_id)
    {
        $data = [
            'note_id' => $note_id,
            ...requestBody()
        ];

        if ($this->validator->validate($data) && !$this->validator->isNoteSharedWithUser($data) && $sharedNote = SharedNote::create($data)) {
            $note = Note::find($sharedNote->note_id);
            return $this->response(body: $note->toArray());
        }

        return $this->response(code: 422, errors: $this->validator->getErrors());
    }

    public function remove(int $note_id)
    {
        $data = [
            'note_id' => $note_id,
            ...requestBody()
        ];

        if ($this->validator->validate($data) && $this->validator->isNoteSharedWithUser($data)) {
            $result = SharedNote::where('user_id', '=', $data['user_id'])
                ->andWhere('note_id', '=', $data['note_id'])
                ->delete();

            return $this->response(body: [
                'result' => $result ? 'Removed' : 'Failed'
            ]);
        }

        return $this->response(code: 422, errors: $this->validator->getErrors());
    }
}
