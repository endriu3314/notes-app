package com.uav.notesapp.model.response.notes;

import com.uav.notesapp.model.Note;

public class NoteCreateResponse {

    private String message;

    private Note note;

    public String getMessage() {
        return message;
    }

    public Note getNote() {
        return note;
    }
}
