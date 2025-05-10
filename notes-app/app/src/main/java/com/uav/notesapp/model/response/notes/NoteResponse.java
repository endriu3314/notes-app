package com.uav.notesapp.model.response.notes;

import com.uav.notesapp.model.Note;

public class NoteResponse {

    private String message;
    private Note data;

    public String getMessage() {
        return message;
    }

    public Note getData() {
        return data;
    }
}
