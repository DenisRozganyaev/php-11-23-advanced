<?php

namespace App\Validators;

use App\Models\Note;
use App\Models\SharedNote;
use App\Models\User;

class SharedNoteValidator extends BaseValidator
{
    protected array $rules = [
        'user_id' => '/\d+/i',
        'note_id' => '/\d+/i',
    ];

    protected array $errors = [
        'user_id' => 'User id should be integer',
        'note_id' => 'User id should be integer',
    ];

    protected function isUserExists(array $fields): bool
    {
        $exists = User::where('id', '=', $fields['user_id'])->exists();

        if (!$exists) {
            $this->setError('user_id', 'The user with id = ' . $fields['user_id'] . ' does not exists');
        }

        return $exists;
    }

    public function isNoteSharedWithUser(array $fields): bool
    {
        $alreadyShared = SharedNote::where('user_id', '=', $fields['user_id'])
            ->andWhere('note_id', '=', $fields['note_id'])
            ->exists();

        if ($alreadyShared) {
            $this->setError('message', 'The note with id = ' . $fields['note_id'] . ' already shared for user id = ' . $fields['user_id']);
        }

        return $alreadyShared;
    }

    protected function sharedUserIsNotOwner(array $fields): bool
    {
        $note = Note::find($fields['note_id']);

        return $fields['user_id'] !== $note->user_id;
    }

    public function validate(array $fields = []): bool
    {
        return parent::validate($fields) && $this->sharedUserIsNotOwner($fields) && $this->isUserExists($fields);
    }
}
