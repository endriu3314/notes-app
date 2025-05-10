package com.uav.notesapp.model.requests.notes;

public class UpdateNoteRequest {

    private final String title;
    private final String content;

    public UpdateNoteRequest(String title, String content) {
        this.title = title;
        this.content = content;
    }

    public String getTitle() {
        return title;
    }

    public String getContent() {
        return content;
    }
}
